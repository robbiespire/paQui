<?php
/*
Plugin Name: Express Store Installer
Version: 1.1.1
Description: Installs required database tables and prepare WordPress to run our framework.
Plugin URI: http://enovastudio.org
Author: EnovaStudio + CBMS
Author URI: http://enovastudio.org

*/

define('ECART_VERSION','1.1.1');
define('ECART_REVISION','$Rev: 1725 $');
define('ECART_GATEWAY_USERAGENT','WordPress Ecart Plugin/'.ECART_VERSION);
define('ECART_HOME','');
define('ECART_CUSTOMERS','');
define('ECART_DOCS','');

require("core/functions.php");
require("core/legacy.php");

ecart_prereqs();
ecart_timezone();

require_once("core/DB.php");
require_once("core/model/Settings.php");

// Serve images and bypass loading all of Ecart
if (isset($_GET['siid']) || preg_match('/images\/\d+/',$_SERVER['REQUEST_URI']))
	require("core/image.php");

if (isset($_GET['sjsl']))
	require("core/scripts.php");

// Load super controllers
require("core/flow/Flow.php");
require("core/flow/Storefront.php");
require("core/flow/Login.php");
require('core/flow/Scripts.php');

// Load frameworks & Ecart-managed data model objects
require("core/model/Modules.php");
require("core/model/Gateway.php");
require("core/model/Lookup.php");
require("core/model/Shopping.php");
require("core/model/Error.php");
require("core/model/Order.php");
require("core/model/Cart.php");
require("core/model/Meta.php");
require("core/model/Asset.php");
require("core/model/Catalog.php");
require("core/model/Purchase.php");
require("core/model/Customer.php");

// Start up the core
$Ecart = new Ecart();
do_action('ecart_loaded');

/**
 * Ecart class
 *
 * @package ecart
 * @since 1.0
 **/
class Ecart {
	var $Settings;			// Ecart settings registry
	var $Flow;				// Controller routing
	var $Catalog;			// The main catalog
	var $Category;			// Current category
	var $Product;			// Current product
	var $Purchase; 			// Currently requested order receipt
	var $Shopping; 			// The shopping session
	var $Errors;			// Error system
	var $Order;				// The current session Order
	var $Promotions;		// Active promotions registry
	var $SmartCategories;	// Smart Categories registry
	var $Gateways;			// Gateway modules
	var $Shipping;			// Shipping modules
	var $Storage;			// Storage engine modules

	var $path;		  		// File ystem path to the plugin
	var $file;		  		// Base file name for the plugin (this file)
	var $directory;	  		// The parent directory name
	var $uri;		  		// The URI fragment to the plugin
	var $siteurl;	  		// The full site URL
	var $wpadminurl;  		// The admin URL for the site

	var $_debug;

