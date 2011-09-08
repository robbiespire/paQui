<?php
/**
 * Offline Payment
 *
 * @version 1.1.5
 * @package Ecart
 * @since 1.1
 * @subpackage OfflinePayment
 *
 * $Id$
 **/

class OfflinePayment extends GatewayFramework implements GatewayModule {

	var $secure = false;	// SSL not required
	var $multi = true;		// Support multiple methods
	var $methods = array(); // List of active OfflinePayment payment methods

	/**
	 * Setup the Offline Payment module
	 *
	 * @since 1.1
	 *
	 * @return void Description...
	 **/
	function __construct () {
		parent::__construct();
		$this->setup('instructions');

		// Reset the index count to shift setting indices so we don't break the JS environment
		if (isset($this->settings['label']) && is_array($this->settings['label']))
			$this->settings['label'] = array_merge(array(),$this->settings['label']);
		if (isset($this->settings['instructions']) && is_array($this->settings['instructions']))
		$this->settings['instructions'] = array_merge(array(),$this->settings['instructions']);

		// Scan and build a runtime index of active payment methods
		if (isset($this->settings['label']) && is_array($this->settings['label'])) {
			foreach ($this->settings['label'] as $i => $entry)
				if (isset($this->settings['instructions']) && isset($this->settings['instructions'][$i]))
					$this->methods[$entry] = $this->settings['instructions'][$i];
		}

		add_filter('ecart_tag_checkout_offline-instructions',array(&$this,'tag_instructions'),10,2);
		add_filter('ecart_payment_methods',array(&$this,'methods'));
	}

	function actions () {
		add_action('ecart_process_order',array(&$this,'process'));
		add_action('ecart_save_payment_settings',array(&$this,'reset'));
	}

	/**
	 * Process the order
	 *
	 * Process the order but leave it in PENDING status.
	 *
	 * @since 1.1
	 *
	 * @return void
	 **/
	function process () {
		$this->Order->transaction($this->txnid());
		return true;
	}

	/**
	 * Render the settings for this gateway
	 *
	 * Uses ModuleSettingsUI to generate a JavaScript/jQuery based settings
	 * panel.
	 *
	 * @since 1.1
	 *
	 * @return void
	 **/
	function settings () {

		$this->ui->textarea(0,array(
			'name' => 'instructions',
			'value' => stripslashes_deep($this->settings['instructions'])
		));

		$this->ui->p(1,array(
			'name' => 'help',
			'label' => __('Manual Payment Instructions','Ecart'),
			'content' => __('Use this area to provide your customers with instructions on how to complete the payment.','Ecart')
		));

	}

	function tag_instructions ($result,$options) {
		global $Ecart;
		$methods = array_map('sanitize_title_with_dashes',$this->settings['label']);

		$module = $Ecart->Order->processor;
		$method = $Ecart->Order->paymethod;

		if (empty($method) || !in_array($method,$methods) || $module != $this->module) return;

		add_filter('ecart_offline_payment_instructions', 'stripslashes');
		add_filter('ecart_offline_payment_instructions', 'wptexturize');
		add_filter('ecart_offline_payment_instructions', 'convert_chars');
		add_filter('ecart_offline_payment_instructions', 'wpautop');

		$index = 0;
		foreach ($methods as $index => $label) {
			if ($method == $label)
				return apply_filters('ecart_offline_payment_instructions',
									$this->settings['instructions'][$index]);
		}
		return false;
	}

	function reset () {
		$Settings =& EcartSettings();
		if (!in_array($this->module,explode(',',$_POST['settings']['active_gateways'])))
			$Settings->save('OfflinePayment',false);

	}

	function methods ($methods) {
		return $methods+(count($this->methods)-1);
	}

} // END class OfflinePayment

?>