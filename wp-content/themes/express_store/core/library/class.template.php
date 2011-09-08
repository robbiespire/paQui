<?php

if(__FILE__ == $_SERVER['SCRIPT_FILENAME'])
{
    header('HTTP/1.0 404 Not Found');
      exit;
}
/*-----------------------------------------------------------------------------------*/
/* Template handler class - under development not for production
/*-----------------------------------------------------------------------------------*/
class EPANEL_WPTemplate {

	var $id;		// Root id for section.
	var $name;		// Name for this section.
	var $settings;	// Settings for this section
	
	var $layout;	// Layout type selected
	var $sections = array(); // HTML sections/effects for the page
	
	
	//php5
	function __construct( $postID = null ) {
		
		if(is_admin()){
			$this->template_type = $this->admin_get_page_template();
			$this->main_type = $this->admin_set_main_type();
		}else{
			$this->template_type = $this->get_page_template();
			$this->main_type = $this->set_main_type();
		}

		$this->map = $this->get_map();
	
		$this->get_loaded_sections();
	}
	

	function set_main_type(){

		if(is_home() || is_tag() || is_search() || is_archive() || is_category()){
			return 'posts';
		}elseif(is_single()){
			return 'single';
		}elseif(is_404()){
			return '404';
		} else { 
			return 'default';
		}
	}
	function get_page_template(){
		global $post;
		
		if(is_404()){ return '404';}
		elseif(is_home() || is_tag() || is_search() || is_archive() || is_category()){ return 'posts';}
		elseif(is_single()){return 'single';}
		elseif(is_page_template()){
			/*
				Strip the page. and .php from page.[template-name].php
			*/
			$page_filename = str_replace('.php', '', get_post_meta($post->ID,'_wp_page_template',true));
			$template_name = str_replace('page.', '', $page_filename);
			return $template_name;
		}else{return 'default';}
		
	}
	
	
		function admin_set_main_type(){
			global $post; 
			if(isset($post) && $post->post_type == 'post'){
				return 'single';
			} elseif( isset($_GET['page']) && $_GET['page'] == 'epanel' ){
				return 'posts';
			} else {
				return 'default';
			}
		}
	
		function admin_get_page_template(){
			global $post;
			if(isset($post) && $post->post_type == 'post'){
				return 'single';
			} elseif( isset($_GET['page']) && $_GET['page'] == 'epanel' ){
				return 'posts';
			} elseif(isset($post) && !empty($post->page_template) && $post->post_type == "page") {
				$page_filename = str_replace('.php', '', $post->page_template);
				$template_name = str_replace('page.', '', $page_filename);
				return $template_name;
			} elseif(isset($post) && get_post_meta($post->ID,'_wp_page_template',true)){
				$page_filename = str_replace('.php', '', get_post_meta($post->ID,'_wp_page_template',true));
				$template_name = str_replace('page.', '', $page_filename);
				return $template_name;
			} else {
				return 'default';
			}
		}
	
	
	
	function get_loaded_sections(){
		
		global $post;
		$this->all_template_sections = array();
		$this->default_all_template_sections = array();
		
		//Global Section Control Option
		$section_control = epanel_option('section-control');
		
		
		
		foreach( $this->map as $hook_id => $hook_info ){
			
			
			$template_area_sections = array();
			
			if( $hook_id == 'main' ){
			
				
				if(isset($hook_info['templates'][$this->main_type]['sections'])){
					
					$template_area_sections = $hook_info['templates'][$this->main_type]['sections'];
				
				} elseif(isset($hook_info['templates']['default']['sections'])) {
					$template_area_sections = $hook_info['templates']['default']['sections'];
				}
				
			} elseif( $hook_id == 'templates' ) {
				
				if(isset($hook_info['templates'][$this->template_type]['sections'])){

					$template_area_sections = $hook_info['templates'][$this->template_type]['sections'];

				} elseif(isset($hook_info['templates']['default']['sections'])) {
					$template_area_sections = $hook_info['templates']['default']['sections'];
				}
				
			} elseif(isset($hook_info['sections'])) { 
				
				// Get Sections assigned in map
				$template_area_sections = $hook_info['sections'];
	
			} else {
				
				$template_area_sections = array();
				
			}
			
			// Set All Sections As Defined By the Map
			if(is_array($template_area_sections)) $this->default_all_template_sections = array_merge($this->default_all_template_sections, $template_area_sections);
			
			// Remove sections deactivated by Section Control
			$template_area_sections = $this->unset_hidden_sections($template_area_sections, $hook_id);
			
			// Set Property after Template Hook Args
			$this->$hook_id = $template_area_sections;
			
			// Create an array with all sections used on current page - 
			if(is_array($this->$hook_id)) $this->all_template_sections = array_merge($this->all_template_sections, $this->$hook_id);
			
		}
		
	}
	
