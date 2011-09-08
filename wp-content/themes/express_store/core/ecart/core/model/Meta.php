<?php
/**
 * Meta.php
 *
 * The meta object abstract
 * 
 * @version 1.0
 * @package ecart
 * @subpackage meta
 **/

/**
 * MetaObject
 *
 * @since 1.1
 * @package ecart
 * @subpackage meta
 **/
class MetaObject extends DatabaseObject {
	static $table = "meta";

	var $context = 'product';
	var $type = 'meta';

	/**
	 * Meta constructor
	 *	 
	 *
	 * @return void
	 **/
	function __construct ($id=false,$key='id') {
		$this->init(self::$table);
		$this->load($id,$key);
	}

} // END class Meta

/**
 * MetasetObject
 *
 * Constructs a runtime object from a set of meta records
 *
 * @since 1.1
 * @package ecart
 * @subpackage meta
 **/
abstract class MetasetObject extends DatabaseObject {
	static $table = "meta";

	var $_table = false;	// Fully qualified table name
	var $_loaded = false;	// If he record is successfully loaded
	var $_meta = array();	// The meta record definitions
	var $_context = 'meta';	// The meta context
	var $_parent = 0;		// Linking reference to the root record
	var $_type = false;		// Type (class) of object

	var $id = false;		// The root record for the set

	function __construct ($id=false,$key='id') {
		$this->init(self::$table);
		$this->load($id,$key);
	}

	function init () {
		$this->_table = DatabaseObject::tablename(MetasetObject::$table);
		$this->_type = get_class($this);
		$properties = array_keys(get_object_vars($this));
		$this->_properties = array_filter($properties,array('MetasetObject','_ignore_'));
	}

	function load () {
		$db = &DB::get();

		$args = func_get_args();
		if (empty($args[0])) return false;
		if (is_array($args[0])) {
			foreach ($args[0] as $key => $id)
				$where .= ($where == ""?"":" AND ")."$key='".$db->escape($id)."'";
		} else $where = "{$args[1]}='{$args[0]}' OR (parent={$args[0]} AND context='meta')";

		$r = $db->query("SELECT * FROM $this->_table WHERE $where",AS_ARRAY);

		foreach ($r as $row) {
			$meta = new MetaObject();
			$meta->populate($row,'',array());
			$this->_meta[$meta->name] = $meta;

			// Seed properties
			$property = $meta->name;
			if ($property[0] != "_" && in_array($property,$this->_properties))
				$this->{$property} = $meta->value;

		}

		if (count($row) == 0) $this->_loaded = false;
		$this->_loaded = true;

		return $this->loaded;
	}

	/**
	 * Saves updates or creates records for the defined object properties
	 * 
	 * @since 1.1
	 *
	 * @return void Description...
	 **/
	function save () {
		$db = &DB::get();

		if (empty($this->id)) {
			$meta = new MetaObject();
			$meta->parent = 0;
			$meta->context = $this->_context;
			$meta->type = $this->_type;
			$meta->name = 'id';
			$meta->value = $meta->save();
			$this->_parent = $meta->value;
			$this->id = $meta->value;
			$this->_meta['id'] = $meta;
		}

		// Go through each data property of the object
		foreach(get_object_vars($this) as $property => $value) {
			if ($property[0] == "_") continue; // Skip mapping properties
			if (!isset($this->_meta[$property])) {
				$meta = new MetaObject();
				$meta->parent = $this->_parent;
				$meta->context = $this->_context;
				$meta->type = $this->_type;
				$meta->name = $property;
				$meta->value = $value;
				$this->_meta[$property] = $meta;
			} else $this->_meta[$property]->value = $value;
			$this->_meta[$property]->save();
		}

	}

	/**
	 * Deletes the entire set of meta entries for the combined record
	 *	 
	 * @since 1.1
	 *
	 * @return boolean
	 **/
	function delete () {
		$db = &DB::get();
		// Delete records
		if (!empty($this->id) && !empty($this->_parent))
			return $db->query("DELETE FROM $this->_table WHERE (id='$this->_parent' AND parent=0 AND context='$this->_meta') OR (parent='$this->_parent' AND context='$this->_meta')");
		else return false;
	}

	function _ignore_ ($property) {
		return ($property[0] != "_");
	}

}

/**
 * ObjectMeta
 *
 * Loads a group of meta data records that have been attached to another object
 *
 * @since 1.1
 * @package ecart
 * @subpackage meta
 **/
class ObjectMeta {
	static $table = "meta";

	var $_loaded = false;
	var $meta = array();
	var $named = array();

	function __construct ($parent=false,$context='product',$type=false,$sort='sortorder') {
		$this->_table = DatabaseObject::tablename(self::$table);

		$params = array(
			'parent' => $parent,
			'context' => $context
		);

		if ($type !== false) $params['type'] = $type;
		if ($parent !== false) $this->load($params);
	}

	function load () {
		$db = &DB::get();

		$args = func_get_args();
		if (empty($args[0])) return false;
		if (!is_array($args[0])) return false;

		$where = "";
		foreach ($args[0] as $key => $id)
			$where .= ($where == ""?"":" AND ")."$key='".$db->escape($id)."'";

		$r = $db->query("SELECT * FROM $this->_table WHERE $where",AS_ARRAY);

		foreach ($r as $row) {
			$meta = new MetaObject();
			$meta->populate($row,'',array());

			$this->meta[$meta->id] = $meta;
			$this->named[$meta->name] =& $this->meta[$meta->id];
		}

		if (isset($row) && count($row) == 0) $this->_loaded = false;
		$this->_loaded = true;

		return $this->_loaded;
	}

	function is_empty () {
		if (!$this->_loaded) return true;
		return (empty($this->meta));
	}

}

?>