	function Ecart () {
		if (WP_DEBUG) {
			$this->_debug = new StdClass();
			if (function_exists('memory_get_peak_usage'))
				$this->_debug->memory = memory_get_peak_usage(true);
			if (function_exists('memory_get_usage'))
				$this->_debug->memory = memory_get_usage(true);
		}

		// Determine system and URI paths

		$this->path = sanitize_path(dirname(__FILE__));
		$this->file = basename(__FILE__);
		$this->directory = basename($this->path);

		$languages_path = array($this->directory,'lang');
		load_plugin_textdomain('Ecart',false,sanitize_path(join('/',$languages_path)));

		$this->uri =  get_template_directory_uri()."/core/".$this->directory;
		$this->siteurl = get_bloginfo('url');
		$this->wpadminurl = admin_url();

		if ($this->secure = is_ecart_secure()) {
			$this->uri = str_replace('http://','https://',$this->uri);
			$this->siteurl = str_replace('http://','https://',$this->siteurl);
			$this->wpadminurl = str_replace('http://','https://',$this->wpadminurl);
		}

		// Initialize settings & macros

		$this->Settings = new Settings();

		if (!defined('BR')) define('BR','<br />');

		// Overrideable macros
		if (!defined('ECART_NOSSL')) define('ECART_NOSSL',false);
		if (!defined('ECART_PREPAYMENT_DOWNLOADS')) define('ECART_PREPAYMENT_DOWNLOADS',false);
		if (!defined('ECART_SESSION_TIMEOUT')) define('ECART_SESSION_TIMEOUT',7200);
		if (!defined('ECART_QUERY_DEBUG')) define('ECART_QUERY_DEBUG',false);
		if (!defined('ECART_GATEWAY_TIMEOUT')) define('ECART_GATEWAY_TIMEOUT',10);
		if (!defined('ECART_SHIPPING_TIMEOUT')) define('ECART_SHIPPING_TIMEOUT',10);
		if (!defined('ECART_TEMP_PATH')) define('ECART_TEMP_PATH',sys_get_temp_dir());

		// Settings & Paths
		define("ECART_DEBUG",($this->Settings->get('error_logging') == 2048));
		define("ECART_PATH",$this->path);
		define("ECART_PLUGINURI",$this->uri);
		define("ECART_PLUGINFILE",$this->directory."/".$this->file);

		define("ECART_ADMIN_DIR","/core/ui");
		define("ECART_ADMIN_PATH",ECART_PATH.ECART_ADMIN_DIR);
		define("ECART_ADMIN_URI",ECART_PLUGINURI.ECART_ADMIN_DIR);
		define("ECART_FLOW_PATH",ECART_PATH."/core/flow");
		define("ECART_MODEL_PATH",ECART_PATH."/core/model");
		define("ECART_GATEWAYS",ECART_PATH."/gateways");
		define("ECART_SHIPPING",ECART_PATH."/shipping");
		define("ECART_STORAGE",ECART_PATH."/storage");
		define("ECART_DBSCHEMA",ECART_MODEL_PATH."/schema.sql");

		define("ECART_TEMPLATES",($this->Settings->get('theme_templates') != "off"
			&& is_dir(sanitize_path(get_stylesheet_directory().'/ecart')))?
					  sanitize_path(get_stylesheet_directory().'/ecart'):
					  ECART_PATH.'/'."templates");
		define("ECART_TEMPLATES_URI",($this->Settings->get('theme_templates') != "off"
			&& is_dir(sanitize_path(get_stylesheet_directory().'/ecart')))?
					  sanitize_path(get_bloginfo('stylesheet_directory')."/ecart"):
					  ECART_PLUGINURI."/templates");

		define("ECART_PRETTYURLS",(get_option('permalink_structure') == "")?false:true);
		define("ECART_PERMALINKS",ECART_PRETTYURLS); // Deprecated

		// Initialize application control processing

		$this->Flow = new Flow();
		$this->Shopping = new Shopping();

		add_action('init', array(&$this,'init'));

		// Plugin management
        add_action('after_plugin_row_'.ECART_PLUGINFILE, array(&$this, 'status'),10,2);
        add_action('install_plugins_pre_plugin-information', array(&$this, 'changelog'));
        add_action('ecart_check_updates', array(&$this, 'updates'));
		add_action('ecart_init',array(&$this, 'loaded'));

		// Theme integration
		add_action('widgets_init', array(&$this, 'widgets'));
		add_filter('wp_list_pages',array(&$this,'secure_links'));
		add_filter('rewrite_rules_array',array(&$this,'rewrites'));
		add_action('admin_head-options-reading.php',array(&$this,'pages_index'));
		add_action('generate_rewrite_rules',array(&$this,'pages_index'));
		add_action('save_post', array(&$this, 'pages_index'),10,2);
		add_action('ecart_reindex_pages', array(&$this, 'pages_index'));

		add_filter('query_vars', array(&$this,'queryvars'));

		if (!wp_next_scheduled('ecart_check_updates'))
			wp_schedule_event(time(),'twicedaily','ecart_check_updates');

	}

