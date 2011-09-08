<?php 

if(__FILE__ == $_SERVER['SCRIPT_FILENAME'])
{
    header('HTTP/1.0 404 Not Found');
      exit;
}

/*-----------------------------------------------------------------------------------*/
/* Define Options Framework Core Informations and version
/*-----------------------------------------------------------------------------------*/

$theme_data = get_theme_data(TEMPLATEPATH . '/style.css');
define('CORE_VERSION', $theme_data['Version']);


/*-----------------------------------------------------------------------------------*/
/* Options Framework Theme Name and base paths
/*-----------------------------------------------------------------------------------*/

$theme = $theme_data['Title'];

define('THEMENAME', $theme);
define('CHILDTHEMENAME', get_option('stylesheet'));


define('PARENT_DIR', TEMPLATEPATH);
define('CHILD_DIR', STYLESHEETPATH);

define('PARENT_URL', get_template_directory_uri());
define('CHILD_URL', get_stylesheet_directory_uri());
define('CHILD_IMAGES', CHILD_URL . '/images');

/*-----------------------------------------------------------------------------------*/
/* Options Framework Settings Database Storage
/*-----------------------------------------------------------------------------------*/

define('EPANEL_SETTINGS', apply_filters('epanel_settings_field', 'epanel-settings'));
define('EPANEL_LAYOUT_SETTINGS', apply_filters('epanel_layout_settings_field', 'epanel-layout-settings'));	
define('EPANEL_SECTION_SETTINGS', apply_filters('epanel_section_settings_field', 'epanel-section-settings'));	

/*-----------------------------------------------------------------------------------*/
/* Options Framework Core Additional Components Paths - not needed at this moment
/*-----------------------------------------------------------------------------------*/

define('CORE_PLUGINS', CORE . '/plugins');
define('CORE_CLASSES', CORE . '/classes');
define('CORE_ADMIN', CORE . '/admin');
define('CORE_INITS', CORE . '/inits');
define('CORE_LIB', CORE . '/library');
define('CORE_TEMPLATES', CORE . '/templates');
define('CORE_CSS_DIR', CORE . '/css');

/*-----------------------------------------------------------------------------------*/
/* Options Framework Theme Paths
/*-----------------------------------------------------------------------------------*/

define('THEME_CONFIG', TEMPLATEPATH . '/config');
define('THEME_WIDGETS', THEME_CONFIG . '/widgets');
define('THEME_SECTIONS', TEMPLATEPATH . '/core/components');

/*-----------------------------------------------------------------------------------*/
/* Options Framework Dynamic Css Writer - disabled at this moment still under dev
/*-----------------------------------------------------------------------------------*/

$main_upload_dir = wp_upload_dir();
define('MAIN_UPLOAD_DIR', $main_upload_dir['baseurl']);
define('EPANEL_UPLOAD_URL', $main_upload_dir['baseurl'] . '/epanel');
define('EPANEL_UPLOAD_DIR', $main_upload_dir['basedir'] . '/epanel');

define('EPANEL_DCSS', EPANEL_UPLOAD_DIR . '/dynamic.css');
define('EPANEL_DCSS_URI', EPANEL_UPLOAD_URL . '/dynamic.css');


/*-----------------------------------------------------------------------------------*/
/* Framework additional web paths
/*-----------------------------------------------------------------------------------*/

define('THEME_ROOT', get_template_directory_uri());
define('CORE_ROOT', THEME_ROOT . '/'.CORENAME);
define('CONFIG_ROOT', THEME_ROOT . '/config');
define('SECTION_ROOT', THEME_ROOT . '/core/components');
	
/*-----------------------------------------------------------------------------------*/
/* Framework Core Paths
/*-----------------------------------------------------------------------------------*/

define('CORE_JS', CORE_ROOT . '/js');
define('CORE_CSS', CORE_ROOT . '/css');
define('CORE_IMAGES', CORE_ROOT . '/images');

/*-----------------------------------------------------------------------------------*/
/* Framework Theme Paths
/*-----------------------------------------------------------------------------------*/

define('THEME_CSS', THEME_ROOT . '/css');
define('THEME_JS', THEME_ROOT . '/js');
define('THEME_IMAGES', THEME_ROOT . '/images');

/*-----------------------------------------------------------------------------------*/
/* Additional Paths for additional development purpose
/*-----------------------------------------------------------------------------------*/

define('EPANEL_PRO', TEMPLATEPATH . '/pro' );
define('PRO_SECTIONS', EPANEL_PRO . '/core/components');
define('EPANEL_DEV', TEMPLATEPATH . '/dev' );

define('EPANEL_PRO_ROOT', THEME_ROOT . '/pro' );
define('PRO_SECTION_ROOT', EPANEL_PRO_ROOT . '/core/components' );

/*-----------------------------------------------------------------------------------*/
/* Additional framework required settings - only for development not production
/*-----------------------------------------------------------------------------------*/

// Removed

/*-----------------------------------------------------------------------------------*/
/* Hook and filter runner
/*-----------------------------------------------------------------------------------*/

$GLOBALS['epanel_user_pages'] = array();

$GLOBALS['global_meta_options'] = array();


