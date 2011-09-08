  <div class="wrap ecart">
 <?php $theme_data = get_theme_data(TEMPLATEPATH . '/style.css'); ?>
  
  <table id="optionstable" class="form-table">
    <tbody>
      <tr>
        <td valign="top" width="100%">
        <form name="settings" id="checkout" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="post" class=
        "main_settings_form">
          
          <?php wp_nonce_field('ecart-settings-checkout'); ?>
          
                        </div>
            <div id="header">
              <h2>E-Panel Theme Configuration</h2>
            </div>

            <div id="optionssubheader">
              <div class="hl"></div>

              <div class="padding fix">
                <div class="subheader_links">
                	<a class="sh_preview" href="<?php echo home_url(); ?>/" target="_blank" target-position="front">View Your Website</a>
                	<a class="sh_docs" href="<?php doc_url();?>" target="_blank" ><?php _e('Documentation', 'epanel');?></a>
                	<a class="sh_support" href="<?php support_url();?>" target="_blank" ><?php _e('Support', 'epanel');?></a>
                	<a class="sh_version" href="<?php theme_url();?>" target="_blank" ><?php _e('Theme Version : ', 'epanel'); echo $theme_data['Version'];?></a>
                </div>

                <div class="subheader_right">
                  <input type="submit" class="button-primary" name="save" value="<?php _e('Save Shopping Cart Configuration','Ecart'); ?>" />
                </div>
              </div>
            </div>

            <div class="clear"></div>

            <div id="tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all">
              <ul id="tabsnav" class=
              "ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
              

                <?php include("navigation.php"); ?> 
                
                  <li><span class="graphic bottom">&nbsp;</span></li>
                
                                <li style="list-style: none; display: inline">
                                  <div class="framework_loading" style="display: none;">
                                    <a href="#" target="_blank" title=
                                    "Javascript Issue Detector"><span class=
                                    "framework_loading_gif">&nbsp;</span></a>
                                  </div>
                                </li>
                
                
              </ul>

              <div id="thetabs" class="fix">
                
                <div id="general_settings" class=
                "tabinfo ui-tabs-panel ui-widget-content ui-corner-bottom" style="">
                  <div class="tabtitle">
                    Shopping Cart Checkout Settings
                  </div>
					
                  <div class="ecart">
                  	
                                   
                                    
                  	<form name="settings" id="general" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="post">
                  		
                  		<?php if (!empty($updated)): ?><div class="setup"><p>Shopping Cart Settings Successfully Updated</p></div><?php endif; ?>
                  		
                  		
                  		<?php wp_nonce_field('ecart-settings-checkout'); ?>
                  
                  		
       					<div class="optionrow fix">
                           <div class="optiontitle fix">
                             <strong><label for="confirm_url"><?php _e('Show Order Confirmation Page','Ecart'); ?></label></strong><br>
                           </div>
       
                           <div class="theinputs">
                             <div class="optioninputs">
                             
                             
                             <input type="radio" name="settings[order_confirmation]" value="ontax" id="order_confirmation_ontax"<?php if($this->Settings->get('order_confirmation') == "ontax") echo ' checked="checked"' ?> /> <label for="order_confirmation_ontax"><?php _e('Show for taxed orders only','Ecart'); ?></label><br />
                             	<input type="radio" name="settings[order_confirmation]" value="always" id="order_confirmation_always"<?php if($this->Settings->get('order_confirmation') == "always") echo ' checked="checked"' ?> /> <label for="order_confirmation_always"><?php _e('Show for all orders','Ecart') ?></label>
                                                                                       <br>
                             <br>
                            </div>
                           </div>
       
                           <div class="theexplanation">
                             <div class="context"></div>
       
                             <p><?php _e('Choose if you want to show the order confirmation page for taxed orders only or for every order placed on your store.','Ecart'); ?></p>
                           </div>
       
                           <div class="clear"></div>
                         </div>
                  			
       					 <div class="optionrow fix">
                           <div class="optiontitle fix">
                             <strong><label for="receipt_copy_both"><?php _e('Order Receipt Confirmation Emails','Ecart'); ?></label></strong><br>
                           </div>
       
                           <div class="theinputs">
                             <div class="optioninputs">
                            
                             
                           <input type="radio" name="settings[receipt_copy]" value="0" id="receipt_copy_customer_only"<?php if ($this->Settings->get('receipt_copy') == "0") echo ' checked="checked"'; ?> /> <label for="receipt_copy_customer_only"><?php _e('Send to Customer Only','Ecart'); ?></label><br />
                           	<input type="radio" name="settings[receipt_copy]" value="1" id="receipt_copy_both"<?php if ($this->Settings->get('receipt_copy') == "1") echo ' checked="checked"'; ?> /> <label for="receipt_copy_both"><?php _e('Send to Customer &amp; Shop Owner Email','Ecart'); ?></label> 
                             	<br />
                                                                                       <br>
                             <br>
                            </div>
                           </div>
       
                           <div class="theexplanation">
                             <div class="context"></div>
       
                             <p><?php _e('You can setup the email address from the general settings tab.','Ecart'); ?></p>
                           </div>
       
                           <div class="clear"></div>
                         </div>           			
                  			
          					
          					<div class="optionrow fix">
                              <div class="optiontitle fix">
                                <strong><label for="account-system-none"><?php _e('Store Customer Accounts System','Ecart'); ?></label></strong><br>
                              </div>
          
                              <div class="theinputs">
                                <div class="optioninputs">
                              <br/>
               						
               						
               						<input type="radio" name="settings[account_system]" value="none" id="account-system-none"<?php if($this->Settings->get('account_system') == "none") echo ' checked="checked"' ?> /> <label for="account-system-none"><?php _e('No Accounts Record','Ecart'); ?></label><br />
           
               							<input type="radio" name="settings[account_system]" value="wordpress" id="account-system-wp"<?php if($this->Settings->get('account_system') == "wordpress") echo ' checked="checked"' ?> /> <label for="account-system-wp"><?php _e('Enable Account System integrated with WordPress Accounts','Ecart'); ?></label>              
                                
                                <br />
                                                                                          <br>
                                <br>
                               </div>
                              </div>
          
                              <div class="theexplanation">
                                <div class="context"></div>
          
                                <p><?php _e('Select where you are selling products to. This setting is required only when you need to ship products','Ecart'); ?></p>
                              </div>
          
                              <div class="clear"></div>
                            </div>
					    
					    
					    <div class="optionrow fix">
                        <div class="optiontitle fix">
                          <strong><label for="accounting-serial"><?php _e('Order Increment Number','Ecart'); ?></label></strong><br>
                        </div>
    
                        <div class="theinputs">
                          <div class="optioninputs">
                        <br/>
         						
         						
         					<input type="text" name="settings[next_order_id]" id="accounting-serial" value="<?php echo esc_attr($next_setting); ?>" size="7" class="selectall" />        
                          
                          <br />
                                                                                    <br>
                          <br>
                         </div>
                        </div>
    
                        <div class="theexplanation">
                          <div class="context"></div>
    
                          <p><?php _e('Set the next order number to sync with your store orders system.','Ecart'); ?></p>
                        </div>
    
                        <div class="clear"></div>
                      </div>
      					
      					
      					<div class="optionrow fix">
                          <div class="optiontitle fix">
                            <strong><?php _e('Coupons Usage Limit Per order','Ecart'); ?></strong><br>
                          </div>
      
                          <div class="theinputs">
                            <div class="optioninputs">
                          <br/>
           						
           					<select name="settings[promo_limit]" id="promo-limit">
           						<option value="">&infin;</option>
           						<?php echo menuoptions($promolimit,$this->Settings->get('promo_limit')); ?>
           						</select>        
                            
                            <br />
                                                                                      <br>
                            <br>
                           </div>
                          </div>
      
                          <div class="theexplanation">
                            <div class="context"></div>
      
                            <p><?php _e('Set the coupons usage limit per order for your store coupons system.','Ecart'); ?></p>
                          </div>
      
                          <div class="clear"></div>
                        </div>
 
 					<div class="optionrow fix">
                     <div class="optiontitle fix">
                       <strong><label for="download-limit"><?php _e('Digital Products Download Limit','Ecart'); ?></label></strong><br>
                     </div>
 
                     <div class="theinputs">
                       <div class="optioninputs">
                     <br/>
      						
      					<select name="settings[download_limit]" id="download-limit">
      						<option value="">&infin;</option>
      						<?php echo menuoptions($downloads,$this->Settings->get('download_limit')); ?>
      						</select>       
                       
                       <br />
                                                                                 <br>
                       <br>
                      </div>
                     </div>
 
                     <div class="theexplanation">
                       <div class="context"></div>
 
                       <p><?php _e('Set the digital products download limit per product for your store.','Ecart'); ?></p>
                     </div>
 
                     <div class="clear"></div>
                   </div>
              			
                  			<!-- these settings are hidden - still under development  please do not enable -->
                  			
                  			<table class="form-table" class="notavailable" style="display:none !important;">
                  					<tr>
                  						<th scope="row" valign="top"><label for="download-timelimit"><?php _e('Time Limit','Ecart'); ?></label></th>
                  						<td><select name="settings[download_timelimit]" id="download-timelimit">
                  							<option value=""><?php _e('No Limit','Ecart'); ?></option>
                  							<?php echo menuoptions($time,$this->Settings->get('download_timelimit'),true); ?>
                  								</select>
                  						</td>
                  					</tr>
                  					<tr>
                  						<th scope="row" valign="top"><label for="download-restriction"><?php _e('IP Restriction','Ecart'); ?></label></th>
                  						<td><input type="hidden" name="settings[download_restriction]" value="off" />
                  							<label for="download-restriction"><input type="checkbox" name="settings[download_restriction]" id="download-restriction" value="ip" <?php echo ($this->Settings->get('download_restriction') == "ip")?'checked="checked" ':'';?> /> <?php _e('Restrict to the computer the product is purchased from','Ecart'); ?></label></td>
                  					</tr>
                  				</table>
                  			
                      	</form>
                  </div>
                  
                  <script type="text/javascript">
                  /* <![CDATA[ */
                  var labels = <?php echo json_encode($statusLabels); ?>,
                  	labelInputs = [],
                  	activated = <?php echo ($activated)?'true':'false'; ?>,
                  	ECART_PLUGINURI = "<?php echo ECART_PLUGINURI; ?>",
                  	ECART_ACTIVATE_KEY = <?php _jse('Activate Key','Ecart'); ?>,
                  	ECART_DEACTIVATE_KEY = <?php _jse('Deactivate Key','Ecart'); ?>,
                  	ECART_CONNECTING = <?php _jse('Connecting','Ecart'); ?>,
                  	ECART_CONFIRM_DELETE_LABEL = <?php _jse('Are you sure you want to remove this order status label?','Ecart'); ?>,
                  	ECART_CUSTOMER_SERVICE = <?php printf(json_encode(__('Contact <a href="%s">customer service</a>.','Ecart')),ECART_CUSTOMERS); ?>,
                  	keyStatus = {
                  		'-000':<?php _jse('The server could not be reached because of a connection problem.','Ecart'); ?>,
                  		'-1':<?php _jse('An unkown error occurred.','Ecart'); ?>,
                  		'0':<?php _jse('This key has been deactivated.','Ecart'); ?>,
                  		'1':<?php _jse('This key has been activated.','Ecart'); ?>,
                  		'-100':<?php _jse('An unknown activation error occurred.','Ecart'); ?>+ECART_CUSTOMER_SERVICE,
                  		'-101':<?php _jse('The key provided is not valid.','Ecart'); ?>+ECART_CUSTOMER_SERVICE,
                  		'-102':<?php _jse('This site is not valid to activate the key.','Ecart'); ?>+ECART_CUSTOMER_SERVICE,
                  		'-103':<?php _jse('The key provided could not be validated by ecartlugin.net.','Ecart'); ?>+ECART_CUSTOMER_SERVICE,
                  		'-104':<?php _jse('The key provided is already active on another site.','Ecart'); ?>+ECART_CUSTOMER_SERVICE,
                  		'-200':<?php _jse('An unkown deactivation error occurred.','Ecart'); ?>+ECART_CUSTOMER_SERVICE,
                  		'-201':<?php _jse('The key provided is not valid.','Ecart'); ?>+ECART_CUSTOMER_SERVICE,
                  		'-202':<?php _jse('The site is not valid to be able to deactivate the key.','Ecart'); ?>+ECART_CUSTOMER_SERVICE,
                  		'-203':<?php _jse('The key provided could not be validated by ecartlugin.net.','Ecart'); ?>+ECART_CUSTOMER_SERVICE
                  	},
                  
                  	zones_url = '<?php echo wp_nonce_url(admin_url('admin-ajax.php'), 'wp_ajax_ecart_country_zones'); ?>',
                  	act_key_url = '<?php echo wp_nonce_url(admin_url('admin-ajax.php'), 'wp_ajax_ecart_activate_key'); ?>',
                  	deact_key_url = '<?php echo wp_nonce_url(admin_url('admin-ajax.php'), 'wp_ajax_ecart_deactivate_key'); ?>';
                  /* ]]> */
                  </script>

                  <div class="clear"></div>
                </div>
			
                              </div><!-- End the tabs -->
            </div><!-- End tabs -->

            <div id="optionsfooter">
              <div class="hl"></div>

              <div class="theinputs">
                              </div>

              <div class="clear"></div>
            </div>
          </form>

          
        </td>
      </tr>
    </tbody>
  </table>
</div>