	/**
	 * Initializes the Ecart runtime environment
	 * 
	 * @since 1.0
	 *
	 * @return void
	 **/
	function init () {

		$this->Errors = new EcartErrors($this->Settings->get('error_logging'));
		$this->Order = Ecart_buyObject::__new('Order');
		$this->Promotions = Ecart_buyObject::__new('CartPromotions');
		$this->Gateways = new GatewayModules();
		$this->Shipping = new ShippingModules();
		$this->Storage = new StorageEngines();
		$this->SmartCategories = array();

		$this->ErrorLog = new EcartErrorLogging($this->Settings->get('error_logging'));
		$this->ErrorNotify = new EcartErrorNotification($this->Settings->get('merchant_email'),
									$this->Settings->get('error_notifications'));

		if (!$this->Shopping->handlers) new EcartError(__('The Cart session handlers could not be initialized because the session was started by the active theme or an active plugin before Ecart could establish its session handlers. The cart will not function.','Ecart'),'ecart_cart_handlers',ECART_ADMIN_ERR);
		if (ECART_DEBUG && $this->Shopping->handlers) new EcartError('Session handlers initialized successfully.','ecart_cart_handlers',ECART_DEBUG_ERR);
		if (ECART_DEBUG) new EcartError('Session started.','ecart_session_debug',ECART_DEBUG_ERR);

		global $pagenow;

		new Login();
		do_action('ecart_init');
	}


	/**
	 * Initializes theme widgets
	 * 
	 * @since 1.0
	 *
	 * @return void
	 **/
	function widgets () {
		global $wp_version;
		include('core/ui/widgets/account.php');
		include('core/ui/widgets/cart.php');
		include('core/ui/widgets/categories.php');
		include('core/ui/widgets/search.php');
	}

	/**
	 * Relocates the Ecart-installed pages and indexes any changes
	 *
	 * @since 1.0
	 *
	 * @param boolean $update (optional) Used in a filter callback context
	 * @param boolean $updates (optional) Used in an action callback context
	 * @return boolean The update status
	 **/
	function pages_index ($update=false,$updates=false) {
		global $wpdb;
		$pages = $this->Settings->get('pages');
		$pages = ecart_locate_pages();
		$this->Settings->save('pages',$pages);
		if ($update) return $update;
	}

	/**
	 * Adds Ecart-specific pretty-url rewrite rules to WordPress rewrite rules
	 * 
	 * @since 1.0
	 *
	 * @param array $wp_rewrite_rules An array of existing WordPress rewrite rules
	 * @return array Modified rewrite rules
	 **/
	function rewrites ($wp_rewrite_rules) {
		$this->pages_index(true);
		$pages = $this->Settings->get('pages');
		if (!$pages) return $wp_rewrite_rules;

		// Collect Ecart page URIs and IDs
		$uris = array();
		$builtins = array();
		foreach ($pages as $page => $data) {
			if ($page == "catalog")	$catalogid = $data['id'];
			$uris[$page] = $data['uri'];
			$builtins[] = $data['id'];
		}
		extract($uris);

		// Find sub-pages of the main catalog page so we can add rewrite exclusions
		$pagenames = array();
		$subpages = get_pages(array('child_of'=>$catalogid,'exclude'=>join(',',$builtins)));
		foreach ($subpages as $page) $pagenames[] = $page->post_name;

		// Build the rewrite rules for Ecart
		$rules = array(
			$cart.'?$' => 'index.php?pagename='.ecart_pagename($cart),
			$account.'?$' => 'index.php?pagename='.ecart_pagename($account),
			$checkout.'?$' => 'index.php?pagename='.ecart_pagename($checkout).'&ecart_proc=checkout',

			/* Exclude sub-pages of the main storefront page (catalog page) */
			'('.$catalog.'/('.join('|',$pagenames).'))?$' => 'index.php?pagename=$matches[1]',

			/* catalog */
			$catalog.'/feed/?$' // Catalog feed
				=> 'index.php?src=category_rss&ecart_category=new',
			$catalog.'/(thanks|receipt)/?$' // Thanks page handling
				=> 'index.php?pagename='.ecart_pagename($checkout).'&ecart_proc=thanks',
			$catalog.'/confirm-order/?$' // Confirm order page handling
				=> 'index.php?pagename='.ecart_pagename($checkout).'&ecart_proc=confirm-order',
			$catalog.'/download/([a-f0-9]{40})/?$' // Download handling
				=> 'index.php?pagename='.ecart_pagename($account).'&src=download&ecart_download=$matches[1]',
			$catalog.'/images/(\d+)/?.*?$' // Image handling
				=> 'index.php?siid=$matches[1]',

			/* catalog/category/category-slug */
			$catalog.'/category/(.+?)/feed/?$' // Category feeds
				=> 'index.php?src=category_rss&ecart_category=$matches[1]',
			$catalog.'/category/(.+?)/page/?(0\-9|[A-Z0-9]{1,})/?$' // Category pagination
				=> 'index.php?pagename='.ecart_pagename($catalog).'&ecart_category=$matches[1]&paged=$matches[2]',
			$catalog.'/category/(.+)/?$' // Category permalink
				=> 'index.php?pagename='.ecart_pagename($catalog).'&ecart_category=$matches[1]',

			/* catalog/tags */
			$catalog.'/tag/(.+?)/feed/?$' // Tag feeds
				=> 'index.php?src=category_rss&ecart_tag=$matches[1]',
			$catalog.'/tag/(.+?)/page/?([0-9]{1,})/?$' // Tag pagination
				=> 'index.php?pagename='.ecart_pagename($catalog).'&ecart_tag=$matches[1]&paged=$matches[2]',
			$catalog.'/tag/(.+)/?$' // Tag permalink
				=> 'index.php?pagename='.ecart_pagename($catalog).'&ecart_tag=$matches[1]',

			/* catalog/product-slug */
			$catalog.'/(.+)/?$' => 'index.php?pagename='.ecart_pagename($catalog).'&ecart_product=$matches[1]'

		);

		// Add mod_rewrite rule for image server for low-resource, speedy delivery
		$corepath = array(PLUGINDIR,$this->directory,'core');
		add_rewrite_rule('.*'.$catalog.'/images/(\d+)/?\??(.*)$',join('/',$corepath).'/image.php?siid=$1&$2');

		return $rules + $wp_rewrite_rules;
	}

