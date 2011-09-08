  <div class="wrap ecart">
 <?php $theme_data = get_theme_data(TEMPLATEPATH . '/style.css'); ?>
  
  <table id="optionstable" class="form-table">
    <tbody>
      <tr>
        <td valign="top" width="100%">
          <form name="settings" id="shipping" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="post" class="main_settings_form">
          
          <?php wp_nonce_field('ecart-settings-shipping'); ?>
          
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
                    Shopping Cart Shipping Settings
                  </div>
					
                  <div class="ecart">
                  		
                  		<?php if (!empty($updated)): ?><div class="setup"><p><?php echo $updated; ?></p></div><?php endif; ?>
                  		
       					<div class="optionrow fix">
                           <div class="optiontitle fix">
                             <strong><label for="shipping-toggle"><?php _e('Enable Shipping ?','Ecart'); ?></label></strong><br>
                           </div>
       
                           <div class="theinputs">
                             <div class="optioninputs">
                            
                             
                             <input type="hidden" name="settings[shipping]" value="off" /><input type="checkbox" name="settings[shipping]" value="on" id="shipping-toggle"<?php if ($this->Settings->get('shipping') == "on") echo ' checked="checked"'?> /><label for="shipping-toggle"> <?php _e('Shipping is Currently Enabled On your Store','Ecart'); ?></label><br />
                                                                                       <br>
                             <br>
                            </div>
                           </div>
       
                           <div class="theexplanation">
                             <div class="context"></div>
       
                             <p><?php _e('Check this option to enable shipping. Uncheck this option if you don\'t need shipping or you are selling digital products','Ecart'); ?></p>
                           </div>
       
                           <div class="clear"></div>
                         </div>
                  			
       					 <div class="optionrow fix">
                           <div class="optiontitle fix">
                             <strong><label for="weight-unit"><?php _e('Product Weight Unit','Ecart'); ?></label></strong><br>
                           </div>
       
                           <div class="theinputs">
                             <div class="optioninputs">
                            <br>
                             
                             <select name="settings[weight_unit]" id="weight-unit">
                             	<option value="">Please select an option &raquo;</option>
                             		<?php
                             			if ($base['units'] == "imperial") $units = array("oz" => __("ounces (oz)","Ecart"),"lb" => __("pounds (lbs)","Ecart"));
                             			else $units = array("g"=>__("gram (g)","Ecart"),"kg"=>__("kilogram (kg)","Ecart"));
                             			echo menuoptions($units,$this->Settings->get('weight_unit'),true);
                             		?>
                             </select><br />
                                                                                       <br>
                             <br>
                            </div>
                           </div>
       
                           <div class="theexplanation">
                             <div class="context"></div>
       
                             <p><?php _e('Please select your weight unit, this will be used for shipping products','Ecart'); ?></p>
                           </div>
       
                           <div class="clear"></div>
                         </div>           			
                  			
          					
          					<div class="optionrow fix">
                              <div class="optiontitle fix">
                                <strong><label for="weight-unit"><?php _e('Product Dimension Unit','Ecart'); ?></label></strong><br>
                              </div>
          
                              <div class="theinputs">
                                <div class="optioninputs">
                              <br/>
               						
               						<select name="settings[dimension_unit]" id="dimension-unit">
               							<option value="">Please select an option &raquo;</option>
               								<?php
               									if ($base['units'] == "imperial") $units = array("in" => __("inches (in)","Ecart"),"ft" => __("feet (ft)","Ecart"));
               									else $units = array("cm"=>__("centimeters (cm)","Ecart"),"m"=>__("meters (m)","Ecart"));
               									echo menuoptions($units,$this->Settings->get('dimension_unit'),true);
               								?>
               						</select><br />
                                                                                          <br>
                                <br>
                               </div>
                              </div>
          
                              <div class="theexplanation">
                                <div class="context"></div>
          
                                <p><?php _e('Please select your dimension unit, this will be used for shipping products','Ecart'); ?></p>
                              </div>
          
                              <div class="clear"></div>
                            </div>
                            
                            
                            
           					<div class="optionrow fix">
                               <div class="optiontitle fix">
                                 <strong><label for="order_handling_fee"><?php _e('Order Handling Fee','Ecart'); ?></label></strong><br>
                               </div>
           
                               <div class="theinputs">
                                 <div class="optioninputs">
                               <br/>
                						
                						<input type="text" name="settings[order_shipfee]" value="<?php echo money($this->Settings->get('order_shipfee')); ?>" id="order_handling_fee" size="7" class="right selectall" /><br />
                                                                                           <br>
                                 <br>
                                </div>
                               </div>
           
                               <div class="theexplanation">
                                 <div class="context"></div>
           
                                 <p><?php _e('You can add an handling fee for your products, this fee will be applied once to each order with shipped products ','Ecart'); ?></p>
                               </div>
           
                               <div class="clear"></div>
                             </div>                 
    					
    					
    					<div class="optionrow fix">
                        <div class="optiontitle fix">
                          <strong><label for="free_shipping_text"><?php _e('Free Shipping Text Product Notice','Ecart'); ?></label></strong><br>
                        </div>
    
                        <div class="theinputs">
                          <div class="optioninputs">
                        <br/>
         						<label class="context" for="free_shipping_text"><?php _e('Free Shipping Text Product Notice','Ecart'); ?></label><br/>
         						<input type="text" name="settings[free_shipping_text]" value="<?php echo esc_attr($this->Settings->get('free_shipping_text')); ?>" id="free_shipping_text" size="50" /><br />
                                                                   
                          <br>
                         </div>
                        </div>
    
                        <div class="theexplanation">
                          <div class="context"></div>
    
                          <p><?php _e('Text used to highlight no shipping costs (examples: Free shipping available!)','Ecart'); ?></p>
                        </div>
    
                        <div class="clear"></div>
                      </div> 
                      
                      
     					<div class="optionrow fix">
                         <div class="optiontitle fix">
                           <strong><label for="outofstock-text"><?php _e('Out-of-stock Product Notice','Ecart'); ?></label></strong><br>
                         </div>
     
                         <div class="theinputs">
                           <div class="optioninputs">
                         <br/>
          						<label class="context" for="outofstock-text"><?php _e('Out-of-stock Product Notice','Ecart'); ?></label><br/>
          						<input type="text" name="settings[outofstock_text]" value="<?php echo esc_attr($this->Settings->get('outofstock_text')); ?>" id="outofstock-text" size="50" /><br />
                                                                    
                           <br>
                          </div>
                         </div>
     
                         <div class="theexplanation">
                           <div class="context"></div>
     
                           <p><?php _e('Text used to notify the customer the product is out-of-stock','Ecart'); ?></p>
                         </div>
     
                         <div class="clear"></div>
                       </div>  
                       
                       
        				<div class="optionrow fix">
                            <div class="optiontitle fix">
                              <strong><label for="lowstock-level"><?php _e('Low Inventory Warning','Ecart'); ?></label></strong><br>
                            </div>
        
                            <div class="theinputs">
                              <div class="optioninputs">
                            <br/>
             						<label class="context" for="lowstock-level"><?php _e('Low Inventory Warning','Ecart'); ?></label><br/>
             						<input type="text" name="settings[lowstock_level]" value="<?php echo esc_attr($lowstock); ?>" id="lowstock-level" size="10" /><br />
                                                                       
                              <br>
                             </div>
                            </div>
        
                            <div class="theexplanation">
                              <div class="context"></div>
        
                              <p><?php _e('Text used to notify the customer the product is out-of-stock','Ecart'); ?></p>
                            </div>
        
                            <div class="clear"></div>
                          </div>                                 
                      
                      
                                              
                  			
                  			<!-- these settings are hidden - still under development  please do not enable -->
                  			
							<div class="notavailable" style="display:none !important;">
                  			
                  				
                  					<label for="regional_rates"><?php _e('Domestic Regions','Ecart'); ?></label>
                  					<input type="hidden" name="settings[shipping_regions]" value="off" /><input type="checkbox" name="settings[shipping_regions]" value="on" id="regional_rates"<?php echo ($this->Settings->get('shipping_regions') == "on")?' checked="checked"':''; ?> /><label for="regional_rates"> <?php _e('Enabled','Ecart'); ?></label><br />
                  				    <?php _e('Used for domestic regional shipping rates (only applies to operations based in the U.S. &amp; Canada)','Ecart'); ?><br />
                  					<strong><?php _e('Note:','Ecart'); ?></strong> <?php _e('You must click the "Save Changes" button for changes to take effect.','Ecart'); ?>
                  				
                  			
                  			</div>
                  			
                  			<div style="margin-left:15px;">
                  			<h3><?php _e('Shipping Methods &amp; Rates','Ecart'); ?></h3>
                  			<p><small><?php _e('Shipping rates based on the order amount are calculated once against the order subtotal (which does not include tax). Shipping rates based on item quantity are calculated against the total quantity of each different item ordered.','Ecart'); ?></small></p>
                  			<?php $base = $this->Settings->get('base_operations'); if (!empty($base['country'])): ?>
                  			<table id="shipping-rates" class="form-table"><tr><td></td></tr></table>
                  			<br/>
                  			<div class="tablenav"><div class="alignright actions"><button type="button" name="add-shippingrate" id="add-shippingrate" class="button-secondary" tabindex="9999"><?php _e('Add New Shipping Method','Ecart'); ?></button></div></div>
                  			<?php else: ?>
                  				<p class="tablenav"><small><strong>Note:</strong> <?php _e('You must select a Base of Operations location under','Ecart'); ?> <a href="?page=<?php echo $this->Admin->settings['settings'][0] ?>"><?php _e('Main settings','Ecart'); ?></a> <?php _e('before you can configure shipping methods.','Ecart'); ?></small></p>
                  			<?php endif; ?>
                  			</div>
                  
                      	</form>
                  </div>
                  
                  <script type="text/javascript">
                  /* <![CDATA[ */
                  var weight_units = '<?php echo $Ecart->Settings->get("weight_unit"); ?>',
                  	uniqueMethods = new Array();
                  
                  jQuery(document).ready(function() {
                  	var $ = jqnc();
                  
                  	$('#order_handling_fee').change(function() { this.value = asMoney(this.value); });
                  
                  	$('#weight-unit').change(function () {
                  		weight_units = $(this).val();
                  		$('#shipping-rates table.rate td.units span.weightunit').html(weight_units+' = ');
                  	});
                  
                  	function addShippingRate (r) {
                  		if (!r) r = false;
                  		var i = shippingRates.length;
                  		var row = $('<tr></tr>').appendTo($('#shipping-rates'));
                  		var heading = $('<th scope="row" valign="top"><label for="name['+i+']" id="label-'+i+'"><?php echo addslashes(__('Shipping Method Name','Ecart')); ?></label><input type="hidden" name="priormethod-'+i+'" id="priormethod-'+i+'" /></th>').appendTo(row);
                  		$('<br />').appendTo(heading);
                  		var name = $('<input type="text" name="settings[shipping_rates]['+i+'][name]" value="" id="name-'+i+'" size="50" tabindex="'+(i+1)+'00" class="selectall" />').appendTo(heading);
                  		$('<br />').appendTo(heading);
                  
                  
                  		var deliveryTimesMenu = $('<br/><select name="settings[shipping_rates]['+i+'][delivery]" id="delivery-'+i+'" style="width:280px;" class="methods" tabindex="'+(i+1)+'01"></select>').appendTo(heading);
                  		var lastGroup = false;
                  		$.each(deliveryTimes,function(range,label){
                  			if (range.indexOf("group")==0) {
                  				lastGroup = $('<optgroup label="'+label+'"></optgroup>').appendTo(deliveryTimesMenu);
                  			} else {
                  				if (lastGroup) $('<option value="'+range+'">'+label+'</option>').appendTo(lastGroup);
                  				else $('<option value="'+range+'">'+label+'</option>').appendTo(deliveryTimesMenu);
                  			}
                  		});
                  
                  		$('<br />').appendTo(heading);
                  
                  		var methodMenu = $('<select name="settings[shipping_rates]['+i+'][method]" id="method-'+i+'" style="width:200px;" class="methods" tabindex="'+(i+1)+'02"></select>').appendTo(heading);
                  		var lastGroup = false;
                  		$.each(methods,function(m,methodtype){
                  			if (m.indexOf("group")==0) {
                  				lastGroup = $('<optgroup label="'+methodtype+'"></optgroup>').appendTo(methodMenu);
                  			} else {
                  				var methodOption = $('<option value="'+m+'">'+methodtype+'</option>');
                  				for (var disabled in uniqueMethods)
                  					if (uniqueMethods[disabled] == m) methodOption.attr('disabled',true);
                  				if (lastGroup) methodOption.appendTo(lastGroup);
                  				else methodOption.appendTo(methodMenu);
                  			}
                  		});
                  		var rateTableCell = $('<td/>').appendTo(row);
                  		var deleteRateButton = $('<button type="button" name="deleteRate" class="delete deleteRate"></button>').appendTo(rateTableCell).hide();
                  		$('<img src="<?php echo ECART_PLUGINURI; ?>/core/ui/icons/delete.png" width="16" height="16"  />').appendTo(deleteRateButton);
                  
                  
                  		var rowBG = row.css("background-color");
                  		var deletingBG = "";
                  		row.hover(function () {
                  				deleteRateButton.show();
                  			}, function () {
                  				deleteRateButton.hide();
                  		});
                  
                  		deleteRateButton.hover (function () {
                  				row.animate({backgroundColor:deletingBG},250);
                  			},function() {
                  				row.animate({backgroundColor:rowBG},250);
                  		}).click(function () {
                  			if (confirm("<?php echo addslashes(__('Are you sure you want to delete this shipping method?','Ecart')); ?>")) {
                  				row.remove();
                  				shippingRates.splice(i,1);
                  			}
                  		});
                  
                  		var rateTable = $('<table class="rate"/>').appendTo(rateTableCell);
                  
                  		$(methodMenu).change(function() {
                  
                  			var id = $(this).attr('id').split("-"),
                  				methodid = id[1],
                  				priormethod = $('#priormethod-'+methodid).val(),
                  				uniqueMethodIndex = $.inArray(priormethod,uniqueMethods);
                  
                  			if (priormethod != "" && uniqueMethodIndex != -1)
                  				uniqueMethods.splice(uniqueMethodIndex,1);
                  
                  			$('#label-'+methodid).show();
                  			$('#name-'+methodid).show();
                  			$('#delivery-'+methodid).show();
                  			$('#shipping-rates select.methods').not($(methodMenu)).find('option').attr('disabled',false);
                  			$(uniqueMethods).each(function (i,method) {
                  				$('#shipping-rates select.methods').not($(methodMenu)).find('option[value='+method+']:not(:selected)').attr('disabled',true);
                  			});
                  
                  			methodHandlers.call($(this).val(),i,rateTable,r);
                  			methodName = $(this).val().split("::");
                  			$(this).parent().attr('class',methodName[0].toLowerCase());
                  			$('#priormethod-'+methodid).val($('#method-'+methodid).val());
                  		});
                  
                  		if (r) {
                  			name.val(r.name);
                  			methodMenu.val(r.method).change();
                  			deliveryTimesMenu.val(r.delivery);
                  		} else {
                  			methodMenu.change();
                  		}
                  
                  		quickSelects();
                  		shippingRates.push(row);
                  
                  	}
                  
                  	function uniqueMethod (methodid,option) {
                  		$('#label-'+methodid).hide();
                  		$('#name-'+methodid).hide();
                  		$('#delivery-'+methodid).hide();
                  
                  		var methodMenu = $('#method-'+methodid),
                  			methodOptions = methodMenu.children(),
                  			optionid = methodMenu.get(0).selectedIndex;
                  
                  		if ($(methodOptions.get(optionid)).attr('disabled')) {
                  			methodMenu.val(methodMenu.find('option:not(:disabled):first').val()).change();
                  			return false;
                  		}
                  		$('#shipping-rates select.methods').not($(methodMenu)).find('option[value='+option+']').attr('disabled',true);
                  
                  		uniqueMethods.push(option);
                  		return true;
                  	}
                  
                  	var methodHandlers = new CallbackRegistry();
                  
                  	<?php do_action('ecart_settings_shipping_ui'); ?>
                  
                  	if ($('#shipping-rates')) {
                  		var shippingRates = new Array(),
                  			rates = <?php echo json_encode($rates); ?>,
                  			methods = <?php echo json_encode($methods); ?>,
                  			deliveryTimes = {"prompt":"<?php _e('Shipping Method Delivery time','Ecart'); ?>&hellip;","group1":"<?php _e('Business Days','Ecart'); ?>","1d-1d":"1 <?php _e('business day','Ecart'); ?>","1d-2d":"1-2 <?php _e('business days','Ecart'); ?>","1d-3d":"1-3 <?php _e('business days','Ecart'); ?>","1d-5d":"1-5 <?php _e('business days','Ecart'); ?>","2d-3d":"2-3 <?php _e('business days','Ecart'); ?>","2d-5d":"2-5 <?php _e('business days','Ecart'); ?>","2d-7d":"2-7 <?php _e('business days','Ecart'); ?>","3d-5d":"3-5 <?php _e('business days','Ecart'); ?>","group2":"<?php _e('Weeks','Ecart'); ?>","1w-2w":"1-2 <?php _e('weeks','Ecart'); ?>","2w-3w":"2-3 <?php _e('weeks','Ecart'); ?>"},
                  			domesticAreas = <?php echo json_encode($areas); ?>,
                  			region = '<?php echo $region; ?>';
                  
                  		$('#add-shippingrate').click(function() {
                  			addShippingRate();
                  		});
                  
                  		$('#shipping-rates').empty();
                  		if (!rates) $('#add-shippingrate').click();
                  		else {
                  			$.each(rates,function(i,rate) {
                  				addShippingRate(rate);
                  			});
                  		}
                  	}
                  
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