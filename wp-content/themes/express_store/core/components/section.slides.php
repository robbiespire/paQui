<?php
/*

	Section: EPANEL_WP Home Slider
	Description: Creates the homepage slider manager
	Version: 1.0.0
	
*/

class EPANEL_WPSlides extends EPANEL_WPSection {

   function __construct( $registered_settings = array() ) {
	
		$name = __('EPANEL_WP Slides', 'epanel');
		$id = 'slides';
		
		$default_settings = array(
			'description' 	=> 'Homepage Slider Manager - choose the slider type from E-Panel Theme Settings',
			'icon'			=> CORE_IMAGES.'/admin/images-stack.png', 
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
					'label' => __('Home Slider', 'epanel'),  
					'singular_label' => __('Slide', 'epanel'),
					'description' => 'Creates Slides for the homepage',
					'menu_icon' => CORE_IMAGES.'/admin/images-stack.png',
					'supports' => array('title','editor','thumbnail','page-attributes'/*,'author','excerpt'*/),
				);
			
			
			$taxonomies = array(
				
			);
			$columns = array(
				"cb" => "<input type=\"checkbox\" />",
				"title" => "Title",
				"bdescription" => "Slide Content or Caption",
				"bmedia" => "Media"
				//"slide-sets" => "Box Sets"
			);
		
			$column_value_function = 'slide_column_display';
		
			$this->post_type = new EPANEL_WPPostType($this->id, $args, $taxonomies, $columns, $column_value_function);
		
				/* Set default posts if none are present */
				$this->post_type->set_default_posts('epanel_default_slides');


		/*
			Meta Options
		*/
		
				/*
					Create meta fields for the post type
				*/
					$type_meta_array = array(
						'nivo_url' 		=> array(
								
								'type' => 'text',					
								'title' => 'Slide Url',
								'label'			=> 'Enter Slide Link url if needed',
								'desc' => 'You can add an url to this slide, if you don\' need an url leave this field blank. <strong>Note : this setting affects only Nivo Slider</strong>'
							),
						
						'nivo_caption' 		=> array(
								
								'type' => 'text',					
								'title' => 'Nivo Caption',
								'label'			=> 'Enter Slide Caption if needed',
								'desc' => 'You can add a caption to this slide, if you don\' need a caption leave this field blank. <strong>Note : this setting affects only Nivo Slider</strong>'
							),
					);

					$post_types = array($this->id); 
					
					$type_metapanel_settings = array(
							'id' 		=> 'slides-metapanel',
							'name' 		=> THEMENAME." Slides Options",
							'posttype' 	=> $post_types,
						);
					
					global $slides_meta_panel;
					
					$slides_meta_panel =  new EPANEL_WPMetaPanel( $type_metapanel_settings );
					
					$type_metatab_settings = array(
						'id' 		=> 'slides-type-metatab',
						'name' 		=> "Nivo Slider Slides Settings",
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
							'id' => 'fslides_meta',
							'name' => "Boxes Section",
							'icon' => $this->icon
						);

					//register_metatab($metatab_settings, $metatab_array);
	}

   
	
// End of Section Class //
}

function slide_column_display($column){
	global $post;
	
	switch ($column){
		case "bdescription":
			the_excerpt();
			break;
		case "bmedia":
			
			if (get_post_meta($post->ID, 'bx_type', true ) !=='video_mode') {
			
				if(has_post_thumbnail($post->ID)){
					
					$thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id( $post->ID ));
					
					echo '<img src="'.$thumbnail[0].'" style="max-width: 80px; margin: 10px; border: 1px solid #ccc; padding: 5px; background: #fff"/>';	
				}
		} else { ?>
			
			<p>A video has been added to the following slide - <a href="#" onClick="jQuery(this).parent().parent().parent().find('.fullcode').slideToggle();">View Full Video Code &raquo;</a></p>
			
			<div style="display:none;" class="fullcode">
			<xmp><?php echo get_post_meta($post->ID, 'bx_video_code', true ); ?></xmp>
			</div>
		
			
		<?php }
			
			break;
		case "slide-sets":
			echo get_the_term_list($post->ID, 'slide-sets', '', ', ','');
			break;
	}
}

		
function epanel_default_slides($post_type){
	
	$default_posts =  array_reverse(get_default_slides());
	
	foreach($default_posts as $dpost){
		// Create post object
		$default_post = array();
		$default_post['post_title'] = $dpost['title'];
		$default_post['post_content'] = $dpost['text'];
		$default_post['post_type'] = $post_type;
		$default_post['post_status'] = 'publish';
		
		$newPostID = wp_insert_post( $default_post );

		update_post_meta($newPostID, 'the_slide_icon', $dpost['media']);
		
		$object_terms = array('default-slides');
		
		wp_set_object_terms($newPostID, 'default-slides', 'slide-sets');
		
		// Add other default sets, if applicable.
		if(isset($dpost['set'])) wp_set_object_terms($newPostID, $dpost['set'], 'slide-sets', true);

	}
}
