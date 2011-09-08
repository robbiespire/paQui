<?php 
/*-----------------------------------------------------------------------------------*/
/* Options Framework Backend Interface Builder
/*-----------------------------------------------------------------------------------*/

if(__FILE__ == $_SERVER['SCRIPT_FILENAME'])
{
    header('HTTP/1.0 404 Not Found');
      exit;
}

class EPANEL_WPOptionsUI {

// Let's build the layout
	function __construct() {
		$this->option_array = get_option_array();
		
		$this->build_header();
		$this->build_body();
		$this->build_footer();	
		
	}
		
// Interface Header 

function build_header(){?>
			<div class='wrap'>
				<table id="optionstable"><tbody><tr><td valign="top" width="100%">
					
				  <form id="epanel-settings-form" method="post" action="options.php" class="main_settings_form">
					
						 <!-- hidden fields -->
							<?php wp_nonce_field('update-options') ?>
							<?php settings_fields(EPANEL_SETTINGS); // important! ?>
							
							<input type="hidden" name="<?php echo EPANEL_SETTINGS; ?>[theme_version]>" value="<?php echo esc_attr(epanel_option('theme_version')); ?>" />
							<input type="hidden" name="<?php echo EPANEL_SETTINGS; ?>[selectedtab]" id="selectedtab" value="<?php print_epanel_option('selectedtab', 0); ?>" />
							<input type="hidden" name="<?php echo EPANEL_SETTINGS; ?>[just_saved]" id="just_saved" value="1" />
							<input type="hidden" id="input-full-submit" name="input-full-submit" value="0" />
							
							<?php $this->_get_confirmations_and_system_checking(); ?>
							

					<?php
					
						if(isset($_COOKIE['EPANEL_WPTabCookie']))
							$selected_tab = (int) $_COOKIE['EPANEL_WPTabCookie'];
						elseif(epanel_option('selectedtab'))
							$selected_tab = epanel_option('selectedtab');
						else
							$selected_tab = 0;
				
					//echo $_COOKIE['EPANEL_WPTabCookie'];
					?>
				
						<script type="text/javascript">
								jQuery.noConflict();
								jQuery(document).ready(function($) {						
									var $myTabs = $("#tabs").tabs({ fx: { opacity: "toggle", duration: "fast" }, selected: <?php echo $selected_tab; ?>});
									
									$('#tabs').bind('tabsshow', function(event, ui) {
										
										var selectedTab = $('#tabs').tabs('option', 'selected');
										
										$("#selectedtab").val(selectedTab);
										
										$.cookie('EPANEL_WPTabCookie', selectedTab);
									});

								});
						</script>
								
								
									
									<div id="header">
									
										<h2>E-Panel Theme Configuration</h2>
									
									</div>

									<?php $theme_data = get_theme_data(TEMPLATEPATH . '/style.css'); ?>
									
									<?php if(isset($_GET['updated']) || isset($_GET['pageaction']) || isset($_GET['reset'])):?>
													
											<div id="message" class="confirmation slideup_message fade <?php if(isset($_GET['reset']) && !isset($_GET['updated'])) echo ' reset'; elseif(isset($_GET['pageaction']) && $_GET['pageaction']=='activated' && !isset($_GET['updated'])) echo ' activated'; elseif($_GET['pageaction']=='import' && !isset($_GET['updated'])) echo 'settings-import';?>" style="">
												<div class="confirmation-pad">
													<?php if(isset($_GET['updated'])) echo THEMENAME.' Settings Saved. &nbsp;<a class="sh_preview" href="'.home_url().'/" target="_blank" target-position="front">View Your Site &rarr;</a>' ;
														
														elseif(isset($_GET['pageaction']) && $_GET['pageaction']=='activated' && !isset($_GET['updated'])) echo "Congratulations! ".THEMENAME ." Has Been Successfully Activated. Remember To Check The Documentation.";
														
														elseif(isset($_GET['pageaction']) && $_GET['pageaction']=='import' && isset($_GET['imported']) && !isset($_GET['updated'])) echo "Congratulations! New settings have been successfully imported.";
														
														elseif(isset($_GET['pageaction']) && $_GET['pageaction']=='import' && isset($_GET['error']) && !isset($_GET['updated'])) echo "There was an error with import. Please make sure you are using the correct file.";
														
														elseif(isset($_GET['reset']) && isset($_GET['opt_id']) && $_GET['opt_id'] == 'resettemplates') echo "Template Configuration Restored To Default.";
														
														elseif(isset($_GET['reset']) && isset($_GET['opt_id']) && $_GET['opt_id'] == 'resetlayout') echo "Layout Dimensions Restored To Default.";
														
														elseif(isset($_GET['reset'])) echo "Settings Successfully Restored To Default Values.";
															?>
															</div>
														</div>
														
												<?php endif;?>
									
									
									
									<?php if(ini_get('safe_mode')):?>
										<div id="message" class="confirmation fade plerror">
											<div class="confirmation-pad">
												
													Your server is currently running PHP in 'Safe Mode' a deprecated security feature used by some hosts.<br/>
													This causes issues with <?php echo $theme_data['Title'];?> Theme, please contact your host to disable the Safe Mode.
											</div>
									
										</div>
									
									<?php endif;?>
									
									<?php if(floatval(phpversion()) < 5.2):?>
									<div id="message" class="confirmation plerror fade">	
										<div class="confirmation-pad">
											<div class="confirmation-head">You are using PHP version <?php echo phpversion(); ?></div>
											<div class="confirmation-subtext">
												Version 5.2 or higher is required for this theme to work correctly. Please contact your hosting provider and request an update. 
											</div>
										</div>
									</div>
									<?php endif;?>
									
									<?php if(ie_version() && ie_version() < 8):?>
									<div id="message" class="confirmation plerror fade">	
										<div class="confirmation-pad">
											<div class="confirmation-head">You are using Internet Explorer version: <?php echo ie_version();?></div>
											<div class="confirmation-subtext">
												Advanced options don't support Internet Explorer version 7 or lower. Please switch to a standards based browser that will allow you to easily configure your site (e.g. Firefox, Chrome, Safari, even IE8 or better would work).
											</div>
										</div>
									</div>
									
									<?php endif; ?>
												
									<div id="optionssubheader">
										<div class="hl"></div>
										<div class="padding fix">
											<div class="subheader_links">
												<a class="sh_preview" href="<?php echo home_url(); ?>/" target="_blank" target-position="front">View Your Website</a>
					
												<a class="sh_support" href="<?php support_url();?>" target="_blank" ><?php _e('Support', 'epanel');?></a>
												<a class="sh_version" href="<?php theme_url();?>" target="_blank" ><?php _e('Theme Version : ', 'epanel'); echo $theme_data['Version'];?></a>
											</div>

											<div class="subheader_right">

												<input class="button-primary" type="submit" name="submit" value="<?php _e('Save Theme Settings', 'epanel');?>" />
											</div>
										</div>
									</div>



									<div class="clear"></div>
								</div>
		<?php }
		
