<?php
/**
 * Test Mode
 *
 * @version 1.1.5
 * @package Ecart
 * @since 1.1
 * @subpackage TestMode
 *
 * $Id: TestMode.php 1596 2010-12-29 16:18:25Z jond $
 **/

class TestMode extends GatewayFramework {

	var $secure = false;							// SSL not required
	var $cards = array("visa","mc","disc","amex");	// Support cards

	/**
	 * Setup the TestMode gateway
	 *
	 * @since 1.1
	 *
	 * @return void Description...
	 **/
	function __construct () {
		parent::__construct();
		$this->setup('cards','error');

		// Autoset useable payment cards
		$this->settings['cards'] = array();
		foreach ($this->cards as $card)	$this->settings['cards'][] = $card->symbol;
	}

	function actions () {
		add_action('ecart_process_order',array(&$this,'process'));
	}

	/**
	 * Process the order
	 *
	 * @since 1.1
	 *
	 * @return void
	 **/
	function process () {
		// If the error option is checked, always generate an error
		if ($this->settings['error'] == "on")
			return new EcartError(__("This is an example error message. Disable the 'always show an error' setting to stop displaying this error.","Ecart"),'test_mode_error',ECART_TRXN_ERR);

		// Set the transaction data for the order
		$this->Order->transaction($this->txnid(),'CHARGED');
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
		
	}

} // END class TestMode

?>