	function unset_hidden_sections($template_area_sections, $hook_id){
			
		global $post;

		//Global Section Control Option
		$section_control = epanel_option('section-control');
		
		
		if(is_array($template_area_sections)){
		
			foreach($template_area_sections as $key => $section){
				
				 
				if($hook_id == 'templates') {
					$template_slug = $hook_id.'-'.$this->template_type;
				} elseif ($hook_id == 'main'){
					$template_slug = $hook_id.'-'.$this->main_type;
				} else {
					$template_slug = $hook_id;
				}
				
				$sc = (isset($section_control[$template_slug][$section])) ? $section_control[$template_slug][$section] : null;

					if( isset($sc['hide']) && (!isset($post) || ( isset($post) && !m_epanel('_show_'.$section, $post->ID ))) && (!is_home() || ( is_home() && !isset($sc['posts-page']['show']))) ){
						unset($template_area_sections[$key]);
					} elseif ( (is_home() && isset($sc['posts-page']['hide'])) || (isset($post) && m_epanel('_hide_'.$section, $post->ID ))) {
						unset($template_area_sections[$key]);
					} 
				
				
			}
			
		}
		
		return $template_area_sections;
		
	}
	
	
	/*
		Hook section areas to actual theme hooks
	*/
	function hook_and_print_sections(){
		
		foreach($this->map as $hook_id => $hook_info){

			add_action($hook_info['hook'], array(&$this, 'print_section_html'));

		}		
		
	}

	/*
		Called by hooks in the theme with args
	*/
	function print_section_html( $hook_id ){
	
		global $pl_section_factory;
		global $post;
		global $epanel_post;		
		
		$section_control = epanel_option('section-control');

		if( is_array( $this->$hook_id ) ){

			$markup_type = $this->map[$hook_id]['markup'];

			foreach( $this->$hook_id as $section ){

				$template_slug = ($hook_id == 'templates' || $hook_id == 'main') ? $hook_id.'-'.$this->template_type : $hook_id;
				
				$sc = (isset($section_control[$template_slug][$section])) ? $section_control[$template_slug][$section] : null;
				
				if(isset($pl_section_factory->sections[$section]) && is_object($pl_section_factory->sections[ $section ])){
					$pl_section_factory->sections[ $section ]->before_section( $markup_type );
					
					// If overridden in child theme get that, if not load the class template function
					$pl_section_factory->sections[ $section ]->section_template_load();
					
					$pl_section_factory->sections[ $section ]->after_section( $markup_type );
				}
			
				$post = $epanel_post; // Set the $post variable back to the default for the page (prevents sections from messing with others)
	
			}
		}
	}
	
	/*
		Used for when the default map is updated 
	*/
	function update_template_config($map){
		
		foreach(the_template_map() as $hook_id => $hook_info){
			if( !isset( $map[$hook_id] ) || !is_array( $map[$hook_id] ) ){
				$map[$hook_id] = $hook_info;
			}
		
			$map[$hook_id]['name'] = $hook_info['name'];
			$map[$hook_id]['hook'] = $hook_info['hook'];
			$map[$hook_id]['markup'] = $hook_info['markup'];
			
			if(isset($hook_info['templates']) && is_array($hook_info['templates'])){
				foreach($hook_info['templates'] as $sub_template => $stemplate){
					if( !isset( $map[$hook_id]['templates'][$sub_template] ) ){
						$map[$hook_id]['templates'][$sub_template] = $stemplate;
					}
					
					$map[$hook_id]['templates'][$sub_template]['name'] = $stemplate['name'];
				}
			}
		}
		
		return $map;
		
	}

	function get_map(){
		
		// Get Section / Layout Map
		if(get_option('epanel_template_map') && is_array(get_option('epanel_template_map'))){
			$map = get_option('epanel_template_map');
			return $this->update_template_config($map);
			
		}else{
			update_option('epanel_template_map', the_template_map());
			return the_template_map();
		}
	}
	
	function reset_templates_to_default(){
		update_option('epanel_template_map', the_template_map());
	}

