<?php
/**
 * Google Checkout
 * @class GoogleCheckout
 *
 * @version 1.1.5
 * @package Ecart
 * @since 1.1
 * @subpackage GoogleCheckout
 *
 * $Id: GoogleCheckout.php 1725 2011-02-08 15:43:30Z jond $
 **/

require_once(ECART_PATH."/core/model/XML.php");

class GoogleCheckout extends GatewayFramework implements GatewayModule {

	var $secure = false;

	function __construct () {
		parent::__construct();

		global $Ecart;

		$this->urls['schema'] = 'http://checkout.google.com/schema/2';

		$this->urls['checkout'] = array(
			'live' => 'https://%s:%s@checkout.google.com/api/checkout/v2/merchantCheckout/Merchant/%s',
			'test' => 'https://%s:%s@sandbox.google.com/checkout/api/checkout/v2/merchantCheckout/Merchant/%s'
			);

		$this->urls['order'] = array(
			'live' => 'https://%s:%s@checkout.google.com/api/checkout/v2/request/Merchant/%s',
			'test' => 'https://%s:%s@sandbox.google.com/checkout/api/checkout/v2/request/Merchant/%s'
			);

		$this->urls['button'] = array(
			'live' => (is_ecart_secure()?'https':'http').'://checkout.google.com/buttons/checkout.gif',
			'test' => (is_ecart_secure()?'https':'http').'://sandbox.google.com/checkout/buttons/checkout.gif'
			);

		$this->merchant_calc_url = esc_url(add_query_string('_txnupdate=gc',ecarturl(false,'catalog',true)));

		$this->setup('id','key','apiurl');
		$this->settings['merchant_email'] = $Ecart->Settings->get('merchant_email');
		$this->settings['location'] = "en_US";
		$base = $Ecart->Settings->get('base_operations');
		if ($base['country'] == "GB") $this->settings['location'] = "en_UK";

		$this->settings['base_operations'] = $Ecart->Settings->get('base_operations');
		$this->settings['currency'] = $this->settings['base_operations']['currency']['code'];
		if (empty($this->settings['currency'])) $this->settings['currency'] = "USD";

		$this->settings['taxes'] = $Ecart->Settings->get('taxrates');

		if (isset($_GET['gctest'])) $this->order('');

		add_action('ecart_txn_update',array(&$this,'notifications'));
		add_filter('ecart_checkout_submit_button',array(&$this,'submit'),10,3);
		add_action('get_header',array(&$this,'analytics'));
		add_filter('ecart_tag_cart_google',array($this,'cartcheckout'));
		add_action('parse_request',array(&$this,'intercept_cartcheckout'));

	}

	function analytics() {  do_action('ecart_google_checkout_analytics'); }

	function actions () {
		add_action('ecart_init_checkout',array(&$this,'init'));

		add_action('ecart_save_payment_settings',array(&$this,'apiurl'));
		add_action('ecart_process_order',array(&$this, 'process'));
		add_filter('ecart_ordering_no_shipping_costs',array(&$this, 'hasshipping_filter'));

	}

	function init () {
		if (count($this->Order->payoptions) == 1) add_filter('ecart_shipping_hasestimates',array(&$this, 'hasestimates_filter'));
	}


	function hasestimates_filter () { return false; }

	function hasshipping_filter ( $valid ) {
		if ($this->settings['use_google_shipping'] == 'on') {
			return true;
		}
		return $valid;
	}

	function submit ($tag=false,$options=array(),$attrs=array()) {
		$type = "live";
		if ($this->settings['testmode'] == "on") $type = "test";
		$buttonuri = $this->urls['button'][$type];
		$buttonuri .= '?merchant_id='.$this->settings['id'];
		$buttonuri .= '&'.$this->settings['button'];
		$buttonuri .= '&style='.$this->settings['buttonstyle'];
		$buttonuri .= '&variant=text';
		$buttonuri .= '&loc='.$this->settings['location'];

		$tag[$this->settings['label']] = '<input type="image" name="process" src="'.$buttonuri.'" '.inputattrs($options,$attrs).' />';
		return $tag;

	}

