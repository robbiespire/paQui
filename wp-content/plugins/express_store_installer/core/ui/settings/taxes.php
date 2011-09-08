  <div class="wrap ecart">
 <?php $theme_data = get_theme_data(TEMPLATEPATH . '/style.css'); ?>
  
  <table id="optionstable" class="form-table">
    <tbody>
      <tr>
        <td valign="top" width="100%">
          <form name="settings" id="taxes" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="post" class="main_settings_form">
          
          <?php wp_nonce_field('ecart-settings-taxes'); ?>
          
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
                    Shopping Cart Taxes Configuration
                  </div>
					
                  <div class="ecart">
                  		
                  		<?php if (!empty($updated)): ?><div class="setup"><p><?php echo $updated; ?></p></div><?php endif; ?>
                  		                  		
       					<div class="optionrow fix">
                           <div class="optiontitle fix">
                             <strong><label for="taxes-toggle"><?php _e('Enable Taxes Calculation','Ecart'); ?></label></strong><br>
                           </div>
       
                           <div class="theinputs">
                             <div class="optioninputs">
                             
                             <input type="hidden" name="settings[taxes]" value="off" /><input type="checkbox" name="settings[taxes]" value="on" id="taxes-toggle"<?php if ($this->Settings->get('taxes') == "on") echo ' checked="checked"'?> /><?php _e('Enables tax calculations ?','Ecart'); ?><br/>
                                                                                       <br>
                             <br>
                            </div>
                           </div>
       
                           <div class="theexplanation">
                             <div class="context"></div>
       
                             <p><?php _e('Check this option to enable taxes calculation. Disable if you are selling non-taxable products.','Ecart'); ?></p>
                           </div>
       
                           <div class="clear"></div>
                         </div>
                  			
       					 <div class="optionrow fix">
                           <div class="optiontitle fix">
                             <strong><label for="tax-shipping-toggle"><?php _e('Enable Tax Shipping','Ecart'); ?></label></strong><br>
                           </div>
       
                           <div class="theinputs">
                             <div class="optioninputs">
                             <br/>
                             
                             <input type="hidden" name="settings[tax_shipping]" value="off" /><input type="checkbox" name="settings[tax_shipping]" value="on" id="tax-shipping-toggle"<?php if ($this->Settings->get('tax_shipping') == "on") echo ' checked="checked"'?> /><?php _e('Enable to add shipping and handling in taxes.','Ecart'); ?>
                             	<br />
                                                                                       <br>
                             <br>
                            </div>
                           </div>
       
                           <div class="theexplanation">
                             <div class="context"></div>
       
                             <p><?php _e('Check this option to enable shipping taxes and handling fee in taxes, disable this option if you are selling non-taxable products','Ecart'); ?></p>
                           </div>
       
                           <div class="clear"></div>
                         </div>           			
        				
        				
   <div class="optiontitle fix">
     <strong><?php _e('Taxes Configuration','Ecart'); ?></strong><br>
   </div>
                  			
          				<div style="margin-left:-120px;">	
          				
          						<?php if ($this->Settings->get('target_markets')): ?>
          						<table id="tax-rates" class="form-table"><tbody></tbody></table>
          						<?php else: ?>
          						<p><strong><?php _e('Error :','Ecart'); ?>:</strong> <?php sprintf(__('You must select the target markets you will be selling to under %s before you can setup tax rates.','Ecart'),'<a href="?page='.$this->Admin->settings['settings'][0].'">'.__('General settings','Ecart').'</a>'); ?></p>
          						<?php endif; ?>
          				
          						<div class="tablenav">
          							<div class="alignright actions"><button type="button" id="add-taxrate" class="button-secondary"><?php _e('Add a New Tax Rate','Ecart'); ?></button>
          					        </div>
          						</div> 
          				</div>
          				                 
                      	</form>
                  </div>
                  
                  <script type="text/javascript">
                  /* <![CDATA[ */
                  var ratetable,rates,base,countries,zones,localities,taxrates,
                  	ratesidx,countriesInUse,zonesInUse,allCountryZonesInUse,
                  	APPLY_LOGIC,LOCAL_RATES,LOCAL_RATE_INSTRUCTIONS,ECART_PLUGINURI,RULE_LANG,
                  	sugg_url='<?php echo wp_nonce_url(admin_url('admin-ajax.php'),'wp_ajax_ecart_suggestions'); ?>',
                  	upload_url='<?php echo wp_nonce_url(admin_url('admin-ajax.php'),'wp_ajax_ecart_upload_local_taxes'); ?>',
                  	ratetable = jQuery('#tax-rates'),
                  	rates = <?php echo json_encode($rates); ?>,
                  	base = <?php echo json_encode($base); ?>,
                  	countries = <?php echo json_encode($countries); ?>,
                  	zones = <?php echo json_encode($zones); ?>,
                  	localities = <?php echo json_encode(Lookup::localities()); ?>,
                  	taxrates = new Array(),
                  	ratesidx = 0,
                  	countriesInUse = new Array(),
                  	zonesInUse = new Array(),
                  	allCountryZonesInUse = new Array(),
                  	APPLY_LOGIC = <?php _jse("Apply tax rate when %s of the following conditions match","Ecart"); ?>,
                  	LOCAL_RATES = <?php _jse("Local Rates","Ecart"); ?>,
                  	LOCAL_RATE_INSTRUCTIONS = <?php _jse("No local regions have been setup for this location. Local regions can be specified by uploading a formatted local rates file.","Ecart"); ?>,
                  	LOCAL_RATES_UPLOADERR = <?php _jse("The file was uploaded successfully, but the data returned by the server cannot be used.","Ecart"); ?>,
                  	ANY_OPTION = <?php _jse("any","Ecart"); ?>,
                  	ALL_OPTION = <?php _jse("all","Ecart"); ?>,
                  	ECART_PLUGINURI = '<?php echo ECART_PLUGINURI; ?>',
                  	RULE_LANG = {
                  		"product-name":<?php _jse('Product name is','Ecart'); ?>,
                  		"product-tags":<?php _jse('Product is tagged','Ecart'); ?>,
                  		"product-category":<?php _jse('Product in category','Ecart'); ?>,
                  		"customer-type":<?php _jse('Customer type is','Ecart'); ?>
                  	};
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