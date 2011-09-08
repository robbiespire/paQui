<?php
/**
 * Purchase class
 * Order invoice logging
 *
 * @version 1.0
 * @package ecart
 **/

require_once("Purchased.php");

class Purchase extends DatabaseObject {
	static $table = "purchase";
	var $purchased = array();
	var $columns = array();
	var $downloads = false;

	function Purchase ($id=false,$key=false) {

		$this->init(self::$table);
		if (!$id) return true;
		if ($this->load($id,$key)) return true;
		else return false;
	}

	function load_purchased () {
		$db = DB::get();

		$table = DatabaseObject::tablename(Purchased::$table);
		$meta = DatabaseObject::tablename(MetaObject::$table);
		if (empty($this->id)) return false;
		$this->purchased = $db->query("SELECT * FROM $table WHERE purchase=$this->id",AS_ARRAY);
		foreach ($this->purchased as &$purchase) {
			if (!empty($purchase->download)) $this->downloads = true;
			$purchase->data = unserialize($purchase->data);
			if ($purchase->addons == "yes") {
				$purchase->addons = new ObjectMeta($purchase->id,'purchased','addon');
				if (!$purchase->addons) $purchase->addons = new ObjectMeta();
			}
		}

		return true;
	}

	function notification ($addressee,$address,$subject,$template="order.php",$receipt="receipt.php") {
		global $Ecart;
		global $is_IIS;

		if ($template == "order.php" && file_exists(ECART_TEMPLATES."/order.html")) $template = ECART_TEMPLATES."/order.html";
		else $template = trailingslashit(ECART_TEMPLATES).$template;
		if (!file_exists($template))
			return new EcartError(__('A purchase notification could not be sent because the template for it does not exist.','purchase_notification_template',ECART_ADMIN_ERR));

		// Send the e-mail receipt
		$email = array();
		$email['from'] = '"'.wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ).'"';
		if ($Ecart->Settings->get('merchant_email'))
			$email['from'] .= ' <'.$Ecart->Settings->get('merchant_email').'>';
		if($is_IIS) $email['to'] = $address;
		else $email['to'] = '"'.html_entity_decode($addressee,ENT_QUOTES).'" <'.$address.'>';
		$email['subject'] = $subject;
		$email['receipt'] = $this->receipt($receipt);
		$email['url'] = get_bloginfo('siteurl');
		$email['sitename'] = get_bloginfo('name');
		$email['orderid'] = $this->id;

		$email = apply_filters('ecart_email_receipt_data',$email);

		if (ecart_email($template,$email)) {
			if (ECART_DEBUG) new EcartError('A purchase notification was sent to: '.$email['to'],false,ECART_DEBUG_ERR);
			return true;
		}

