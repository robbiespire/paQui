<?php
/*-----------------------------------------------------------------------------------*/
/* Page Component Settings */
/*-----------------------------------------------------------------------------------*/

class EPANEL_WPPage extends EPANEL_WPSection {

   function __construct( $registered_settings = array() ) {
	
		$name = __('Page', 'epanel');
		$id = 'page';
		
		$default_settings = array(
			'description' 	=> 'Add additional settings for pages',
			'icon'			=> CORE_IMAGES.'/admin/page.png', 
			 
			'posttype'		=> array('post', 'page'),
		);
		
		$settings = wp_parse_args( $registered_settings, $default_settings );
		
		parent::__construct($name, $id, $settings);    
   }


/*-----------------------------------------------------------------------------------*/
/* Metabox options array */
/*-----------------------------------------------------------------------------------*/

	function section_persistent(){

						$type_meta_array = array(
						
							// Only for custom post type 
														
					);

					$post_types = array($this->settings['posttype']);
										
					global $page_meta_panel;
				

						$type_metatab_settings = array(
							
							// not needed
							
						);

						//$page_meta_panel->register_tab( $type_metatab_settings, $type_meta_array );

						
						$metatab_array = array(
								'sidebar_position' => array(
										
										'default'	=> 'left',
										'type'		=> 'select',
										'selectvalues'=> array(
											'left'		=> array( 'name' => 'Place The Sidebar At The Left Side' ),
											'right' 	=> array( 'name' => 'Place The Sidebar At The Right Side' ),
											),
										'title'		=> "Inner Pages Sidebar Position",
										'shortexp'	=> "Inner Pages Sidebar Position",
										'inputlabel'	=> 'Inner Pages Sidebar Position',
										'desc'		=> 'Use this option to select where you want the sidebar. <strong>Note :</strong> you can create your own custom sidebar and replace the default one, use the settings panel below to change the sidebar.'
										
								),
														
							);

						$metatab_settings = array(
								'id' => 'page_meta',
								'name' => "Inner Page Settings",
								'icon' => $this->icon
							);
						
						
						register_metatab($metatab_settings, $metatab_array);
	}

			

// End of Section Class //
}

