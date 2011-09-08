<?php
/**
 * Setup
 *
 * Flow controller for settings management
 *
 * @version 1.0
 * @package ecart
 * @subpackage ecart
 **/

/**
 * Setup
 *
 * @package ecart
 **/
class Setup extends FlowController {

	var $screen = false;

	/**
	 * Setup constructor
	 *
	 * @return void	 
	 **/
	function __construct () {
		parent::__construct();

		$pages = explode("-",$_GET['page']);
		$this->screen = end($pages);
		switch ($this->screen) {
			case "taxes":
				wp_enqueue_script("suggest");
				ecart_enqueue_script('ocupload');
				ecart_enqueue_script('taxes');
				break;
			case "system":
				ecart_enqueue_script('colorbox');
				break;
			case "settings":
				ecart_enqueue_script('setup');
				break;
		}

	}

	/**
	 * Parses settings interface requests
	 *
	 * @return void	 
	 **/
	function admin () {
		switch($this->screen) {
			case "catalog": 		$this->catalog(); break;
			case "cart": 			$this->cart(); break;
			case "checkout": 		$this->checkout(); break;
			case "payments": 		$this->payments(); break;
			case "shipping": 		$this->shipping(); break;
			case "taxes": 			$this->taxes(); break;
			case "presentation":	$this->presentation(); break;
			case "system":			$this->system(); break;
			case "update":			$this->update(); break;
			default: 				$this->general();
		}
	}

	/**
	 * Displays the General Settings screen and processes updates
	 *	 
	 * @since 1.0
	 *
	 * @return void
	 **/
	function general () {
		global $Ecart;
		if ( !(current_user_can('manage_options') && current_user_can('ecart_settings')) )
			wp_die(__('You do not have sufficient permissions to access this page.'));

		$updatekey = $Ecart->Settings->get('updatekey');
		$activated = ($updatekey[0] == "1");
		$type = "text";
		$key = $updatekey[1];
		if (isset($updatekey[2]) && $updatekey[2] == "dev") {
			$type = "password";
			$key = preg_replace('/\w/','?',$key);
		}

		$country = (isset($_POST['settings']))?$_POST['settings']['base_operations']['country']:'';
		$countries = array();
		$countrydata = Lookup::countries();
		foreach ($countrydata as $iso => $c) {
			if (isset($_POST['settings']) && $_POST['settings']['base_operations']['country'] == $iso)
				$base_region = $c['region'];
			$countries[$iso] = $c['name'];
		}

		if (!empty($_POST['setup'])) {
			$_POST['settings']['display_welcome'] = "off";
			$this->settings_save();
		}

		if (!empty($_POST['save'])) {
			check_admin_referer('ecart-settings-general');
			$vat_countries = Lookup::vat_countries();
			$zone = $_POST['settings']['base_operations']['zone'];
			$_POST['settings']['base_operations'] = $countrydata[$_POST['settings']['base_operations']['country']];
			$_POST['settings']['base_operations']['country'] = $country;
			$_POST['settings']['base_operations']['zone'] = $zone;
			$_POST['settings']['base_operations']['currency']['format'] =
				scan_money_format($_POST['settings']['base_operations']['currency']['format']);
			if (in_array($_POST['settings']['base_operations']['country'],$vat_countries))
				$_POST['settings']['base_operations']['vat'] = true;
			else $_POST['settings']['base_operations']['vat'] = false;

			if (!isset($_POST['settings']['target_markets']))
				asort($_POST['settings']['target_markets']);

			$this->settings_save();
			$updated = __('Ecart settings saved.', 'Ecart');
		}

		$operations = $Ecart->Settings->get('base_operations');
		if (!empty($operations['zone'])) {
			$zones = Lookup::country_zones();
			$zones = $zones[$operations['country']];
		}

		$targets = $Ecart->Settings->get('target_markets');
		if (!$targets) $targets = array();

		$statusLabels = $Ecart->Settings->get('order_status');
		include(ECART_ADMIN_PATH."/settings/settings.php");
	}

