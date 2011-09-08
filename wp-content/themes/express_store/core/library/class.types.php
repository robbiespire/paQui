<?php

if(__FILE__ == $_SERVER['SCRIPT_FILENAME'])
{
    header('HTTP/1.0 404 Not Found');
      exit;
}

/*-----------------------------------------------------------------------------------*/
/* Options Framework Custom post types handler
/*-----------------------------------------------------------------------------------*/
class EPANEL_WPPostType {

	var $id;		// Root id for section.
	var $settings;	// Settings for this section
	
	
	// php 5
	function __construct($id, $settings, $taxonomies = array(), $columns = array(), $column_display_function = '') {
		$this->id = $id;
		$this->taxonomies = $taxonomies;
		$this->columns = $columns;
		$this->columns_display_function = $column_display_function;
		
		$defaults = array(
				
				'label' 			=> 'Posts',
				'singular_label' 	=> 'Post',
				'description' 		=> null,
				'public' 			=> false,  
				'show_ui' 			=> true,  
				'capability_type'	=> 'post',  
				'hierarchical' 		=> false,  
				'rewrite' 			=> false,
				'show_in_menu' 		=> null,  
				'supports' 			=> array('title', 'editor', 'thumbnail','excerpt'), 
				'menu_icon' 		=> CORE_IMAGES."/favicon-epanel.png", 
				'taxonomies'		=> array(),
				'menu_position'		=> 57
			);
		
		$this->settings = wp_parse_args($settings, $defaults); // settings for post type
		
		$this->register_post_type();
		$this->register_taxonomies();
		$this->register_columns();
	
	}
	
	
	function register_post_type(){
		register_post_type( $this->id , array(  
				'labels' => array(
							'name' 			=> $this->settings['label'],
							'singular_name' => $this->settings['singular_label'],
							'add_new'		=> __('Add New ', 'epanel') . $this->settings['singular_label'], 
							'add_new_item'	=> __('Add New ', 'epanel') . $this->settings['singular_label'], 
							'edit'			=> __('Edit ', 'epanel') . $this->settings['singular_label'],
							'edit_item'		=> __('Edit ', 'epanel') . $this->settings['singular_label'], 
							'view'			=> __('View ', 'epanel') . $this->settings['singular_label'],
							'view_item'		=> __('View ', 'epanel') . $this->settings['singular_label'],
						),
			
	 			'label' 			=> $this->settings['label'],  
				'singular_label' 	=> $this->settings['singular_label'],
				'description' 		=> $this->settings['description'],
				'public' 			=> $this->settings['public'],  
				'show_ui' 			=> $this->settings['show_ui'],
				'show_in_menu' 		=> $this->settings['show_in_menu'],  
				'capability_type'	=> $this->settings['capability_type'],  
				'hierarchical' 		=> $this->settings['hierarchical'],  
				'rewrite' 			=> $this->settings['rewrite'],  
				'supports' 			=> $this->settings['supports'], 
				'menu_icon' 		=> $this->settings['menu_icon'], 
				'taxonomies'		=> $this->settings['taxonomies'],
				'menu_position'		=> $this->settings['menu_position']
				
			));
	}
	
	function register_taxonomies(){
		
		foreach($this->taxonomies as $tax_id => $tax_settings){
			
			register_taxonomy($tax_id, array($this->id), array(
					"hierarchical" => true, 
					"label" => $tax_settings['label'], 
					"singular_label" => $tax_settings['singular_label'], 
					"rewrite" => true
				)
			);
		}
		
	}
	
	function register_columns(){
		
		add_filter("manage_edit-{$this->id}_columns", array(&$this, 'set_columns'));
		
		add_action("manage_posts_custom_column",  array(&$this, 'set_column_values'));
	}
		
	function set_columns($columns){ 
		
		return $this->columns; 
	}
	
	function set_column_values($wp_column){
		
		call_user_func($this->columns_display_function, $wp_column);
						
	}
	
	function set_default_posts($callback){
	
		if(!get_posts('post_type='.$this->id)){
			
			call_user_func($callback, $this->id);
		}
						
	}
	
	
	

}
/////// END OF PostType CLASS ////////

