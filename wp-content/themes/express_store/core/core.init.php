<?php
if(__FILE__ == $_SERVER['SCRIPT_FILENAME'])
{
    header('HTTP/1.0 404 Not Found');
      exit;
}

/*-----------------------------------------------------------------------------------*/
/* Options Framework Core Init
/*-----------------------------------------------------------------------------------*/

// Core Hook
do_action('epanel_hook_pre'); //hook

define('CORE', TEMPLATEPATH . "/core");
define('CORENAME', "core");
define('COMPONENTS', CORE.'/components/'); 

// Framework core globals paths and informations
require_once( CORE . '/core.globals.php');


/*-----------------------------------------------------------------------------------*/
/* Options Framework Library Functions
/*-----------------------------------------------------------------------------------*/

// Framework Core Functions
require_once( CORE_LIB . '/library.functions.php');

// Framework Core Options functions
require_once( CORE_LIB . '/library.options.php' );

// Theme related functions
require_once( CORE_LIB . '/library.templates.php');

// Shortcode library - rev 1.0 - advanced component under development so keep the development of shortcodes slow....
require_once( CORE_LIB . '/library.shortcodes.php');

require_once( CORE_LIB . '/library.dashboard.php');

/*-----------------------------------------------------------------------------------*/
/* Load Theme Configurations and Components Files
/*-----------------------------------------------------------------------------------*/

require_once( THEME_CONFIG . '/config.options.php' ); 
require_once( THEME_CONFIG . '/config.posttypes.php' );
require_once( THEME_CONFIG . '/config.sidebars.php');
require_once( THEME_CONFIG . '/config.intro.php' );

// Globalize settings
$GLOBALS['global_epanel_settings'] = get_option(EPANEL_SETTINGS);	
	
// Custom Post Type Core
require_once( CORE_LIB . '/class.types.php' );

// Layout Builder Component - STILL UNDER DEVELOPMENT DO NOT USE FOR PRODUCTION
require_once( CORE_LIB . '/class.layout.php' ); 
$GLOBALS['epanel_layout'] = new EPANEL_WPLayout();
	
// Framework Sections Handler - some features are still under development
require_once( CORE_LIB . '/class.sections.php' );

// Framework template handler
require_once( CORE_LIB . '/class.template.php' );

// Framework Posts and pages metabox handler
require_once( CORE_LIB . '/class.options.meta.php' );

// Framework Metaboxes layout handler
require_once( CORE_LIB . '/class.options.metapanel.php' );

// globalize metaboxes settings
$GLOBALS['metapanel_options'] =  new EPANEL_WPMetaPanel();

// Dynamic Typography component - still under development some features are hidden
require_once( CORE_LIB . '/class.typography.php' );

// Megamenu Component For WordPress

/*-----------------------------------------------------------------------------------*/
/* Options Framework Backend Interface Builder
/*-----------------------------------------------------------------------------------*/

require_once( CORE_LIB . '/class.options.ui.php' );

// Dynamic Css handler
require_once( CORE_LIB . '/class.css.php' );

// Template and sections handler
$GLOBALS['pl_section_factory'] = new EPANEL_WPSectionFactory();
require_once( THEME_CONFIG . '/config.templates.php' );

do_action('epanel_setup'); //hook

load_section_persistent(); // Load persistent section functions (e.g. custom post types)
if(is_admin()) load_section_admin(); // Load admin only functions from sections
do_global_meta_options(); // Load the global meta settings tab

	
/*-----------------------------------------------------------------------------------*/
/* WordPress framework additioanl required Components
/*-----------------------------------------------------------------------------------*/

// add thumbs and menus here....

// Add editor styling
//add_editor_style( 'core/css/editor-style.css' );

// set width for layout builder - still under development and not used for production
epanel_current_page_content_width(); 

/*-----------------------------------------------------------------------------------*/
/* Options Framework Admin Actions
/*-----------------------------------------------------------------------------------*/

require_once (CORE_LIB.'/actions.admin.php'); 

// Admin Options actions
require_once (CORE_LIB.'/actions.options.php');

// Dynamic layout builder sections
require_once (CORE_LIB.'/actions.site.php'); 

// Epanel hook
do_action('epanel_hook_init'); //hook

/*-----------------------------------------------------------------------------------*/
/* Load Framework Additional Components
/*-----------------------------------------------------------------------------------*/

//require_once(CORE_LIB.'/library.newmeta.php');

require_once(CORE.'/components/sidebar-generator.php');

require_once(TEMPLATEPATH . "/functions/tinymce/tinymce.php"); // additional button for wordpress editor

require_once(CORE.'/ecart/Ecart.php');

// component under construction do not enable

//require_once (CORE . '/components/shortcode-manager/shortcode-manager.php');

?>