		if (ECART_DEBUG) new EcartError('A purchase notification FAILED to be sent to: '.$email['to'],false,ECART_DEBUG_ERR);
		return false;
	}

	function copydata ($Object,$prefix="") {
		$ignores = array("_datatypes","_table","_key","_lists","id","created","modified");
		foreach(get_object_vars($Object) as $property => $value) {
			$property = $prefix.$property;
			if (property_exists($this,$property) &&
				!in_array($property,$ignores))
				$this->{$property} = $value;
		}
	}

	function exportcolumns () {
		$prefix = "o.";
		return array(
			$prefix.'id' => __('Order ID','Ecart'),
			$prefix.'ip' => __('Customer\'s IP Address','Ecart'),
			$prefix.'firstname' => __('Customer\'s First Name','Ecart'),
			$prefix.'lastname' => __('Customer\'s Last Name','Ecart'),
			$prefix.'email' => __('Customer\'s Email Address','Ecart'),
			$prefix.'phone' => __('Customer\'s Phone Number','Ecart'),
			$prefix.'company' => __('Customer\'s Company','Ecart'),
			$prefix.'card' => __('Credit Card Number','Ecart'),
			$prefix.'cardtype' => __('Credit Card Type','Ecart'),
			$prefix.'cardexpires' => __('Credit Card Expiration Date','Ecart'),
			$prefix.'cardholder' => __('Credit Card Holder\'s Name','Ecart'),
			$prefix.'address' => __('Billing Street Address','Ecart'),
			$prefix.'xaddress' => __('Billing Street Address 2','Ecart'),
			$prefix.'city' => __('Billing City','Ecart'),
			$prefix.'state' => __('Billing State/Province','Ecart'),
			$prefix.'country' => __('Billing Country','Ecart'),
			$prefix.'postcode' => __('Billing Postal Code','Ecart'),
			$prefix.'shipaddress' => __('Shipping Street Address','Ecart'),
			$prefix.'shipxaddress' => __('Shipping Street Address 2','Ecart'),
			$prefix.'shipcity' => __('Shipping City','Ecart'),
			$prefix.'shipstate' => __('Shipping State/Province','Ecart'),
			$prefix.'shipcountry' => __('Shipping Country','Ecart'),
			$prefix.'shippostcode' => __('Shipping Postal Code','Ecart'),
			$prefix.'shipmethod' => __('Shipping Method','Ecart'),
			$prefix.'promos' => __('Promotions Applied','Ecart'),
			$prefix.'subtotal' => __('Order Subtotal','Ecart'),
			$prefix.'discount' => __('Order Discount','Ecart'),
			$prefix.'freight' => __('Order Shipping Fees','Ecart'),
			$prefix.'tax' => __('Order Taxes','Ecart'),
			$prefix.'total' => __('Order Total','Ecart'),
			$prefix.'fees' => __('Transaction Fees','Ecart'),
			$prefix.'txnid' => __('Transaction ID','Ecart'),
			$prefix.'txnstatus' => __('Transaction Status','Ecart'),
			$prefix.'gateway' => __('Payment Gateway','Ecart'),
			$prefix.'status' => __('Order Status','Ecart'),
			$prefix.'data' => __('Order Data','Ecart'),
			$prefix.'created' => __('Order Date','Ecart'),
			$prefix.'modified' => __('Order Last Updated','Ecart')
			);
	}

	// Display a sales receipt
	function receipt ($template="receipt.php") {
		if (!file_exists(ECART_TEMPLATES."/$template")) $template = "receipt.php";
		if (empty($this->purchased)) $this->load_purchased();
		ob_start();
		include(ECART_TEMPLATES."/$template");
		$content = ob_get_contents();
		ob_end_clean();
		return apply_filters('ecart_order_receipt',$content);
	}

	function tag ($property,$options=array()) {
		global $Ecart;

		$taxes = isset($options['taxes'])?$options['taxes']:false;
		$taxrate = 0;
		if ($property == "item-unitprice" || $property == "item-total")
			$taxrate = ecart_taxrate($taxes);

		// Return strings with no options
		switch ($property) {
			case "receipt":
				// Skip the receipt processing when sending order notifications in admin without the receipt
				if (defined('WP_ADMIN') && isset($_POST['receipt']) && $_POST['receipt'] == "no") return;
				if (isset($options['template']) && is_readable(ECART_TEMPLATES."/".$options['template']))
					return $this->receipt($template);
				else return $this->receipt();
				break;
			case "url": return ecarturl(false,'account'); break;
			case "id": return $this->id; break;
			case "customer": return $this->customer; break;
			case "date":
				if (empty($options['format'])) $options['format'] = get_option('date_format').' '.get_option('time_format');
				return _d($options['format'],((is_int($this->created))?$this->created:mktimestamp($this->created)));
				break;
			case "card": return (!empty($this->card))?sprintf("%'X16d",$this->card):''; break;
			case "cardtype": return $this->cardtype; break;
			case "txnid":
			case "transactionid": return $this->txnid; break;
			case "firstname": return esc_html($this->firstname); break;
			case "lastname": return esc_html($this->lastname); break;
			case "company": return esc_html($this->company); break;
			case "email": return esc_html($this->email); break;
			case "phone": return esc_html($this->phone); break;
			case "address": return esc_html($this->address); break;
			case "xaddress": return esc_html($this->xaddress); break;
			case "city": return esc_html($this->city); break;
			case "state":
				if (strlen($this->state > 2)) return esc_html($this->state);
				$regions = Lookup::country_zones();
				$states = $regions[$this->country];
				return $states[$this->state];
				break;
			case "postcode": return esc_html($this->postcode); break;
			case "country":
				$countries = $Ecart->Settings->get('target_markets');
				return $countries[$this->country]; break;
			case "shipaddress": return esc_html($this->shipaddress); break;
			case "shipxaddress": return esc_html($this->shipxaddress); break;
			case "shipcity": return esc_html($this->shipcity); break;
			case "shipstate":
				if (strlen($this->shipstate > 2)) return esc_html($this->shipstate);
				$regions = Lookup::country_zones();
				$states = $regions[$this->country];
				return $states[$this->shipstate];
				break;
			case "shippostcode": return esc_html($this->shippostcode); break;
			case "shipcountry":
				$countries = $Ecart->Settings->get('target_markets');
				return $countries[$this->shipcountry]; break;
			case "shipmethod": return esc_html($this->shipmethod); break;
			case "totalitems": return count($this->purchased); break;
			case "has-items":
			case "hasitems":
				if (empty($this->purchased)) $this->load_purchased();
				return (count($this->purchased) > 0);
				break;
			case "items":
				if (!isset($this->_items_loop)) {
					reset($this->purchased);
					$this->_items_loop = true;
				} else next($this->purchased);

				if (current($this->purchased) !== false) return true;
				else {
					unset($this->_items_loop);
					return false;
				}
			case "item-id":
				$item = current($this->purchased);
				return $item->id; break;
			case "item-product":
				$item = current($this->purchased);
				return $item->product; break;
			case "item-price":
				$item = current($this->purchased);
				return $item->price; break;
			case "item-name":
				$item = current($this->purchased);
				return $item->name; break;
			case "item-description":
				$item = current($this->purchased);
				return $item->description; break;
			case "item-options":
				if (!isset($options['after'])) $options['after'] = "";
				$item = current($this->purchased);
				return (!empty($item->optionlabel))?$options['before'].$item->optionlabel.$options['after']:''; break;
			case "item-sku":
				$item = current($this->purchased);
				return $item->sku; break;
			case "item-download":
				$item = current($this->purchased);
				if (empty($item->download)) return "";
				if (!isset($options['label'])) $options['label'] = __('Download','Ecart');
				$classes = "";
				if (isset($options['class'])) $classes = ' class="'.$options['class'].'"';
				$request = ECART_PRETTYURLS?
					"download/$item->dkey":
					array('src'=>'download','ecart_download'=>$item->dkey);
				$url = ecarturl($request,'catalog');
				return '<a href="'.$url.'"'.$classes.'>'.$options['label'].'</a>'; break;
			case "item-quantity":
				$item = current($this->purchased);
				return $item->quantity; break;
			case "item-unitprice":
				$item = current($this->purchased);
				$amount = $item->unitprice+($this->taxing == 'inclusive'?$item->unittax:0);
				return money($amount); break;
			case "item-total":
				$item = current($this->purchased);
				$amount = $item->total+($this->taxing == 'inclusive'?$item->unittax*$item->quantity:0);
				return money($amount); break;
			case "item-has-inputs":
			case "item-hasinputs":
				$item = current($this->purchased);
				return (count($item->data) > 0); break;
			case "item-inputs":
				$item = current($this->purchased);
				if (!isset($this->_iteminputs_loop)) {
					reset($item->data);
					$this->_iteminputs_loop = true;
				} else next($item->data);

				if (current($item->data) !== false) return true;
				else {
					unset($this->_iteminputs_loop);
					return false;
				}
				break;
			case "item-input":
				$item = current($this->purchased);
				$data = current($item->data);
				$name = key($item->data);
				if (isset($options['name'])) return esc_html($name);
				return esc_html($data);
				break;
			case "item-inputs-list":
			case "item-inputslist":
			case "item-inputs-list":
			case "iteminputslist":
				$item = current($this->purchased);
				if (empty($item->data)) return false;
				$before = ""; $after = ""; $classes = ""; $excludes = array();
				if (!empty($options['class'])) $classes = ' class="'.$options['class'].'"';
				if (!empty($options['exclude'])) $excludes = explode(",",$options['exclude']);
				if (!empty($options['before'])) $before = $options['before'];
				if (!empty($options['after'])) $after = $options['after'];

				$result .= $before.'<ul'.$classes.'>';
				foreach ($item->data as $name => $data) {
					if (in_array($name,$excludes)) continue;
					$result .= '<li><strong>'.esc_html($name).'</strong>: '.esc_html($data).'</li>';
				}
				$result .= '</ul>'.$after;
				return $result;
				break;
			case "item-has-addons":
			case "item-hasaddons":
				$item = current($this->purchased);
				return (count($item->addons) > 0); break;
			case "item-addons":
				$item = current($this->purchased);
				if (!isset($this->_itemaddons_loop)) {
					reset($item->addons->meta);
					$this->_itemaddons_loop = true;
				} else next($item->addons->meta);

				if (current($item->addons->meta) !== false) return true;
				else {
					unset($this->_itemaddons_loop);
					return false;
				}
				break;
			case "item-addons":
				$item = current($this->purchased);
				$addon = current($item->addons->meta);
				if (isset($options['id'])) return esc_html($addon->id);
				if (isset($options['name'])) return esc_html($addon->name);
				if (isset($options['label'])) return esc_html($addon->name);
				if (isset($options['type'])) return esc_html($addon->value->type);
				if (isset($options['onsale'])) return $addon->value->onsale;
				if (isset($options['inventory'])) return $addon->value->inventory;
				if (isset($options['sku'])) return esc_html($addon->value->sku);
				if (isset($options['unitprice'])) return money($addon->value->unitprice);
				return money($addon->value->unitprice);
				break;
			case "item-addons-list":
			case "item-addonslist":
			case "item-addons-list":
			case "itemaddonslist":
				$item = current($this->purchased);
				if (empty($item->addons)) return false;
				$defaults = array(
					'prices' => "on",
					'download' => __('Download','Ecart'),
					'before' => '',
					'after' => '',
					'classes' => '',
					'excludes' => ''
				);
				$options = array_merge($defaults,$options);
				extract($options);

				$class = !empty($classes)?' class="'.join(' ',explode(',',$classes)).'"':'';
				$taxrate = 0;
				if ($item->unitprice > 0)
					$taxrate = round($item->unittax/$item->unitprice,4);

				$result = $before.'<ul'.$class.'>';
				foreach ($item->addons->meta as $id => $addon) {
					if (in_array($addon->name,$excludes)) continue;
					if ($this->taxing == "inclusive")
						$price = $addon->value->unitprice+($addon->value->unitprice*$taxrate);
					else $price = $addon->value->unitprice;

					$link = false;
					if (isset($addon->value->download) && isset($addon->value->dkey)) {
						$dkey = $addon->value->dkey;
						$request = ECART_PRETTYURLS?"download/$dkey":array('ecart_download'=>$dkey);
						$url = ecarturl($request,'catalog');
						$link = '<br /><a href="'.$url.'">'.$download.'</a>';
					}

					$pricing = value_is_true($prices)?" (".money($price).")":"";
					$result .= '<li>'.esc_html($addon->name.$pricing).$link.'</li>';
				}
				$result .= '</ul>'.$after;
				return $result;
				break;
			case "has-data":
			case "hasdata": return (is_array($this->data) && count($this->data) > 0); break;
			case "orderdata":
				if (!isset($this->_data_loop)) {
					reset($this->data);
					$this->_data_loop = true;
				} else next($this->data);

				if (current($this->data) !== false) return true;
				else {
					unset($this->_data_loop);
					return false;
				}
				break;
			case "data":
				if (!is_array($this->data)) return false;
				$data = current($this->data);
				$name = key($this->data);
				if (isset($options['name'])) return esc_html($name);
				return esc_html($data);
				break;
			case "promolist":
			case "promo-list":
				$output = "";
				if (!empty($this->promos)) {
					$output .= '<ul>';
					foreach ($this->promos as $promo)
						$output .= '<li>'.$promo.'</li>';
					$output .= '</ul>';
				}
				return $output;
			case "has-promo":
			case "haspromo":
				if (empty($options['name'])) return false;
				return (in_array($options['name'],$this->promos));
				break;
			case "subtotal": return money($this->subtotal); break;
			case "hasfreight": return (!empty($this->shipmethod) || $this->freight > 0);
			case "freight": return money($this->freight); break;
			case "hasdownloads": return ($this->downloads);
			case "hasdiscount": return ($this->discount > 0);
			case "discount": return money($this->discount); break;
			case "hastax": return ($this->tax > 0)?true:false;
			case "tax": return money($this->tax); break;
			case "total": return money($this->total); break;
			case "status":
				$labels = $Ecart->Settings->get('order_status');
				if (empty($labels)) $labels = array('');
				return $labels[$this->status];
				break;
			case "paid": return ($this->txnstatus == "CHARGED"); break;
			case "notpaid": return ($this->txnstatus != "CHARGED"); break;
			case "payment":
				$labels = Lookup::payment_status_labels();
				return isset($labels[$this->txnstatus])?$labels[$this->txnstatus]:$this->txnstatus; break;
		}
	}

} // end Purchase class

