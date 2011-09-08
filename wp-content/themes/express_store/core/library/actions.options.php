<?php 
if(__FILE__ == $_SERVER['SCRIPT_FILENAME'])
{
    header('HTTP/1.0 404 Not Found');
      exit;
}
/*-----------------------------------------------------------------------------------*/
/* Options Framework Interface Creation
/*-----------------------------------------------------------------------------------*/

//	This function adds the top-level menu
 add_action('admin_menu', 'epanel_add_admin_menu');
function epanel_add_admin_menu() {
	global $menu;

	// Creating a new dashboard separator for our awesome panel
	$menu['58.995'] = array( '', 'edit_theme_options', 'separator-epanel', '', 'wp-menu-separator' );
	

	// Create the new top-level Menu
	add_menu_page ('Page Title', 'E-Panel', 'edit_theme_options','epanel', 'epanel_build_option_interface', CORE_IMAGES. '/favicon-epanel.png', '58.996');
}

// Create theme options panel
add_action('admin_menu', 'epanel_add_admin_submenus');
function epanel_add_admin_submenus() {
	global $_epanel_options_page_hook;
		
	$_epanel_options_page_hook = add_submenu_page('epanel', 'Theme Settings', 'Theme Settings', 'edit_theme_options', 'epanel','epanel_build_option_interface'); // Default

}

// Build option interface
function epanel_build_option_interface(){ 
	do_action('epanel_before_optionUI');
	$optionUI = new EPANEL_WPOptionsUI;
}

/*-----------------------------------------------------------------------------------*/
/* Options Framework Loading required backend scripts
/*-----------------------------------------------------------------------------------*/

add_action('admin_menu', 'epanel_theme_settings_init');
function epanel_theme_settings_init() {
	global $_epanel_options_page_hook;
	global $_epanel_options_page_hook_int;
	
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ajaxupload', CORE_JS . '/jquery.ajaxupload.js');
	wp_enqueue_script( 'jquery-cookie', CORE_JS . '/jquery.ckie.js');
	wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_script( 'jquery-ui-tabs' );
	
	add_action('load-'.$_epanel_options_page_hook, 'epanel_theme_settings_scripts');
	wp_enqueue_script( 'platform-admin-js', CORE_JS . '/platform.admin.js');
}

function epanel_theme_settings_scripts() {	
	
	wp_enqueue_script( 'jquery-ui-draggable' );	
	wp_enqueue_script( 'jquery-ui-sortable' );
	wp_enqueue_script( 'thickbox' );	
	wp_enqueue_style('thickbox'); 
	wp_enqueue_script( 'jquery-layout', CORE_JS . '/jquery.layout.js');
}

add_action( 'admin_head', 'load_head' );
function load_head(){

	// Always Load
	echo '<link rel="stylesheet" href="'.CORE_CSS.'/admin.css?ver='.CORE_VERSION.'" type="text/css" media="screen" />';
	if(epanel_option('epanel_favicon'))  echo '<link rel="shortcut icon" href="'.epanel_option('epanel_favicon').'" type="image/x-icon" />';

	// Load on EPANEL_WP pages
	if(isset($_GET['page']) && ($_GET['page'] == 'epanel')){
			include( CORE_LIB.'/admin.head.php' );
			
	}

}


/*-----------------------------------------------------------------------------------*/
/* Options Framework : add default values to database at theme activation - also
/* restore default values if [reset]
/*-----------------------------------------------------------------------------------*/