		function _get_confirmations_and_system_checking(){?>
			
			<div id="outer" class="ajax-saved" style="">
				<div id="wrapper" class="ajax-saved-pad">
					<div class="ajax-saved-icon"></div>
				</div>
			</div>
			
			
<?php 		
			
			do_action('epanel_config_checking');

		}
		
		/**
		 * Option Interface Body, including vertical tabbed nav
		 *
		 */
		function build_body(){
			global $pl_section_factory; 
?>
			<div id="tabs">	
				<ul id="tabsnav">
					
				
					<?php foreach($this->option_array as $menu => $oids):?>
						
						<li>
							<a class="<?php echo $menu;?>  tabnav-element" href="#<?php echo $menu;?>">
								<span><?php echo ucwords( str_replace('_',' ',$menu) );?></span>
							</a>
						</li>
					
					<?php endforeach;?>

					<li><span class="graphic bottom">&nbsp;</span></li>
					
					<div class="framework_loading"> 
						<a href="#" target="_blank" title="Javascript Issue Detector"><span class="framework_loading_gif" >&nbsp;</span></a>
						
					</div>
				</ul>
				<div id="thetabs" class="fix">
				<?php foreach($this->option_array as $menu => $oids):?>
						
							<div id="<?php echo $menu;?>" class="tabinfo">
							
								<?php if( stripos($menu, '_') !== 0 ): ?>
									<div class="tabtitle"><?php echo ucwords(str_replace('_',' ',$menu));?></div>
								<?php endif;?>
							
								<?php foreach($oids as $oid => $o){
									$this->option_engine($oid, $o);
								} ?>
								<div class="clear"></div>
							</div>
						
					<?php endforeach; ?>	
				</div> <!-- End the tabs -->
			</div> <!-- End tabs -->
<?php 	}
		
/**
 * Option generation engine
 *
 */
function option_engine($oid, $o){
	
	$defaults = array(
		'default' 				=> '',
		'default_free'		 	=> null,
		'inputlabel' 			=> '',
		'type' 					=> 'check',
		'title' 				=> '',				
		'shortexp' 				=> '',
		'exp'					=> '',
		'wp_option'				=> false,
		'version' 				=> null,
		'version_set_default' 	=> 'free',
		'imagepreview' 			=> 200, 
		'selectvalues' 			=> array(),
		'fields'				=> array(),
		'optionicon' 			=> '', 
		'vidlink' 				=> null, 
		'vidtitle'				=> '',
		'docslink' 				=> null,
		'layout' 				=> 'normal', 
		'count_number' 			=> 10, 
		'selectors'				=> '', 
		'inputsize'				=> 'regular',
		'callback'				=> '',
		'css_prop'				=> '',
	);

	$o = wp_parse_args( $o, $defaults );

	if($o['wp_option']) {
		$val = get_option($oid);
	} else {
		$val = epanel_option($oid);
	}

if( !isset( $o['version'] ) || ( isset($o['version']) && $o['version'] == 'free') || (isset($o['version']) && $o['version'] == 'pro' && VPRO ) ): 
?>
<div class="optionrow fix <?php if( isset( $o['layout'] ) && $o['layout']=='full' ) echo 'wideinputs'; if( $o['type'] == 'options_info' ) echo ' options_info_row';?>">
		<?php if( $o['title'] ): ?>
		<div class="optiontitle fix">
			<?php if( $o['optionicon'] ) echo '<img src="'.$o['optionicon'].'" class="optionicon" />'; ?>
			
			<?php if( isset($o['vidlink']) ):?>
				<a class="vidlink thickbox" title="<?php if($o['vidtitle']) echo $o['vidtitle']; ?>" href="<?php echo $o['vidlink']; ?>?hd=1&KeepThis=true&TB_iframe=true&height=450&width=700">
					<img src="<?php echo CORE_IMAGES . '/link-video.jpg';?>" class="docslink-video" alt="Video Tutorial" />
				</a>
			<?php endif;?>
			
			<?php if( isset($o['docslink']) ):?>
				<a class="vidlink" title="<?php if($o['vidtitle']) echo $o['vidtitle']; ?>" href="<?php echo $o['docslink']; ?>" target="_blank">
					<img src="<?php echo CORE_IMAGES . '/link-docs.jpg';?>" class="docslink-video" alt="Video Tutorial" />
				</a>
			<?php endif;?>
			
			<strong><?php echo $o['title'];?></strong><br/>
			
			
		</div>
		<?php endif;?>
		<div class="theinputs ">
			<div class="optioninputs">
				<?php $this->option_breaker($oid, $o, $val); ?>
			</div>
		</div>

		<?php if($o['exp'] && $o['type'] != 'text_content' && $o['type'] != 'options_info'):?>
		<div class="theexplanation">
			<div class="context"></div>
			<p><?php echo $o['exp'];?></p>
		</div>
		<?php endif;?>
<div class="clear"></div>
</div>
<?php endif; 
}
		
function option_breaker($oid, $o, $val = ''){
	
		switch ( $o['type'] ){

			case 'select' :
				$this->_get_select_option($oid, $o);
				break;
			case 'select_same' :
				$this->_get_select_option($oid, $o);
				break;
			case 'radio' :
				$this->_get_radio_option($oid, $o);
				break;
			case 'colorpicker' :
				$this->_get_color_picker($oid, $o);
				break;
			case 'color_multi' :
				$this->_get_color_multi($oid, $o);
				break;
			case 'count_select' :
				$this->_get_count_select_option($oid, $o);
				break;
			case 'select_taxonomy' :
				$this->_get_taxonomy_select($oid, $o);
				break;
			case 'textarea' :
				$this->_get_textarea($oid, $o, $val);
				break;
			case 'textarea_big' :
				$this->_get_textarea($oid, $o, $val);
				break;
			case 'text' :
				$this->_get_text($oid, $o, $val);
				break;
			case 'text_small' :
				$this->_get_text_small($oid, $o, $val);
				break;
			case 'css_option' :
				$this->_get_text_small($oid, $o, $val);
				break;
			case 'text_multi' :
				$this->_get_text_multi($oid, $o, $val);
				break;
			case 'check' :
				$this->_get_check_option($oid, $o);
				break;
			case 'check_multi' :
				$this->_get_check_multi($oid, $o, $val);
				break;
	
			case 'typography' :
				$this->_get_typography_option($oid, $o, $val);
				break;
			case 'select_menu' :
				$this->_get_menu_select($oid, $o);
				break;
			case 'image_upload' :
				$this->_get_image_upload_option($oid, $o, $val);
				break;
			case 'background_image' :
				$this->_get_background_image_control($oid, $o); 
				break;
			case 'layout' :
				$this->_get_layout_builder($oid, $o);
				break;
			case 'layout_select' :
				$this->_get_layout_select($oid, $o); 
				break;
			case 'templates' :
				$this->_get_template_builder(); 
				break;
			case 'text_content' :
				$this->_get_text_content($oid, $o, $val);
				break;
			case 'options_info' :
				$this->_get_options_info($oid, $o, $val);
				break;
			case 'reset' :
				$this->_get_reset_option($oid, $o, $val);
				break;
		
			default :
				do_action( 'epanel_options_' . $o['type'] , $oid, $o);
				break;

		} 
	
}

function _get_menu_select($oid, $o){ ?>
	<p>
		<label for="<?php epanel_option_id($oid); ?>" class="context"><?php echo $o['inputlabel'];?></label><br/>
		<select id="<?php epanel_option_id($oid); ?>" name="<?php epanel_option_name($oid); ?>">
			<option value="">Please select an option &raquo;</option>
			<?php	$menus = wp_get_nav_menus( array('orderby' => 'name') );
					foreach ( $menus as $menu )
						printf( '<option value="%d" %s>%s</option>', $menu->term_id, selected($menu->term_id, epanel_option($oid)), esc_html( $menu->name ) );
			?>
		</select>
	</p>
	
<?php }

function _get_typography_option($oid, $o, $val){
	
	global $pl_foundry; 
	
	$fonts = $pl_foundry->foundry; 
	
	$preview_styles = '';
	
	$preview_styles = $pl_foundry->get_type_css(epanel_option($oid));
	
	// Choose Font
	?>
	<label for="<?php epanel_option_id($oid, 'font'); ?>" class="context">Select Font</label><br/>
	<select id="<?php epanel_option_id($oid, 'font'); ?>" name="<?php epanel_option_name($oid, 'font'); ?>" onChange="EPANEL_WPStyleFont(this, 'font-family')" class="fontselector" size="1" >
		<option value="">Please select an option &raquo;</option>
		<?php foreach($fonts as $fid => $f):
		
			$font_name = $f['name']; 
			
			if($f['web_safe']) $font_name .= ' *';
			if($f['google']) $font_name .= ' G';
			
		?>
			<option value='<?php echo $fid;?>' id='<?php echo $f['family'];?>' title="<?php echo $pl_foundry->gfont_key($fid);?>" <?php selected( $fid, epanel_sub_option($oid, 'font') ); ?>><?php echo $font_name;?></option>
		<?php endforeach;?>
	</select>
	<div class="font_preview_wrap">
		<label class="context">Preview</label>
		<div class="font_preview" >
			<div class="font_preview_pad" style='<?php echo $preview_styles;?>' >
				The quick brown fox jumps over the lazy dog.
			</div>
		</div>
	</div>

	<!-- ADVANCED FONT STYLING COMPONENT UNDER CONSTRUCTION -->

	<!--<span id="<?php epanel_option_id($oid, '_set_styling_button'); ?>" class="button" onClick="EPANEL_WPSimpleToggle('#<?php epanel_option_id($oid, '_set_styling'); ?>', '#<?php epanel_option_id($oid, '_set_advanced'); ?>')">Edit Font Styling</span>
	
	<span id="<?php epanel_option_id($oid, '_set_advanced_button'); ?>" class="button" onClick="EPANEL_WPSimpleToggle('#<?php epanel_option_id($oid, '_set_advanced'); ?>', '#<?php epanel_option_id($oid, '_set_styling'); ?>')">Advanced</span>
		
	<div id="<?php epanel_option_id($oid, '_set_styling'); ?>" class="font_styling type_inputs">
		<?php $this->get_type_styles($oid, $o); ?>
		<div class="clear"></div>
	</div>
	
	<div id="<?php epanel_option_id($oid, '_set_advanced'); ?>" class="advanced_type type_inputs">
		<?php $this->get_type_advanced($oid, $o); ?>
		<div class="clear"></div>
	</div>
	-->
	
<?php }

/*-----------------------------------------------------------------------------------*/
/* TYPOGRAPHY MANAGEMENT COMPONENT - STILL UNDER CONSTRUCTION NOT FOR PRODUCTION
/*-----------------------------------------------------------------------------------*/

function get_type_styles($oid, $o){
	
	// Set Letter Spacing (em)
	$this->_get_type_em_select($oid, array());
	 
	// Convert to caps, small-caps?
	$this->_get_type_select($oid, array('id' => 'transform', 'inputlabel' => 'Text Transform', 'prop' => 'text-transform',  'selectvalues' => array('none' => 'None', 'uppercase' => 'Uppercase', 'capitalize' => 'Capitalize', 'lowercase' => 'lowercase'), 'default' => 'none'));
	
	// Small Caps?
	$this->_get_type_select($oid, array('id' => 'variant', 'inputlabel' => 'Variant', 'prop' => 'font-variant',  'selectvalues' => array('normal' => 'Normal', 'small-caps' => 'Small-Caps'), 'default' => 'normal'));
	
	// Bold? 
	$this->_get_type_select($oid, array('id' => 'weight', 'inputlabel' => 'Weight', 'prop' => 'font-weight', 'selectvalues' => array('normal' => 'Normal', 'bold' => 'Bold'), 'default' => 'normal'));
	// 
	// Italic?
	$this->_get_type_select($oid, array('id' => 'style', 'inputlabel' => 'Style', 'prop' => 'font-style',  'selectvalues' => array('normal' => 'Normal', 'italic' => 'Italic'), 'default' => 'normal'));
}

function get_type_advanced($oid, $o){ ?>
	<div class="type_advanced">
		<label for="<?php epanel_option_id($oid, 'selectors'); ?>" class="context">Additional Selectors</label><br/>
		<textarea class=""  name="<?php epanel_option_name($oid, 'selectors'); ?>" id="<?php epanel_option_id($oid, 'selectors'); ?>" rows="3"><?php esc_attr_e( epanel_sub_option($oid, 'selectors'), 'epanel' ); ?></textarea>
	</div>
<?php }

function _get_type_em_select($oid, $o){ 
	
	$option_value = ( epanel_sub_option($oid, 'kern') ) ? epanel_sub_option($oid, 'kern') : '0.00em';
	?>
	<div class="type_select">
	<label for="<?php epanel_option_id($oid, 'kern'); ?>" class="context">Letter Spacing</label><br/>
	<select id="<?php epanel_option_id($oid, 'kern'); ?>" name="<?php epanel_option_name($oid, 'kern'); ?>" onChange="EPANEL_WPStyleFont(this, 'letter-spacing')">
		<option value="">Please select an option &raquo;</option>
		<?php 
			$count_start = -.3;
			for($i = $count_start; $i <= 1; $i += 0.05):
				$em = number_format(round($i, 2), 2).'em';
		?>
				<option value="<?php echo $em;?>" <?php selected($em, $option_value); ?>><?php echo $em;?></option>
		<?php endfor;?>
	</select>
	</div>
<?php }

function _get_type_select($oid, $o){ 
	
	$option_value = ( epanel_sub_option($oid, $o['id']) ) ? epanel_sub_option($oid, $o['id']) : $o['default'];
	?>
	<div class="type_select">
		<label for="<?php epanel_option_id($oid, $o['id']); ?>" class="context"><?php echo $o['inputlabel'];?></label><br/>
		<select id="<?php epanel_option_id($oid, $o['id']); ?>" name="<?php epanel_option_name($oid, $o['id']); ?>" onChange="EPANEL_WPStyleFont(this, '<?php echo $o['prop'];?>')">
			<option value="">Please select an option &raquo;</option>
			<?php foreach($o['selectvalues'] as $sid => $s):?>
					<option value="<?php echo $sid;?>" <?php selected($sid, $option_value); ?>><?php echo $s;?></option>
			<?php endforeach;?>
		</select>
	</div>
<?php }	
		
function _get_check_option($oid, $o){ ?>
	<p>
		<label for="<?php epanel_option_id($oid); ?>" class="context">
			<input class="admin_checkbox styled" type="checkbox" id="<?php epanel_option_id($oid); ?>" name="<?php epanel_option_name($oid); ?>" <?php checked((bool) epanel_option($oid)); ?> />
			<?php echo $o['inputlabel'];?>
		</label>
	</p>
<?php }	

function _get_check_multi($oid, $o, $val){ 
	foreach($o['selectvalues'] as $mid => $mo):?>
	<p>
		<label for="<?php echo $mid;?>" class="context"><input class="admin_checkbox" type="checkbox" id="<?php echo $mid;?>" name="<?php epanel_option_name($mid); ?>" <?php checked((bool) epanel_option($mid)); ?>  /><?php echo $mo['inputlabel'];?></label>
	</p>
<?php endforeach; 
}

function _get_text_multi($oid, $o, $val){ 
	foreach($o['selectvalues'] as $mid => $m):?>
	<p>
		<label for="<?php echo $mid;?>" class="context"><?php echo $m['inputlabel'];?></label><br/>
		<input class="<?php echo $o['inputsize'];?>-text" type="text" id="<?php echo $mid;?>" name="<?php epanel_option_name($mid); ?>" value="<?php echo esc_attr( epanel_option($mid) ); ?>"  />
	</p>
	<?php endforeach;
}

function _get_text_small($oid, $o, $val){ ?>
	<p>
		<label for="<?php echo $oid;?>" class="context"><?php echo $o['inputlabel'];?></label><br/>
		<input class="small-text"  type="text" name="<?php epanel_option_name($oid); ?>" id="<?php echo $oid;?>" value="<?php esc_attr_e( epanel_option($oid) ); ?>" />
	</p>
<?php }

function _get_text($oid, $o, $val){ ?>
	<p>
		<label for="<?php echo $oid;?>" class="context"><?php echo $o['inputlabel'];?></label>
		<input class="regular-text"  type="text" name="<?php epanel_option_name($oid); ?>" id="<?php echo $oid;?>" value="<?php esc_attr_e( epanel_option($oid) ); ?>" />
	</p>
<?php }

function _get_textarea($oid, $o, $val){ ?>
	<p>
		<label for="<?php echo $oid;?>" class="context"><?php echo $o['inputlabel'];?></label><br/>
		<textarea name="<?php epanel_option_name($oid); ?>" class="html-textarea <?php if($o['type']=='textarea_big') echo "longtext";?>" cols="70%" rows="5"><?php esc_attr_e( epanel_option($oid) ); ?></textarea>
	</p>
<?php }


function _get_text_content($oid, $o, $val){ ?>
	<div class="text_content fix"><?php echo $o['exp'];?></div>
<?php }

function _get_reset_option($oid, $o, $val){ 
	
	pl_action_confirm('Confirm'.$oid, 'Are you sure?');
	
?>
	<div class="insidebox context">
		<input class="button-secondary reset-options" type="submit" name="<?php epanel_option_name($oid); ?>" onClick="return Confirm<?php echo $oid;?>();" value="<?php echo $o['inputlabel'];?>" /> <?php echo $o['exp'];?>
	</div>
<?php 

}

function _get_options_info($oid, $o, $val){ ?>
	<span class="toggle_option_info" onClick="jQuery(this).next().slideToggle();">Additional <?php echo ucwords(str_replace('_', ' ', $oid));?> Info &darr;</span>
	<div class="text_content admin_option_info fix">
		<h3>More Information on <?php echo ucwords(str_replace('_', ' ', $oid));?></h3>
		<?php echo $o['exp'];?>
	</div>
<?php }
		

function _get_image_upload_option( $oid, $o, $optionvalue = ''){ 

	?><p>	
		<label class="context" for="<?php echo $oid;?>"><?php echo $o['inputlabel'];?></label><br/>
		<input class="regular-text uploaded_url" type="text" name="<?php epanel_option_name($oid); ?>" value="<?php echo esc_url(epanel_option($oid));?>" /><br/><br/>
		<span id="<?php echo $oid; ?>" class="image_upload_button button">Upload Image</span>
		<span title="<?php echo $oid;?>" id="reset_<?php echo $oid; ?>" class="image_reset_button button">Remove</span>
		<input type="hidden" class="ajax_action_url" name="wp_ajax_action_url" value="<?php echo admin_url("admin-ajax.php"); ?>" />
		<input type="hidden" class="image_preview_size" name="img_size_<?php echo $oid;?>" value="<?php echo $o['imagepreview'];?>"/>
	</p>
	<?php if(epanel_option($oid)):?>
		<img class="epanel_image_preview" id="image_<?php echo $oid;?>" src="<?php echo epanel_option($oid);?>" style="max-width:<?php echo $o['imagepreview'];?>px"/>
	<?php endif;?>
	
<?php }

		
function _get_count_select_option( $oid, $o, $optionvalue = '' ){ ?>
	
		<p>
			<label for="<?php echo $oid;?>" class="context"><?php echo $o['inputlabel'];?></label><br/>
			<select id="<?php echo $oid;?>" name="<?php epanel_option_name($oid); ?>">
				<option value="">Please select an option &raquo;</option>
				<?php if(isset($o['count_start'])): $count_start = $o['count_start']; else: $count_start = 0; endif;?>
				<?php for($i = $count_start; $i <= $o['count_number']; $i++):?>
						<option value="<?php echo $i;?>" <?php selected($i, epanel_option($oid)); ?>><?php echo $i;?></option>
				<?php endfor;?>
			</select>
		</p>
	
<?php }

function _get_radio_option( $oid, $o ){ ?>
	
		<?php foreach($o['selectvalues'] as $selectid => $selecttext):?>
			<p>
				<input type="radio" id="<?php echo $o;?>_<?php echo $selectid;?>" name="<?php epanel_option_name($oid); ?>" value="<?php echo $selectid;?>" <?php checked($selectid, epanel_option($oid)); ?>> 
				<label for="<?php echo $oid;?>_<?php echo $selectid;?>"><?php echo $selecttext;?></label>
			</p>
		<?php endforeach;?>
	
<?php }

function _get_select_option( $oid, $o ){ ?>
	
		<p>
			<label for="<?php echo $oid;?>" class="context"><?php echo $o['inputlabel'];?></label><br/>
			<select id="<?php echo $oid;?>" name="<?php epanel_option_name($oid); ?>">
				<option value="">Please select an option &raquo;</option>

				<?php foreach($o['selectvalues'] as $sval => $select_set):?>
					<?php if($o['type'] == 'select_same'):?>
							<option value="<?php echo $select_set;?>" <?php selected($select_set, epanel_option($oid)); ?>><?php echo $select_set;?></option>
					<?php else:?>
							<option value="<?php echo $sval;?>" <?php selected($sval, epanel_option($oid)); ?>><?php echo $select_set['name'];?></option>
					<?php endif;?>

				<?php endforeach;?>
			</select>
		</p>
<?php }

function _get_taxonomy_select( $oid, $o ){ 
	$terms_array = get_terms( $o['taxonomy_id']); 
	
	if(is_array($terms_array) && !empty($terms_array)):	?>
		<label for="<?php echo $oid;?>" class="context"><?php echo $o['inputlabel'];?></label><br/>
		<select id="<?php echo $oid;?>" name="<?php epanel_option_name($oid); ?>">
			<option value="">&mdash;<?php _e("SELECT", 'epanel');?>&mdash;</option>
			<?php foreach($terms_array as $term):?>
				<option value="<?php echo $term->slug;?>" <?php if( epanel_option($oid) == $term->slug ) echo 'selected';?>><?php echo $term->name; ?></option>
			<?php endforeach;?>
		</select>
<?php else:?>
		<div class="meta-message"><?php _e('No sets have been created and added to a post yet!', 'epanel');?></div>
<?php endif;

}

function _get_color_multi($oid, $o){ 	
	
	foreach($o['selectvalues'] as $mid => $m):
	
		if( !isset($m['version']) || (isset($m['version']) && $m['version'] != 'pro') || (isset($m['version']) && $m['version'] == 'pro' && VPRO )):
			$this->_get_color_picker($mid, $m);
		endif;
		
	endforeach; 

}


function _get_color_picker($oid, $o){ // Color Picker Template ?>
	<div class="the_picker">
		<label for="<?php echo $oid;?>" class="colorpicker_label context"><?php echo $o['inputlabel'];?></label>
		<div id="<?php echo $oid;?>_picker" class="colorSelector"><div></div></div>
		<input class="colorpickerclass"  type="text" name="<?php epanel_option_name($oid); ?>" id="<?php echo $oid;?>" value="<?php echo epanel_option($oid); ?>" />
	</div>
<?php  }

function _get_background_image_control($oid, $option_settings){
	
	$bg_fields = $this->_background_image_array();
	
	$this->_get_image_upload_option($oid.'_url', $bg_fields['_url'], epanel_option($oid.'_url'));
	$this->_get_select_option($oid.'_repeat', $bg_fields['_repeat']);
	$this->_get_count_select_option( $oid.'_pos_vert', $bg_fields['_pos_vert']);
	$this->_get_count_select_option( $oid.'_pos_hor', $bg_fields['_pos_hor']);
	
}


function _background_image_array(){
	return array(
		'_url' => array(		
				'inputlabel' 	=> 'Background Image',
				'imagepreview'	=> 150
		),
		'_repeat' => array(			
				'inputlabel'	=> 'Set Background Image Repeat',
				'type'			=> 'select',
				'selectvalues'	=> array(
					'no-repeat'	=> array('name' => 'Do Not Repeat'), 
					'repeat'	=> array('name' => 'Tile'), 
					'repeat-x'	=> array('name' => 'Repeat Horizontally'), 
					'repeat-y'	=> array('name' => 'Repeat Vertically')
				)
		),
		'_pos_vert' => array(				
				'inputlabel'	=> 'Vertical Position In Percent',
				'type'			=> 'count_select',
				'count_start'	=> 0, 
				'count_number'	=> 100,
		),
		'_pos_hor' => array(				
				'inputlabel'	=> 'Horizontal Position In Percent',
				'type'			=> 'count_select',
				'count_start'	=> 0, 
				'count_number'	=> 100,
		),
		
	);
}