	function print_template_section_headers(){

		global $pl_section_factory;
		if(is_array($this->all_template_sections)){ 
			
			foreach($this->all_template_sections as $section){
				if(isset($pl_section_factory->sections[$section]) && is_object($pl_section_factory->sections[$section])){
					$pl_section_factory->sections[$section]->section_head();
				}
			}
			
		}
		
	}
	
	
	/**
	 * Print Section Styles (Hooked to wp_print_styles)
	 *
	 */
	function print_template_section_styles(){

		global $pl_section_factory;
	
		if(is_array($this->all_template_sections)){
			foreach($this->all_template_sections as $section){
				if(isset($pl_section_factory->sections[$section]) && is_object( $pl_section_factory->sections[$section] )){
					$pl_section_factory->sections[$section]->section_styles();
				}
			}	
		}
	
	}
	

	
	function print_template_section_scripts(){

		global $pl_section_factory;

		foreach($this->all_template_sections as $section){

			if(isset($pl_section_factory->sections[$section]) && is_object($pl_section_factory->sections[$section])){
				
				$section_scripts = $pl_section_factory->sections[$section]->section_scripts();
				
				
				if(is_array( $section_scripts )){
					
					foreach( $section_scripts as $js_id => $js_atts){
						
						$defaults = array(
								'version' => '1.0',
								'dependancy' => null,
								'footer' => true
							);

						$parsed_js_atts = wp_parse_args($js_atts, $defaults);
						
						wp_register_script($js_id, $parsed_js_atts['file'], $parsed_js_atts['dependancy'], $parsed_js_atts['version'], true);

						wp_print_scripts($js_id);

					}

				}
			}

		}
	}
	


}
/////// END OF TEMPLATE CLASS ////////

// Build the template
function build_epanel_template(){	
	$GLOBALS['epanel_template'] = new EPANEL_WPTemplate;	
}

// Save layout 
function save_template_map($templatemap){	
	update_option('epanel_template_map', $templatemap);
}

// showing posts ?
function epanel_is_posts_page(){	
	if(is_home() || is_search() || is_archive() || is_category() || is_tag()) return true; 
	else return false;
}

function epanel_non_meta_data_page(){
	if(epanel_is_posts_page() || is_404()) return true; 
	else return false;
}

// parse html
function epanel_ob_section_template($section){

	/*
		Start Output Buffering
	*/
	ob_start();
	
	/*
		Run The Section Template
	*/
	$section->section_template( true );

	/*
		Clean Up Buffered Output
	*/
	ob_end_clean();

	
}

function reset_templates_to_default(){	
	EPANEL_WPTemplate::reset_templates_to_default();
}

// we should replace this....
function workaround_epanel_template_styles(){	
	global $epanel_template; 
	if(is_object($epanel_template)){
		$epanel_template->print_template_section_styles();
	}
	else return;
}

// Layout builder points
function epanel_process_template_map( $callback , $args = array()){

	$defaults = array(
			
			'section_point' 	=> false,
			'area_point' 		=> false,
			'count_point' 		=> false,
			'count_string' 		=> false,
			'area_titles'		=> false, 
			'area_point'		=> false,
		);
		
	$settings = wp_parse_args($args, $defaults); // settings for post type
	
	global $epanel_template;
	
	$count = 0;
	
	foreach($epanel_template->map as $template => $template_info):

		
		if($template == 'templates'){
			
			if($settings['area_titles']) echo '<span class="area_title">Template Area</span>';
			
			if(is_array($template_info['templates'][$epanel_template->template_type]['sections'])){
				foreach( $template_info['templates'][$epanel_template->template_type]['sections'] as $section ):
			
					$template_slug = 'templates-'.$epanel_template->template_type;
					call_user_func( $callback, $section, $template_slug, 'templates', $epanel_template->template_type);
				
					if( $settings['section_point'] ){
						$count++;  if($count == $settings['count_point']) { echo $settings['count_string']; $count = 0; }
					}
				endforeach;
			}
			if( $settings['area_point'] ){
				
				$count++;  if($count == $settings['count_point']) { echo $settings['count_string']; $count = 0; }
			}
			
		}elseif($template == 'main'){
		
			if($settings['area_titles']) echo '<span class="area_title">Main Content Area</span>';
			
			if(is_array($template_info['templates'][$epanel_template->main_type]['sections'])){
				foreach( $template_info['templates'][$epanel_template->main_type]['sections'] as $section ):
				
					$template_slug = 'main-'.$epanel_template->main_type;
					call_user_func( $callback, $section, $template_slug, 'main', $epanel_template->main_type);
				
					if( $settings['section_point'] ){
						$count++;  if($count == $settings['count_point']) { echo $settings['count_string']; $count = 0; }
					}
				endforeach;
			}
		
			if( $settings['area_point'] ){
				$count++;  if($count == $settings['count_point']) { echo $settings['count_string']; $count = 0; }
			}
			
		}else{
		
			if($settings['area_titles']) echo '<span class="area_title">'. ucwords(str_replace('_',' ',$template)) .'</span>';
			
			if(is_array($template_info['sections'] )){
				 foreach( $template_info['sections'] as $section ):
			
					$template_slug = $template;
					call_user_func( $callback, $section, $template_slug, $template, $template);
				
					if( $settings['section_point'] ){
						$count++;  if($count == $settings['count_point']) { echo $settings['count_string']; $count = 0; }
					}
				 endforeach;
			}
			
			if( $settings['area_point'] ){
				$count++;  if($count == $settings['count_point']) { echo $settings['count_string']; $count = 0; }
			}
		}
	
			
	endforeach;
}