	/**
	 * Registers the query variables used by Ecart
	 *	 
	 * @since 1.0
	 *
	 * @param array $vars The current list of handled WordPress query vars
	 * @return array Augmented list of query vars including Ecart vars
	 **/
	function queryvars ($vars) {
		$vars[] = 'ecart_proc';			// Ecart process parameter
		$vars[] = 'ecart_category';		// Category slug or id
		$vars[] = 'ecart_tag';			// Tag slug
		$vars[] = 'ecart_pid';			// Product ID
		$vars[] = 'ecart_product';		// Product slug
		$vars[] = 'ecart_download';		// Download key
		$vars[] = 'ecart_orderby';		// Product sort order (category view)
		$vars[] = 'src';				// Ecart resource
		$vars[] = 'siid';				// Ecart image id
		$vars[] = 'catalog';			// Catalog flag
		$vars[] = 'acct';				// Account process

		return $vars;
	}

	/**
	 * Reset the shopping session
	 *
	 * Controls the cart to allocate a new session ID and transparently
	 * move existing session data to the new session ID.
	 *	 
	 * @since 1.0
	 *
	 * @return boolean True on success
	 **/
	function resession ($session=false) {
		// commit current session
		session_write_close();
		$this->Shopping->handling(); // Workaround for PHP 5.2 bug #32330

		if ($session) { // loading session
			$this->Shopping->session = session_id($session); // session_id while session is closed
			$this->Shopping = new Shopping();
			session_start();
			return true;
		}

		session_start();
		session_regenerate_id(); // Generate new ID while session is started

		// Ensure we have the newest session ID
		$this->Shopping->session = session_id();

		// Commit the session and restart
		session_write_close();
		$this->Shopping->handling(); // Workaround for PHP 5.2 bug #32330
		session_start();

		do_action('ecart_reset_session'); // Deprecated
		do_action('ecart_resession');
		return true;

	}

	/**
	 * @deprecated {@see ecarturl()}
	 *
	 **/
	function link ($target,$secure=false) {
		return ecarturl(false,$target,$secure);
	}