	function presentation () {
		if ( !(current_user_can('manage_options') && current_user_can('ecart_settings_presentation')) )
			wp_die(__('You do not have sufficient permissions to access this page.'));

		$builtin_path = ECART_PATH.'/templates';
		$theme_path = sanitize_path(STYLESHEETPATH.'/ecart');

		if (!empty($_POST['save'])) {
			check_admin_referer('ecart-settings-presentation');
			$updated = __('Ecart presentation settings saved.','Ecart');

			if (isset($_POST['settings']['theme_templates'])
				&& $_POST['settings']['theme_templates'] == "on"
				&& !is_dir($theme_path)) {
					$_POST['settings']['theme_templates'] = "off";
					$updated = __('Ecart theme templates can\'t be used because they don\'t exist.','Ecart');
			}

			if (empty($_POST['settings']['catalog_pagination']))
				$_POST['settings']['catalog_pagination'] = 0;
			$this->settings_save();
		}


		// Copy templates to the current WordPress theme
		if (!empty($_POST['install'])) {
			check_admin_referer('ecart-settings-presentation');
			copy_ecart_templates($builtin_path,$theme_path);
		}

		$status = "available";
		if (!is_dir($theme_path)) $status = "directory";
		else {
			if (!is_writable($theme_path)) $status = "permissions";
			else {
				$builtin = array_filter(scandir($builtin_path),"filter_dotfiles");
				$theme = array_filter(scandir($theme_path),"filter_dotfiles");
				if (empty($theme)) $status = "ready";
				else if (array_diff($builtin,$theme)) $status = "incomplete";
			}
		}

		$category_views = array("grid" => __('Grid','Ecart'),"list" => __('List','Ecart'));
		$row_products = array(2,3,4,5,6,7);
		$productOrderOptions = Category::sortoptions();
		$productOrderOptions['custom'] = __('Custom','Ecart');

		$orderOptions = array("ASC" => __('Order','Ecart'),
							  "DESC" => __('Reverse Order','Ecart'),
							  "RAND" => __('Shuffle','Ecart'));

		$orderBy = array("sortorder" => __('Custom arrangement','Ecart'),
						 "name" => __('File name','Ecart'),
						 "created" => __('Upload date','Ecart'));


		include(ECART_ADMIN_PATH."/settings/presentation.php");
	}

	function checkout () {
		global $Ecart;
		$db =& DB::get();
		if ( !(current_user_can('manage_options') && current_user_can('ecart_settings_checkout')) )
			wp_die(__('You do not have sufficient permissions to access this page.'));

		$purchasetable = DatabaseObject::tablename(Purchase::$table);
		$next = $db->query("SELECT IF ((MAX(id)) > 0,(MAX(id)+1),1) AS id FROM $purchasetable LIMIT 1");
		$next_setting = $Ecart->Settings->get('next_order_id');

		if ($next->id > $next_setting) $next_setting = $next->id;

		if (!empty($_POST['save'])) {
			check_admin_referer('ecart-settings-checkout');

			$next_order_id = $_POST['settings']['next_order_id'] = intval($_POST['settings']['next_order_id']);

			if ($next_order_id >= $next->id) {
				if ($db->query("ALTER TABLE $purchasetable AUTO_INCREMENT=".$db->escape($next_order_id)))
					$next_setting = $next_order_id;
			}


			$this->settings_save();
			$updated = __('Ecart checkout settings saved.','Ecart');
		}

		$downloads = array("1","2","3","5","10","15","25","100");
		$promolimit = array("1","2","3","4","5","6","7","8","9","10","15","20","25");
		$time = array(
			'1800' => __('30 minutes','Ecart'),
			'3600' => __('1 hour','Ecart'),
			'7200' => __('2 hours','Ecart'),
			'10800' => __('3 hours','Ecart'),
			'21600' => __('6 hours','Ecart'),
			'43200' => __('12 hours','Ecart'),
			'86400' => __('1 day','Ecart'),
			'172800' => __('2 days','Ecart'),
			'259200' => __('3 days','Ecart'),
			'604800' => __('1 week','Ecart'),
			'2678400' => __('1 month','Ecart'),
			'7952400' => __('3 months','Ecart'),
			'15901200' => __('6 months','Ecart'),
			'31536000' => __('1 year','Ecart'),
			);

		include(ECART_ADMIN_PATH."/settings/checkout.php");
	}