	function cartcheckout ($result) {
		$tag = $this->submit();
		$form = '<form id="checkout" action="'.ecarturl(false,'checkout').'" method="post" >'
		.'<input type="hidden" name="google_cartcheckout" value="true" />'
		.ecart('checkout','function','return=1')
		.$tag[$this->settings['label']].'</form>';
		return $form;
	}

	function intercept_cartcheckout () {
		if (!empty($_POST['google_cartcheckout'])) {
			$this->process();
		}
	}

	function process () {
		global $Ecart;

		$stock = true;
		foreach( $this->Order->Cart->contents as $item ) { //check stock before redirecting to Google
			if (!$item->instock()){
				new EcartError(sprintf(__("There is not sufficient stock on %s to process order."),$item->name),'invalid_order',ECART_TRXN_ERR);
				$stock = false;
			}
		}
		if (!$stock) ecart_redirect(ecarturl(false,'cart',false));

		$message = $this->buildCheckoutRequest();
		$Response = $this->send($message,$this->urls['checkout']);

		if (!empty($Response)) {
			if ($Response->tag('error')) {
				new EcartError($Response->content('error-message'),'google_checkout_error',ECART_TRXN_ERR);
				ecart_redirect(ecarturl(false,'checkout'));
			}
			$redirect = false;
			$redirect = $Response->content('redirect-url');

			if ($redirect) {
				$Ecart->resession();
				ecart_redirect($redirect);
			}
		}

		return false;
	}

	function notifications () {
		if ($this->authentication()) {

			// Read incoming request data
			$data = trim(file_get_contents('php://input'));
			if(ECART_DEBUG) new EcartError($data,'google_incoming_request',ECART_DEBUG_ERR);

			// Handle notifications
			$XML = new xmlQuery($data);
			$type = $XML->context();
			if ( $type === false ) {
				if(ECART_DEBUG) new EcartError('Unable to determine context of request.','google_checkout_unknown_notification',ECART_DEBUG_ERR);
				return;
			}
			$serial = $XML->attr($type,'serial-number');

			$ack = true;
			switch($type) {
				case "new-order-notification": $this->order($XML); break;
				case "risk-information-notification": $this->risk($XML); break;
				case "order-state-change-notification": $this->state($XML); break;
				case "merchant-calculation-callback": $ack = $this->merchant_calc($XML); break;
				case "charge-amount-notification":	break;		// Not implemented
				case "refund-amount-notification":	$this->refund($XML); break;
				case "chargeback-amount-notification":		break;	// Not implemented
				case "authorization-amount-notification":	break;	// Not implemented
					break;
			}
			// Send acknowledgement
			if($ack) $this->acknowledge($serial);
		}
		exit();
	}

	/**
	 * authcode()
	 * Build a hash code for the merchant id and merchant key */
	function authcode ($id,$key) {
		return sha1($id.$key);
	}

	/**
	 * authentication()
	 * Authenticate an incoming request */
	function authentication () {
		if (isset($_GET['merc'])) $merc = $_GET['merc'];

		if (!empty($this->settings['id']) && !empty($this->settings['key'])
				&& $_GET['merc'] == $this->authcode($this->settings['id'],$this->settings['key']));
		 	return true;

		header('HTTP/1.1 401 Unauthorized');
		die("<h1>401 Unauthorized Access</h1>");
		exit();
	}

	/**
	 * acknowledge()
	 * Sends an acknowledgement message back to Google to confirm the notification
	 * was received and processed */
	function acknowledge ($serial) {
		header('HTTP/1.1 200 OK');
		$_ = array("<?xml version=\"1.0\" encoding=\"UTF-8\"?>");
		$_[] .= '<notification-acknowledgment xmlns="'.$this->urls['schema'].'" serial-number="'.$serial.'"/>';
		echo join("\n",$_);
	}

	/**
	* response()
	* Send a response for a callback
	* $message is a array containing XML response lines
	*
	* */
	function response ($message) {
		header('HTTP/1.1 200 OK');
		echo join("\n",$message);
	}

