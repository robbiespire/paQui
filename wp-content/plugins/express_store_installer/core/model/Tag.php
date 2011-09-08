<?php
/**
 * Tag class
 * Catalog product tag table
 * 
 * @version 1.0
 * @package ecart
 **/

class Tag extends DatabaseObject {
	static $table = "tag";

	function Tag ($id=false,$key=false) {
		$this->init(self::$table);
		if ($this->load($id,$key)) return true;
		else return false;
	}

} // end Tag class

?>