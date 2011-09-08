  <div class="wrap ecart">
 <?php $theme_data = get_theme_data(TEMPLATEPATH . '/style.css'); ?>
  
  <table id="optionstable" class="form-table">
    <tbody>
      <tr>
        <td valign="top" width="100%">
          <form name="settings" id="general" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="post" class=
          "main_settings_form">
          
          <?php wp_nonce_field('ecart-settings-general'); ?>
          
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
                    Shopping Cart General Settings
                  </div>
					
                  <div class="ecart">
                  	
                                   
                                    
                  	<form name="settings" id="general" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="post">
                  		
                  		<?php if (!empty($updated)): ?><div class="setup"><p>Shopping Cart Settings Successfully Updated</p></div><?php endif; ?>
                  		
                  		<?php wp_nonce_field('ecart-settings-general'); ?>
                  
                  		
       					<div class="optionrow fix">
                           <div class="optiontitle fix">
                             <strong><?php _e('Store Administrator Merchant Email','Ecart'); ?></strong><br>
                           </div>
       
                           <div class="theinputs">
                             <div class="optioninputs">
                             <p><label class="context" for="merchant_email"><?php _e('Administrator Merchant Email','Ecart'); ?></label><br>
                             
                             <input class="regular-text" type="text" name="settings[merchant_email]" value="<?php echo esc_attr($this->Settings->get('merchant_email')); ?>" id="merchant_email" size="30" /><br />
                                                                                       <br>
                             <br>
                            </div>
                           </div>
       
                           <div class="theexplanation">
                             <div class="context"></div>
       
                             <p><?php _e('Enter the email address of the administrator of this store, this is where you\'ll receive e-mail notifications.','Ecart'); ?></p>
                           </div>
       
                           <div class="clear"></div>
                         </div>
                  			
       					 <div class="optionrow fix">
                           <div class="optiontitle fix">
                             <strong><label for="base_operations"><?php _e('Store Location (Needed for shipping)','Ecart'); ?></label></strong><br>
                           </div>
       
                           <div class="theinputs">
                             <div class="optioninputs">
                             <p><label class="context" for="base_operations"><?php _e('Store Location (Needed for shipping)','Ecart'); ?></label><br>
                             
                             <select name="settings[base_operations][country]" id="base_operations">
                             	<option value="">Select an option&nbsp;</option>
                             		<?php echo menuoptions($countries,$operations['country'],true); ?>
                             	</select>
                             	<select name="settings[base_operations][zone]" id="base_operations_zone">
                             		<?php if (isset($zones)) echo menuoptions($zones,$operations['zone'],true); ?>
                             	</select>
                             	<br />
                                                                                       <br>
                             <br>
                            </div>
                           </div>
       
                           <div class="theexplanation">
                             <div class="context"></div>
       
                             <p><?php _e('Please select the physical location of your store, this setting is required only when you need to ship products','Ecart'); ?></p>
                           </div>
       
                           <div class="clear"></div>
                         </div>           			
                  			
          					
          					<div class="optionrow fix">
                              <div class="optiontitle fix">
                                <strong><label for="target_markets"><?php _e('Where do you sell products ?','Ecart'); ?></label></strong><br>
                              </div>
          
                              <div class="theinputs">
                                <div class="optioninputs">
                              <br/>
               						
               						<div id="target_markets" class="multiple-select" style="width:290px;">
               						<ul>
               
               							<?php $even = true; foreach ($targets as $iso => $country): ?>
               								<li<?php if ($even) echo ' class="odd"'; $even = !$even; ?>><input type="checkbox" name="settings[target_markets][<?php echo $iso; ?>]" value="<?php echo $country; ?>" id="market-<?php echo $iso; ?>" checked="checked" /><label for="market-<?php echo $iso; ?>" accesskey="<?php echo substr($iso,0,1); ?>"><?php echo $country; ?></label></li>
               							<?php endforeach; ?>
               							<li<?php if ($even) echo ' class="odd"'; $even = !$even; ?>><input type="checkbox" name="selectall_targetmarkets"  id="selectall_targetmarkets" /><label for="selectall_targetmarkets"><strong><?php _e('Select All','Ecart'); ?></strong></label></li>
               							<?php foreach ($countries as $iso => $country): ?>
               							<?php if (!in_array($country,$targets)): ?>
               							<li<?php if ($even) echo ' class="odd"'; $even = !$even; ?>><input type="checkbox" name="settings[target_markets][<?php echo $iso; ?>]" value="<?php echo $country; ?>" id="market-<?php echo $iso; ?>" /><label for="market-<?php echo $iso; ?>" accesskey="<?php echo substr($iso,0,1); ?>"><?php echo $country; ?></label></li>
               							<?php endif; endforeach; ?>
               						</ul>
               						</div>                 
                                
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
                  			
                  			<!-- these settings are hidden - still under development  please do not enable -->
                  			
							<div class="notavailable" style="display:none !important;">
                  			
                  				<label for="dashboard-toggle"><?php _e('Dashboard Widgets','Ecart'); ?></label></th>
                  				<input type="hidden" name="settings[dashboard]" value="off" /><input type="checkbox" name="settings[dashboard]" value="on" id="dashboard-toggle"<?php if ($this->Settings->get('dashboard') == "on") echo ' checked="checked"'?> /><label for="dashboard-toggle"> <?php _e('Enabled','Ecart'); ?></label><br />
                  			    <?php _e('Check this to display store performance metrics and more on the WordPress Dashboard.','Ecart'); ?>
                  			
                  			<label for="cart-toggle"><?php _e('Order Status Labels','Ecart'); ?></label>
                  				
                  				<ol id="order-statuslabels">
                  				</ol>
                  				<?php _e('Add your own order processing status labels. Be sure to click','Ecart'); ?> <strong><?php _e('Save Changes','Ecart'); ?></strong> <?php _e('below!','Ecart'); ?>
                  			
                  			</div>
                  
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