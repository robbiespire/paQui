<?php
/**
 * ImageServer
 * Provides low-overhead image service support
 *
 * @version 1.0
 * @package ecart
 * @subpackage image
 **/

chdir(dirname(__FILE__));

require_once(realpath('DB.php'));
require_once(realpath('functions.php'));
require_once('model/Error.php');
require_once('model/Settings.php');
require_once("model/Modules.php");

require_once("model/Meta.php");
require_once("model/Asset.php");

/**
 * ImageServer class
 *
 * @since 1.1
 * @package image
 **/
class ImageServer extends DatabaseObject {

	var $request = false;
	var $caching = true;
	var $parameters = array();
	var $args = array('width','height','scale','sharpen','quality','fill');
	var $scaling = array('all','matte','crop','width','height');
	var $width;
	var $height;
	var $scale = 0;
	var $sharpen = 0;
	var $quality = 80;
	var $fill = false;
	var $valid = false;
	var $Image = false;

	function __construct () {
		define('ECART_PATH',sanitize_path(dirname(dirname(__FILE__))));
		define("ECART_STORAGE",ECART_PATH."/storage");

		$this->dbinit();
		$this->request();
		$this->settings();
		if ($this->load())
			$this->render();
		else $this->error();
	}

	/**
	 * Parses the request to determine the image to load
	 *
	 * @since 1.1
	 *
	 * @return void
	 **/
	function request () {
		foreach ($_GET as $key => $value) {
			if ($key == "siid") $this->request = $value;
			if (isset($key) && empty($value))
				$this->parameters = explode(',',$key);
				$this->valid = array_pop($this->parameters);
		}

		// Handle pretty permalinks
		if (preg_match('/\/images\/(\d+).*$/',$_SERVER['REQUEST_URI'],$matches))
			$this->request = $matches[1];

		foreach ($this->parameters as $index => $arg)
			if ($arg !== false) $this->{$this->args[$index]} = intval($arg);

		if ($this->height == 0 && $this->width > 0) $this->height = $this->width;
		if ($this->width == 0 && $this->height > 0) $this->width = $this->height;
		$this->scale = $this->scaling[$this->scale];

		// Handle clear image requests (used in product gallery to reserve DOM dimensions)
		if ('000' == substr($this->request,0,3)) $this->clearpng();
	}

	/**
	 * Loads the requested image for display
	 *
	 * @since 1.1
	 * @return boolean Status of the image load
	 **/
	function load () {
		$this->Image = new ImageAsset($this->request);
		if (max($this->width,$this->height) > 0) $this->loadsized();
		if (!empty($this->Image->id) || !empty($this->Image->data)) return true;
		else return false;
	}

	function loadsized () {
		// Same size requested, skip resizing
		if ($this->Image->width == $this->width && $this->Image->height == $this->height) return;

		$Cached = new ImageAsset(array(
				'parent' => $this->Image->id,
				'context'=>'image',
				'type'=>'image',
				'name'=>'cache_'.implode('_',$this->parameters)
		));

		// Use the cached version if it exists, otherwise resize the image
		if (!empty($Cached->id) && $this->caching) $this->Image = $Cached;
		else $this->resize(); // No cached copy exists, recreate

	}

	function resize () {
		$key = (defined('SECRET_AUTH_KEY') && SECRET_AUTH_KEY != '')?SECRET_AUTH_KEY:DB_PASSWORD;
		$message = $this->Image->id.','.implode(',',$this->parameters);
		if ($this->valid != crc32($key.$message)) {
			header("HTTP/1.1 404 Not Found");
			die('');
		}

		require_once(ECART_PATH."/core/model/Image.php");
		$Resized = new ImageProcessor($this->Image->retrieve(),$this->Image->width,$this->Image->height);
		$scaled = $this->Image->scaled($this->width,$this->height,$this->scale);
		$alpha = ($this->Image->mime == "image/png");
		$Resized->scale($scaled['width'],$scaled['height'],$this->scale,$alpha,$this->fill);

		// Post sharpen
		if ($this->sharpen !== false)
			$Resized->UnsharpMask($this->sharpen);

		$ResizedImage = new ImageAsset();
		$ResizedImage->copydata($this->Image,false,array());
		$ResizedImage->name = 'cache_'.implode('_',$this->parameters);
		$ResizedImage->filename = $ResizedImage->name.'_'.$ResizedImage->filename;
		$ResizedImage->parent = $this->Image->id;
		$ResizedImage->context = 'image';
		$ResizedImage->mime = "image/jpeg";
		$ResizedImage->id = false;
		$ResizedImage->width = $Resized->width;
		$ResizedImage->height = $Resized->height;
		foreach ($this->args as $index => $arg)
			$ResizedImage->settings[$arg] = isset($this->parameters[$index])?intval($this->parameters[$index]):false;

		$ResizedImage->data = $Resized->imagefile($this->quality);
		if (empty($ResizedImage->data)) return false;

		$ResizedImage->size = strlen($ResizedImage->data);
		$this->Image = $ResizedImage;
		if ($ResizedImage->store( $ResizedImage->data ) === false)
			return false;

		$ResizedImage->save();

	}