class PurchasesExport {
	var $sitename = "";
	var $headings = false;
	var $data = false;
	var $defined = array();
	var $purchase_cols = array();
	var $purchased_cols = array();
	var $selected = array();
	var $recordstart = true;
	var $content_type = "text/plain";
	var $extension = "txt";
	var $date_format = 'F j, Y';
	var $time_format = 'g:i:s a';
	var $set = 0;
	var $limit = 1024;

	function PurchasesExport () {
		global $Ecart;

		$this->purchase_cols = Purchase::exportcolumns();
		$this->purchased_cols = Purchased::exportcolumns();
		$this->defined = array_merge($this->purchase_cols,$this->purchased_cols);

		$this->sitename = get_bloginfo('name');
		$this->headings = ($Ecart->Settings->get('purchaselog_headers') == "on");
		$this->selected = $Ecart->Settings->get('purchaselog_columns');
		$this->date_format = get_option('date_format');
		$this->time_format = get_option('time_format');
		$Ecart->Settings->save('purchaselog_lastexport',mktime());
	}

	function query ($request=array()) {
		$db =& DB::get();
		if (empty($request)) $request = $_GET;

		if (!empty($request['start'])) {
			list($month,$day,$year) = explode("/",$request['start']);
			$starts = mktime(0,0,0,$month,$day,$year);
		}

		if (!empty($request['end'])) {
			list($month,$day,$year) = explode("/",$request['end']);
			$ends = mktime(0,0,0,$month,$day,$year);
		}

		$where = "WHERE o.id IS NOT NULL AND p.id IS NOT NULL ";
		if (isset($request['status']) && !empty($request['status'])) $where .= "AND status='{$request['status']}'";
		if (isset($request['s']) && !empty($request['s'])) $where .= " AND (id='{$request['s']}' OR firstname LIKE '%{$request['s']}%' OR lastname LIKE '%{$request['s']}%' OR CONCAT(firstname,' ',lastname) LIKE '%{$request['s']}%' OR transactionid LIKE '%{$request['s']}%')";
		if (!empty($request['start']) && !empty($request['end'])) $where .= " AND  (UNIX_TIMESTAMP(o.created) >= $starts AND UNIX_TIMESTAMP(o.created) <= $ends)";

		$purchasetable = DatabaseObject::tablename(Purchase::$table);
		$purchasedtable = DatabaseObject::tablename(Purchased::$table);
		$offset = ($this->set*$this->limit);

		$c = 0; $columns = array();
		foreach ($this->selected as $column) $columns[] = "$column AS col".$c++;
		$query = "SELECT ".join(",",$columns)." FROM $purchasedtable AS p LEFT JOIN $purchasetable AS o ON o.id=p.purchase $where ORDER BY o.created ASC LIMIT $offset,$this->limit";
		$this->data = $db->query($query,AS_ARRAY);
	}

