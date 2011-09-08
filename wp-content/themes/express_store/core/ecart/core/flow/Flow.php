<?php
/**
 * Flow
 *
 * Super controller for handling low level request processing
 *
 * @version 1.0
 * @package ecart
 * @subpackage ecart
 **/

/**
 * Flow
 *
 * @since 1.1
 * @package ecart
 **/
class Flow {

	var $Controller = false;
	var $Admin = false;
	var $Installer = false;
	var $Logins = false;

	/**
	 * Flow constructor
	 *	 
	 *
	 * @return void
	 **/
	function __construct () {
		register_deactivation_hook(ECART_PLUGINFILE, array(&$this, 'deactivate'));
		register_activation_hook(ECART_PLUGINFILE, array(&$this, 'activate'));

		if (defined('DOING_AJAX')) add_action('admin_init',array(&$this,'ajax'));

		add_action('admin_menu',array(&$this,'menu'));

		// Handle automatic updates
		add_action('update-custom_ecart',array(&$this,'update'));

		if (defined('WP_ADMIN')) add_action('admin_init',array(&$this,'parse'));
		else add_action('parse_request',array(&$this,'parse'));
	}

	/**
	 * Parses requests and hands off processing to specific subcontrollers
	 *	 
	 *
	 * @return boolean
	 **/
	function parse () {
		global $Ecart,$wp;

		$this->transactions();

		if (isset($wp->query_vars['src']) ||
			(defined('WP_ADMIN') && isset($_GET['src']))) $this->resources();

		if (defined('WP_ADMIN')) {
			if (!isset($_GET['page'])) return;
			if ($this->Admin === false) {
				require_once(ECART_FLOW_PATH."/Admin.php");
				$this->Admin = new AdminFlow();
			}
			$controller = $this->Admin->controller(strtolower($_GET['page']));
			if (!empty($controller)) $this->handler($controller);
		} else $this->handler("Storefront");
	}

	function transactions () {

		if (!empty($_REQUEST['_txnupdate'])) {
			return do_action('ecart_txn_update');
		}

		if (!empty($_REQUEST['rmtpay'])) {
			return do_action('ecart_remote_payment');
		}


		if (isset($_POST['checkout'])) {
			if ($_POST['checkout'] == "process") do_action('ecart_process_checkout');
			if ($_POST['checkout'] == "confirmed") do_action('ecart_confirm_order');
		} else {
			if (!empty($_POST['shipmethod'])) do_action('ecart_process_shipmethod');
		}

	}

	/**
	 * Loads a specified flow controller
	 *	 
	 *
	 * @param string $controller The base name of the controller file
	 * @return void
	 **/
	function handler ($controller) {
		if (!$controller) return false;
		require_once(ECART_FLOW_PATH."/$controller.php");
		$this->Controller = new $controller();
		return true;
	}

	/**
	 * Initializes the Admin controller
	 *	 
	 *
	 * @return void
	 **/
	function admin () {
		if (!defined('WP_ADMIN')) return false;
		$controller = $this->Admin->controller(strtolower($_GET['page']));
		require_once(ECART_FLOW_PATH."/$controller.php");
		$this->Controller = new $controller();
		$this->Controller->admin();
		return true;
	}

	/**
	 * Defines the Ecart admin page and menu structure
	 *	 
	 *
	 * @return void
	 **/
	function menu () {
		require_once(ECART_FLOW_PATH."/Admin.php");
		$this->Admin = new AdminFlow();
		$this->Admin->menus();
	}

	function ajax () {
		if (!isset($_REQUEST['action']) || !defined('DOING_AJAX')) return;
		require_once(ECART_FLOW_PATH."/Ajax.php");
		$this->Ajax = new AjaxFlow();
	}

	function resources () {
		require_once(ECART_FLOW_PATH."/Resources.php");
		$this->Controller = new Resources();
	}

	/**
	 * Activates the plugin
	 *	 
	 * @since 1.0
	 *
	 * @return void
	 **/
	function activate () {
		$this->installation();
		do_action('ecart_activate');
	}

	/**
	 * deactivate()
	 * Resets the data_model to prepare for potential upgrades/changes to the table schema */
	function deactivate() {
		$this->installation();
		do_action('ecart_deactivate');
	}

	function installation () {
		if (!defined('WP_ADMIN')) return;
		if ($this->Installer !== false) return;

		require_once(ECART_FLOW_PATH."/Install.php");
		if (!$this->Installer) $this->Installer = new EcartInstallation();
	}

	function update () {
		$this->installation();
		do_action('ecart_autoupdate');
	}

	function save_settings () {
		if (empty($_POST['settings']) || !is_array($_POST['settings'])) return false;
		foreach ($_POST['settings'] as $setting => $value)
			$this->Settings->save($setting,$value);
		return true;
	}


} // End class Flow

/**
 * FlowController
 *
 * Provides a template for flow controllers
 *
 * @since 1.1
 * @package ecart
 **/
abstract class FlowController  {

	var $Settings = false;

	/**
	 * FlowController constructor
	 *	 
	 * @since 1.1
	 *
	 * @return void
	 **/
	function __construct () {
		if (defined('WP_ADMIN')) {
			add_action('admin_init',array(&$this,'settings'));
			$this->settings();
		} else add_action('ecart_loaded',array(&$this,'settings'));
	}

	function settings () {
		global $Ecart;
		if (!$this->Settings && !empty($Ecart))
			$this->Settings = &$Ecart->Settings;
	}

} // END class FlowController

/**
 * AdminController
 *
 * Provides a template for admin controllers
 *
 * @since 1.1
 * @package ecart
 **/
abstract class AdminController extends FlowController {

	var $Admin = false;

	/**
	 * AdminController constructor
	 *	 
	 * @since 1.1
	 *
	 * @return void
	 **/
	function __construct () {
		parent::__construct();
		global $Ecart;
		if (!empty($Ecart->Flow->Admin)) $this->Admin = &$Ecart->Flow->Admin;
	}

}

/**
 * Helper to access the Ecart Storefront contoller
 * 
 * @since 1.1.5
 *
 * @return Storefront|false
 **/
function &EcartStorefront () {
	global $Ecart;
	$false = false;
	if (!isset($Ecart->Flow) || !is_object($Ecart->Flow->Controller)) return $false;
	if (get_class($Ecart->Flow->Controller) != "Storefront") return $false;
	return $Ecart->Flow->Controller;
}

?>