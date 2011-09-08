<?php

if(__FILE__ == $_SERVER['SCRIPT_FILENAME'])
{
    header('HTTP/1.0 404 Not Found');
      exit;
}


/*
	TEMPLATES CONFIG
	
	This file is used to configure page template defaults and 
	to set up the HTML/JS sections that will be used in the site. 
	Platform themes should add sections through the appropriate hooks
	
*/

/*
	SECTION REGISTER 
	Register HTML sections for use in the theme.
*/
	
// Common WP 
	
	// Currently removed - some sections are still under development
	
// Sections With Custom Post Types
	
	epanel_register_section('EPANEL_WPScroller', 'scroller');
	
	epanel_register_section('EPANEL_WPSlides', 'slides'); // 'boxes'
	
	epanel_register_section('EPANEL_WPBxslider', 'bxslider');
	epanel_register_section('EPANEL_WPBxmini', 'bxmini');
	
	
// Sidebar Sections & Widgets
	
	// Currently removed - some sections are still under development
	
// Misc & Dependent Sections

	epanel_register_section('EPANEL_WPPage', 'page');
	
// Do a hook for registering sections
	do_action('epanel_register_sections'); //hook


/*
	TEMPLATE MAP
	
	This array controls the default template map of section in the theme
	Each top level needs a hook; and the top-level template needs to be included 
	as an arg in said hook...
*/
function the_template_map() {
	
	$template_map = array();
	
	$page_templates = epanel_get_page_templates();
	$content_templates = epanel_get_content_templates();
	
	$template_map['header'] = array(
		'hook' 			=> 'epanel_header', 
		'name'			=> 'Site Header',
		'markup'		=> 'content', 
		'sections' 		=> array( 'EPANEL_WPBranding' , 'EPANEL_WPNav', 'EPANEL_WPSecondNav' )
	);
	
	$template_map['footer'] = array(
		'hook' 			=> 'epanel_footer', 
		'name'			=> 'Site Footer', 
		'markup'		=> 'content', 
		'sections' 		=> array('EPANEL_WPFootCols')
	);
	
	$template_map['templates'] = array(
		'hook'			=> 'epanel_template', 
		'name'			=> 'Page Templates', 
		'markup'		=> 'content', 
		'templates'		=> $page_templates,
	);
	
	$template_map['main'] = array(
		'hook'			=> 'epanel_main', 
		'name'			=> 'Text Content Area',
		'markup'		=> 'copy', 
		'templates'		=> $content_templates,
	);
	
	$template_map['morefoot'] = array(
		'name'			=> 'Morefoot Area',
		'hook' 			=> 'epanel_morefoot',
		'markup'		=> 'content', 
		'version'		=> 'pro',
		'sections' 		=> array('EPANEL_WPMorefoot', 'EPANEL_WPTwitterBar')
	);
	
	$template_map['sidebar1'] = array(
		'name'			=> 'Sidebar 1',
		'hook' 			=> 'epanel_sidebar1',
		'markup'		=> 'copy', 
		'sections' 		=> array('PrimarySidebar')
	);
	
	$template_map['sidebar2'] = array(
		'name'			=> 'Sidebar 2',
		'hook' 			=> 'epanel_sidebar2',
		'markup'		=> 'copy', 
		'sections' 		=> array('SecondarySidebar')
	);
	
	$template_map['sidebar_wrap'] = array(
		'name'			=> 'Sidebar Wrap',
		'hook' 			=> 'epanel_sidebar_wrap',
		'markup'		=> 'copy', 
		'version'		=> 'pro',
		'sections' 		=> array()
	);
	
	return apply_filters('epanel_template_map', $template_map); 
}

function epanel_get_page_templates(){
	
	$page_templates = array(
		'default' => array(
				'name'			=> 'Default Page',
				'sections' 		=> array('EPANEL_WPContent')
		),
		'posts' => array(
				'name'			=> 'Posts Pages',
				'sections' 		=> array('EPANEL_WPContent')
			),
		"404" => array(
				'name'			=> '404 Error Page',
				'sections' 		=> array( 'EPANEL_WPNoPosts' ),
			),
		'single' => array(
				'name'			=> 'Single Post Page',
				'sections' 		=> array('EPANEL_WPContent')
			),
		'alpha' => array(
				'name'			=> 'Feature Page',
				'sections' 		=> array('EPANEL_WPFeatures', 'EPANEL_WPBoxes', 'EPANEL_WPContent'),
				'version'		=> 'pro'
			),
		'beta' => 	array(
				'name'			=> 'Carousel Page',
				'sections' 		=> array('EPANEL_WPCarousel', 'EPANEL_WPContent'),
				'version'		=> 'pro'
			),
		'gamma' => 	array(
				'name'			=> 'Box Page',
				'sections' 		=> array( 'EPANEL_WPHighlight', 'EPANEL_WPSoapbox', 'EPANEL_WPBoxes' ),
				'version'		=> 'pro'
			),
		'delta' => 	array(
				'name'			=> 'Highlight Page',
				'sections' 		=> array( 'EPANEL_WPHighlight', 'EPANEL_WPContent' ),
				'version'		=> 'pro'
			),
		'epsilon' => 	array(
				'name'			=> 'Banner Page',
				'sections' 		=> array( 'EPANEL_WPHighlight', 'EPANEL_WPBanners', 'EPANEL_WPContent' ),
				'version'		=> 'pro'
			),
		
	);

	return apply_filters('epanel_page_template_array', $page_templates); 
	
}

function epanel_get_content_templates(){
	$content_templates = array(
		'default' => array(
				'name'			=> 'Page Content Area',
				'sections' 		=> array('EPANEL_WPPostLoop', 'EPANEL_WPComments')
			),
		'posts' => array(
				'name'			=> 'Posts Page Content Area',
				'sections' 		=> array('EPANEL_WPPostsInfo','EPANEL_WPPostLoop', 'EPANEL_WPPagination')
			),

		'single' => array(
				'name'			=> 'Single Post Content Area',
				'sections' 		=> array('EPANEL_WPPostNav', 'EPANEL_WPPostLoop', 'EPANEL_WPShareBar', 'EPANEL_WPComments', 'EPANEL_WPPagination')
			)
	);
	
	return apply_filters('epanel_content_template_array', $content_templates); 
}

				

