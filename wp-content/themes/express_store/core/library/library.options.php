<?php
if(__FILE__ == $_SERVER['SCRIPT_FILENAME'])
{
    header('HTTP/1.0 404 Not Found');
      exit;
}
/*-----------------------------------------------------------------------------------*/
/* Framework options values handler
/*-----------------------------------------------------------------------------------*/

function epanel_option_name( $oid, $sub_oid = null, $grand_oid = null){
	if( isset($grand_oid) ){
		echo EPANEL_SETTINGS . '['.$oid.']' . '['.$sub_oid.']' . '['.$grand_oid.']';	
	}elseif( isset($sub_oid) ){
		echo EPANEL_SETTINGS . '['.$oid.']' . '['.$sub_oid.']';
	} else {
		echo EPANEL_SETTINGS.'['.$oid.']';
	}
	
}

function epanel_option_id( $oid, $sub_oid = null, $grand_oid = null){
	echo get_epanel_option_id($oid, $sub_oid, $grand_oid);
}

function get_epanel_option_id( $oid, $sub_oid = null, $grand_oid = null){
	if( isset($grand_oid) ){
		$oid = 'epanel_' . $oid . '_' . $sub_oid . '_' . $grand_oid;
	}
	elseif( isset($sub_oid) ){
		$oid = 'epanel_' . $oid . '_' . $sub_oid;
	}
	else {
		$oid = 'epanel_' . $oid;
	}
	
	return $oid;
}


/*-----------------------------------------------------------------------------------*/
/* Pull options from database
/*-----------------------------------------------------------------------------------*/

function get_epanel_option($key, $setting = null) {

	global $global_epanel_settings;

	// get setting
	$setting = $setting ? $setting : EPANEL_SETTINGS;

	if(isset($global_epanel_settings[$key])){
		return $global_epanel_settings[$key];
	} else {
		return false;
	}

}

function epanel_option( $key, $post_id = '', $setting = null){
	
	if($post_id && get_post_meta($post_id, $key, true) && !is_home()){
		//if option is set for a page/post
		return get_post_meta($post_id, $key, true);
		
	}elseif( get_epanel_option($key, $setting) ){
		
		return get_epanel_option($key, $setting);
			
	}else {
		return false;
	}
}

function epanel_sub_option( $key, $subkey, $post_id = '', $setting = null){
	
	$primary_option = epanel_option($key, $post_id, $setting);
	
	if(is_array($primary_option) && isset($primary_option[$subkey]))
		return $primary_option[$subkey];
	else return false;

}

// Need to keep until the forums are redone, or don't check for it.
function epanel( $key, $post_id = null, $setting = null ){ 
	return epanel_option($key, $post_id, $setting);
}

function e_epanel($key, $alt = null, $post_id = null, $setting = null){
	print_epanel_option( $key, $alt, $post_id, $setting);
}


function epanel_pro($key, $post_id = null, $setting = null){

	if(NOT_SET) return epanel_option($key, $post_id, $setting);
	else return false;
}

function print_epanel_option($key, $alt = null, $post_id = null, $setting = null) {
	
	echo load_epanel_option($key, $alt, $post_id, $setting);
	
}

function load_epanel_option($key, $alt = null, $post_id = null, $setting = null) {
	
		if($post_id && get_post_meta($post_id, $key, true) && !is_home()){
			
			//if option is set for a page/post
			return get_post_meta($post_id, $key, true);
			
		}elseif(epanel_option($key, $post_id, $setting)){
			
			return epanel_option($key, $post_id, $setting);
			
		}else{
			return $alt;
		}
	
}

function epanel_update_option($optionid, $optionval){
	
		$theme_options = get_option(EPANEL_SETTINGS);
		$new_options = array(
			$optionid => $optionval
		);

		$settings = wp_parse_args($new_options, $theme_options);
		update_option(EPANEL_SETTINGS, $settings);
}

function get_epanel_meta($option, $post){
	$meta = get_post_meta($post, $option, true);
	if(isset($meta) && !epanel_is_posts_page()){
		return $meta;
	}else{
		return false;
	}
}

	/* Deprecated in favor of get_epanel_meta */
	function m_epanel($option, $post){
		return get_epanel_meta($option, $post);
	}


	function em_epanel($option, $post, $alt = ''){
		$post_meta = m_epanel($option, $post);
	
		if(isset($post_meta)){
			echo $post_meta;
		}else{
			echo $alt;
		}
	}
	
/*-----------------------------------------------------------------------------------*/
/* Register default options for database
/*-----------------------------------------------------------------------------------*/