	/**
	 * Output the image to the browser
	 *
	 * @since 1.1
	 * @return void
	 **/
	function render () {
		$found = $this->Image->found();
		if (!$found) return $this->error();

		if (is_array($found) && isset($found['redirect'])) {
			$this->Image->output(false);
		} else $this->Image->output();
		exit();
	}

	/**
	 * Output a default image when the requested image is not found
	 *
	 * @since 1.1
	 * @return void
	 **/
	function error () {
		header("HTTP/1.1 404 Not Found");
		$notfound = sanitize_path(dirname(__FILE__)).'/ui/icons/notfound.png';
		if (defined('ECART_NOTFOUND_IMAGE') && file_exists(ECART_NOTFOUND_IMAGE))
			$notfound = ECART_NOTFOUND_IMAGE;
		if (!file_exists($notfound)) die('<h1>404 Not Found</h1>');
		else {
			header("Cache-Control: no-cache, must-revalidate");
			header("Content-type: image/png");
			header("Content-Disposition: inline; filename=".basename($notfound)."");
			header("Content-Description: Delivered by WordPress/Ecart Image Server");
			header("Content-length: ".@strlen($notfound));
			@readfile($notfound);
		}
		die();
	}

	/**
	 * Renders a transparent PNG of the requested dimensions
	 *
	 * Used in the product gallery to reserve DOM dimensions so the
	 * gallery is rendered with the proper layout
	 *
	 * @since 1.1.7
	 *
	 * @return void Description...
	 **/
	function clearpng () {
		require_once(ECART_PATH."/core/model/Image.php");
		$max = 1920;
		$this->width = min($max,$this->width);
		$this->height = min($max,$this->height);
		$ImageData = new ImageProcessor(false,$this->width,$this->height);
		$ImageData->canvas($this->width,$this->height,true);
		$image = $ImageData->imagefile(100);
		header("Cache-Control: no-cache, must-revalidate");
		header("Content-type: image/png");
		header("Content-Disposition: inline; filename=clear.png");
		header("Content-Description: Delivered by WordPress/Ecart Image Server");
		header("Content-length: ".@strlen($image));
		die($image);
	}

	function settings () {
		global $Ecart;
		$Ecart->Settings = new Settings();
		$this->Settings = &EcartSettings();
	}

	/**
	 * Read the wp-config file to connect to the database
	 *
	 * @since 1.1
	 * @return void
	 **/
	function dbinit () {
		$db = DB::get();

		// Skip init if a connection exists
		if (defined('ABSPATH') && $db->dbh !== false) return;

		global $table_prefix;

		if (!load_ecarts_wpconfig())
			$this->error();

		chdir(ABSPATH.'wp-content');

		// Establish database connection
		$db->connect(DB_USER,DB_PASSWORD,DB_NAME,DB_HOST);
		if ($db->dbh === false) {
			error_reporting(E_ALL);
			ini_set('display_errors',1);
			trigger_error('Error establishing a database connection',E_USER_ERROR);
			die();
		}

		if (defined('DB_CHARSET')) {
			if (function_exists('mysql_set_charset'))
				mysql_set_charset(DB_CHARSET, $db->dbh);
			else {
				$query = "SET NAMES '".DB_CHARSET."'";
				if (defined('DB_COLLATE')) $query .= " COLLATE '".DB_COLLATE."'";
				$db->query($query);
			}
		}

		if (is_multisite()) ecart_ms_tableprefix();

	}

} // end ImageServer class

/**
 * Stub for compatibility
 **/
if (!function_exists('__')) {
	// Localization API is not available at this point
	function __ ($string,$domain=false) {
		return $string;
	}
}

if (!function_exists('is_multisite')) {
	function is_multisite() {
		if ( defined( 'MULTISITE' ) )
			return MULTISITE;

		if ( defined( 'VHOST' ) || defined( 'SUNRISE' ) )
			return true;

		return false;
	}
}

// Start the server
new ImageServer();

?>