	// Implement for exporting all the data
	function output () {
		if (!$this->data) $this->query();
		if (!$this->data) return false;

		header("Content-type: $this->content_type; charset=UTF-8");
		header("Content-Disposition: attachment; filename=\"$this->sitename Purchase Log.$this->extension\"");
		header("Content-Description: Delivered by WordPress/Ecart ".ECART_VERSION);
		header("Cache-Control: maxage=1");
		header("Pragma: public");

		$this->begin();
		if ($this->headings) $this->heading();
		$this->records();
		$this->end();
	}

	function begin() {}

	function heading () {
		foreach ($this->selected as $name)
			$this->export($this->defined[$name]);
		$this->record();
	}

	function records () {
		while (!empty($this->data)) {
			foreach ($this->data as $key => $record) {
				foreach(get_object_vars($record) as $column)
					$this->export($this->parse($column));
				$this->record();
			}
			$this->set++;
			$this->query();
		}
	}

	function parse ($column) {
		if (preg_match("/^[sibNaO](?:\:.+?\{.*\}$|\:.+;$|;$)/",$column)) {
			$list = unserialize($column);
			$column = "";
			foreach ($list as $name => $value)
				$column .= (empty($column)?"":";")."$name:$value";
		}
		return $column;
	}

	function end() {}

