<?php
/**
 * Login
 *
 * Controller for handling logins
 *
 * @version 1.0
 * @package ecart
 * @subpackage ecart
 **/

/**
 * Login
 * 
 * @since 1.1
 * @package ecart
 **/
class Login {

	var $Customer = false;
	var $Billing = false;
	var $Shipping = false;

	var $accounts = "none";		// Account system setting

	function __construct () {
		global $Ecart;

		$this->accounts = $Ecart->Settings->get('account_system');

		$this->Customer =& $Ecart->Order->Customer;
		$this->Billing =& $Ecart->Order->Billing;
		$this->Shipping =& $Ecart->Order->Shipping;

		add_action('ecart_logout',array(&$this,'logout'));

		if ($this->accounts == "wordpress") {
			add_action('set_logged_in_cookie',array(&$this,'wplogin'),10,4);
			add_action('wp_logout',array(&$this,'logout'));
			add_action('ecart_logout','wp_clear_auth_cookie',1);
		}

		if (isset($_POST['ecart_registration']))
			$this->registration();

		$this->process();

	}

	/**
	 * Handle Ecart login processing
	 *	 
	 * @since 1.0
	 *
	 * @return void
	 **/
	function process () {
		global $Ecart;

		if (isset($_GET['acct']) && $_GET['acct'] == "logout") {
			// Redirect to remove the logout request
			add_action('ecart_logged_out',array(&$this,'redirect'));
			// Trigger the logout
			do_action('ecart_logout');
		}

		if ("wordpress" == $this->accounts) {
			// See if the wordpress user is already logged in
			$user = wp_get_current_user();

			// Wordpress user logged in, but Ecart customer isn't
			if (!empty($user->ID) && !$this->Customer->login) {
				if ($Account = new Customer($user->ID,'wpuser')) {
					$this->login($Account);
					$this->Customer->wpuser = $user->ID;
					return;
				}
			}
		}

		if (empty($_POST['process-login'])) return false;
		if ($_POST['process-login'] != "true") return false;

		add_action('ecart_login',array(&$this,'redirect'));

		// Prevent checkout form from processing
		remove_all_actions('ecart_process_checkout');

		switch ($this->accounts) {
			case "wordpress":
				if (!empty($_POST['account-login'])) {
					if (strpos($_POST['account-login'],'@') !== false) $mode = "email";
					else $mode = "loginname";
					$loginname = $_POST['account-login'];
				} else {
					new EcartError(__('You must provide a valid login name or email address to proceed.'), 'missing_account', ECART_AUTH_ERR);
				}

				if ($loginname) {
					$this->auth($loginname,$_POST['password-login'],$mode);
				}
				break;
			case "ecart":
				$mode = "loginname";
				if (!empty($_POST['account-login']) && strpos($_POST['account-login'],'@') !== false)
					$mode = "email";
				$this->auth($_POST['account-login'],$_POST['password-login'],$mode);
				break;
		}

	}

	/**
	 * Authorize login
	 *	 
	 * @since 1.0
	 *
	 * @param int $id The supplied identifying credential
	 * @param string $password The password provided for login authentication
	 * @param string $type (optional) Type of identifying credential provided (defaults to 'email')
	 * @return void
	 **/
	function auth ($id,$password,$type='email') {
		global $Ecart;

		$db = DB::get();
		switch($this->accounts) {
			case "ecart":
				$Account = new Customer($id,'email');

				if (empty($Account)) {
					new EcartError(__("No customer account was found with that email.","Ecart"),'invalid_account',ECART_AUTH_ERR);
					return false;
				}

				if (!wp_check_password($password,$Account->password)) {
					new EcartError(__("The password is incorrect.","Ecart"),'invalid_password',ECART_AUTH_ERR);
					return false;
				}

				break;

  		case "wordpress":
			if($type == 'email'){
				$user = get_user_by_email($id);
				if ($user) $loginname = $user->user_login;
				else {
					new EcartError(__("No customer account was found with that email.","Ecart"),'invalid_account',ECART_AUTH_ERR);
					return false;
				}
			} else $loginname = $id;
			$user = wp_authenticate($loginname,$password);
			if (!is_wp_error($user)) {
				wp_set_auth_cookie($user->ID, false, $Ecart->secure);
				do_action('wp_login', $loginname);

				return true;
			} else { // WordPress User Authentication failed
				$_e = $user->get_error_code();
				if($_e == 'invalid_username') new EcartError(__("No customer account was found with that login.","Ecart"),'invalid_account',ECART_AUTH_ERR);
				else if($_e == 'incorrect_password') new EcartError(__("The password is incorrect.","Ecart"),'invalid_password',ECART_AUTH_ERR);
				else new EcartError(__('Unknown login error: ').$_e,false,ECART_AUTH_ERR);
				return false;
			}
  			break;
			default: return false;
		}

		$this->login($Account);
		do_action('ecart_auth');

	}

