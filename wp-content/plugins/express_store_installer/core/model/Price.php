<?php
/**
 * Price.php
 *
 * Product price objects
 *
 * @version 1.0
 * @package ecart
 * @since 1.0
 * @subpackage products
 **/
class Price extends DatabaseObject {

	static $table = "price";

	function __construct ($id=false,$key=false) {
		$this->init(self::$table);
		if ($this->load($id,$key))
			$this->load_download();
	}

	/**
	 * Loads a product download attached to the price object
	 *	 
	 * @since 1.0
	 *
	 * @return boolean
	 **/
	function load_download () {
		if ($this->type != "Download") return false;
		$this->download = new ProductDownload(array(
			'parent' => $this->id,
			'context' => 'price',
			'type' => 'download'
			));

		if (empty($this->download->id)) return false;
		return true;
	}

	/**
	 * Attaches a product download asset to the price object
	 * 
	 * @since 1.1
	 *
	 * @return boolean
	 **/
	function attach_download ($id) {
		if (!$id) return false;

		$Download = new ProductDownload($id);
		$Download->parent = $this->id;
		$Download->save();

		do_action('attach_product_download',$id,$this->id);

		return true;
	}

} // END class Price

?>