	function buildCheckoutRequest () {
		global $Ecart;
		$Cart = $this->Order->Cart;

		$_ = array('<?xml version="1.0" encoding="UTF-8"?>');
		$_[] = '<checkout-shopping-cart xmlns="'.$this->urls['schema'].'">';

			// Build the cart
			$_[] = '<shopping-cart>';
				$_[] = '<items>';
				foreach($Cart->contents as $i => $Item) {
					// if(ECART_DEBUG) new EcartError("Item $i: "._object_r($Item),'google_checkout_item_'.$i,ECART_DEBUG_ERR);
					$_[] = '<item>';
					$_[] = '<item-name>'.htmlspecialchars($Item->name).htmlspecialchars((!empty($Item->option->label))?' ('.$Item->option->label.')':'').'</item-name>';
					$_[] = '<item-description>'.htmlspecialchars($Item->description).'</item-description>';
					if ($Item->type == 'Download') $_[] = '<digital-content><description>'.
						apply_filters('ecart_googlecheckout_download_instructions', __('You will receive an email with download instructions upon receipt of payment.','Ecart')).
						'</description>'.
						apply_filters('ecart_googlecheckout_download_delivery_markup', '<email-delivery>true</email-delivery>').
						'</digital-content>';
					// Shipped Item
					if ($Item->weight > 0) $_[] = '<item-weight unit="LB" value="'.number_format(convert_unit($Item->weight,'lb'),2,'.','').'" />';
					$_[] = '<unit-price currency="'.$this->settings['currency'].'">'.number_format($Item->unitprice,$this->precision,'.','').'</unit-price>';
					$_[] = '<quantity>'.$Item->quantity.'</quantity>';
					if (!empty($Item->sku)) $_[] = '<merchant-item-id>'.$Item->sku.'</merchant-item-id>';
					$_[] = '<merchant-private-item-data>';
						$_[] = '<ecart-product-id>'.$Item->product.'</ecart-product-id>';
						$_[] = '<ecart-price-id>'.$Item->option->id.'</ecart-price-id>';
						if (is_array($Item->data) && count($Item->data) > 0) {
							$_[] = '<ecart-item-data-list>';
							foreach ($Item->data AS $name => $data) {
								$_[] = '<ecart-item-data name="'.esc_attr($name).'">'.esc_attr($data).'</ecart-item-data>';
							}
							$_[] = '</ecart-item-data-list>';
						}
					$_[] = '</merchant-private-item-data>';

					if ($this->settings['use_google_taxes'] != 'on' && is_array($this->settings['taxes'])) { // handle tax free or per item tax
						if ($Item->taxable === false)
							$_[] = '<tax-table-selector>non-taxable</tax-table-selector>';
						elseif ($item_tax_table_selector = apply_filters('ecart_google_item_tax_table_selector', false, $Item) !== false)
							$_[] = $item_tax_table_selector;
					}

					$_[] = '</item>';
				}

				// Include any discounts
				if ($Cart->Totals->discount > 0) {
					foreach($Cart->discounts as $promo) $discounts[] = $promo->name;
					$_[] = '<item>';
						$_[] = '<item-name>Discounts</item-name>';
						$_[] = '<item-description>'.join(", ",$discounts).'</item-description>';
						$_[] = '<unit-price currency="'.$this->settings['currency'].'">'.number_format($Cart->Totals->discount*-1,$this->precision,'.','').'</unit-price>';
						$_[] = '<quantity>1</quantity>';
					$_[] = '</item>';
				}
				$_[] = '</items>';

				// Include notification that the order originated from Ecart
				$_[] = '<merchant-private-data>';
					$_[] = '<shopping-session>'.$this->session.'</shopping-session>';
					$_[] = '<shopping-cart-agent>'.ECART_GATEWAY_USERAGENT.'</shopping-cart-agent>';
					$_[] = '<customer-ip>'.$_SERVER['REMOTE_ADDR'].'</customer-ip>';

					if (is_array($this->Order->data) && count($this->Order->data) > 0) {
						$_[] = '<ecart-order-data-list>';
						foreach ($this->Order->data AS $name => $data) {
							$_[] = '<ecart-order-data name="'.esc_attr($name).'">'.esc_attr($data).'</ecart-order-data>';
						}
						$_[] = '</ecart-order-data-list>';
					}
				$_[] = '</merchant-private-data>';

			$_[] = '</shopping-cart>';

			// Build the flow support request
			$_[] = '<checkout-flow-support>';
				$_[] = '<merchant-checkout-flow-support>';
				// Shipping Methods
				// Merchant Calculations
				$_[] = '<merchant-calculations>';
				$_[] = '<merchant-calculations-url>'.$this->merchant_calc_url.'</merchant-calculations-url>';
				$_[] = '</merchant-calculations>';

				if ($this->settings['use_google_shipping'] != 'on' && $Cart->shipped()) {
					if ($Cart->freeshipping === true) { // handle free shipping case and ignore all shipping methods
						$free_shipping_text = $Ecart->Settings->get('free_shipping_text');
						if (empty($free_shipping_text)) $free_shipping_text = __('Free Shipping!','Ecart');
						$_[] = '<shipping-methods>';
						$_[] = '<flat-rate-shipping name="'.$free_shipping_text.'">';
						$_[] = '<price currency="'.$this->settings['currency'].'">0.00</price>';
						$_[] = '<shipping-restrictions>';
						$_[] = '<allowed-areas><world-area /></allowed-areas>';
						$_[] = '</shipping-restrictions>';
						$_[] = '</flat-rate-shipping>';
						$_[] = '</shipping-methods>';
					}
					elseif (!empty($Cart->shipping)) {
						$_[] = '<shipping-methods>';
							foreach ($Cart->shipping as $i => $shipping) {
								$label = __('Shipping Option','Ecart').' '.($i+1);
								if (!empty($shipping->name)) $label = $shipping->name;
								$_[] = '<merchant-calculated-shipping name="'.$label.'">';
								$_[] = '<price currency="'.$this->settings['currency'].'">'.number_format($shipping->amount,$this->precision,'.','').'</price>';
								$_[] = '<shipping-restrictions>';
								$_[] = '<allowed-areas><world-area /></allowed-areas>';
								$_[] = '</shipping-restrictions>';
								$_[] = '</merchant-calculated-shipping>';
							}
						$_[] = '</shipping-methods>';
					}
				}

				if ($this->settings['use_google_taxes'] != 'on' && is_array($this->settings['taxes'])) {
					$_[] = '<tax-tables>';

					$_[] = '<alternate-tax-tables>';
						$_[] = '<alternate-tax-table standalone="true" name="non-taxable">'; // Built-in non-taxable table
							$_[] = '<alternate-tax-rules>';
								$_[] = '<alternate-tax-rule>';
									$_[] = '<rate>'.number_format(0,4).'</rate><tax-area><world-area /></tax-area>';
								$_[] = '</alternate-tax-rule>';
							$_[] = '</alternate-tax-rules>';
						$_[] = '</alternate-tax-table>';
						if ($alternate_tax_tables_content = apply_filters('ecart_google_alternate_tax_tables_content', false) !== false)
							$_[] = $alternate_tax_tables_content;
					$_[] = '</alternate-tax-tables>';

						$_[] = '<default-tax-table>';
							$_[] = '<tax-rules>';
							foreach ($this->settings['taxes'] as $tax) {
								$_[] = '<default-tax-rule>';
									$_[] = '<shipping-taxed>'.($Ecart->Settings->get('tax_shipping') == 'on' ? 'true' : 'false').'</shipping-taxed>';
									$_[] = '<rate>'.number_format($tax['rate']/100,4).'</rate>';
									$_[] = '<tax-area>';
										if ($tax['country'] == "US" && isset($tax['zone'])) {
											$_[] = '<us-state-area>';
												$_[] = '<state>'.$tax['zone'].'</state>';
											$_[] = '</us-state-area>';
										} elseif ($tax['country'] == "*") {
											$_[] = '<world-area />';
										} else {
											$_[] = '<postal-area>';
												$_[] = '<country-code>'.$tax['country'].'</country-code>';
											$_[] = '</postal-area>';
										}
									$_[] = '</tax-area>';
								$_[] = '</default-tax-rule>';
							}
							$_[] = '</tax-rules>';
						$_[] = '</default-tax-table>';
					$_[] = '</tax-tables>';
				}

				if (isset($_POST['analyticsdata'])) $_[] = '<analytics-data>'.$_POST['analyticsdata'].'</analytics-data>';
				$_[] = '</merchant-checkout-flow-support>';
			$_[] = '</checkout-flow-support>';


		$_[] = '</checkout-shopping-cart>';
		$request = join("\n", apply_filters('ecart_googlecheckout_build_request', $_));

		if(ECART_DEBUG) new EcartError($request,'googlecheckout_build_request',ECART_DEBUG_ERR);
		return $request;
	}


