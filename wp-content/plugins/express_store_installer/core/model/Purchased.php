<?php
/**
 * Purchased class
 * Purchased line items for orders
 *
 * @version 1.0
 * @package ecart
 **/

class Purchased extends DatabaseObject {
	static $table = "purchased";

	function Purchased ($id=false,$key=false) {
		$this->init(self::$table);
		if ($this->load($id,$key)) return true;
		else return false;
	}

	function copydata ($Item) {
		parent::copydata ($Item);
		if (isset($Item->option->label))
			$this->optionlabel = $Item->option->label;

		$this->addons = 'no';
		if (empty($Item->addons) || !is_array($Item->addons)) return true;
		$addons = array();
		// Create meta records for any addons
		foreach ((array)$Item->addons as $i => $Addon) {
			$Download = false;
			$Meta = new MetaObject(array(
				'parent' => $this->id,
				'context' => 'purchased',
				'type' => 'meta',
				'name' => $Addon->label
			));
			$Meta->context = 'purchased';
			$Meta->type = 'addon';
			$Meta->name = $Addon->label;
			$Meta->numeral = $Addon->unitprice;

			// Add a meta record to the purchased line item for an addon download
			if (!empty($Addon->download)) {
				$hash = array($this->name,$Addon->label,$this->purchase,$this->product,$this->price,$i,time());
				$Addon->dkey = sha1(join('',$hash));

				$Download = new MetaObject(array(
					'parent' => $this->id,
					'context' => 'purchased',
					'type' => 'download',
					'name' => $Addon->dkey
				));
				$Download->context = 'purchased';
				$Download->type = 'download';
				$Download->name = $Addon->dkey;
				$Download->value = $Addon->download;
			}

			$Meta->value = serialize($Addon);
			$addons[] = $Meta;
			if ($Download !== false) $addons[] = $Download;

		}
		$this->addons = $addons;
	}

	function save() {
		$addons = $this->addons;					// Save current addons model
		if (!empty($addons)) $this->addons = "yes";	// convert property to usable flag
		parent::save();
		if (!empty($addons) && is_array($addons)) {
			foreach ($addons as $Addon) {
				$Addon->parent = $this->id;
				$Addon->save();
			}
		}
		$this->addons = $addons; // restore addons model
	}

	function keygen () {
		$message = $this->name.$this->purchase.$this->product.$this->price.$this->download;
		$key = sha1($message);
		if (empty($key)) $key = md5($message);
		$this->dkey = $key;
		do_action_ref_array('ecart_download_keygen',array(&$this));
	}

	function exportcolumns () {
		$prefix = "p.";
		return array(
			$prefix.'id' => __('Line Item ID','Ecart'),
			$prefix.'name' => __('Product Name','Ecart'),
			$prefix.'optionlabel' => __('Product Variation Name','Ecart'),
			$prefix.'description' => __('Product Description','Ecart'),
			$prefix.'sku' => __('Product SKU','Ecart'),
			$prefix.'quantity' => __('Product Quantity Purchased','Ecart'),
			$prefix.'unitprice' => __('Product Unit Price','Ecart'),
			$prefix.'total' => __('Product Total Price','Ecart'),
			$prefix.'data' => __('Product Data','Ecart'),
			$prefix.'downloads' => __('Product Downloads','Ecart')
			);
	}

} // end Purchased class

?>