		/**
		 * 
		 *
		 *  Layout Builder (Layout Drag & Drop)
		 *
		 *
		 *  @package EPANEL_WP Core
		 *  @subpackage Options
		 *  @since 4.0
		 *
		 */
		function _get_layout_builder($optionid, $option_settings){ ?>
			<div class="layout_controls selected_template">
			

				<div id="layout-dimensions" class="template-edit-panel">
					<h3>Configure Layout Dimensions</h3>
					<div class="select-edit-layout">
						<div class="layout-selections layout-builder-select fix">
							<div class="layout-overview">Select Layout To Edit</div>
							<?php


							global $epanel_layout;
							foreach(get_the_layouts() as $layout):
							?>
							<div class="layout-select-item">
								<span class="layout-image-border <?php if($epanel_layout->layout_map['last_edit'] == $layout) echo 'selectedlayout';?>"><span class="layout-image <?php echo $layout;?>">&nbsp;</span></span>
								<input type="radio" class="layoutinput" name="<?php epanel_option_name('layout', 'last_edit'); ?>" value="<?php echo $layout;?>" <?php if($epanel_layout->layout_map['last_edit'] == $layout) echo 'checked';?>>
							</div>
							<?php endforeach;?>

						</div>	
					</div>
					<?php

				foreach(get_the_layouts() as $layout):

				$buildlayout = new EPANEL_WPLayout($layout);
					?>
				<div class="layouteditor <?php echo $layout;?> <?php if($buildlayout->layout_map['last_edit'] == $layout) echo 'selectededitor';?>">
						<div class="layout-main-content" style="width:<?php echo $buildlayout->builder->bwidth;?>px">

							<div id="innerlayout" class="layout-inner-content" >
								<?php if($buildlayout->west->id != 'hidden'):?>
								<div id="<?php echo $buildlayout->west->id;?>" class="ui-layout-west innerwest loelement locontent"  style="width:<?php echo $buildlayout->west->bwidth;?>px">
									<div class="loelement-pad">
										<div class="loelement-info">
											<div class="layout_text"><?php echo $buildlayout->west->text;?></div>
											<div class="width "><span><?php echo $buildlayout->west->width;?></span>px</div>
										</div>
									</div>
								</div>
								<?php endif;?>
								<div id="<?php echo $buildlayout->center->id;?>" class="ui-layout-center loelement locontent innercenter">
									<div class="loelement-pad">
										<div class="loelement-info">
											<div class="layout_text"><?php echo $buildlayout->center->text;?></div>
											<div class="width "><span><?php echo $buildlayout->center->width;?></span>px</div>
										</div>
									</div>
								</div>
								<?php if( $buildlayout->east->id != 'hidden'):?>
								<div id="<?php echo $buildlayout->east->id;?>" class="ui-layout-east innereast loelement locontent" style="width:<?php echo $buildlayout->east->bwidth;?>px">
									<div class="loelement-pad">
										<div class="loelement-info">
											<div class="layout_text"><?php echo $buildlayout->east->text;?></div>
											<div class="width "><span><?php echo $buildlayout->east->width;?></span>px</div>
										</div>
									</div>
								</div>
								<?php endif;?>
								<div id="contentwidth" class="ui-layout-south loelement locontent" style="background: #fff;">
									<div class="loelement-pad"><div class="loelement-info"><div class="width"><span><?php echo $buildlayout->content->width;?></span>px</div></div></div>
								</div>
								<div id="top" class="ui-layout-north loelement locontent"><div class="loelement-pad"><div class="loelement-info">Content Area</div></div></div>
							</div>
							<div class="margin-west loelement"><div class="loelement-pad"><div class="loelement-info">Margin<div class="width"></div></div></div></div>
							<div class="margin-east loelement"><div class="loelement-pad"><div class="loelement-info">Margin<div class="width"></div></div></div></div>

						</div>


							<div class="layoutinputs">
								<label class="context" for="input-content-width">Global Content Width</label>
								<input type="text" name="<?php epanel_option_name('layout', 'content_width'); ?>" id="input-content-width" value="<?php echo $buildlayout->content->width;?>" size=5 readonly/>
								<label class="context"  for="input-maincolumn-width">Main Column Width</label>
								<input type="text" name="<?php echo EPANEL_SETTINGS; ?>[layout][<?php echo $layout;?>][maincolumn_width]" id="input-maincolumn-width" value="<?php echo $buildlayout->main_content->width;?>" size=5 readonly/>

								<label class="context"  for="input-primarysidebar-width">Sidebar1 Width</label>
								<input type="text" name="<?php echo EPANEL_SETTINGS; ?>[layout][<?php echo $layout;?>][primarysidebar_width]" id="input-primarysidebar-width" value="<?php echo  $buildlayout->sidebar1->width;?>" size=5 readonly/>
							</div>
				</div>
				<?php endforeach;?>

			</div>
		</div>
		<?php }
		
/**
 * 
 *
 *  Layout Select (Layout Selector)
 *
 *
 *  @package EPANEL_WP Core
 *  @subpackage Options
 *  @since 4.0
 *
 */
function _get_layout_select($optionid, $option_settings){ ?>
	<div id="layout_selector" class="template-edit-panel">

		<div class="layout-selections layout-select-default fix">
			<div class="layout-overview">Default Layout</div>
			<?php


			global $epanel_layout;
			foreach(get_the_layouts() as $layout):
			?>
			<div class="layout-select-item">
				<span class="layout-image-border <?php if($epanel_layout->layout_map['saved_layout'] == $layout) echo 'selectedlayout';?>"><span class="layout-image <?php echo $layout;?>">&nbsp;</span></span>
				<input type="radio" class="layoutinput" name="<?php echo EPANEL_SETTINGS; ?>[layout][saved_layout]" value="<?php echo $layout;?>" <?php if($epanel_layout->layout_map['saved_layout'] == $layout) echo 'checked';?>>
			</div>
			<?php endforeach;?>

		</div>

	</div>
	<div class="clear"></div>
<?php }

/**
 * 
 *
 *  Template Builder (Sections Drag & Drop)
 *
 *
 *  @package EPANEL_WP Core
 *  @subpackage Options
 *  @since 4.0
 *
 */
function _get_template_builder(){
	
		global $epanel_template;
		global $unavailable_section_areas;
		$dtoggle = (get_option('pl_section_desc_toggle')) ? get_option('pl_section_desc_toggle') : 'hide'; 
	?>
	<input type="hidden" value="<?php echo $dtoggle;?>" id="describe_toggle" class="describe_toggle" name="describe_toggle"  />	
	<div class="confirm_save">Template Configuration Saved!</div>
	<label for="tselect" class="tselect_label">Select Template Area</label>
	<select name="tselect" id="tselect" class="template_select" >
<?php 	foreach(the_template_map() as $hook => $hook_info):?>
	
	 <?php if(isset($hook_info['templates'])): ?>
		
				<optgroup label="<?php echo $hook_info['name'];?>" class="selectgroup_header">
			<?php foreach($hook_info['templates'] as $template => $tfield):
					if(!isset($tfield['version']) || ($tfield['version'] == 'pro' && VPRO)):
			?>				
						<option value="<?php echo $hook . '-' . $template;?>"><?php echo $tfield['name'];?></option>
				<?php endif;?>
				<?php endforeach;?>
				</optgroup>
			<?php else: ?>
		
		<?php 
				if(!isset($hook_info['version']) || ($hook_info['version'] == 'pro' && VPRO)):
?>
			<option value="<?php echo $hook;?>" <?php if($hook == 'default') echo 'selected="selected"';?>><?php echo $hook_info['name'];?></option>
<?php endif; ?>
			<?php endif;?>
		
	<?php endforeach;?>
	</select>
	<div class="the_template_builder">
		<?php 
		foreach($epanel_template->map as $hook_id => $hook_info){
			 if(isset($hook_info['templates'])){
				foreach($hook_info['templates'] as $template_id => $template_info ){
					$this->_sortable_section($template_id, $template_info, $hook_id, $hook_info);
				}
			} else {
				$this->_sortable_section($hook_id, $hook_info);
			}

		}?>
	</div>
	<?php 
	
}

/**
 * 
 *
 *  Get Sortable Sections (Sections Drag & Drop)
 *
 *
 *  @package EPANEL_WP Core
 *  @subpackage Options
 *  @since 4.0
 *
 */
function _sortable_section($template, $tfield, $hook_id = null, $hook_info = array()){
		global $pl_section_factory;
		
		$available_sections = $pl_section_factory->sections;
		
		$template_slug = ( isset($hook_id) ) ? $hook_id.'-'.$template : $template;
		
		$template_area = ( isset($hook_id) ) ? $hook_id : $template;
		
		$dtoggle = (get_option('pl_section_desc_toggle')) ? get_option('pl_section_desc_toggle') : 'show'; 
		
			?>
		
				<div id="template_data" class="<?php echo $template_slug; ?> layout-type-<?php echo $template_area;?>">
					<div class="editingtemplate fix">
						<span class="edit_template_title"><?php echo $tfield['name'];?> Template Sections</span>

					</div>
					<div class="section_layout_description">
						<div class="config_title">Place Sections <span class="makesubtle">(drag &amp; drop)</span></div>
						<div class="layout-type-frame">
							<div class="layout-type-thumb"></div>
							Template Area: <?php echo ucwords( str_replace('_', ' ', $template_area) );?>
						</div>
					</div>
					
					<div id="section_map" class="template-edit-panel ">

						<div class="sbank template_layout">

							<div class="bank_title">Displayed <?php echo $tfield['name'];?> Sections</div>

							<ul id="sortable_template" class="connectedSortable ">
								<?php if(is_array($tfield['sections'])):?>
									<?php foreach($tfield['sections'] as $section):
									
									 		if(isset( $pl_section_factory->sections[$section] )):
									
												$s = $pl_section_factory->sections[$section];
												
												$section_id =  $s->id;
											
										?>
										<li id="section_<?php echo $section; ?>" class="section_bar <?php if($s->settings['required'] == true) echo 'required-section';?>">
											<div class="section-pad fix" style="background: url(<?php echo $s->settings['icon'];?>) no-repeat 10px 8px;">
												
												<h4><?php echo $s->name;?></h4>
												<span class="s-description" <?php if($dtoggle = 'hide'):?>style="display:none"<?php endif;?> >
												<?php echo $s->settings['description'];?>
												</span>
												
												<?php 
												
												$section_control = epanel_option('section-control');
												
												// Options 
												$check_name = EPANEL_SETTINGS.'[section-control]['.$template_slug.']['.$section.'][hide]';
												$check_value = isset($section_control[$template_slug][$section]['hide']) ? $section_control[$template_slug][$section]['hide'] : null;
												
												$posts_check_type = ($check_value) ? 'show' : 'hide';
												
												if($template == 'posts' || $template == 'single' || $template == '404' ){
													$default_display_check_disabled = true;
												} else {
													$default_display_check_disabled = false;
												}

												if($template_area == 'main' || $template_area == 'templates'){
													
													$posts_check_disabled = true;
												} else {
													$posts_check_label = ucfirst($posts_check_type) .' On Posts Pages';
													$posts_check_name = EPANEL_SETTINGS.'[section-control]['.$template_slug.']['.$section.'][posts-page]['.$posts_check_type.']';
													$posts_check_value = isset($section_control[$template_slug][$section]['posts-page'][$posts_check_type]) ? $section_control[$template_slug][$section]['posts-page'][$posts_check_type] : null;
													$posts_check_disabled = false;
												}
												
												// Hooks
											
												//epanel_ob_section_template( $s );
												global $registered_hooks;
												
												?>
												<div class="section-moreinfo">
													<div><span class="section-moreinfo-toggle" onClick="jQuery(this).parent().next('.section-moreinfo-info').slideToggle();">Advanced Setup &darr;</span></div>
													<div class="section-moreinfo-info">
														<?php if(!$default_display_check_disabled):?>
														<strong>Settings</strong> 
														<div class="section-options">
															<div class="section-options-row">
																<input class="section_control_check" type="checkbox" id="<?php echo $check_name; ?>" name="<?php echo $check_name; ?>" <?php checked((bool) $check_value); ?> />
																<label for="<?php echo $check_name; ?>">Hide This By Default</label>
															</div>
															<?php if(!$posts_check_disabled):?>
															<div class="section-options-row">
																	<input class="section_control_check" type="checkbox" id="<?php echo $posts_check_name; ?>" name="<?php echo $posts_check_name; ?>" <?php checked((bool) $posts_check_value); ?>/>
																	<label for="<?php echo $posts_check_name; ?>" class="<?php echo 'check_type_'.$posts_check_type; ?>"><?php echo $posts_check_label;?></label>
															</div>
															<?php endif;?>
														</div>
														<?php endif;?>
														<p>
															 <strong>Standard Hooks:</strong> 
															<div class="moreinfolist">
																<span>epanel_before_<?php echo $section_id; ?></span>
																<span>epanel_inside_top_<?php echo  $section_id; ?></span>
																<span>epanel_inside_bottom_<?php echo  $section_id; ?></span>
																<span>epanel_after_<?php echo $section_id; ?></span>
																<span>(View template for additional hooks &amp; filters)</span>
																<?php if(isset($registered_hooks[$section_id]) && is_array($registered_hooks[$section_id])){
																	foreach($registered_hooks[$section_id] as $reg_hook){
																		echo '<span>'.$reg_hook.'</span>';
																	}
																}?>
															</div>
														</p>
														<p><strong>CSS Selectors: </strong>
															<div class="moreinfolist">
																<?php if( (isset($tfield['markup']) && $tfield['markup'] == 'content') || (isset($hook_info['markup']) && $hook_info['markup'] == 'content') ):?>
																	<span>#<?php echo $section_id; ?> <small>(Full Screen Width)</small></span>
																	<span>#<?php echo $section_id; ?> .content <small>(Content Width)</small></span>
																	<span>#<?php echo $section_id; ?> .content-pad <small>(Content Inner)</small></span>
																<?php elseif( (isset($tfield['markup']) && $tfield['markup'] == 'copy') || (isset($hook_info['markup']) && $hook_info['markup'] == 'copy') ):?>
																	<span>#<?php echo $section_id; ?> <small>(Width of Container)</small></span>
																	<span>#<?php echo $section_id; ?> .copy <small>(Section Width)</small></span>
																	<span>#<?php echo $section_id; ?> .copy-pad <small>(Section Inner)</small></span>
																<?php endif;?>
															</div>
														</p>
													</div>
												</div>
											</div>
										</li>
										<?php if(isset($available_sections[$section])) { unset($available_sections[$section]); } ?>
							
									<?php endif; endforeach;?>

								<?php endif;?>
							</ul>
							<div class="section_setup_controls fix">
							
							
								<span class="setup_control" onClick="EPANEL_WPSlideToggle('.s-description', '.describe_toggle', '.setup_control_text','Hide Section Descriptions', 'Show Section Descriptions', 'pl_section_desc_toggle');">
									<span class="setup_control_text">
										<?php if($dtoggle == 'show'):?>
											Hide Section Descriptions
										<?php else: ?>
											Show Section Descriptions
										<?php endif;?>
									</span>
								</span>
							</div>
						</div>
						<div class="sbank available_sections">

							<div class="bank_title">Available/Disabled Sections</div>
							<ul id="sortable_sections" class="connectedSortable ">
								<?php 
								foreach($available_sections as $sectionclass => $section):
								
							
										/* Flip values and keys */
										$works_with = array_flip($section->settings['workswith']);
										$fails_with = array_flip($section->settings['failswith']);
										
										$markup_type = (!empty($hook_info)) ? $hook_info['markup'] : $tfield['markup'];
									
										if(isset( $works_with[$template] ) || isset( $works_with[$hook_id]) || isset($works_with[$hook_id.'-'.$template]) || isset($works_with[$markup_type])):?>
											<?php if( !isset($fails_with[$template]) && !isset($fails_with[$hook_id]) ):?>
											<li id="section_<?php echo $sectionclass;?>" class="section_bar" >
												<div class="section-pad fix" style="background: url(<?php echo $section->settings['icon'];?>) no-repeat 10px 10px;">
													<h4><?php echo $section->name;?></h4>
													<span class="s-description" <?php if($dtoggle = 'hide'):?>style="display:none"<?php endif;?>>
														<?php echo $section->settings['description'];?>
													</span>
												</div>
											</li>
											<?php endif;?>
										<?php endif;?>
									
								<?php endforeach;?>
							</ul>
						</div>
					
						<div class="clear"></div>
					</div>


					<div class="clear"></div>
						
						
 
				</div>
	
<?php
}




/**
 * Option Interface Footer
 *
 */
function build_footer(){?>
		<div id="optionsfooter">
			<div class="hl"></div>
				<div class="theinputs">
				
					<input class="button-secondary reset-options" type="submit" name="<?php epanel_option_name('reset'); ?>" onClick="return ConfirmRestore();" value="Restore Theme Settings" style="float:left;"/>
					<?php pl_action_confirm('ConfirmRestore', 'Are you sure? This will restore your theme options settings to default.');?>
					
					<input class="button-primary" type="submit" name="submit" value="<?php _e('Save Theme Settings', 'epanel');?>" />
					
				</div>
			<div class="clear"></div>
		</div>

	  	</form>
		
		<div class="metabox-holder" style="width:930px;">
		
		<div class="postbox-container-new">
		
			<div id="settings-importexport" class="postbox">
				
				<div class="handlediv" title="Click to toggle"><br></div><h3 class="hndle"><span><?php echo $theme_data['Title'];?> Theme Settings Import &amp; Export</span></h3>
				
				<div class="inside">
					
					<div class="importandexport" style="padding-left:15px;">
								<div class="one_half">
									<h4><?php _e('Export Theme Settings', 'epanel'); ?></h4>
									<p class="fix">
										<a class="button-secondary download-button" href="<?php echo admin_url('admin.php?page=epanel&amp;download=settings'); ?>">Download Theme Settings</a>
									</p>
								</div>
								
								<div class="one_half last">
									<h4><?php _e('Import Theme Settings', 'epanel'); ?></h4>
									<form method="post" enctype="multipart/form-data">
										<input type="hidden" name="settings_upload" value="settings" />
										<p class="form_input">
											<input type="file" class="text_input" name="file" id="settings-file" />
											<input class="button-secondary" type="submit" value="Upload New Settings" onClick="return ConfirmImportSettings();" />
										</p>
									</form>
					
									<?php pl_action_confirm('ConfirmImportSettings', 'Are you sure? This will overwrite your current settings and configurations with the information in this file!');?>
								</div>
							</div>
							<div class="clearboth"></div>
				
				</div>
			
			</div>
		
		</div>
		</div>
		
		
		
				
				
	</td></tr></tbody></table>
	
	
	<div class="clear"></div>
	<script type="text/javascript">/*<![CDATA[*/
	jQuery(document).ready(function(){
		jQuery('.framework_loading').hide();
	});
	/*]]>*/</script>
	
	</div>
<?php }


} 
// ===============================
// = END OF OPTIONS LAYOUT CLASS =
// ===============================