function epanel_settings_defaults() {

	$default_options = array();

		foreach(get_option_array() as $menuitem => $options ){
			foreach($options as $optionid => $o ){

				if($o['type']=='layout'){
					
					$dlayout = new EPANEL_WPLayout;
					$default_options['layout'] = $dlayout->default_layout_setup();
					
				}elseif($o['type']=='check_multi' || $o['type']=='text_multi' || $o['type']=='color_multi'){
					foreach($o['selectvalues'] as $multi_optionid => $multi_o){
						if(isset($multi_o['default'])) $default_options[$multi_optionid] = $multi_o['default'];
					}

				}else{ 
					if(!VPRO && isset($o['version_set_default']) && $o['version_set_default'] == 'pro') $default_options[$optionid] = null;
					elseif(!VPRO && isset($o['default_free'])) $default_options[$optionid] = $o['default_free'];
					elseif(isset($o['default'])) $default_options[$optionid] = $o['default'];
				}

			}
		}

	return apply_filters('epanel_settings_defaults', $default_options);
}

/*-----------------------------------------------------------------------------------*/
/* Register options for wp_option database
/*-----------------------------------------------------------------------------------*/

function epanel_wp_option_defaults($reset = false) {


	foreach(get_option_array() as $menuitem => $options ){
		foreach($options as $optionid => $o ){
			if( isset($o['wp_option']) && $o['wp_option'] ){

				if($reset){
					
					if(!VPRO && isset($o['version_set_default']) && $o['version_set_default'] == 'pro') update_option( $optionid, null);
					elseif(!VPRO && isset($o['default_free'])) update_option( $optionid, $o['default_free']);
					elseif(isset($o['default'])) update_option( $optionid, $o['default']);
					
				}else{
					if(!VPRO && isset($o['version_set_default']) && $o['version_set_default'] == 'pro') add_option( $optionid, null);
					elseif(!VPRO && isset($o['default_free'])) add_option( $optionid, $o['default_free']);
					elseif(isset($o['default'])) add_option( $optionid, $o['default']);
				}

			}

		}
	}

}

/*-----------------------------------------------------------------------------------*/
/* Framework reset process
/*-----------------------------------------------------------------------------------*/

function epanel_process_reset_options() {


	foreach(get_option_array() as $menuitem => $options ){
		foreach($options as $optionid => $o ){
			if( $o['type']=='reset' && epanel_option($optionid) ){

					call_user_func($o['callback']);
				
					// Set the 'reset' option back to not set !important 
					epanel_update_option($optionid, null);
				
					wp_redirect( admin_url( 'admin.php?page=epanel&reset=true&opt_id='.$optionid ) );
					exit;

			}

		}
	}

}

/*-----------------------------------------------------------------------------------*/
/* Import and export framework settings
/*-----------------------------------------------------------------------------------*/

function epanel_import_export(){
	
	if ( isset($_GET['download']) && $_GET['download'] == 'settings') {
		
			header("Cache-Control: public, must-revalidate");
			header("Pragma: hack");
			header("Content-Type: text/plain");
			header('Content-Disposition: attachment; filename="WORDPRESS-EPANEL-'.THEMENAME.'-Settings-' . date("Ymd") . '.dat"');

			$epanel_settings = get_option(EPANEL_SETTINGS);
			$epanel_template = get_option('epanel_template_map');

			echo (serialize(array('epanel_settings' => $epanel_settings, 'epanel_template' => $epanel_template)));
			exit();
			
	}
	
	if ( isset($_POST['settings_upload']) && $_POST['settings_upload'] == 'settings') {
		
		if (strpos($_FILES['file']['name'], 'Settings') === false && strpos($_FILES['file']['name'], 'settings') === false){
			wp_redirect( admin_url('admin.php?page=epanel&pageaction=import&error=wrongfile') ); 
		} elseif ($_FILES['file']['error'] > 0){
			$error_type = $_FILES['file']['error'];
			wp_redirect( admin_url('admin.php?page=epanel&pageaction=import&error=file&'.$error_type) );
		} else {
			
			$raw_options = file_get_contents($_FILES['file']['tmp_name']);
			$all_options = unserialize($raw_options);
		
			if(isset($all_options['epanel_settings']) && isset($all_options['epanel_template'])){
				$epanel_settings = $all_options['epanel_settings'];
				$epanel_template = $all_options['epanel_template'];

			
				if (is_array($epanel_settings)) update_option(EPANEL_SETTINGS, $epanel_settings); 
				if (is_array($epanel_template)) update_option('epanel_template_map', $epanel_template); 
			
			}
			if (function_exists('wp_cache_clean_cache')) { 
				global $file_prefix;
				wp_cache_clean_cache($file_prefix); 
			}

			epanel_build_dynamic_css();
			wp_redirect(admin_url( 'admin.php?page=epanel&pageaction=import&imported=true' )); 
		}
		
	}

}