	/**
	 * order()
	 * Handles new order notifications from Google */
	function order ($XML) {
		global $Ecart;

		if (empty($XML)) {
			new EcartError("No transaction data was provided by Google Checkout.",'google_missing_txn_data',ECART_DEBUG_ERR);
			$this->error();
		}

		$sessionid = $XML->content('shopping-session:first');
		$order_summary = $XML->tag('order-summary');

		$Ecart->Order->unhook();
		$Ecart->resession($sessionid);
		$Ecart->Order = Ecart_buyObject::__new('Order',$Ecart->Order);
		$Ecart->Order->listeners();

		$Shopping = &$Ecart->Shopping;
		$Order = &$Ecart->Order;

		// Couldn't load the session data
		if ($Shopping->session != $sessionid) {
			new EcartError("Session could not be loaded: $sessionid",'google_session_load_failure',ECART_DEBUG_ERR);
			$this->error();
		} else new EcartError("Google Checkout successfully loaded session: $sessionid",'google_session_load_success',ECART_DEBUG_ERR);

		// // Check if this is a Ecart order or not
		// $origin = $order_summary->content('shopping-cart-agent');
		// if (empty($origin) ||
		// 	substr($origin,0,strpos("/",ECART_GATEWAY_USERAGENT)) == ECART_GATEWAY_USERAGENT)
		// 		return true;

		$buyer = $XML->tag('buyer-billing-address'); // buyer billing address not in order summary
		$name = $buyer->tag('structured-name');

		$Order->Customer->firstname = $name->content('first-name');
		$Order->Customer->lastname = $name->content('last-name');
		if (empty($name)) {
			$name = $buyer->content('contact-name');
			$names = explode(" ",$name);
			$Order->Customer->firstname = $names[0];
			$Order->Customer->lastname = $names[count($names)-1];
		}

		$email = $buyer->content('email');
		$Order->Customer->email = !empty($email) ? $email : '';
		$phone = $buyer->content('phone');
		$Order->Customer->phone = !empty($phone) ? $phone : '';

		$Order->Customer->marketing = $order_summary->content('buyer-marketing-preferences > email-allowed') != 'false' ? 'yes' : 'no';
		$Order->Billing->address = $buyer->content('address1');
		$Order->Billing->xaddress = $buyer->content('address2');
		$Order->Billing->city = $buyer->content('city');
		$Order->Billing->state = $buyer->content('region');
		$Order->Billing->country = $buyer->content('country-code');
		$Order->Billing->postcode = $buyer->content('postal-code');
		$Order->Billing->cardtype = "GoogleCheckout";

		$shipto = $order_summary->tag('buyer-shipping-address');
		$Order->Shipping->address = $shipto->content('address1');
		$Order->Shipping->xaddress = $shipto->content('address2');
		$Order->Shipping->city = $shipto->content('city');
		$Order->Shipping->state = $shipto->content('region');
		$Order->Shipping->country = $shipto->content('country-code');
		$Order->Shipping->postcode = $shipto->content('postal-code');

		$Ecart->Order->gateway = $this->name;


 		$txnid = $order_summary->content('google-order-number');

		// Google Adjustments
		$order_adjustment = $order_summary->tag('order-adjustment');
		$Order->Shipping->method = $order_adjustment->content('shipping-name') ? $order_adjustment->content('shipping-name') : $Order->Shipping->method;
		$Order->Cart->Totals->shipping = $order_adjustment->content('shipping-cost');
		$Order->Cart->Totals->tax = $order_adjustment->content('total-tax');

		// New total from order summary
		$Order->Cart->Totals->total = $order_summary->content('order-total');

		$Ecart->Order->transaction($txnid);

	}