	// Implement for exporting a single value
	function export ($value) {
		echo ($this->recordstart?"":"\t").$value;
		$this->recordstart = false;
	}

	function record () {
		echo "\n";
		$this->recordstart = true;
	}

	function settings () {}

}

class PurchasesTabExport extends PurchasesExport {
	function PurchasesTabExport () {
		parent::PurchasesExport();
		$this->output();
	}
}

class PurchasesCSVExport extends PurchasesExport {
	function PurchasesCSVExport () {
		parent::PurchasesExport();
		$this->content_type = "text/csv";
		$this->extension = "csv";
		$this->output();
	}

	function export ($value) {
		$value = str_replace('"','""',$value);
		if (preg_match('/^\s|[,"\n\r]|\s$/',$value)) $value = '"'.$value.'"';
		echo ($this->recordstart?"":",").$value;
		$this->recordstart = false;
	}

}

class PurchasesXLSExport extends PurchasesExport {
	function PurchasesXLSExport () {
		parent::PurchasesExport();
		$this->content_type = "application/vnd.ms-excel";
		$this->extension = "xls";
		$this->c = 0; $this->r = 0;
		$this->output();
	}

	function begin () {
		echo pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0);
	}

	function end () {
		echo pack("ss", 0x0A, 0x00);
	}

	function export ($value) {
		if (preg_match('/^[\d\.]+$/',$value)) {
		 	echo pack("sssss", 0x203, 14, $this->r, $this->c, 0x0);
			echo pack("d", $value);
		} else {
			$l = strlen($value);
			echo pack("ssssss", 0x204, 8+$l, $this->r, $this->c, 0x0, $l);
			echo $value;
		}
		$this->c++;
	}

	function record () {
		$this->c = 0;
		$this->r++;
	}
}

