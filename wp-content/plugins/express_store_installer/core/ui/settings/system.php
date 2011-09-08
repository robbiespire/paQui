  <div class="wrap ecart">
 <?php $theme_data = get_theme_data(TEMPLATEPATH . '/style.css'); ?>
  
  <table id="optionstable" class="form-table">
    <tbody>
      <tr>
        <td valign="top" width="100%">
          <form name="settings" id="system" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="post" class="main_settings_form">
          	<?php wp_nonce_field('ecart-settings-system'); ?>
          
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
                    Shopping Cart Storage Settings
                  </div>
					
                  <div class="ecart">
                  		
                  		<?php if (!empty($updated)): ?><div class="setup"><p><?php echo $updated; ?></p></div><?php endif; ?>
                  	
                  		
       					<div class="optionrow fix">
                           <div class="optiontitle fix">
                             <strong><label for="image-storage"><?php _e('Products Image Storage Path','Ecart'); ?></label></strong><br>
                           </div>
       
                           <div class="theinputs">
                             <div class="optioninputs">
                            
                             <div class="notavailable" style="display:none !important;">
                             <select name="settings[image_storage]" id="image-storage">
                             	<?php echo menuoptions($storage,$this->Settings->get('image_storage'),true); ?>
                             	</select>
                             </div>
                             	<div id="image-storage-engine" class="storage-settings"></div><br />
                                                                                       <br>
                             <br>
                            </div>
                           </div>
       
                           <div class="theexplanation">
                             <div class="context"></div>
       
                             <p><?php _e('Type the product pictures storage path, follow the documentation for more informations','Ecart'); ?></p>
                           </div>
       
                           <div class="clear"></div>
                         </div>
                  			
       					 <div class="optionrow fix">
                           <div class="optiontitle fix">
                             <strong><label for="product-storage"><?php _e('Digital Product File Storage Path','Ecart'); ?></label></strong><br>
                           </div>
       
                           <div class="theinputs">
                             <div class="optioninputs">
                            
                             <div class="notavailable" style="display:none !important;">
                             <select name="settings[product_storage]" id="product-storage">
                             	<?php echo menuoptions($storage,$this->Settings->get('product_storage'),true); ?>
                             	</select>
                             </div>
                             	<div id="product-storage-engine" class="storage-settings"></div>
                             	<br />
          						<br>
                             <br>
                            </div>
                           </div>
       
                           <div class="theexplanation">
                             <div class="context"></div>
       
                             <p><?php _e('Type the digital product storage path, follow the documentation for more informations','Ecart'); ?></p>
                           </div>
       
                           <div class="clear"></div>
                         </div>           			
                  			
          					
          				                  			
                  			<!-- these settings are hidden - still under development  please do not enable -->
                  			
							<div class="notavailable" style="display:none !important;">
                  				
                  				<label for="rebuild-index"><?php _e('Search Index','Ecart'); ?></label>
                  				<button type="button" id="rebuild-index" name="rebuild" class="button-secondary"><?php _e('Rebuild Product Search Index','Ecart'); ?></button><br />
                  				<?php _e('Update search indexes for all the products in the catalog.','Ecart'); ?>
                  				
                  				
                  						
                  				<label for="image-cache"><?php _e('Image Cache','Ecart'); ?></label>
                  				<button type="submit" id="image-cache" name="rebuild" value="true" class="button-secondary"><?php _e('Delete Cached Images','Ecart'); ?></button><br />
                  				<?php _e('Removes all cached images so that they will be recreated.','Ecart'); ?>
                  							
                  				
                  			 
                  			</div>
                  			
                  			<!-- end hidden under construction settings -->
                  			
           					<div class="optionrow fix">
                               <div class="optiontitle fix">
                                 <strong><label for="uploader-toggle"><?php _e('Enable Flash Uploader','Ecart'); ?></label></strong><br>
                               </div>
           
                               <div class="theinputs">
                                 <div class="optioninputs">
                                 <br>
                                 
                                <input type="hidden" name="settings[uploader_pref]" value="browser" /><input type="checkbox" name="settings[uploader_pref]" value="flash" id="uploader-toggle"<?php if ($this->Settings->get('uploader_pref') == "flash") echo ' checked="checked"'?> /><label for="uploader-toggle"> <?php _e('Enable Flash picture uploader','Ecart'); ?></label>
                                 	<br />
                                                                                           <br>
                                 <br>
                                </div>
                               </div>
           
                               <div class="theexplanation">
                                 <div class="context"></div>
           
                                 <p><?php _e('Enable to use Flash uploader. Disable this setting if you are having problems uploading.','Ecart'); ?></p>
                               </div>
           
                               <div class="clear"></div>
                             </div>           			
                      			     
                      	
                      	<!-- hidden settings under construction -->
                      	
                      	<div class="notavailable" style="display:none !important;">
                      	
                      	<label for="script-server"><?php _e('Script Loading','Ecart'); ?></label>
                      	<input type="hidden" name="settings[script_server]" value="script" /><input type="checkbox" name="settings[script_server]" value="plugin" id="script-server"<?php if ($this->Settings->get('script_server') == "plugin") echo ' checked="checked"'?> /><label for="script-server"> <?php _e('Load behavioral scripts through WordPress','Ecart'); ?></label><br />
                      	<?php _e('Enable this setting when experiencing problems loading scripts with the Ecart Script Server','Ecart'); ?>
                      	<div><input type="hidden" name="settings[script_loading]" value="catalog" /><input type="checkbox" name="settings[script_loading]" value="global" id="script-loading"<?php if ($this->Settings->get('script_loading') == "global") echo ' checked="checked"'?> /><label for="script-loading"> <?php _e('Enable Ecart behavioral scripts site-wide','Ecart'); ?></label><br />
                      	<?php _e('Enable this to make Ecart behaviors available across all of your WordPress posts and pages.','Ecart'); ?></div>  	
                      	
                      	<!-- admin logger under construction -->
                      	
                      	<label for="error-notifications"><?php _e('Error Notifications','Ecart'); ?></label>
                      		<ul id="error_notify">
                      			<?php foreach ($notification_errors as $id => $level): ?>
                      				<li><input type="checkbox" name="settings[error_notifications][]" id="error-notification-<?php echo $id; ?>" value="<?php echo $id; ?>"<?php if (in_array($id,$notifications)) echo ' checked="checked"'; ?>/><label for="error-notification-<?php echo $id; ?>"> <?php echo $level; ?></label></li>
                      			<?php endforeach; ?>
                      			</ul>
                      			<label for="error-notifications"><?php _e("Send email notifications of the selected errors to the merchant's email address.","Ecart"); ?></label>
                      	   
                      
                      		<label for="error-logging"><?php _e('Error Logging','Ecart'); ?></label>
                      		<select name="settings[error_logging]" id="error-logging">
                      			<?php echo menuoptions($errorlog_levels,$this->Settings->get('error_logging'),true); ?>
                      			</select><br />
                      			<label for="error-notifications"><?php _e("Limit logging errors up to the level of the selected error type.","Ecart"); ?></label>
                      	    
                    
                      	<?php if (count($recentlog) > 0): ?>
                      	
                      		<label for="error-logging"><?php _e('Error Log','Ecart'); ?></label>
                      		<div id="errorlog"><ol><?php foreach ($recentlog as $line): ?>
                      			<li><?php echo esc_html($line); ?></li>
                      		<?php endforeach; ?></ol></div>
                      		<p class="alignright"><button name="resetlog" id="resetlog" value="resetlog" class="button"><small><?php _e("Reset Log","Ecart"); ?></small></button></p>
                      		
                      	
                      	<?php endif; ?>
                      	
                      	<!-- end logger -->
                      	
                      	</div>		
                  
                      	</form>
                  </div>
                  
                  <script type="text/javascript">
                  /* <![CDATA[ */
                  jQuery(document).ready(function() {
                  	var $ = jqnc();
                  
                  	var handlers = new CallbackRegistry();
                  	handlers.options = {};
                  	handlers.enabled = [];
                  	handlers.register = function (name,object) {
                  		this.callbacks[name] = function () {object['storage']();}
                  		this.options[name] = object;
                  	}
                  
                  	handlers.call = function(id,setting,name,arg1,arg2,arg3) {
                  		var module = this.options[name];
                  		module.element = $(id);
                  		module.setting = setting;
                  		this.callbacks[name](arg1,arg2,arg3);
                  		module.behaviors();
                  	}
                  
                  	<?php do_action('ecart_storage_module_settings'); ?>
                  
                  	$('#image-storage').change(function () {
                  		var module = $(this).val();
                  		var selected = $('#image-storage :selected');
                  		$('#image-storage-engine').empty();
                  		handlers.call('#image-storage-engine','image',module);
                  	}).change();
                  
                  	$('#product-storage').change(function () {
                  		var module = $(this).val();
                  		var selected = $('#product-storage :selected');
                  		$('#product-storage-engine').empty();
                  		handlers.call('#product-storage-engine','download',module);
                  	}).change();
                  
                  	$('#errorlog').scrollTop($('#errorlog').attr('scrollHeight'));
                  
                  
                  	var progressbar = false;
                  	var search_url = '<?php echo wp_nonce_url(admin_url('admin-ajax.php'),'wp_ajax_ecart_rebuild_search_index'); ?>';
                  	var searchprog_url = '<?php echo wp_nonce_url(admin_url('admin-ajax.php'),'wp_ajax_ecart_rebuild_search_index_progress'); ?>';
                  	function progress () {
                  		$.ajax({url:searchprog_url+'&action=ecart_rebuild_search_index_progress',
                  			type:"GET",
                  			timeout:500,
                  			dataType:'text',
                  			success:function (results) {
                  				var p = results.split(':'),
                  					width = Math.ceil((p[0]/p[1])*76);
                  				if (p[0] < p[1]) setTimeout(progress,1000);
                  				progressbar.animate({'width':width+'px'},500);
                  			}
                  		});
                  
                  	}
                  
                  	$('#rebuild-index').click(function () {
                  		$.fn.colorbox({'title':'<?php _e('Product Indexing','Ecart'); ?>',
                  			'innerWidth':'250',
                  			'innerHeight':'50',
                  			'html':
                  			'<div id="progress"><div class="bar"><\/div><div class="gloss"><\/div><\/div><iframe id="process" width="0" height="0" src="'+search_url+'&action=ecart_rebuild_search_index"><\/iframe>',
                  			'onComplete':function () {
                  				progressbar = $('#progress div.bar');
                  				progress();
                  				$('#process').load(function () {
                  					progressbar.animate({'width':'100%'},500,'swing',function () {
                  						setTimeout($.fn.colorbox.close,1000);
                  					});
                  				});
                  			}
                  		});
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