	function risk ($XML) {
		$summary = $XML->tag('order-summary');
 		$id = $summary->content('google-order-number');

		if (empty($id)) {
			new EcartError("No transaction ID was provided with a risk information message sent by Google Checkout",false,ECART_DEBUG_ERR);
			$this->error();
		}

		$Purchase = new Purchase($id,'txnid');
		if ( empty($Purchase->id) ) {
			new EcartError('Transaction update on non existing order.','google_order_state_missing_order',ECART_DEBUG_ERR);
			return;
		}

		$Purchase->ip = $XML->content('ip-address');
		$Purchase->card = $XML->content('partial-cc-number');
		$Purchase->save();
	}

	function state ($XML) {
		$summary = $XML->tag('order-summary');
 		$id = $summary->content('google-order-number');
		if (empty($id)) {
			new EcartError("No transaction ID was provided with an order state change message sent by Google Checkout",'google_state_notification_error',ECART_DEBUG_ERR);
			$this->error();
		}

		$state = $XML->content('new-financial-order-state');

		$Purchase = new Purchase($id,'txnid');
		if ( empty($Purchase->id) ) {
			new EcartError('Transaction update on non existing order.','google_order_state_missing_order',ECART_DEBUG_ERR);
			return;
		}

		$Purchase->txnstatus = $state;
		$Purchase->card = $XML->content('partial-cc-number');
		$Purchase->save();

		if (strtoupper($state) == "CHARGEABLE" && $this->settings['autocharge'] == "on") {
			$_ = array('<?xml version="1.0" encoding="UTF-8"?>'."\n");
			$_[] = '<charge-order xmlns="'.$this->urls['schema'].'" google-order-number="'.$id.'">';
			$_[] = '<amount currency="'.$this->settings['currency'].'">'.number_format($Purchase->total,$this->precision,'.','').'</amount>';
			$_[] = '</charge-order>';
			$Response = $this->send(join("\n",$_), $this->urls['order']);
			if ($Response->tag('error')) {
				new EcartError($Response->content('error-message'),'google_checkout_error',ECART_TRXN_ERR);
				return;
			}
		}
	}