add_action('admin_init', 'epanel_register_settings', 5);
function epanel_register_settings() {
	
	
	register_setting( EPANEL_SETTINGS, EPANEL_SETTINGS );
	
	 // Set framework default options
		add_option( EPANEL_SETTINGS, epanel_settings_defaults() ); // only fires first time
	
		epanel_wp_option_defaults(); // Add stand alone wp options, only fires first time
	

	if ( !isset($_REQUEST['page']) || $_REQUEST['page'] != 'epanel' )
		return;	
	
	// Dynamic typography builder
	$GLOBALS['pl_foundry'] = new EPANEL_WPFoundry;

	// Import and export theme options reset
	epanel_import_export();
			
	
	// Reset process informations	
	epanel_process_reset_options();

	if ( isset($_GET['activated']) || isset($_GET['updated']) || isset($_GET['reset']) ) {
		epanel_build_dynamic_css( 'Page Load' );
	}
	
	if ( epanel_option('reset') ) {
		update_option(EPANEL_SETTINGS, epanel_settings_defaults());
		epanel_wp_option_defaults(true);
		epanel_build_dynamic_css( 'Reset' );
		wp_redirect( admin_url( 'admin.php?page=epanel&reset=true' ) );
		exit;
	}

}

/*-----------------------------------------------------------------------------------*/
/* Options Framework Sections on load - still under development
/*-----------------------------------------------------------------------------------*/

add_action("admin_menu", 'add_section_control_box');

add_action('save_post', 'save_section_control_box');

function add_section_control_box(){
	
	//add_meta_box('section_control', 'EPANEL_WP Section Control', "epanel_section_control_callback", 'page', 'side', 'low');

	//add_meta_box('section_control', 'EPANEL_WP Section Control', "epanel_section_control_callback", 'post', 'side', 'low');
}

// Layout controller 
function epanel_section_control_callback(){
	global $post; 
	global $global_epanel_settings; 
	global $pl_section_factory;
	global $epanel_template;
	
	$section_control = epanel_option('section-control');
	
	echo '<div class="section_control_desc"><p>Below are all the sections that are active for this template.</p> <p>Here you can turn sections off or on (if hidden by default) for this individual page/post.</p> <p><small><strong>Note:</strong> Individual page settings do not work on the blog page (<em>use the settings panel</em>).</small></p></div>';?>
		
		<div class="admin_section_control section_control_individual">
			<div class="section_control_pad">
			<?php  epanel_process_template_map('section_control_checkbox', array('area_titles'=> true)); ?>
	 		</div>
		</div>
		
<?php 	
}

function section_control_checkbox($section, $template_slug, $template_area, $template){ 
		global $pl_section_factory;
		global $post;
		
		$s = $pl_section_factory->sections[$section];
		
		// Load Global Section Control Options
		$section_control = epanel_option('section-control');
		$hidden_by_default = isset($section_control[$template_slug][$section]['hide']) ? $section_control[$template_slug][$section]['hide'] : null;
		
		$check_type = ($hidden_by_default) ? 'show' : 'hide';
		
		// used to be _hide_SectionClass;
		// needs to be _hide_TemplateSlug_SectionClass
		// Why? 
		$check_name = "_".$check_type."_".$section;
		
		$check_label = ucfirst($check_type)." ".$s->name;
		
		$check_value = get_epanel_meta($check_name, $post->ID);
		
	?>
	
	<div class="section_checkbox_row <?php echo 'type_'.$check_type;?>" >
	
		<input class="section_control_check" type="checkbox" id="<?php echo $check_name; ?>" name="<?php echo $check_name; ?>" <?php checked((bool) $check_value); ?> />
		<label for="<?php echo $check_name;?>"><?php echo $check_label;?></label>
		
	</div>
<?php }

function save_section_control_box($postID){
	global $epanel_template;
	
	global $post; 

	$save_template = new EPANEL_WPTemplate();

	if(isset($_POST['update']) || isset($_POST['save']) || isset($_POST['publish'])){

		foreach($save_template->default_all_template_sections as $section){
					
			$option_value =  isset($_POST['_hide_'.$section]) ? $_POST['_hide_'.$section] : null;
			
			if(!empty($option_value) || get_post_meta($postID, '_hide_'.$section)){
				update_post_meta($postID, '_hide_'.$section, $option_value );
			}
			
			$option_value =  isset($_POST['_show_'.$section]) ? $_POST['_show_'.$section] : null;
			
			if(!empty($option_value) || get_post_meta($postID, '_show_'.$section)){
				update_post_meta($postID, '_show_'.$section, $option_value );
			}
		}
	}
}


