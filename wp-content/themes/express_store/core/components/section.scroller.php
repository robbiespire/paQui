<?php
/*

	Section: EPANEL_WP Home Slider
	Description: Creates the homepage slider manager
	Version: 1.0.0
	
*/

class EPANEL_WPScroller extends EPANEL_WPSection {

   function __construct( $registered_settings = array() ) {
	
		$name = __('EPANEL_WP Scroller', 'epanel');
		$id = 'scroller';
		
		$default_settings = array(
			'description' 	=> 'Homepage Scroller Manager',
			'icon'			=> CORE_IMAGES.'/admin/scroller.png', 
		);
		
		$settings = wp_parse_args( $registered_settings, $default_settings );
		
		parent::__construct($name, $id, $settings);    
   }


	/*
		Loads php that will run on every page load (admin and site)
		Used for creating administrative information, like post types
	*/

	function section_persistent(){
		/* 
			Create Custom Post Type 
		*/
			$args = array(
					'label' => __('Home Scroller', 'epanel'),  
					'singular_label' => __('Item', 'epanel'),
					'description' => 'Creates items for the homepage scroller',
					'menu_icon' => CORE_IMAGES.'/admin/scroller.png',
					'supports' => array('title','thumbnail','page-attributes'/*,'author','excerpt'*/),
				);
			
			
			$taxonomies = array(
				
			);
			$columns = array(
				"cb" => "<input type=\"checkbox\" />",
				"title" => "Item Title",
				"scroller-desc" => "Scroller Item Redirection",
				"scroller-media" => "Item Thumbnail"
				//"slide-sets" => "Box Sets"
			);
		
			$column_value_function = 'scroller_column_display';
		
			$this->post_type = new EPANEL_WPPostType($this->id, $args, $taxonomies, $columns, $column_value_function);
		
				/* Set default posts if none are present */
				$this->post_type->set_default_posts('epanel_default_scrolls');


		/*
			Meta Options
		*/
		
				/*
					Create meta fields for the post type
				*/
					$type_meta_array = array(
						'item_url' 		=> array(
								
								'type' => 'text',					
								'title' => 'Scroller Item Url',
								'label'			=> 'Enter scroller item url if needed',
								'desc' => 'You can add an url to this scroll item, if you don\' need an url leave this field blank.'
							),
					);

					$post_types = array($this->id); 
					
					$type_metapanel_settings = array(
							'id' 		=> 'scroller-metapanel',
							'name' 		=> "Homepage Scroller Settings",
							'posttype' 	=> $post_types,
							
							
						);
					
					global $slides_meta_panel;
					
					$slides_meta_panel =  new EPANEL_WPMetaPanel( $type_metapanel_settings );
					
					$type_metatab_settings = array(
						'id' 		=> 'scroller-type-metatab',
						'name' 		=> "Homepage Scroller Settings",
						'icon' 		=> $this->icon,
					);

					$slides_meta_panel->register_tab( $type_metatab_settings, $type_meta_array );
						
						
		/*
			Build Ind. Page and Post Options
		*/
					$metatab_array = array(
						
						// Not required
						
					);

					$metatab_settings = array(
							'id' => 'scroller_meta',
							'name' => "Scroller Section",
							'icon' => $this->icon
						);

					//register_metatab($metatab_settings, $metatab_array);
	}

   
	
// End of Section Class //
}

function scroller_column_display($column){
	global $post;
	
	switch ($column){
		case "scroller-desc":
			
			if (get_post_meta($post->ID, 'item_url', true ) == '') { echo 'This item has no redirection'; } else { echo get_post_meta($post->ID, 'item_url', true ); } 
			
			
			break;
		case "scroller-media":
			
			if(has_post_thumbnail($post->ID)){
				
				$thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id( $post->ID ));
				
				echo '<img src="'.$thumbnail[0].'" style="max-width: 80px; margin: 10px; border: 1px solid #ccc; padding: 5px; background: #fff"/>';	
			}
			
			break;
		case "scroller-sets":
			echo get_the_term_list($post->ID, 'scroller-sets', '', ', ','');
			break;
	}
}

		
function epanel_default_scrolls($post_type){
	
	$default_posts =  array_reverse(get_default_scrolls());
	
	foreach($default_posts as $dpost){
		// Create post object
		$default_post = array();
		$default_post['post_title'] = $dpost['title'];
		$default_post['post_content'] = $dpost['text'];
		$default_post['post_type'] = $post_type;
		$default_post['post_status'] = 'publish';
		
		$newPostID = wp_insert_post( $default_post );

		update_post_meta($newPostID, 'the_scroller_icon', $dpost['media']);
		
		$object_terms = array('default-scrolls');
		
		wp_set_object_terms($newPostID, 'default-scrolls', 'scroller-sets');
		
		// Add other default sets, if applicable.
		if(isset($dpost['set'])) wp_set_object_terms($newPostID, $dpost['set'], 'scroller-sets', true);

	}
}