	/**
	 * Login to the linked Ecart account when logging into WordPress
	 *	 
	 * @since 1.1
	 *
	 * @param $cookie N/A
	 * @param $expire N/A
	 * @param $expiration N/A
	 * @param int $user_id The WordPress user ID
	 * @return void
	 **/
	function wplogin ($cookie,$expire,$expiration,$user_id) {
		if ($Account = new Customer($user_id,'wpuser')) {
			$this->login($Account);
			add_action('wp_logout',array(&$this,'logout'));
		}
	}

	/**
	 * Initialize Ecart customer login data
	 *	 
	 * @since 1.0
	 *
	 * @return void
	 **/
	function login ($Account) {
		global $Ecart;
		$this->Customer->copydata($Account,"",array());
		$this->Customer->login = true;
		unset($this->Customer->password);
		$this->Billing->load($Account->id,'customer');
		$this->Billing->card = "";
		$this->Billing->cardexpires = "";
		$this->Billing->cardholder = "";
		$this->Billing->cardtype = "";
		$this->Shipping->load($Account->id,'customer');
		if (empty($this->Shipping->id))
			$this->Shipping->copydata($this->Billing);
		do_action_ref_array('ecart_login',array(&$this->Customer));
	}

	/**
	 * Clear the Customer-related session data
	 *	 
	 * @since 1.0
	 *
	 * @return void
	 **/
	function logout () {
		$this->Customer->login = false;
		$this->Customer->wpuser = false;
		$this->Customer->id = false;
		$this->Billing->id = false;
		$this->Billing->customer = false;
		$this->Shipping->id = false;
		$this->Shipping->customer = false;
		session_commit();
		do_action_ref_array('ecart_logged_out',array(&$this->Customer));
	}

	function registration () {
		$Errors =& EcartErrors();

		if (isset($_POST['info'])) $this->Customer->info = stripslashes_deep($_POST['info']);

		$this->Customer = new Customer();
		$this->Customer->updates($_POST);

		if (isset($_POST['confirm-password']))
			$this->Customer->confirm_password = $_POST['confirm-password'];

		$this->Billing = new Billing();
		if (isset($_POST['billing']))
			$this->Billing->updates($_POST['billing']);

		$this->Shipping = new Shipping();
		if (isset($_POST['shipping']))
			$this->Shipping->updates($_POST['shipping']);

		// Override posted shipping updates with billing address
		if ($_POST['sameshipaddress'] == "on")
			$this->Shipping->updates($this->Billing,
				array("_datatypes","_table","_key","_lists","id","created","modified"));

		// WordPress account integration used, customer has no wp user
		if ("wordpress" == $this->accounts && empty($this->Customer->wpuser)) {
			if ( $wpuser = get_current_user_id() ) $this->Customer->wpuser = $wpuser; // use logged in WordPress account
			else $this->Customer->create_wpuser(); // not logged in, create new account
		}

		if ($Errors->exist(ECART_ERR)) return false;

		// New customer, save hashed password
		if (empty($this->Customer->id) && !empty($this->Customer->password))
			$this->Customer->password = wp_hash_password($this->Customer->password);
		else unset($this->Customer->password); // Existing customer, do not overwrite password field!

		$this->Customer->save();
		if ($Errors->exist(ECART_ERR)) return false;

		$this->Billing->customer = $this->Customer->id;
		$this->Billing->save();

		if (!empty($this->Shipping->address)) {
			$this->Shipping->customer = $this->Customer->id;
			$this->Shipping->save();
		}

		if (!empty($this->Customer->id)) $this->login($this->Customer);

		ecart_redirect(ecarturl(false,'account'));
	}

	function redirect () {
		global $Ecart;
		if (!empty($_POST['redirect'])) {
			if ($_POST['redirect'] == "checkout") ecart_redirect(ecarturl(false,'checkout',$Ecart->Gateways->secure));
			else ecart_safe_redirect($_POST['redirect']);
			exit();
		}
		ecart_safe_redirect(ecarturl(false,'account',$Ecart->Gateways->secure));
		exit();
	}

} // END class Login

?>