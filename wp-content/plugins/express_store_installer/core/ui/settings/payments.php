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
                    Shopping Cart Payment Settings
                  </div>
					
                  <div class="ecart" id="new-window">
                  	
                  <div id="payment-button" class="tablenav"><div class="alignright actions">
                  	<select name="payment-option-menu" id="payment-option-menu">
                  	</select>
                  	<button type="button" name="add-payment-option" id="add-payment-option" class="button-secondary" tabindex="9999"><?php _e('Add Payment Gateway','Ecart'); ?></button>
                  	</div>
                  </div>                 
                                    
             	<form name="settings" id="payments" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="post">
             		<?php wp_nonce_field('ecart-settings-payments'); ?>
             		<div><input type="hidden" id="active-gateways" name="settings[active_gateways]" /></div>
             
             	
             
             		<table id="payment-settings" class="form-table">
              		</table>
             
             		<br class="clear" />
             
             		
  
             	</form>
             </div>
             
             <script type="text/javascript">
             /* <![CDATA[ */
             var ECART_PAYMENT_OPTION = <?php _jse('Option Name','Ecart'); ?>,
             	ECART_DELETE_PAYMENT_OPTION = <?php echo _jse('Are you sure you want to delete this payment option?','Ecart'); ?>,
             	ECART_GATEWAY_MENU_PROMPT = <?php _jse('Select a payment gateway&hellip;','Ecart'); ?>,
             	ECART_PLUGINURI = "<?php echo ECART_PLUGINURI; ?>",
             	ECART_SELECT_ALL = <?php _jse('Select All','Ecart'); ?>,
             	gateways = <?php echo json_encode($gateways); ?>;
             
             jQuery(document).ready( function() {
             	var $=jqnc(),
             		handlers = new CallbackRegistry();
             
             	handlers.options = {};
             	handlers.enabled = [];
             	handlers.register = function (name,object) {
             		this.callbacks[name] = function () {object['payment']();}
             		this.options[name] = object;
             	}
             
             	handlers.call = function(name,arg1,arg2,arg3) {
             
             		this.callbacks[name](arg1,arg2,arg3);
             		var module = this.options[name];
             		module.behaviors();
             	}
             
             	<?php do_action('ecart_gateway_module_settings'); ?>
             
             	// Populate the payment options menu
             	var options = '';
             	options += '<option disabled="disabled">'+ECART_GATEWAY_MENU_PROMPT+'<\/option>';
             	$.each(handlers['options'],function (id,object) {
             		var disabled = '';
             		if ($.inArray(id,gateways) != -1) {
             			handlers.call(id);
             			if (!object.multi) disabled = ' disabled="disabled"';
             		}
             		options += '<option value="'+id+'"'+disabled+'>'+object.name+'<\/option>';
             	});
             	$('#payment-option-menu').html(options);
             
             	$('#add-payment-option').click(function () {
             		var module = $('#payment-option-menu').val(),
             			selected = $('#payment-option-menu :selected');
             		if (!selected.attr('disabled')) {
             			handlers.call(module);
             			if (!handlers.options[module].multi) selected.attr('disabled',true);
             		}
             	});
             
             });
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