	/**
	 * Provides the JavaScript environment with Ecart settings
	 * 
	 * @since 1.1
	 *
	 * @return void
	 **/
	function settingsjs () {
		$baseop = $this->Settings->get('base_operations');

		$currency = array();
		if (isset($baseop['currency'])
			&& isset($baseop['currency']['format'])
			&& isset($baseop['currency']['format']['decimals'])
			&& !empty($baseop['currency']['format']['decimals'])
		) {
			$currency = array(
				// Currency formatting
				'cp' => $baseop['currency']['format']['cpos'],
				'c' => $baseop['currency']['format']['currency'],
				'p' => $baseop['currency']['format']['precision'],
				't' => $baseop['currency']['format']['thousands'],
				'd' => $baseop['currency']['format']['decimals'],
				'g' => is_array($baseop['currency']['format']['grouping'])?join(',',$baseop['currency']['format']['grouping']):$baseop['currency']['format']['grouping'],
			);
		}

		$base = array(
			'nocache' => is_ecart_page('account'),

			// Validation alerts
			'REQUIRED_FIELD' => __('Your %s is required.','Ecart'),
			'INVALID_EMAIL' => __('The e-mail address you provided does not appear to be a valid address.','Ecart'),
			'MIN_LENGTH' => __('The %s you entered is too short. It must be at least %d characters long.','Ecart'),
			'PASSWORD_MISMATCH' => __('The passwords you entered do not match. They must match in order to confirm you are correctly entering the password you want to use.','Ecart'),
			'REQUIRED_CHECKBOX' => __('%s must be checked before you can proceed.','Ecart')
		);

		$checkout = array();
		if (ecart_script_is('checkout')) {
			$checkout = array(
				'ajaxurl' => admin_url('admin-ajax.php'),

				// Alerts
				'LOGIN_NAME_REQUIRED' => __('You did not enter a login.','Ecart'),
				'LOGIN_PASSWORD_REQUIRED' => __('You did not enter a password to login with.','Ecart'),
			);
		}

		// Admin only
		if (defined('WP_ADMIN'))
			$base['UNSAVED_CHANGES_WARNING'] = __('There are unsaved changes that will be lost if you continue.','Ecart');

		$calendar = array();
		if (ecart_script_is('calendar')) {
			$calendar = array(
				// Month names
				'month_jan' => __('January','Ecart'),
				'month_feb' => __('February','Ecart'),
				'month_mar' => __('March','Ecart'),
				'month_apr' => __('April','Ecart'),
				'month_may' => __('May','Ecart'),
				'month_jun' => __('June','Ecart'),
				'month_jul' => __('July','Ecart'),
				'month_aug' => __('August','Ecart'),
				'month_sep' => __('September','Ecart'),
				'month_oct' => __('October','Ecart'),
				'month_nov' => __('November','Ecart'),
				'month_dec' => __('December','Ecart'),

				// Weekday names
				'weekday_sun' => __('Sun','Ecart'),
				'weekday_mon' => __('Mon','Ecart'),
				'weekday_tue' => __('Tue','Ecart'),
				'weekday_wed' => __('Wed','Ecart'),
				'weekday_thu' => __('Thu','Ecart'),
				'weekday_fri' => __('Fri','Ecart'),
				'weekday_sat' => __('Sat','Ecart')
			);
		}


		$defaults = apply_filters('ecart_js_settings',array_merge($currency,$base,$checkout,$calendar));
		ecart_localize_script('ecart','sjss',$defaults);
	}

	/**
	 * Filters the WP page list transforming unsecured URLs to secure URLs
	 * 
	 * @since 1.1
	 *
	 * @return void
	 **/
	function secure_links ($linklist) {
		if (!$this->Gateways->secure) return $linklist;
		$hrefs = array(
			'checkout' => ecarturl(false,'checkout'),
			'account' => ecarturl(false,'account')
		);
		if (empty($this->Gateways->active)) return str_replace($hrefs['checkout'],ecarturl(false,'cart'),$linklist);

		foreach ($hrefs as $href) {
			$secure_href = str_replace("http://","https://",$href);
			$linklist = str_replace($href,$secure_href,$linklist);
		}
		return $linklist;
	}

	/**
	 * Registers a smart category
	 * 
	 * @since 1.1
	 *
	 * @param string $name Class name of the smart category
	 * @return void
	 **/
	function add_smartcategory ($name) {
		global $Ecart;
		if (empty($Ecart)) return;
			$Ecart->SmartCategories[] = $name;
	}

	/**
	 * Detect if this Ecart installation needs maintenance
	 * 
	 * @since 1.1
	 *
	 * @return boolean
	 **/
	function maintenance () {
		// Settings unavailable
		if (!$this->Settings->available || !$this->Settings->get('ecart_setup') != "completed")
			return false;

		$this->Settings->save('maintenance','on');
		return true;
	}

} // END class Ecart