	/**
	 * refund
	 *
	 * Currently only sets the transaction status to refunded
	 *
	 * @since 1.1
	 *
	 * @param string $XML The XML request from google
	 * @return void
	 **/
	function refund($XML) {
		$summary = $XML->tag('order-summary');
 		$id = $summary->content('google-order-number');
		$refund = $summary->content('total-refund-amount');

		if (empty($id)) {
			new EcartError("No transaction ID was provided with an order refund notification sent by Google Checkout",'google_refund_notification_error',ECART_DEBUG_ERR);
			$this->error();
		}
		$Purchase = new Purchase($id,'txnid');
		if ( empty($Purchase->id) ) {
			new EcartError('Transaction refund on non-existing order.','google_refund_missing_order',ECART_DEBUG_ERR);
			return;
		}

		$Purchase->txnstatus = "REFUNDED";
		$Purchase->save();
	}


	/**
	* merchant_calc()
	* Callback function for merchant calculated shipping and taxes
	* taxes calculations unimplemented
	* returns false when it responds, as acknowledgement of merchant calculations is unnecessary
	* */
	function merchant_calc ($XML) {
		global $Ecart;

		if ($XML->content('shipping') == 'false') return true;  // ack

		$sessionid = $XML->content('shopping-session');
		$Ecart->resession($sessionid);
		$Ecart->Order = Ecart_buyObject::__new('Order',$Ecart->Order);
		$Ecart->Order->listeners();
		$Shopping = &$Ecart->Shopping;
		$Order = &$Ecart->Order;

		// Get new address information on order
		$shipto = $XML->tag('anonymous-address');

		$Order->Shipping->city = $shipto->content('city'); //['city']['CONTENT']
		$Order->Shipping->state = $shipto->content('region'); //['region']['CONTENT']
		$Order->Shipping->country = $shipto->content('country-code'); //['country-code']['CONTENT']
		$Order->Shipping->postcode = $shipto->content('postal-code'); //['postal-code']['CONTENT']

		// Calculate shipping options
		$Shipping = new CartShipping();
		$Shipping->calculate();
		$options = $Shipping->options();
		if (empty($options)) return true; // acknowledge, but don't respond

		$methods = $XML->attr('method','name');

		$address_id = $XML->attr('anonymous-address','id');
		$_ = array('<?xml version="1.0" encoding="UTF-8"?>');
		$_[] = "<merchant-calculation-results xmlns=\"http://checkout.google.com/schema/2\">";
		$_[] = "<results>";
		foreach ($options as $option) {
			if (in_array($option->name, $methods)) {
				$_[] = '<result shipping-name="'.$option->name.'" address-id="'.$address_id.'">';
				$_[] = '<shipping-rate currency="'.$this->settings['currency'].'">'.number_format($option->amount,$this->precision,'.','').'</shipping-rate>';
				$_[] = '<shippable>true</shippable>';
				$_[] = '</result>';
			}
		}
		$_[] = "</results>";
		$_[] = "</merchant-calculation-results>";

		if(ECART_DEBUG) new EcartError(join("\n",$_),'google-merchant-calculation-results',ECART_DEBUG_ERR);
		$this->response($_);
		return false; //no ack
	}

