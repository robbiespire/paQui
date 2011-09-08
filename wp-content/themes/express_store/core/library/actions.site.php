<?php
if(__FILE__ == $_SERVER['SCRIPT_FILENAME'])
{
    header('HTTP/1.0 404 Not Found');
      exit;
}
// Theme Global
global $epanel_template;

// Global access to conditionals


/*-----------------------------------------------------------------------------------*/
/* Our framework is awesome - new hooks actions and filters here
/*-----------------------------------------------------------------------------------*/

add_action('epanel_before_html', 'build_epanel_template');

// In Admin
add_action('admin_head', 'build_epanel_template');

// In Site
//add_action('wp_head', array(&$epanel_template, 'print_template_section_headers'));
add_action('wp_print_styles', 'workaround_epanel_template_styles'); // Used as workaround on WP login page (and other pages with wp_print_styles and no wp_head/epanel_before_html)
add_action('epanel_head', array(&$epanel_template, 'hook_and_print_sections'));
//add_action('wp_footer', array(&$epanel_template, 'print_template_section_scripts'));

// Global Page Id for reference
add_action('epanel_before_html', 'epanel_id_setup');

// Callback for child theme - development purpose only - too hard for production
add_filter('epanel_page_template_array', 'epanel_add_page_callback');

// Dynamic Css Enq
//add_action('wp_head', 'do_dynamic_css');

// New links for the administration bar but we hate it - we should remove this.
add_action( 'admin_bar_menu', 'epanel_settings_menu_link', 100 );