/**
 * Defines the ecart() 'tag' handler for complete template customization
 *
 * Appropriately routes tag calls to the tag handler for the requested object.
 *
 * @param $object The object to get the tag property from
 * @param $property The property of the object to get/output
 * @param $options Custom options for the property result in query form
 *                   (option1=value&option2=value&...) or alternatively as an associative array
 */
function ecart () {
	global $Ecart;
	$args = func_get_args();

	$object = strtolower($args[0]);
	$property = strtolower($args[1]);
	$options = array();

	if (isset($args[2])) {
		if (is_array($args[2]) && !empty($args[2])) {
			// handle associative array for options
			foreach(array_keys($args[2]) as $key)
				$options[strtolower($key)] = $args[2][$key];
		} else {
			// regular url-compatible arguments
			$paramsets = explode("&",$args[2]);
			foreach ((array)$paramsets as $paramset) {
				if (empty($paramset)) continue;
				$key = $paramset;
				$value = "";
				if (strpos($paramset,"=") !== false)
					list($key,$value) = explode("=",$paramset);
				$options[strtolower($key)] = $value;
			}
		}
	}

	$Object = false; $result = false;
	switch (strtolower($object)) {
		case "cart": if (isset($Ecart->Order->Cart)) $Object =& $Ecart->Order->Cart; break;
		case "cartitem": if (isset($Ecart->Order->Cart)) $Object =& $Ecart->Order->Cart; break;
		case "shipping": if (isset($Ecart->Order->Cart)) $Object =& $Ecart->Order->Cart; break;
		case "category": if (isset($Ecart->Category)) $Object =& $Ecart->Category; break;
		case "subcategory": if (isset($Ecart->Category->child)) $Object =& $Ecart->Category->child; break;
		case "catalog": if (isset($Ecart->Catalog)) $Object =& $Ecart->Catalog; break;
		case "product": if (isset($Ecart->Product)) $Object =& $Ecart->Product; break;
		case "checkout": if (isset($Ecart->Order)) $Object =& $Ecart->Order; break;
		case "purchase": if (isset($Ecart->Purchase)) $Object =& $Ecart->Purchase; break;
		case "customer": if (isset($Ecart->Order->Customer)) $Object =& $Ecart->Order->Customer; break;
		case "error": if (isset($Ecart->Errors)) $Object =& $Ecart->Errors; break;
		default: $Object = apply_filters('ecart_tag_domain',$Object,$object);
	}

	if (!$Object) new EcartError("The ecart('$object') tag cannot be used in this context because the object responsible for handling it doesn't exist.",'ecart_tag_error',ECART_ADMIN_ERR);
	else {
		switch (strtolower($object)) {
			case "cartitem": $result = $Object->itemtag($property,$options); break;
			case "shipping": $result = $Object->shippingtag($property,$options); break;
			default: $result = $Object->tag($property,$options); break;
		}
	}

	// Provide a filter hook for every template tag, includes passed options and the relevant Object as parameters
	$result = apply_filters('ecart_tag_'.strtolower($object).'_'.strtolower($property),$result,$options,$Object);

	// Force boolean result
	if (isset($options['is'])) {
		if (value_is_true($options['is'])) {
			if ($result) return true;
		} else {
			if ($result == false) return true;
		}
		return false;
	}

	// Always return a boolean if the result is boolean
	if (is_bool($result)) return $result;

	// Return the result instead of outputting it
	if ((isset($options['return']) && value_is_true($options['return'])) ||
			isset($options['echo']) && !value_is_true($options['echo']))
		return $result;

	// Output the result
	if (is_scalar($result)) echo $result;
	else return $result;
	return true;
}

function message_act() {

		echo '<div id="message" class="error" style="background: #E2F2CE; color: #466840; border-color: #BFE098; width: 99%; font-size:12px; line-height:1.6em; padding:5px;"><strong>Congratulations! You have successfully installed required functions and database tables for Express Store Theme. NOTE : before you enable Express Store Theme you MUST DISABLE this plugin.</strong></div>';
} 

add_action('admin_head','message_act');

?>