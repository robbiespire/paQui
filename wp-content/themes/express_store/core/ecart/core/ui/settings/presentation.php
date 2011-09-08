  <div class="wrap ecart">
 <?php $theme_data = get_theme_data(TEMPLATEPATH . '/style.css'); ?>
  
  <table id="optionstable" class="form-table">
    <tbody>
      <tr>
        <td valign="top" width="100%">
          <form name="settings" id="presentation" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="post" class="main_settings_form">
          
          <?php wp_nonce_field('ecart-settings-presentation'); ?>
          
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
                  <input type="submit" class="button-primary" name="save" value="<?php _e('Save Ecarting Cart Configuration','Ecart'); ?>" />
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
                    Ecarting Cart General Settings
                  </div>
					
                  <div class="ecart">
           
                  		
                  		<?php if (!empty($updated)): ?><div class="setup"><p>Shopping Cart Settings Successfully Updated</p></div><?php endif; ?>
                  		

       					<div class="optionrow fix">
                           <div class="optiontitle fix">
                             <strong><label for="outofstock-catalog"><?php _e('Catalog Inventory','Ecart'); ?></label></strong><br>
                           </div>
       
                           <div class="theinputs">
                             <div class="optioninputs">
                          <br>
                             
                             <input type="hidden" name="settings[outofstock_catalog]" value="off" /><input type="checkbox" name="settings[outofstock_catalog]" value="on" id="outofstock-catalog"<?php if ($this->Settings->get('outofstock_catalog') == "on") echo ' checked="checked"'?> /><label for="outofstock-catalog"> <?php _e('Show out-of-stock products in the catalog','Ecart'); ?></label><br />
                                                                                       <br>
                             <br>
                            </div>
                           </div>
       
                           <div class="theexplanation">
                             <div class="context"></div>
       
                             <p><?php _e('Check this option to show out of stock products in your catalog, otherwise uncheck this option to automatically hide out of stock products from your catalog','Ecart'); ?></p>
                           </div>
       
                           <div class="clear"></div>
                         </div>
                         
                         <!-- hidden option-->
                         
                         <div style="display:none !important;">
                         
                         	<!-- list mode under construction -->
	                        <select name="settings[default_catalog_view]" id="default-catalog-view">
	                        	<?php echo menuoptions($category_views,$this->Settings->get('default_catalog_view'),true); ?>
	                        </select>
	                     	
	                     	<!-- dynamic grid under construction -->
	                     	<select name="settings[row_products]" id="row-products">
	                     		<?php echo menuoptions($row_products,$this->Settings->get('row_products')); ?>
	                     	</select>
	                        
                         
                         </div>
                         
                         <!-- do not enable this option -->
                         
                         
                  			
       					 <div class="optionrow fix">
                           <div class="optiontitle fix">
                             <strong><label for="catalog-pagination"><?php _e('Catalog Products Per Page','Ecart'); ?></label></strong><br>
                           </div>
       
                           <div class="theinputs">
                             <div class="optioninputs">
                             <p><label class="context" for="catalog-pagination"><?php _e('Catalog Products Per Page','Ecart'); ?></label><br>
                             
                            <input type="text" name="settings[catalog_pagination]" id="catalog-pagination" value="<?php echo esc_attr($this->Settings->get('catalog_pagination')); ?>" size="4" class="selectall" />
                             	<br />
                                                                                       <br>
                             <br>
                            </div>
                           </div>
       
                           <div class="theexplanation">
                             <div class="context"></div>
       
                             <p><?php _e('Please choose how many products per page you want to show, note that this affects only the catalog page.','Ecart'); ?></p>
                           </div>
       
                           <div class="clear"></div>
                         </div>           			
                  			
          					
          					<div class="optionrow fix">
                              <div class="optiontitle fix">
                                <strong><label for="product-order"><?php _e('Catalog Product Order','Ecart'); ?></label></strong><br>
                              </div>
          
                              <div class="theinputs">
                                <div class="optioninputs">
                              <br/>
               						
               						<select name="settings[default_product_order]" id="product-order">
               							<?php echo menuoptions($productOrderOptions,$this->Settings->get('default_product_order'),true); ?>
               						</select>          
                                
                                <br />
                                                                                          <br>
                                <br>
                               </div>
                              </div>
          
                              <div class="theexplanation">
                                <div class="context"></div>
          
                                <p><?php _e('Set the default display order of products shown in categories and catalog.','Ecart'); ?></p>
                              </div>
          
                              <div class="clear"></div>
                            </div>
                  			
                  			
           					 <div class="optionrow fix">
                                <div class="optiontitle fix">
                                  <strong><label for="showcase-order"><?php _e('Product Images Order','Ecart'); ?></label></strong><br>
                                </div>
            
                                <div class="theinputs">
                                  <div class="optioninputs">
                                <br/>
                 						
                 						<select name="settings[product_image_order]" id="showcase-order">
                 							<?php echo menuoptions($orderOptions,$this->Settings->get('product_image_order'),true); ?>
                 						</select> by
                 						<select name="settings[product_image_orderby]" id="showcase-orderby">
                 							<?php echo menuoptions($orderBy,$this->Settings->get('product_image_orderby'),true); ?>
                 						</select>         
                                  
                                  <br />
                                                                                            <br>
                                  <br>
                                 </div>
                                </div>
            
                                <div class="theexplanation">
                                  <div class="context"></div>
            
                                  <p><?php _e('Set the default display order of your product pictures.','Ecart'); ?></p>
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