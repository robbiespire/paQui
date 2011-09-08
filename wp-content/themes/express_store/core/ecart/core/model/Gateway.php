<?php
/**
 * Gateway classes
 *
 * Generic prototype classes for local and remote payment systems
 * 
 * @version 1.0
 * @package ecart
 * @subpackage gateways
 **/

/**
 * GatewayModule interface
 *
 * Provides a template for required gateway methods
 *
 * @since 1.1
 * @package ecart
 * @subpackage gateways
 **/
interface GatewayModule {

	/**
	 * Used for setting up event listeners
	 *	 
	 * @since 1.1
	 *
	 * @return void
	 **/
	public function actions();

	/**
	 * Used for rendering the gateway settings UI
	 *	 
	 * @since 1.1
	 *
	 * @return void
	 **/
	public function settings();

}

/**
 * GatewayFramework class
 *
 * Provides default helper methods for gateway modules.
 *
 * @since 1.1
 * @package ecart
 * @subpackage gateways
 **/
abstract class GatewayFramework {

	var $session = false;		// The current shopping session ID
	var $Order = false;			// The current customer's Order
	var $name = false;			// The proper name of the gateway
	var $module = false;		// The module class name of the gateway
	var $cards = false;			// A list of supported payment cards
	var $secure = true;			// Flag for requiring encrypted checkout process
	var $multi = false;			// Flag to enable a multi-instance gateway
	var $baseop = false; 		// Base of operation setting
	var $precision = 2;			// Currency precision
	var $settings = array();	// List of settings for the module

	/**
	 * Setup the module for runtime
	 *
	 * Auto-loads settings for the module and setups defaults
	 *	 
	 * @since 1.1
	 *
	 * @return void
	 **/
	function __construct () {
		global $Ecart;
		$this->session = $Ecart->Shopping->session;
		$this->Order = &EcartOrder();
		$this->module = get_class($this);
		$this->settings = $Ecart->Settings->get($this->module);
		if (!isset($this->settings['label']) && $this->cards)
			$this->settings['label'] = __("Credit Card","Ecart");

		$this->baseop = $Ecart->Settings->get('base_operations');
		$this->precision = $this->baseop['currency']['format']['precision'];

		$this->_loadcards();
		if ($this->myorder()) $this->actions();
	}

	/**
	 * Generate the settings UI for the module
	 *	 
	 * @since 1.1
	 *
	 * @param string $module The module class name
	 * @param string $name The formal name of the module
	 * @return void
	 **/
	function setupui ($module,$name) {
		if (!isset($this->settings['label'])) $this->settings['label'] = $name;
		$this->ui = new ModuleSettingsUI('payment',$module,$name,$this->settings['label'],$this->multi);
		$this->settings();
	}

	/**
	 * Initialize a list of gateway module settings
	 *	 
	 * @since 1.1
	 *
	 * @param string $name The name of a setting
	 * @param string $name... (optional) Additional setting names to initialize
	 * @return void
	 **/
	function setup () {
		$settings = func_get_args();
		foreach ($settings as $name)
			if (!isset($this->settings[$name]))
				$this->settings[$name] = false;
	}

	/**
	 * Determine if the current order should be processed by this module
	 *	 
	 * @since 1.1
	 *
	 * @return boolean
	 **/
	function myorder () {
		return ($this->Order->processor() == $this->module);
	}

	/**
	 * Generate a unique transaction ID using a timestamp
	 *	 
	 * @since 1.1
	 *
	 * @return string
	 **/
	function txnid () {
		return mktime();
	}

	/**
	 * Generic connection manager for sending data
	 *	 
	 * @since 1.1
	 *
	 * @param string $data The encoded data to send
	 * @param string $url The URL to connect to
	 * @param string $port (optional) Connect to a specific port
	 * @return string Raw response
	 **/
	function send ($data,$url,$port=false, $curlopts = array()) {
		$connection = curl_init();
		curl_setopt($connection,CURLOPT_URL,"$url".($port?":$port":""));
		curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($connection, CURLOPT_NOPROGRESS, 1);
		curl_setopt($connection, CURLOPT_VERBOSE, 1);
		curl_setopt($connection, CURLOPT_POST, 1);
		curl_setopt($connection, CURLOPT_POSTFIELDS, $data);
		curl_setopt($connection, CURLOPT_TIMEOUT, ECART_GATEWAY_TIMEOUT);
		curl_setopt($connection, CURLOPT_USERAGENT, ECART_GATEWAY_USERAGENT);
		curl_setopt($connection, CURLOPT_REFERER, "http://".$_SERVER['SERVER_NAME']);
		curl_setopt($connection, CURLOPT_FAILONERROR, 1);
		curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);

		if (!(ini_get("safe_mode") || ini_get("open_basedir")))
			curl_setopt($connection, CURLOPT_FOLLOWLOCATION,1);

		if (defined('ECART_PROXY_CONNECT') && ECART_PROXY_CONNECT) {
	        curl_setopt($connection, CURLOPT_HTTPPROXYTUNNEL, 1);
	        curl_setopt($connection, CURLOPT_PROXY, ECART_PROXY_SERVER);
			if (defined('ECART_PROXY_USERPWD'))
			    curl_setopt($connection, CURLOPT_PROXYUSERPWD, ECART_PROXY_USERPWD);
	    }

		// Added to handle SSL timeout issues
		// Maybe if a timeout occurs the connection should be
		// re-attempted with this option for better overall performance
		curl_setopt($connection, CURLOPT_FRESH_CONNECT, 1);

		foreach ($curlopts as $key => $value)
			curl_setopt($connection, $key, $value);

		$buffer = curl_exec($connection);
		if ($error = curl_error($connection))
			new EcartError($this->name.": ".$error,'gateway_comm_err',ECART_COMM_ERR);
		curl_close($connection);