	/**
	 * Renders the shipping settings screen and processes updates
	 * 
	 * @since 1.1
	 *
	 * @return void
	 **/
	function shipping () {
		global $Ecart;

		if ( !(current_user_can('manage_options') && current_user_can('ecart_settings_shipping')) )
			wp_die(__('You do not have sufficient permissions to access this page.'));

		if (!empty($_POST['save'])) {
			check_admin_referer('ecart-settings-shipping');

			// Sterilize $values
			foreach ($_POST['settings']['shipping_rates'] as $i => &$method) {
				$method['name'] = stripslashes($method['name']);
				foreach ($method as $key => &$mr) {
					if (!is_array($mr)) continue;
					foreach ($mr as $id => &$v) {
						if ($v == ">" || $v == "+" || $key == "services") continue;
						$v = floatvalue($v);
					}
				}
			}

			$_POST['settings']['order_shipfee'] = floatvalue($_POST['settings']['order_shipfee']);

	 		$this->settings_save();
			$updated = __('Shipping settings saved.','Ecart');

			// Reload the currently active shipping modules
			$active = $Ecart->Shipping->activated();
			$Ecart->Shipping->settings();

			$Errors = &EcartErrors();
			do_action('ecart_verify_shipping_services');

			if ($Errors->exist()) {
				// Get all addon related errors
				$failures = $Errors->level(ECART_ADDON_ERR);
				if (!empty($failures)) {
					$updated = __('Shipping settings saved but there were errors: ','Ecart');
					foreach ($failures as $error)
						$updated .= '<p>'.$error->message(true,true).'</p>';
				}
			}

		}

		$Ecart->Shipping->settings();

		$methods = $Ecart->Shipping->methods;
		$base = $Ecart->Settings->get('base_operations');
		$regions = Lookup::regions();
		$region = $regions[$base['region']];
		$useRegions = $Ecart->Settings->get('shipping_regions');

		$areas = Lookup::country_areas();
		if (is_array($areas[$base['country']]) && $useRegions == "on")
			$areas = array_keys($areas[$base['country']]);
		else $areas = array($base['country'] => $base['name']);
		unset($countries,$regions);

		$rates = $Ecart->Settings->get('shipping_rates');
		if (!empty($rates)) ksort($rates);

		$lowstock = $Ecart->Settings->get('lowstock_level');
		if (empty($lowstock)) $lowstock = 0;

		include(ECART_ADMIN_PATH."/settings/shipping.php");
	}

	function taxes () {
		if ( !(current_user_can('manage_options') && current_user_can('ecart_settings_taxes')) )
			wp_die(__('You do not have sufficient permissions to access this page.'));

		if (!empty($_POST['save'])) {
			check_admin_referer('ecart-settings-taxes');
			$this->settings_save();
			$updated = __('Ecart taxes settings saved.','Ecart');
		}

		$rates = $this->Settings->get('taxrates');
		$base = $this->Settings->get('base_operations');

		$countries = array_merge(array('*' => __('All Markets','Ecart')),
			$this->Settings->get('target_markets'));


		$zones = Lookup::country_zones();

		include(ECART_ADMIN_PATH."/settings/taxes.php");
	}

	function payments () {
		global $Ecart;

		if ( !(current_user_can('manage_options') && current_user_can('ecart_settings_payments')) )
			wp_die(__('You do not have sufficient permissions to access this page.'));

		add_action('ecart_gateway_module_settings',array(&$this,'payments_ui'));

		if (!empty($_POST['save'])) {
			check_admin_referer('ecart-settings-payments');
			do_action('ecart_save_payment_settings');

			$this->settings_save();
			$updated = __('Ecart payments settings saved.','Ecart');
		}

	 	$active_gateways = $Ecart->Settings->get('active_gateways');
		if (!$active_gateways) $gateways = array();
		else $gateways = explode(",",$active_gateways);

		include(ECART_ADMIN_PATH."/settings/payments.php");
	}