	function send ($message,$url) {
		$type = ($this->settings['testmode'] == "on")?'test':'live';
		$url = sprintf($url[$type],$this->settings['id'],$this->settings['key'],$this->settings['id']);
		$response = parent::send($message,$url);
		return new xmlQuery($response);
	}

	function error () { // Error response
		header('HTTP/1.1 500 Internal Server Error');
		die("<h1>500 Internal Server Error</h1>");
	}

	function settings () {
		global $Ecart;
		$buttons = array("w=160&h=43"=>"Small (160x43)","w=168&h=44"=>"Medium (168x44)","w=180&h=46"=>"Large (180x46)");
		$styles = array("white"=>"On White Background","trans"=>"With Transparent Background");

		$this->ui->text(0,array(
			'name' => 'id',
			'value' => $this->settings['id'],
			'size' => 18,
			'label' => __('Enter your Google Checkout merchant ID.','Ecart')
		));

		$this->ui->text(0,array(
			'name' => 'key',
			'value' => $this->settings['key'],
			'size' => 24,
			'label' => __('Enter your Google Checkout merchant key.','Ecart')
		));

 		if (!empty($this->settings['apiurl'])) {
			$this->ui->text(0,array(
				'name' => 'apiurl',
				'value' => $this->settings['apiurl'],
				'size' => 48,
				'readonly' => true,
				'classes' => 'selectall',
				'label' => __('Copy this URL to your Google Checkout integration settings API callback URL.','Ecart')
			));
		}

		$this->ui->checkbox(0,array(
			'name' => 'testmode',
			'checked' => ($this->settings['testmode'] == "on"),
			'label' => sprintf(__('Use the %s','Ecart'),'<a href="http://sandbox.google.com/checkout">Google Checkout Sandbox</a>')
		));

		$this->ui->menu(1,array(
			'name' => 'button',
			'selected' => $this->settings['button']
		),$buttons);

		$this->ui->menu(1,array(
			'name' => 'buttonstyle',
			'selected' => $this->settings['buttonstyle'],
			'label' => __('Select the preferred size and style of the Google Checkout button.','Ecart')
		),$styles);

		$this->ui->checkbox(1,array(
			'name' => 'autocharge',
			'checked' => ($this->settings['autocharge'] == "on"),
			'label' => __('Automatically charge orders','Ecart')
		));

		$this->ui->checkbox(1,array(
			'name' => 'use_google_taxes',
			'checked' => ($this->settings['use_google_taxes']),
			'label' => __('Use Google tax settings','Ecart')
		));

		$this->ui->checkbox(1,array(
			'name' => 'use_google_shipping',
			'checked' => ($this->settings['use_google_shipping']),
			'label' => __('Use Google shipping rate settings','Ecart')
		));

	}

	function apiurl () {
		global $Ecart;
		// Build the Google Checkout API URL if Google Checkout is enabled
		if (!empty($_POST['settings']['GoogleCheckout']['id']) && !empty($_POST['settings']['GoogleCheckout']['key'])) {
			$GoogleCheckout = new GoogleCheckout();
			$url = add_query_arg(array(
				'_txnupdate' => 'gc',
				'merc' => $GoogleCheckout->authcode(
										$_POST['settings']['GoogleCheckout']['id'],
										$_POST['settings']['GoogleCheckout']['key'])
				),ecarturl(false,'checkout',true));
			$_POST['settings']['GoogleCheckout']['apiurl'] = $url;
		}
	}

} // END class GoogleCheckout

?>