		return $buffer;

	}

	/**
	 * Helper to encode a data structure into a URL-compatible format
	 *	 
	 * @since 1.1
	 *
	 * @param array $data Key/value pairs of data to encode
	 * @return string
	 **/
	function encode ($data) {
		$query = "";
		$data = stripslashes_deep($data);
		foreach($data as $key => $value) {
			if (is_array($value)) {
				foreach($value as $item) {
					if (strlen($query) > 0) $query .= "&";
					$query .= "$key=".urlencode($item);
				}
			} else {
				if (strlen($query) > 0) $query .= "&";
				$query .= "$key=".urlencode($value);
			}
		}
		return $query;
	}

	/**
	 * Formats a data structure into POST-able form elements
	 *	 
	 * @since 1.1
	 *
	 * @param array $data Key/value pairs of data to format into form elements
	 * @return string
	 **/
	function format ($data) {
		$query = "";
		foreach($data as $key => $value) {
			if (is_array($value)) {
				foreach($value as $item)
					$query .= '<input type="hidden" name="'.$key.'[]" value="'.esc_attr($item).'" />';
			} else {
				$query .= '<input type="hidden" name="'.$key.'" value="'.esc_attr($value).'" />';
			}
		}
		return $query;
	}

	/**
	 * Provides the accepted PayCards for the gateway
	 *	 
	 * @since 1.1
	 *
	 * @return array
	 **/
	function cards () {
		$accepted = array();
		if (!empty($this->settings['cards'])) $accepted = $this->settings['cards'];
		if (empty($accepted) && is_array($this->cards)) $accepted = array_keys($this->cards);
		$pcs = Lookup::paycards();
		$cards = array();
		foreach ($accepted as $card) {
			$card = strtolower($card);
			if (isset($pcs[$card])) $cards[$card] = $pcs[$card];
		}
		return $cards;
	}

	/**
	 * Loads the enabled payment cards
	 *	 
	 * @since 1.1
	 *
	 * @return void
	 **/
	private function _loadcards () {
		if (empty($this->settings['cards'])) $this->settings['cards'] = $this->cards;
		if ($this->cards) {
			$cards = array();
			$pcs = Lookup::paycards();
			foreach ($this->cards as $card) {
				$card = strtolower($card);
				if (isset($pcs[$card])) $cards[] = $pcs[$card];
			}
			$this->cards = $cards;
		}
	}

} // END class GatewayFramework


/**
 * GatewayModules class
 *
 * Gateway module file manager to load gateways that are active.
 *
 * @since 1.1
 * @package ecart
 * @subpackage gateways
 **/
class GatewayModules extends ModuleLoader {

	var $selected = false;		// The chosen gateway to process the order
	var $secure = false;		// SSL-required flag

	/**
	 * Initializes the shipping module loader
	 *	 
	 * @since 1.1
	 *
	 * @return void Description...
	 **/
	function __construct () {

		$this->path = ECART_GATEWAYS;

		// Get hooks in place before getting things started
		add_action('ecart_module_loaded',array(&$this,'properties'));

		$this->installed();
		$this->activated();

		add_action('ecart_init',array(&$this,'load'));
	}

	/**
	 * Determines the activated gateway modules
	 *	 
	 * @since 1.1
	 *
	 * @return array List of module names for the activated modules
	 **/
	function activated () {
		global $Ecart;
		$this->activated = array();
		$gateways = explode(",",$Ecart->Settings->get('active_gateways'));
		foreach ($this->modules as $gateway)
			if (in_array($gateway->subpackage,$gateways))
				$this->activated[] = $gateway->subpackage;

		return $this->activated;
	}

	/**
	 * Sets Gateway system settings flags based on activated modules
	 *	 
	 * @since 1.1
	 *
	 * @param string $module Activated module class name
	 * @return void
	 **/
	function properties ($module) {
		if (!isset($this->active[$module])) return;
		$this->active[$module]->name = $this->modules[$module]->name;
		if ($this->active[$module]->secure) $this->secure = true;
	}

	/**
	 * Loads all the installed gateway modules for the payments settings
	 *	 
	 * @since 1.1
	 *
	 * @return void
	 **/
	function settings () {
		$this->load(true);
	}

	/**
	 * Initializes the settings UI for each loaded module
	 *	 
	 * @since 1.1
	 *
	 * @return void Description...
	 **/
	function ui () {
		foreach ($this->active as $package => &$module)
			$module->setupui($package,$this->modules[$package]->name);
	}

} // END class GatewayModules

/**
 * PayCard classs
 *
 * Implements structured payment card (credit card) behaviors including
 * card number validation and extra security field requirements.
 *
 * @since 1.1
 * @package ecart
 * @subpackage gateways
 **/
class PayCard {

	var $name;
	var $symbol;
	var $pattern = false;
	var $csc = false;
	var $inputs = array();

	function __construct ($name,$symbol,$pattern,$csc=false,$inputs=array()) {
		$this->name = $name;
		$this->symbol = $symbol;
		$this->pattern = $pattern;
		$this->csc = $csc;
		$this->inputs = $inputs;
	}

	function validate ($pan) {
		$n = preg_replace('/\D/','',$pan);
		return ($this->match($n) && $this->checksum($n));
	}

	function match ($number) {
		if ($this->pattern && !preg_match($this->pattern,$number)) return false;
		return true;
	}

	function checksum ($number) {
		$code = strrev($number);
		for ($i = 0; $i < strlen($code); $i++) {
			$d = intval($code[$i]);
			if ($i & 1) $d *= 2;
			$cs += $d % 10;
			if ($d > 9) $cs += 1;
		}
		return ($cs % 10 == 0);
	}

}


?>