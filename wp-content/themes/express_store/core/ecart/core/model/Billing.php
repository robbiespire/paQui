<?php
/**
 * Billing class
 * Billing information
 *
 * @version 1.0
 * @package ecart
 **/

class Billing extends DatabaseObject {
	static $table = "billing";

	function Billing ($id=false,$key=false) {
		$this->init(self::$table);
		if ($this->load($id,$key)) return true;
		else return false;
	}

	function exportcolumns () {
		$prefix = "b.";
		return array(
			$prefix.'address' => __('Billing Street Address','Ecart'),
			$prefix.'xaddress' => __('Billing Street Address 2','Ecart'),
			$prefix.'city' => __('Billing City','Ecart'),
			$prefix.'state' => __('Billing State/Province','Ecart'),
			$prefix.'country' => __('Billing Country','Ecart'),
			$prefix.'postcode' => __('Billing Postal Code','Ecart'),
			);
	}

} // end Billing class

?>