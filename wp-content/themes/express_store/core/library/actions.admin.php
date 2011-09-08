<?php
if(__FILE__ == $_SERVER['SCRIPT_FILENAME'])
{
    header('HTTP/1.0 404 Not Found');
      exit;
}
/*-----------------------------------------------------------------------------------*/
/* Redirect To Epanel administration once theme is activated
/*-----------------------------------------------------------------------------------*/

if(is_admin() && isset($_GET['activated'] ) && $pagenow == "themes.php" ) {

	//Do redirect
	header( 'Location: '.admin_url().'admin.php?page=epanel&activated=true&pageaction=activated' ) ;

}

/*-----------------------------------------------------------------------------------*/
/* Show warning message if crappy host
/*-----------------------------------------------------------------------------------*/

add_action('epanel_before_optionUI', 'epanel_check_php');
function epanel_check_php(){
	if(floatval(phpversion()) < 5.2){
		_e('<div class="config-error"><h2>PHP Version Problem</h2>Looks like you are using PHP version: <strong>'.phpversion().'</strong>. To run this framework you will need PHP <strong>5.2</strong> or better...<br/><br/> Please contact your hosting provider and request for an upgrade. Php 5.3.3 is recommended.</div>', 'epanel');
	}

}

/*-----------------------------------------------------------------------------------*/
/* Options Framework Ajax Save
/*-----------------------------------------------------------------------------------*/
	add_action('wp_ajax_epanel_ajax_post_action', 'epanel_ajax_callback');

	function epanel_ajax_callback() {
		global $wpdb; // this is how you get access to the database
	
		if($_POST['type']){
			$save_type = $_POST['type'];
		}else $save_type = null;
	
		//Uploads
		if($save_type == 'upload'){
		
			$clickedID = $_POST['data']; // Acts as the name
			$filename = $_FILES[$clickedID];
	       	$filename['name'] = preg_replace('/[^a-zA-Z0-9._\-]/', '', $filename['name']); 
		
			$override['test_form'] = false;
			$override['action'] = 'wp_handle_upload';    
			$uploaded_file = wp_handle_upload($filename,$override);
		 
			$upload_tracking[] = $clickedID;
			
			epanel_update_option( $clickedID , $uploaded_file['url'] );

			if(!empty($uploaded_file['error'])) {echo 'Upload Error: ' . $uploaded_file['error']; }	
			else { echo $uploaded_file['url']; } // Is the Response
		}
		elseif($save_type == 'image_reset'){
			
				$id = $_POST['data']; // Acts as the name
				epanel_update_option($id, null);
				
	
		}
	
		die();
	}
	
/*-----------------------------------------------------------------------------------*/
/* Options Framework layout Builder ajax save
/*-----------------------------------------------------------------------------------*/

	add_action('wp_ajax_epanel_save_sortable', 'ajax_save_template_map');

	function ajax_save_template_map() {
		global $wpdb; // this is how you get access to the database
		
		
		/* Full Template Map */
		
		$templatemap = get_option('epanel_template_map');
		
		/* Order of the sections */
		$section_order =  $_GET['orderdata'];
		
		/* Get array / variable format */
		parse_str($section_order);
		
		/* Selected Template */
		$selected_template = esc_attr($_GET['template']);
		
			/* Explode by slash to get heirarchy */
			$template_heirarchy = explode('-', $selected_template);
			
			if(isset($template_heirarchy[1])){
				$templatemap[$template_heirarchy[0]]['templates'][$template_heirarchy[1]]['sections'] = urlencode_deep($section);
			} else {
				$templatemap[$selected_template]['sections'] = $section;
			}
		
		
		save_template_map($templatemap);
		
		echo true;
		
		die();
	}
	
	add_action('epanel_ajax_save_option', 'epanel_ajax_save_option_callback');
	function epanel_ajax_save_option_callback() {
		global $wpdb; // this is how you get access to the database

		$option_name = $_POST['option_name'];
		$option_value = $_POST['option_value'];

		update_option($option_name, $option_value);
		
		die();
	}
	
	
	// Creates Dynamic CSS file when saving with AJAX
	add_action('wp_ajax_epanel_ajax_create_dynamic_css', 'epanel_ajax_create_dynamic_css_callback');
	function epanel_ajax_create_dynamic_css_callback() {
		global $wpdb; // this is how you get access to the database

		epanel_build_dynamic_css( 'AJAX' );
		
		die();
	}
	

	