	function payments_ui () {
		global $Ecart;
		$Ecart->Gateways->settings();
		$Ecart->Gateways->ui();
	}

	function system () {
		global $Ecart;
		if ( !(current_user_can('manage_options') && current_user_can('ecart_settings_system')) )
			wp_die(__('You do not have sufficient permissions to access this page.'));

		add_action('ecart_storage_module_settings',array(&$this,'storage_ui'));

		if (!empty($_POST['save'])) {
			check_admin_referer('ecart-settings-system');

			if (!isset($_POST['settings']['error_notifications']))
				$_POST['settings']['error_notifications'] = array();

			$this->settings_save();

			// Reinitialize Error System
			$Ecart->Errors = new EcartErrors($this->Settings->get('error_logging'));
			$Ecart->ErrorLog = new EcartErrorLogging($this->Settings->get('error_logging'));
			$Ecart->ErrorNotify = new EcartErrorNotification($this->Settings->get('merchant_email'),
										$this->Settings->get('error_notifications'));

			$updated = __('Ecart system settings saved.','Ecart');
		} elseif (!empty($_POST['rebuild'])) {
			$db =& DB::get();

			$assets = DatabaseObject::tablename(ProductImage::$table);
			$query = "DELETE FROM $assets WHERE context='image' AND type='image'";
			if ($db->query($query))
				$updated = __('All cached images have been cleared.','Ecart');
		}


		if (isset($_POST['resetlog'])) $Ecart->ErrorLog->reset();

		$notifications = $this->Settings->get('error_notifications');
		if (empty($notifications)) $notifications = array();

		$notification_errors = array(
			ECART_TRXN_ERR => __("Transaction Errors","Ecart"),
			ECART_AUTH_ERR => __("Login Errors","Ecart"),
			ECART_ADDON_ERR => __("Add-on Errors","Ecart"),
			ECART_COMM_ERR => __("Communication Errors","Ecart"),
			ECART_STOCK_ERR => __("Inventory Warnings","Ecart")
			);

		$errorlog_levels = array(
			0 => __("Disabled","Ecart"),
			ECART_ERR => __("General Ecart Errors","Ecart"),
			ECART_TRXN_ERR => __("Transaction Errors","Ecart"),
			ECART_AUTH_ERR => __("Login Errors","Ecart"),
			ECART_ADDON_ERR => __("Add-on Errors","Ecart"),
			ECART_COMM_ERR => __("Communication Errors","Ecart"),
			ECART_STOCK_ERR => __("Inventory Warnings","Ecart"),
			ECART_ADMIN_ERR => __("Admin Errors","Ecart"),
			ECART_DB_ERR => __("Database Errors","Ecart"),
			ECART_PHP_ERR => __("PHP Errors","Ecart"),
			ECART_ALL_ERR => __("All Errors","Ecart"),
			ECART_DEBUG_ERR => __("Debugging Messages","Ecart")
			);

		// Load Storage settings
		$Ecart->Storage->settings();

		// Build the storage options menu
		$storage = array();
		foreach ($Ecart->Storage->active as $module)
			$storage[$module->module] = $module->name;

		$loading = array("ecart" => __('Load on Ecart-pages only','Ecart'),"all" => __('Load on entire site','Ecart'));

		if ($this->Settings->get('error_logging') > 0)
			$recentlog = $Ecart->ErrorLog->tail(500);

		include(ECART_ADMIN_PATH."/settings/system.php");
	}

	function storage_ui () {
		global $Ecart;
		$Ecart->Storage->settings();
		$Ecart->Storage->ui();
	}


	function settings_save () {
		if (empty($_POST['settings']) || !is_array($_POST['settings'])) return false;
		foreach ($_POST['settings'] as $setting => $value)
			$this->Settings->save($setting,$value);
	}

} // END class Setup

?>