<?php
/*-----------------------------------------------------------------------------------*/
/* Bx Slider Component Settings */
/*-----------------------------------------------------------------------------------*/

class EPANEL_WPBxslider extends EPANEL_WPSection {

   function __construct( $registered_settings = array() ) {
	
		$name = __('Bxslider', 'epanel');
		$id = 'bxslider';
		
		$default_settings = array(
			'description' 	=> 'Add additional settings for bx slider',
			'icon'			=> CORE_IMAGES.'/admin/images-stack.png', 
			'dependence'	=> 'EPANEL_WPSlides', 
			'posttype'		=> 'slides',
		);
		
		$settings = wp_parse_args( $registered_settings, $default_settings );
		
		parent::__construct($name, $id, $settings);    
   }


/*-----------------------------------------------------------------------------------*/
/* Metabox options array */
/*-----------------------------------------------------------------------------------*/

	function section_persistent(){

						$type_meta_array = array(
						
							'bx_type' => array(
									'default' 	=> 'image',
									'std' => 'image',
									'type' 		=> 'select',
									'selectvalues'	=> array(
										'image'		=> array("name" => 'Image Only Type'),
										'image_link'		=> array("name" => 'Image Only With Link Type'),
										'caption_right'	=> array("name" => 'Full Image with long caption right'),
										'caption_left' => array("name" => 'Full image with long caption left'),
										'title_left' => array("name" => 'Full image with Small Caption Title left'),
										'title_right' => array("name" => 'Full image with Small Caption Title right'),
										'center' => array("name" => 'Full image with centered small title'),
										'video_mode' => array("name" => 'Full Width Video (Change width and height paramenter)')
										
									),
									'inputlabel' 	=> 'Slide Type Mode',
									'title' 	=> 'Slide Singular Type Mode',
									'desc' 	=> 'Choose the slide type for the following slide. <br/><br/>Please note: if you want to add captions you have to write the caption text using the above editor but don\'t left unclosed html tags or you will break the slider.
									<strong>If you don\' want to use the caption just select the option "Image Only Type".</strong>',
									
							),
							
							'bx_video_code' => array(
									
									'default'	=> '180',
									'type'		=> 'textarea',
									'inputlabel'	=> 'Bx Slider Video Code',
									'title'		=> 'Bx Slider Video',						
									'desc'	=> 'If as slide type mode you\'ve select a video mode, please copy and paste the full video embed code into this textarea. <br/> <strong>Please note: when you add a video you have to change the width and height paramenter to width : 960 and height to 360 (or to your edited slider height settings from epanel)</strong>',
									
							),
							
							'bx_link_code' => array(
									
									'default'	=> '',
									'type'		=> 'text',
									'inputlabel'	=> 'Bx Slider Link Url',
									'title'		=> 'Bx Slider Link Url',						
									'desc'	=> 'You can add a link to the slide, this setting works only when the slide type is "Image Only With Link Type"',
									
							),
														
					);

					$post_types = array($this->settings['posttype']);
										
					global $slides_meta_panel;
				

						$type_metatab_settings = array(
							'id' 		=> 'bxslider-type-metatab',
							'name' 		=> "Bx Slider Settings",
							'icon' 		=> $this->icon,
						);

						$slides_meta_panel->register_tab( $type_metatab_settings, $type_meta_array );

						
						$metatab_array = array(

														
							);

						$metatab_settings = array(
								'id' => 'bx_meta',
								'name' => "Bx Section",
								'icon' => $this->icon
							);
						
						
						//register_metatab($metatab_settings, $metatab_array);
	}

			

// End of Section Class //
}