class PurchasesIIFExport extends PurchasesExport {
	function PurchasesIIFExport () {
		global $Ecart;
		parent::PurchasesExport();
		$this->content_type = "application/qbooks";
		$this->extension = "iif";
		$account = $Ecart->Settings->get('purchaselog_iifaccount');
		if (empty($account)) $account = "Merchant Account";
		$this->selected = array(
			"'\nTRNS'",
			"DATE_FORMAT(o.created,'\"%m/%d/%Y\"')",
			"'\"$account\"'",
			"CONCAT('\"',o.firstname,' ',o.lastname,'\"')",
			"'\"Ecart Payment Received\"'",
			"o.total-o.fees",
			"''",
			"'\nSPL'",
			"DATE_FORMAT(o.created,'\"%m/%d/%Y\"')",
			"'\"Other Income\"'",
			"CONCAT('\"',o.firstname,' ',o.lastname,'\"')",
			"o.total*-1",
			"'\nSPL'",
			"DATE_FORMAT(o.created,'\"%m/%d/%Y\"')",
			"'\"Other Expenses\"'",
			"'Fee'",
			"o.fees",
			"''",
			"'\nENDTRNS'"
		);
		$this->output();
	}

	function begin () {
		echo "!TRNS\tDATE\tACCNT\tNAME\tCLASS\tAMOUNT\tMEMO\n!SPL\tDATE\tACCNT\tNAME\tAMOUNT\tMEMO\n!ENDTRNS";
	}

	function export ($value) {
		echo (substr($value,0,1) != "\n")?"\t".$value:$value;
	}

	function record () { }

	function settings () {
		global $Ecart;
		?>
		<div id="iif-settings" class="hidden">
			<input type="text" id="iif-account" name="settings[purchaselog_iifaccount]" value="<?php echo $Ecart->Settings->get('purchaselog_iifaccount'); ?>" size="30"/><br />
			<label for="iif-account"><small><?php _e('QuickBooks account name for transactions','Ecart'); ?></small></label>
		</div>
		<script type="text/javascript">
		/* <![CDATA[ */
		jQuery(document).ready( function() {
			var $=jqnc();
			$('#purchaselog-format').change(function () {
				if ($(this).val() == "iif") {
					$('#export-columns').hide();
					$('#iif-settings').show();
					$('#iif-account').focus();
				} else {
					$('#export-columns').show();
					$('#iif-settings').hide();
				}
			}).change();
		});
		/* ]]> */
		</script>
		<?php
	}
}


?>