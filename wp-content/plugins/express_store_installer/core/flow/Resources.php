<?php
/**
 * Resources.php
 *
 * Processes resource requests for non-HTML data
 *
 * @version 1.0
 * @package ecart
 * @subpackage resources
 **/
class Resources {

	var $Settings = false;
	var $request = array();

	/**
	 * Resources constructor
	 *	 
	 *
	 * @return void
	 **/
	function __construct () {
		global $Ecart,$wp;
		if (empty($wp->query_vars) && !(defined('WP_ADMIN') && isset($_GET['src']))) return;

		$this->Settings = &$Ecart->Settings;

		if (empty($wp->query_vars)) $this->request = $_GET;
		else $this->request = $wp->query_vars;

		add_action('ecart_resource_category_rss',array(&$this,'category_rss'));
		add_action('ecart_resource_download',array(&$this,'download'));

		// For secure, backend lookups
		if (defined('WP_ADMIN') && is_user_logged_in()) {
			add_action('ecart_resource_help',array(&$this,'help'));
			if (current_user_can('ecart_financials')) {
				add_action('ecart_resource_export_purchases',array(&$this,'export_purchases'));
				add_action('ecart_resource_export_customers',array(&$this,'export_customers'));
			}
		}

		if ( !empty( $this->request['src'] ) )
			do_action( 'ecart_resource_' . $this->request['src'] );

		die('-1');
	}

	/**
	 * Handles RSS-feed requests
	 * 
	 * @since 1.1
	 *
	 * @return void Description...
	 **/
	function category_rss () {
		global $Ecart;
		require_once(ECART_FLOW_PATH.'/Storefront.php');
		$Storefront = new Storefront();
		header("Content-type: application/rss+xml; charset=utf-8");
		$Storefront->catalog($this->request);
		echo ecart_rss($Ecart->Category->rss());
		exit();
	}

	/**
	 * Delivers order export files to the browser
	 *
	 * @since 1.1
	 *
	 * @return void
	 **/
	function export_purchases () {
		if (!current_user_can('ecart_financials') || !current_user_can('ecart_export_orders')) exit();

		if (!isset($_POST['settings']['purchaselog_columns'])) {
			$Purchase = Purchase::exportcolumns();
			$Purchased = Purchased::exportcolumns();
			$_POST['settings']['purchaselog_columns'] =
			 	array_keys(array_merge($Purchase,$Purchased));
			$_POST['settings']['purchaselog_headers'] = "on";
		}
		$this->Settings->saveform();

		$format = $this->Settings->get('purchaselog_format');
		if (empty($format)) $format = 'tab';

		switch ($format) {
			case "csv": new PurchasesCSVExport(); break;
			case "xls": new PurchasesXLSExport(); break;
			case "iif": new PurchasesIIFExport(); break;
			default: new PurchasesTabExport();
		}
		exit();

	}

	/**
	 * Delivers customer export files to the browser
	 *	 
	 * @since 1.1
	 *
	 * @return void
	 **/
	function export_customers () {
		if (!current_user_can('ecart_export_customers')) exit();
		if (!isset($_POST['settings']['customerexport_columns'])) {
			$Customer = Customer::exportcolumns();
			$Billing = Billing::exportcolumns();
			$Shipping = Shipping::exportcolumns();
			$_POST['settings']['customerexport_columns'] =
			 	array_keys(array_merge($Customer,$Billing,$Shipping));
			$_POST['settings']['customerexport_headers'] = "on";
		}

		$this->Settings->saveform();

		$format = $this->Settings->get('customerexport_format');
		if (empty($format)) $format = 'tab';

		switch ($format) {
			case "csv": new CustomersCSVExport(); break;
			case "xls": new CustomersXLSExport(); break;
			default: new CustomersTabExport();
		}
		exit();
	}

	/**
	 * Handles product file download requests
	 *	 
	 * @since 1.1
	 *
	 * @return void
	 **/
	function download () {
		global $Ecart;
		$download = $this->request['ecart_download'];
		$Purchase = false;
		$Purchased = false;

		if (defined('WP_ADMIN')) {
			$forbidden = false;
			$Download = new ProductDownload($download);
		} else {
			$Order = &EcartOrder();

			$Download = new ProductDownload();
			$Download->loadby_dkey($download);

			$Purchased = $Download->purchased();
			$Purchase = new Purchase($Purchased->purchase);

			$name = $Purchased->name.(!empty($Purchased->optionlabel)?' ('.$Purchased->optionlabel.')':'');

			$forbidden = false;
			// Purchase Completion check
			if ($Purchase->txnstatus != "CHARGED"
				&& !ECART_PREPAYMENT_DOWNLOADS) {
				new EcartError(sprintf(__('"%s" cannot be downloaded because payment has not been received yet.','Ecart'),$name),'ecart_download_limit');
				$forbidden = true;
			}

			// Account restriction checks
			if ($this->Settings->get('account_system') != "none"
				&& (!$Order->Customer->login
				|| $Order->Customer->id != $Purchase->customer)) {
					new EcartError(__('You must login to download purchases.','Ecart'),'ecart_download_limit');
					ecart_redirect(ecarturl(false,'account'));
			}

			// Download limit checking
			if ($this->Settings->get('download_limit') // Has download credits available
				&& $Purchased->downloads+1 > $this->Settings->get('download_limit')) {
					new EcartError(sprintf(__('"%s" is no longer available for download because the download limit has been reached.','Ecart'),$name),'ecart_download_limit');
					$forbidden = true;
				}

			// Download expiration checking
			if ($this->Settings->get('download_timelimit') // Within the timelimit
				&& $Purchased->created+$this->Settings->get('download_timelimit') < mktime() ) {
					new EcartError(sprintf(__('"%s" is no longer available for download because it has expired.','Ecart'),$name),'ecart_download_limit');
					$forbidden = true;
				}

			// IP restriction checks
			if ($this->Settings->get('download_restriction') == "ip"
				&& !empty($Purchase->ip)
				&& $Purchase->ip != $_SERVER['REMOTE_ADDR']) {
					new EcartError(sprintf(__('"%s" cannot be downloaded because your computer could not be verified as the system the file was purchased from.','Ecart'),$name),'ecart_download_limit');
					$forbidden = true;
				}

			do_action_ref_array('ecart_download_request',array(&$Purchased));
		}

		if ($forbidden) {
			ecart_redirect(ecarturl(false,'account'));
		}

		if ($Download->download()) {
			if ($Purchased !== false) {
				$Purchased->downloads++;
				$Purchased->save();
				do_action_ref_array('ecart_download_success',array(&$Purchased));
			}
			exit();
		}
	}

	/**
	 * Grabs interface help screencasts
	 *	 
	 * @since 1.1
	 *
	 * @return void
	 **/
	function help () {
		if (!isset($_GET['id'])) return;

		$Settings =& EcartSettings();
		list($status,$key) = $Settings->get('updatekey');
		$site = get_bloginfo('siteurl');

		$request = array("EcartScreencast" => $_GET['id'],'key'=>$key,'site'=>$site);
		$response = Ecart::callhome($request);
		echo $response;
		exit();
	}

} // END class Resources

?>