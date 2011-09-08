<?php
if(__FILE__ == $_SERVER['SCRIPT_FILENAME'])
{
    header('HTTP/1.0 404 Not Found');
      exit;
}

function get_option_array( $load_unavailable = false ){
	
	$default_options = array();
	
	$default_options['general_settings'] = array(
				'epanel_custom_logo' => array(
					'default' 	=> THEME_IMAGES.'/logo.png',
					'type' 		=> 'image_upload',
					'imagepreview' 	=> '270',
					'inputlabel' 	=> 'Upload custom logo',
					'title'		=> 'Custom Header Logo',						
					'shortexp' 	=> 'Input or upload your own logo url.',
					'exp' 		=> 'Upload a logo for your theme, or specify the image address of your online logo. (http://yoursite.com/logo.png)'
				),
				'epanel_favicon'	=> array(
					'default' 	=> 	CORE_IMAGES."/favicon-epanel.ico",
					'type' 		=> 	'image_upload',
					'imagepreview' 	=> 	'16',
					'title' 	=> 	'Favicon Image',						
					'shortexp' 	=> 	'Input Full URL to favicon image ("favicon.ico" image file)',
					'exp' 		=> 	'Enter the full URL location of your custom "favicon" which is visible in ' .
								'browser favorites and tabs.<br/> (<strong>Must be .png or .ico file - 16px by 16px</strong> ).'
				),
				
				'epanel_theme'		=> array(
						'default'	=> 'default_skin',
						'type'		=> 'select',
						'selectvalues'	=> array(
							'default_skin'	=> array("name" => "Default Skin"),
							'skin2'	=> array("name" => "Patter Skin 2"),
							'skin3'	=> array("name" => "Patter Skin 3"),
							'skin4'	=> array("name" => "Patter Skin 4"),
							'skin5'	=> array("name" => "Patter Skin 5"),
							'skin6'	=> array("name" => "Patter Skin 6"),
							'skin7'	=> array("name" => "Patter Skin 7"),
							'skin8'	=> array("name" => "Patter Skin 8"),
							'skin9'	=> array("name" => "Patter Skin 9"),
							'skin10'	=> array("name" => "Patter Skin 10"),
							'skin11'	=> array("name" => "Non Wide Skin"),
							'custom'		=> array("name" => "Custom Skin")
							), 
						'inputlabel'	=> 'Select a skin',
						'title'		=> 'Theme Style',						
						'shortexp'	=> 'Here you can choose a different skin.',
						'exp'		=> 'Choose between various available skins. <br/><strong>Note :</strong> if you want to customize the skin of this theme you can choose the option &quot;Custom Skin&quot; than use the light editor tab to customize the layout, however we recommend to directly edit .css file using code editors.'
					),	
				
				
				'enable_related' => array(
						'default'	=> false,
						'type'		=> 'check',
						'inputlabel'	=> 'Show Featured Products?',
						'title'		=> 'Show featured products in Product Single Page?',
						'shortexp'	=> '',
						'exp'		=> 'Enable Featured products slider in product single page.'
				),
				
			'related_title' => array(
					'default' 	=> 'Related Products',
					'type' 		=> 'text',
					'inputlabel' 	=> 'Scroller Heading',
					'title' 	=> 'Scroller Heading Title (Product Single Page)',
					'shortexp' 	=> '',
					'exp' 		=> 'Customize the heading title for the single product page scroller.' 
				),
				
				
		
	);
	

	
	$cufon_path = TEMPLATEPATH . '/js/cufon/';
	$alt_cufon = array();
	
	if ( is_dir($cufon_path) ) {
	    if ($cufon_dir = opendir($cufon_path) ) { 
	        while ( ($cufon_file = readdir($cufon_dir)) !== false ) {
	            if(stristr($cufon_file, ".js") !== false) {
	                $alt_cufon[] = $cufon_file;
	            }
	        }    
	    }
	}
	
	$theme_url =  get_bloginfo('template_url');
	$google_fonts = array(			
					"droid_sans" => "Droid Sans",
					"droid_mono" => "Droid Sans Mono",
					"irish" => "Irish Growler",
					"walter" => "Walter Turncoat",
					"chewy" => "Chewy",
					"cherry_cream" => "Cherry Cream Soda",
					"font_diner" => "Fontdiner Swanky",
					"sunshiney" => "Sunshiney",
					"permanent" => "Permanent Marker",
					"sbell" => "Schoolbell",
					"home_apple" => "Homemade Apple",
					"slackey" => "Slackey",
					"luck" => "Luckiest Guy",
					"crush" => "Crushed",
					"kranky" => "Kranky",
					"crafty" => "Crafty Girls",
					"salt" => "Rock Salt",
					"soon" => "Coming Soon",
					"corben" => "Corben",
					"calli" => "Calligraffitti",
					"orbi" => "Orbitron",
					"unke" => "Unkempt",
					"lek" => "Lekton",
					"buda" => "Buda",
					"benth" => "Bentham",
					"nerry" => "Merriweather",
					"cabin" => "Cabin",
					"cousine" => "Cousine",
					"vibur" => "Vibur",
					"coda" => "Coda",
					"gruppo" => "Gruppo",
					"tinos" => "Tinos",
					"lato" => "Lato",
					"rale" => "Raleway",
					"cove" => "Covered By Your Grace",
					"arimo" => "Arimo",
					"cop" => "Copse",
					"sync" => "Syncopate",
					"arvo" => "Arvo",
					"hand" => "Just Another Hand",
					"pro" => "Anonymous Pro",
					"down" => "Just Me Again Down Here",
					"lob" => "Lobster",
					"geo" => "Geo",
					"pt" => "PT Sans",
					"ubuntu"  => "Ubuntu",
					"unifra" => "UnifrakturMaguntia",
					"neu" => "Neuton",
					"cook" => "UnifrakturCook",
					"alle" => "Allerta",
					"crim" => "Crimson Text",
					"kenia" => "Kenia",
					"phil" => "Philosopher",
					"cup" => "Cuprum",
					"puri" => "Puritan",
					"jose" => "Josefin Sans",
					"nobile" => "Nobile",
					"mol" => "Molengo",
					"all" => "Allerta Stencil",
					"im" => "IM Fell Great Primer",
					"reen" => "Reenie Beanie",
					"yanone" => "Yanone Kaffeesatz",
					"old" => "Old Standard TT",
					"canta" => "Cantarell",
					"allan" => "Allan",
					"cardo" => "Cardo",
					"vol" => "Vollkorn",
					"inco" => "Inconsolata",
					"sni" => "Sniglet",
					"dseri" => "Droid Serif",
					"tang" => "Tangerine",
					"ofl" => "OFL Sorts Mill Goudy TT",
					"josla" => "Josefin Slab",
					"neuc" => "Neucha",
					"kri" => "Kristi",
					"mount" => "Mountains of Christmas");
	/*
		Typography related options
	*/
	$default_options['typography'] = array(
				
				'fontreplacement' => array(
						'default'	=> false,
						'type'		=> 'check',
						'inputlabel'	=> 'Use Cufon font replacement?',
						'title'		=> 'Use Cufon Font Replacement',
						'shortexp'	=> 'Use a special font replacement technique for certain text',
						'exp'		=> 'Cufon is a special technique for allowing you to use fonts outside of the 10 or so "web-safe" fonts. <br/><br/>' .
								 THEMENAME.' is equipped to use it.  Select this option to enable it. Visit the <a href="http://cufon.shoqolate.com/generate/">Cufon site</a>.'
				),
				
				'google_font_url'		=> array(
						'default'	=> 'Droid Sans',
						'type'		=> 'select_same',
						'selectvalues'	=> $google_fonts, 
						'inputlabel'	=> 'Google Font Api Normal Text',
						'title'		=> 'Google Font Replacement',						
						'shortexp'	=> 'Choose a font from the google font api',
						'exp'		=> 'If you have disabled cufon then you can use the google font api as font replacement. <br/><br/> Preview each font at the <a href="http://www.google.com/webfonts?subset=latin">following url</a>'
					),
				
				
				'google_font_url_headings'		=> array(
						'default'	=> 'Droid Serif',
						'type'		=> 'select_same',
						'selectvalues'	=> $google_fonts, 
						'inputlabel'	=> 'Google Font Api Headings',
						'title'		=> 'Google Font Replacement',						
						'shortexp'	=> 'Choose a font from the google font api',
						'exp'		=> 'If you have disabled cufon then you can use the google font api as font replacement. <br/><br/> Preview each font at the <a href="http://www.google.com/webfonts?subset=latin">following url</a>'
					),
				
				/*
				'font_normal_text' => array(
						'default' 	=> array(
						'font' 		=> 'lucida_grande',
						),
						'type' 		=> 'typography',
						'selectors'	=> 'h1, h2, h3, h4, h5, h6, .site-title',
						'inputlabel' 	=> 'Select Font',
						'title' 	=> 'Typography - Normal Text Font',
						'shortexp' 	=> 'Here you can customize the font for normal text',
						'exp' 		=> 'Set typography for your normal text (p)tag. <br/><br/><strong>*</strong> Denotes Web Safe Fonts<br/><strong>G</strong> Denotes Google Fonts<br/><br/><strong>Note:</strong> These options make use of the <a href="http://code.google.com/webfonts" target="_blank">Google fonts API</a> to vastly increase the number of websafe fonts you can use.',
				),*/
				
				/*
				'font_file'	=> array(
						'default'	=> TEMPLATEPATH.'/js/',
						'type'		=> 'text',
						'inputlabel'	=> 'Cufon replacement font file URL',
						'title'		=> 'Cufon: Replacement Font File URL',
						'shortexp'	=> 'The font file used to replace text.',
						'exp'		=> 'Use the <a href="http://cufon.shoqolate.com/generate/">Cufon site</a> to generate a font file for use with this theme.  Place it in your theme folder and add the full URL to it here. The default font is Museo Sans.'
				),
				*/
				
						'cufon_font_file'		=> array(
								'default'	=> 'Lato_400-Lato_700.font.js',
								'type'		=> 'select_same',
								'selectvalues'	=> $alt_cufon, 
								'inputlabel'	=> 'Cufon replacement font file',
								'title'		=> 'Cufon Font File',						
								'shortexp'	=> 'Here you can customize the font file for heading text',
								'exp'		=> 'Choose beetwen an *unlimited* number of cufon fonts, you can generate your own cufon font on <a href="http://cufon.shoqolate.com/generate/">Cufon site</a>  <br/><br/> Upload your own cufon generated font via ftp in <strong>'.$theme_url.'/js/cufon'.'</strong>'
							),
				
	);
	
	// Navigation settings
	
	$easing_fx = array(
		"jswing", 
		"def", 
		"easeInQuad",
		"easeOutQuad",
		"easeInOutQuad",
		"easeInCubic",
		"easeOutCubic",
		"easeInOutCubic",
		"easeInQuart",
		"easeOutQuart",
		"easeInOutQuart",
		"easeInQuint",
		"easeOutQuint",
		"easeInOutQuint",
		"easeInSine",
		"easeOutSine",
		"easeInOutSine",
		"easeInExpo",
		"easeOutExpo",
		"easeInOutExpo",
		"easeInCirc",
		"easeOutCirc",
		"easeInOutCirc",
		"easeInElastic",
		"easeOutElastic",
		"easeInOutElastic",
		"easeInBack",
		"easeOutBack",
		"easeInOutBack",
		"easeInBounce",
		"easeOutBounce",
		"easeInOutBounce" 
	);
	
	// Menu manager not required for this theme
	
	/*
	
	$default_options['menu_manager'] = array(
		
		'menu_hide_speed' => array(
				
				'default'	=> '500',
				'type'		=> 'text_small',
				'inputlabel'	=> 'Submenu Hide Speed',
				'title'		=> 'Header Main Menu submenu hide speed',						
				'shortexp'	=> 'Customize the submenu hide speed',
				'exp' 		=> "Change the number to customize the hiding time (in ms). Note : 1000ms = 1 second."
		),
		
		'menu_show_speed' => array(
				
				'default'	=> '750',
				'type'		=> 'text_small',
				'inputlabel'	=> 'Submenu Show Effect Speed',
				'title'		=> 'Header Main Menu submenu show effet speed',						
				'shortexp'	=> 'Customize the submenu show effect hide speed',
				'exp' 		=> "Change the number to customize the show effect speed time (in ms). Note : 1000ms = 1 second."
		),
		
		'menu_effect_hide_speed' => array(
				
				'default'	=> '350',
				'type'		=> 'text_small',
				'inputlabel'	=> 'Submenu Hide Effect Speed',
				'title'		=> 'Header Main Menu submenu hide effet speed',						
				'shortexp'	=> 'Customize the submenu hide effect hide speed',
				'exp' 		=> "Change the number to customize the hide effect speed time (in ms). Note : 1000ms = 1 second."
		),
		
		
		'menu_show_easing'		=> array(
				'default'	=> 'easeInCirc',
				'type'		=> 'select_same',
				'selectvalues'	=> $easing_fx, 
				'inputlabel'	=> 'Submenu Show Effect',
				'title'		=> 'Submenu Show Effect',						
				'shortexp'	=> 'Here you can customize the show effect of your main menu.',
				'exp'		=> 'Here you can customize the show effect of your main menu. This is the hover effect, if you want to preview each effect <a href=http://gsgd.co.uk/sandbox/jquery/easing/>go here</a>.'
			),
		
		'menu_hide_easing'		=> array(
				'default'	=> 'easeOutCirc',
				'type'		=> 'select_same',
				'selectvalues'	=> $easing_fx, 
				'inputlabel'	=> 'Submenu Hide Effect',
				'title'		=> 'Submenu Hide Effect',						
				'shortexp'	=> 'Here you can customize the hide effect of your main menu.',
				'exp'		=> 'Here you can customize the hide effect of your main menu. This is the hover(out) effect, if you want to preview each effect <a href=http://gsgd.co.uk/sandbox/jquery/easing/>go here</a>.'
			),
		
	); */
	
	
	// Homepage Setup Settings
	
	$default_options['homepage_settings'] = array(
	
		'home_slider'		=> array(
				'default'	=> 'static',
				'type'		=> 'select',
				'selectvalues'	=> array(
					'static'	=> array("name" => "Static Picture No Slide"),
					'nivo'		=> array("name" => "Nivo Slider"),
					'bx'     => array("name" => "BX Slider")
					
					), 
				'inputlabel'	=> 'Homepage Slider Type',
				'title'		=> 'Homepage Slider Type',						
				'shortexp'	=> 'Choose wich slider you need for your homepage',
				'exp'		=> 'Choose between different sliders available for your homepage, you can find more slide settings into the "Home Slider" component from your wordpress admin menu.'
			),
			
			
		'slide_static_picture' => array(
			'default' 	=> THEME_IMAGES.'/static_picture.png',
			'type' 		=> 'image_upload',
			'imagepreview' 	=> '250',
			'inputlabel' 	=> 'Homepage Static Picture',
			'title'		=> 'Homepage Static picture',						
			'shortexp' 	=> 'Input the url or upload your own homepage static picture url.',
			'exp' 		=> 'Upload a picture for your homepage static area or simply paste the url into the input field. Recommended max width is 950px.<br/><br/> Note : the recommended width is only for the static picture! Other sliders type automatically resize pictures.'
		),
		
		'slider_height' => array(
				
				'default'	=> '360',
				'type'		=> 'text_small',
				'inputlabel'	=> 'Homepage slider height',
				'title'		=> 'Homepage Slider Height',						
				'shortexp'	=> 'Customize the homepage slider height',
				'exp' 		=> "Change the number to customize the homepage slider height. <strong>Note : this affects every other slider type but not the static picture.</strong>"
		),
		
		'enabled_intro_text' => array(
				'default'	=> true,
				'type'		=> 'check',
				'inputlabel'	=> 'Enable Homepage Intro Text?',
				'title'		=> 'Enable homepage introduction text ?',
				'shortexp'	=> 'Enable or disable homepage introduction text',
				'exp'		=> 'Check this option to enable the homepage text below the main slider.'
		),
		
		'intro_text' => array(
				
				'default'	=> '<h2>Express Store is an extremely versatile theme with a myriad of options and styles</h2> <p>Aliquam venenatis enim in mi iaculis in tempor lectus tempor et convallis erat pellentesque.</p> ',
				'type'		=> 'textarea',
				'inputlabel'	=> 'Homepage intro text',
				'title'		=> 'Homepage intro text',						
				'shortexp'	=> 'Customize the text of the homepage intro bar text',
				'exp' 		=> "Here you can customize the text of the homepage intro bar, the one below the slider."
		),
		
		'enabled_product_slider' => array(
				'default'	=> true,
				'type'		=> 'check',
				'inputlabel'	=> 'Enable Homepage Product Slider ?',
				'title'		=> 'Enable homepage product slider ?',
				'shortexp'	=> 'Enable or disable homepage product slider',
				'exp'		=> 'Check this option to enable the homepage product scroller below the main slider.'
		),
		
		'home_scroller_position'		=> array(
				'default'	=> 'top',
				'type'		=> 'select',
				'selectvalues'	=> array(
					'top'	=> array("name" => "Show Product Slider Below homepage slider"),
					'bottom'		=> array("name" => "Show produc slider below homepage content area")
					
					
					), 
				'inputlabel'	=> 'Product Slider Position',
				'title'		=> 'Homepage Product Slider Position',						
				'shortexp'	=> 'Choose where you want to show the homepage product slider',
				'exp'		=> 'Choose where you want to show the homepage product slider'
			),
		
		'home_product_slider'		=> array(
				'default'	=> 'custom',
				'type'		=> 'select',
				'selectvalues'	=> array(
					'custom'	=> array("name" => "Custom Flexible Management"),
					'store_scroll_mode'		=> array("name" => "Grab Featured Products From The Catalog")
					), 
				'inputlabel'	=> 'Homepage Product Slider Content',
				'title'		=> 'Homepage Product Slider Content',						
				'shortexp'	=> '',
				'exp'		=> 'Choose how to populate the content of the homepage product slider. <br/><br/> <strong>Custom Flexible Management :</strong> using this method you\'ll have full control on your homepage scroller, you can even use this scroller for something else other than products, to add items using this way you have to use the <a href="#">Product Scroller Component</a> <br/><br/> <strong>Grab Featured Products :</strong> using this method the scroller will automatically grab featured products from your catalog.'
			),
		
					
		
	);
	
	// store homepage template settings
	
	
	$default_options['store_homepage'] = array(
		
		'store_info' => array(
			'type'		=> 'text_content',
				'layout'	=> 'full',
				'exp'		=> '<strong>The following settings affects only on a specific page template (Homepage Store) if you want to show the store within the homepage, create a page and as page template select "Homepage Store"!</strong> <br/><br/>
				
				There are other ways you can set your homepage. For example you can show the entire catalog without the sections contained in the homepage (slider, scroller, etc. ..), or you can show only featured products, it\' up to you.<br/><br/><strong>Note: a few settings (like grabbing specific products) doesn\'t affect the "Homepage Store Catalog With Sidebar" page template</strong>'
			),
					
		'store_info2' => array(
			'type'		=> 'text_content',
				'layout'	=> 'full',
				'exp'		=> '<strong><h3>Grabbing Products On The Homepage Store Template</h3></strong>
				
				The homepage store template was built to be flexible to give users the possibility to display any type of product. You can choose what kind of products to display on the homepage by selecting from the options below.<br/><br/>
				
				<strong>Grab Recent Products :</strong> which displays the products most recently added to the catalog.<br/><br/>
				<strong>Displays a specific category :</strong> wich displays products from a specifc category (given the ID)<br/><br/>
				<strong>Displays Featured Products From Your Catalog :</strong> which displays the featured products from your catalog.<br/><br/>
				<strong>Displays On Sale Products From Your Catalog :</strong> which displays the products that have a Sale Price or a promotion that applies to the product.'
			),
		
		'store_home_catalog_mode' => array(
				'default'	=> 'default',
				'type'		=> 'select',
				'selectvalues'	=> array(
					'default'	=> array("name" => "Grab Every Product From Your Catalog."),
					'latest'		=> array("name" => "Grab Recent Products"),
					'singular'	=> array("name" => "Displays a specific category."),
					'featured' => array("name" => "Displays Featured Products From Your Catalog"),
					'on_sale' => array("name" => "Displays On Sale Products From Your Catalog"),
					), 
				'inputlabel'	=> 'What kind of products you want on the homepage ?',
				'title'		=> 'Grabbing Products On The Homepage Store Template',						
				'shortexp'	=> '',
				'exp'		=> 'Choose how to populate the content of the store homepage <br/><br/> <strong>For additional informations please read the info above this option.</strong>'
		),
		
		'store_home_catid' => array(
				
				'default'	=> '',
				'type'		=> 'text_small',
				'inputlabel'	=> 'Singular Category ID',
				'title'		=> 'Grab Products From A Specific Category',						
				'shortexp'	=> '',
				'exp' 		=> "If you have choosed to show products ONLY from one category, here you can type the ID number of the category. <br/><br/><strong>You Can find the category ID here :</strong>"
		),
		
		'store_home_catalog_limit' => array(
				
				'default'	=> '10',
				'type'		=> 'text_small',
				'inputlabel'	=> 'Homepage Store Products Limit',
				'title'		=> 'Products Limit On The Homepage',						
				'shortexp'	=> '',
				'exp' 		=> "Choose how many products you want to display on the homepage store template."
		),
		
		'store_home_catalog_order' => array(
				'default'	=> 'newest',
				'type'		=> 'select',
				'selectvalues'	=> array(
					'title'	=> array("name" => "Order Products By Title"),
					'newest'		=> array("name" => "Order Products By Newest"),
					'oldest' => array("name" => "Order Products By Oldest"),
					'random'	=> array("name" => "Random Products Order"),
					), 
				'inputlabel'	=> 'Store Homepage Products Order',
				'title'		=> 'Homepage Store Template Products Order',						
				'shortexp'	=> '',
				'exp'		=> 'Choose how to order products of the store homepage.<br/><br/>'
		),
		
		
		'store_home_handler' => array(
				'type'		=> 'check_multi',
				'selectvalues'	=> array(
					'store_home_summary'		=> array('inputlabel'=>'Enable Product Summary on Homepage', 'default'=> true),
					'store_home_read_more'		=> array('inputlabel'=>'Enable Read Details Link on Homepage', 'default'=> true),
					'store_home_sale_show_text' => array('inputlabel' => 'Show Sale Text Before Sale Price on Homepage', 'default' => true),
					//'store_home_buy_button' => array('inputlabel' => 'Show Add To Cart Button on Homepage', 'default' => false),
				),
				'title'		=> 'Homepage Store Products Layout',
				'shortexp'	=> '',
				'exp'		=> 'Use these options to control which elements are shown on the homepage store layout. <br/><br/> 
				<strong>Product Summary :</strong> check this option to show a product summary below the product thumb, you can customize the summary of each product into the product page.<br/><br/> '
		),
		
		
		'store_home_sale_text' => array(
				
				'default'	=> 'Save !',
				'type'		=> 'text_small',
				'inputlabel'	=> 'Sale Price Text',
				'title'		=> 'Custom Sale Price Text',						
				'shortexp'	=> '',
				'exp' 		=> "If you have choosed to show the &quot;sale&quot; text before the sale price, here you can customize it."
		),
		
		
	);
	
	
	// Homepage slider settings
	$nivo_fx = array ("sliceDown","sliceDownLeft","sliceUp","sliceUpLeft","sliceUpDown","sliceUpDownLeft","fold","slideInRight","slideInLeft","boxRandom","boxRain","boxRainReverse","boxRainGrow","boxRainGrowReverse");
	
	$default_options['nivo_slider_settings'] = array (
	
		'nivo_fx'		=> array(
				'default'	=> 'random',
				'type'		=> 'select_same',
				'selectvalues'	=> $nivo_fx, 
				'inputlabel'	=> 'Nivo Slider Effect',
				'title'		=> 'Nivo Slider Effect',						
				'shortexp'	=> 'Choose an effect for your nivo slider',
				'exp'		=> 'Choose an effect for your nivo slider'
			),
		
				
		'nivo_slices' => array(
				
				'default'	=> '15',
				'type'		=> 'text_small',
				'inputlabel'	=> 'Nivo Slider Slices',
				'title'		=> 'Nivo Slider Slices for animations',						
				'shortexp'	=> 'Here you can customize Nivo Slider slices',
				'exp' 		=> "Change the number to customize Nivo Slider slices"
		),
		
		
		'nivo_anim_spd' => array(
				
				'default'	=> '500',
				'type'		=> 'text_small',
				'inputlabel'	=> 'Animation Speed',
				'title'		=> 'Nivo Slider Animationa Speed',						
				'shortexp'	=> 'Here you can customize Nivo slider animation speed',
				'exp' 		=> "Here you can customize Nivo slider animation speed"
		),
		
		'nivo_pause_time' => array(
				
				'default'	=> '3000',
				'type'		=> 'text_small',
				'inputlabel'	=> 'Slide Delay',
				'title'		=> 'Nivo Slider Slide Delay',						
				'shortexp'	=> 'Here you can customize nivo slider slide delay',
				'exp' 		=> "Change the number to customize the nivo slider slide delay"
		),
		
		
		'nivo_pause_hover' => array(
				'default'	=> true,
				'type'		=> 'check',
				'inputlabel'	=> 'Enable Pause on hover on Nivo Slides ?',
				'title'		=> 'Enable Pause on hover on Nivo Slides ?',
				'shortexp'	=> 'Enable or disable Pause on hover on nivo slides',
				'exp'		=> 'Check this option to enable Pause on hover on nivo slides'
		),
		
	);
	
	// cycle slider settings
	
	$cycle_fx = array(
		"blindX",
		"blindZ",
		"blindY",
		"cover",
		"curtainX",
		"curtainY",
		"fade",
		"fadeZoom",
		"growX",
		"growY",
		"none",
		"scrollUp",
		"scrollDown",
		"scrollLeft",
		"scrollRight",
		"scrollHorz",
		"scrollVert",
		"shuffle",
		"slideX",
		"slideY",
		"toss",
		"turnUp",
		"turnDown",
		"turnLeft",
		"turnRight",
		"uncover",
		"wipe",
		"zoom",
	);
	
	$bx_mode = array("horizontal","vertical","fade");
	
	$default_options['bx_slider_settings'] = array (
		
		'bx_mode'		=> array(
				'default'	=> 'horizontal',
				'type'		=> 'select_same',
				'selectvalues'	=> $bx_mode, 
				'inputlabel'	=> 'Bx Slider Mode',
				'title'		=> 'Bx Slider Mode',						
				'shortexp'	=> 'Choose an effect for your cycle slider',
				'exp'		=> 'Choose an effect for your cycle slider <br> You can preview each effect at <a href="http://jquery.malsup.com/cycle/browser.html" target="_blank">the following page</a>'
			),
		
		
		'bx_fx_spd' => array(
				
				'default'	=> '500',
				'type'		=> 'text_small',
				'inputlabel'	=> 'Bx Slider Effect Speed',
				'title'		=> 'Bx Slider Effect Speed',						
				'shortexp'	=> 'Here you can customize the bx slider effect speed',
				'exp' 		=> "Change the number to customize the bx slider effect speed"
		),
		
		
		'bx_easing'		=> array(
				'default'	=> 'fade',
				'type'		=> 'select_same',
				'selectvalues'	=> $easing_fx, 
				'inputlabel'	=> 'BX Slider Transition Easing Fx',
				'title'		=> 'BX Slider Transition Easing Fx',						
				'shortexp'	=> 'Choose the transition easing effect',
				'exp'		=> 'Choose an effect for your bx slider slide transition <br/> Note: if you have enabled the Fade mode, the easing effect won\' work.</a>'
			),
		
		'bx_pause' => array(
				'default'	=> false,
				'type'		=> 'check',
				'inputlabel'	=> 'Stop the slider on mouse hover ?',
				'title'		=> 'Stop the slider on mouse hover ?',
				'shortexp'	=> 'Choose if you want to stop the slider on mouse hover',
				'exp'		=> 'Check this option to stop the slider on mouse hover'
		),
		
		'bx_delay' => array(
				
				'default'	=> '3000',
				'type'		=> 'text_small',
				'inputlabel'	=> 'Bx Slider Slide Delay',
				'title'		=> 'Bx Slider Slide Delay',						
				'shortexp'	=> 'Here you can customize bx slider slide delay',
				'exp' 		=> "integer - in ms, the duration between each slide transition"
		),
		
		/*
		'cycle_fx'		=> array(
				'default'	=> 'fade',
				'type'		=> 'select_same',
				'selectvalues'	=> $cycle_fx, 
				'inputlabel'	=> 'Cycle Slider Effect',
				'title'		=> 'Cycle Slider Effect',						
				'shortexp'	=> 'Choose an effect for your cycle slider',
				'exp'		=> 'Choose an effect for your cycle slider <br> You can preview each effect at <a href="http://jquery.malsup.com/cycle/browser.html" target="_blank">the following page</a>'
			),
		
		'cycle_easing_check' => array(
				'default'	=> false,
				'type'		=> 'check',
				'inputlabel'	=> 'Enable Easing Effect On Transition ?',
				'title'		=> 'Enable Easing Effect On Transition ?',
				'shortexp'	=> 'Enable or disable the easing effect on slide transition',
				'exp'		=> 'Check this option to enable the easing effect on each slide transition, you can choose the easing effect from the option below.'
		),
		
		'cycle_easing'		=> array(
				'default'	=> 'jswing',
				'type'		=> 'select_same',
				'selectvalues'	=> $easing_fx, 
				'inputlabel'	=> 'Cycle Easing Effect',
				'title'		=> 'Cycle Easing Effect',						
				'shortexp'	=> 'Choose an easing effect for your transitions',
				'exp'		=> 'To use this option you must enable the option above this one, then you can choose the easing transition effect-'
			),
		
		'cycle_delay' => array(
				
				'default'	=> '3000',
				'type'		=> 'text_small',
				'inputlabel'	=> 'Cycle Slider Slide Delay',
				'title'		=> 'Cycle Slider Slide Delay',						
				'shortexp'	=> 'Here you can customize cycle slider slide delay',
				'exp' 		=> "Change the number to customize the cycle slide delay"
		),
		
		'cycle_pause' => array(
				'default'	=> false,
				'type'		=> 'check',
				'inputlabel'	=> 'Stop the slider on mouse hover ?',
				'title'		=> 'Stop the slider on mouse hover ?',
				'shortexp'	=> 'Choose if you want to stop the slider on mouse hover',
				'exp'		=> 'Check this option to stop the slider on mouse hover'
		),
		*/
	);

	
	// Customer Area Settings
	
	$of_pages = array();
	$of_pages_obj = get_pages('sort_column=post_parent,menu_order');    
	foreach ($of_pages_obj as $of_page) {
	    $of_pages[$of_page->ID] = $of_page->post_name; }
	$of_pages_tmp = array_unshift($of_pages);
	
	$default_options['customer_area'] = array(
	
		'store_info_general_acc' => array(
			'type'		=> 'text_content',
				'layout'	=> 'full',
				'exp'		=> '
				
				This section gives you the ability to customize certain sections of the customer area of your store.'
			),
		
		'customer_area_links' => array(
				'default'	=> '',
				'type'		=> 'check_multi',
				'selectvalues'	=> array(
					'area_account'	=> array('inputlabel'=>'Show My Account Link', 'default'=> true),
					'area_downloads'		=> array('inputlabel'=>'Show Downloads Area Link', 'default'=> true),
					'area_orders'	=> array('inputlabel'=>'Show Order History Link', 'default'=> true),
					'area_custom'		=> array('inputlabel'=>'Show Additional Custom Link', 'default'=> false),
					'area_logout'		=> array('inputlabel'=>'Show Logout link', 'default'=> true),
				),
				'inputlabel'	=> 'Select Which Links To Show',
				'title'		=> 'Customer Area/Widget Available Links',						
				'shortexp'	=> '',
				'exp'		=> "Select which links you would like to show in your customer area. <br/><br/><strong>Please Note: </strong>if you enable or disable any link from here it will be available/unavailable into the account widget too."
		), 
		
		'custom_link_text' => array(
				'default'	=> 'FAQs',
				'type'		=> 'text',
				'inputlabel'	=> 'Custom Link Text',
				'title'		=> 'Custom Additional Link For The Customer Area',
				'shortexp'	=> '',
				'exp'		=> 'If you choosed to display an additional link into your customer area here you can customize the link label.'
		),
		
		'enable_customer_area_link' => array(
				'default'	=> true,
				'type'		=> 'check',
				'inputlabel'	=> 'Show the customer area back link',
				'title'		=> 'Show the customer area back link',
				'shortexp'	=> '',
				'exp'		=> 'Choose if you want to show the customer area link into the account profile editor page.'
		),
		
		'custom_page_content'		=> array(
				'default'	=> '',
				'type'		=> 'select_same',
				'selectvalues'	=> $of_pages, 
				'inputlabel'	=> 'Custom Page Content Grabber',
				'title'		=> 'Grab Custom Page Content from a page',						
				'shortexp'	=> '',
				'exp'		=> 'In order to give more flexibility to the content of this page, <strong>you must first create a new page</strong> and add the contents, after which select the page you just created using this option.'
			),
		
		'store_info_general_acc2' => array(
		'type'		=> 'text_content',
			'layout'	=> 'full',
			'exp'		=> '<h4>Customer Area Link Customization</h4>
			
			This section gives you the ability to customize the description below each link into the customer area. <br/><strong>Please note :</strong> when you customize the Fourth link you must edit the css skin file in order to see the link icon.<br/><br/> First of all open your stylesheet file (based on the selected skin) under the css folder and edit the "li" class at line 1218. You have to assign the new class name based on your new text link label.<br/><br/>
			For example if your link label is "FAQs" the class is .FAQs, if your text label is "My Custom Link" the class is .My.Custom.Link'
		),
		
		'link_description' => array(
				'type'		=> 'text_multi',
				'inputsize'	=> 'regular',
				'selectvalues'	=> array(
					'first_link_desc'	=> array('inputlabel'=>'First Link Text Description', 'default'=> 'Add your custom description using the theme options panel.'),
					'second_link_desc'	=> array('inputlabel'=>'Second Link Text Description', 'default'=> 'Add your custom description using the theme options panel.'),
					'third_link_desc'	=> array('inputlabel'=>'Third Link Text Description', 'default'=> 'Add your custom description using the theme options panel.'),
					'fourth_link_desc'	=> array('inputlabel'=>'Fourth Link Text Description', 'default'=> 'Add your custom description using the theme options panel.'),
					'fifth_link_desc'	=> array('inputlabel'=>'Fifth Link Text Description', 'default'=> 'Add your custom description using the theme options panel.'),
				),
				'title'		=> 'Customer Area Links Description',
				'shortexp'	=> '',
				'exp'		=> 'Here you can customize the description for each link inside the customer area. <strong>Html is allowed</strong><br/><br/> Remember : when you customize the Fourth link you must edit the css skin file in order to see the link icon. <br/><br/> For example if your link label is "FAQs" the class is .FAQs, if your text label is "My Custom Link" the class is .My.Custom.Link'
		),
		
		'profile_edit_additional_content' => array(
				'default' 	=> '',
				'type' 		=> 'textarea',
				'layout' 	=> 'full',
				'inputlabel' 	=> 'Profile Editor Additional Content',
				'title' 	=> 'Customer Profile Editor Additional Content',
				'shortexp' 	=> '',
				'exp' 		=> 'By default the customer profile editor page shows only profile fields, but you can add additional content using this field. <strong>Html allowed.</strong>', 
			),
	
	);
	
	$default_options['general_store_layout'] = array(
	
		'store_main_settings' => array(
				'default'	=> '',
				'type'		=> 'check_multi',
				'selectvalues'	=> array(
					'show_category_name'	=> array('inputlabel'=>'Show category name on store pages ?', 'default'=> true),
					'show_product_sorter'		=> array('inputlabel'=>'Show Product Sorter ?', 'default'=> true),
					'show_product_summary'	=> array('inputlabel'=>'Show Product Summary ?', 'default'=> true)
				),
				'inputlabel'	=> 'Select Elements You Want To Show',
				'title'		=> 'Store Main Page Layout Settings',						
				'shortexp'	=> '',
				'exp'		=> "Select which elements you would like to show in your store main page. <br/><br/> <strong>Category Name : </strong> Choose if you want to show the category name on each store page (catalog, store category, product page etc...).<br/><br/> <strong>Product Sorter : </strong> Choose if you want to show the product sorter form on your store.."
		), 
		
		'store_main_sale_text' => array(
				
				'default'	=> 'Save !',
				'type'		=> 'text_small',
				'inputlabel'	=> 'Sale Price Text',
				'title'		=> 'Custom Sale Price Text',						
				'shortexp'	=> '',
				'exp' 		=> "If you have choosed to show the &quot;sale&quot; text before the sale price, here you can customize it."
		),

		'catalog_pic_resize_method' => array(
		'default'	=> 'matte',
		'type'		=> 'select',
		'selectvalues'	=> array(
			'matte'	=> array('name'=>'Automatically Resize Picture', 'default'=> true),
			'crop'		=> array('name'=>'Crop Catalog Pictures', 'default'=> true),
		),
		'inputlabel'	=> 'Resize Method',
		'title'		=> 'Store Product Main Image Resize Method',						
		'shortexp'	=> '',
		'exp'		=> "Select which resize method you want to use for the catalog and product category page."
), 
		
		'product_pic_set' => array(
				'type'		=> 'text_multi',
				'inputsize'	=> 'medium',
				'selectvalues'	=> array(
					'inner_width'	=> array('inputlabel'=>'Main Thumb Width', 'default'=> '240'),
					'inner_height'	=> array('inputlabel'=>'Main Thumb Height', 'default'=> '300'),
					),
				'title'		=> 'Single Product Page Main Picture Sizes',
				'shortexp'	=> '',
				'exp'		=> 'Here you can customize the size of the main picture of your product. These sizes will affect only the main picture of the single product page. <br/><br/><strong>Just type the value without &quot;px&quot;</strong>'
		),
		
		'show_free_shipping' => array(
				'default'	=> true,
				'type'		=> 'check',
				'inputlabel'	=> 'Show the message ?',
				'title'		=> 'Show a free shipping message',
				'shortexp'	=> '',
				'exp'		=> 'Check the option if you want to show a free shipping message if the product has the free shipping option enabled.'
		),
		
		
	);
	
	$default_options['contact_page'] = array(
	
		'contact_ok' => array(
				'default' 	=> 'Your inquiry has been successfully sent to us.',
				'type' 		=> 'textarea',
				'inputlabel' 	=> 'Contact Form Confirm',
				'title' 	=> 'Contact Form Confirmation Message',
				'shortexp' 	=> '',
				'exp' 		=> 'Here you can customize the message that appears after the email has been sent, using the contact form.' 
			),
		
		'custom_email' => array(
				'default' 	=> 'your@email.com',
				'type' 		=> 'text',
				'inputlabel' 	=> 'Your Email Address',
				'title' 	=> 'Contact Form Email Address',
				'shortexp' 	=> '',
				'exp' 		=> 'Email where you\'ll receive messages from the contact page' 
			),
		
	);
	
	
	/*
		Footer related options
	*/		
	$default_options['footer_options'] = array(
				
				'footer_columns' => array(
						'default'	=> 'three',
						'type'		=> 'select',
						'selectvalues'=> array(
							'two'		=> array( 'name' => 'Footer With 2 Widgetized Columns' ),
							'three' 	=> array( 'name' => 'Footer With 3 Widgetized Columns' ),
							'four'	=> array( 'name' => 'Footer With 4 Widgetized Columns' ),
							),
						'title'		=> "Widgetized footer columns",
						'shortexp'	=> "Select how many widgetized columns you want",
						'inputlabel'	=> 'Select how many widgetized columns you want',
						'exp'		=> 'Use this option to select how many widgetized columns you want into your footer, remember to use widgets to add content to your footer.'
				),
				'footer_left_content' => array(
						'default'	=> 'content',
						'type'		=> 'select',
						'selectvalues'=> array(
							'content'		=> array( 'name' => 'Add Your Custom Content' ),
							'menu' 	=> array( 'name' => 'Display A Wordpress Powered Menu' ),
							),
						'title'		=> "Footer Bottom Left Content",
						'shortexp'	=> "Footer Bottom Left Content",
						'inputlabel'	=> 'Footer Bottom Left Content',
						'exp'		=> 'Use this option to select wich content you want to display into the footer below widgetized columns (left side)'
				),
				
				'custom_footer_left_content' => array(
						'default' 	=> 'EXPRESS - STORE &copy; 2011 EnovaStudio.org',
						'type' 		=> 'textarea',
						'inputlabel' 	=> 'Footer Bottom Left Custom Content',
						'title' 	=> 'Footer Bottom Left Custom Content',
						'shortexp' 	=> 'Footer Bottom Left Custom Content',
						'exp' 		=> 'If you have choosed to display your own custom content, use this field to add your content (Xhtml Allowed)' 
					),
				
				'footer_right_content' => array(
						'default'	=> 'content',
						'type'		=> 'select',
						'selectvalues'=> array(
							'content'		=> array( 'name' => 'Add Your Custom Content' ),
							'menu' 	=> array( 'name' => 'Display A Wordpress Powered Menu' ),
							),
						'title'		=> "Footer Bottom Right Content",
						'shortexp'	=> "Footer Bottom Right Content",
						'inputlabel'	=> 'Footer Bottom Right Content',
						'exp'		=> 'Use this option to select wich content you want to display into the footer below widgetized columns (right side)'
				),
				
				'custom_footer_right_content' => array(
						'default' 	=> '',
						'type' 		=> 'textarea',
						'inputlabel' 	=> 'Footer Bottom Right Custom Content',
						'title' 	=> 'Footer Bottom Right Custom Content',
						'shortexp' 	=> 'Footer Bottom Right Custom Content',
						'exp' 		=> 'If you have choosed to display your own custom content, use this field to add your content (Xhtml Allowed)' 
					),
	);
	
	$misc_settings = array();
		
	// Import & Export theme options 
	
	
	
	/*
		Custom Scripts, Customization, Option related options...
	*/
	$custom_code = array();
		
	
	$welcome = array();
	
	$default_options['light_editor'] = array(
		
		'page_colors'		=> array(
			'title' 	=> 'Basic Layout Colors',						
			'shortexp' 	=> 'The Main Layout Colors For Your Site',
			'exp' 		=> 'Use these options to configure the main layout colors for your site.<br/><br/> Using this light editor you can change some basic settings of the layout, however we suggest to manually edit css skin files this is the best way if you want to customize the layout of this theme.',
			'type' 		=> 'color_multi',
			'selectvalues'	=> array(
				'bodybg'	=> array(				
				'default' 	=> '#000000',
				'css_prop'	=> 'background-color',
				'selectors'	=> 'body, body.fixed_width',
				'inputlabel' 	=> 'Body Main Background Color',
				),
			'headings_color'		=> array(				
				'default' 	=> '#000000',
				'inputlabel' 	=> 'Layout Headings Text Color',
				),
			'menu_link_hover'	=> array(				
				'default' 	=> '#727171',
				'inputlabel' 	=> 'Main Menu Selected And Hover Color',
				),
			'menu_link'	=> array(				
				'default' 	=> '#AEAEAE',
				'inputlabel' 	=> 'Main Menu Link Color',
				),
			'content_link'	=> array(				
				'default' 	=> '#A7193B2',
				'inputlabel' 	=> 'Page Content Area Links Color',
				),
			'content_text'	=> array(				
				'default' 	=> '#717171',
				'inputlabel' 	=> 'Content Page Text Color',
				),
			),
		),
		
		'background_custom_img' => array(
			'default' 	=> '',
			'type' 		=> 'image_upload',
			'imagepreview' 	=> '270',
			'inputlabel' 	=> 'Upload Background Picture',
			'title'		=> 'Custom Background Picture',						
			'shortexp' 	=> '',
			'exp' 		=> 'Upload a custom background picture for your website, you can add additional background rules using the field below.'
		),
		
		'additional_bg_rules'	=> array(
				'default'	=> 'repeat',
				'type'		=> 'text',
				'inputlabel'	=> 'Additional Rules',
				'title'		=> 'Custom Additional background rules',
				'shortexp'	=> '',
				'exp'		=> 'Use this field to add additional background rules repeat-x or repeat etc...'
		),
		
	);
	
	if( epanel_option('forum_options') ){
		$forum_options = array(
				'forum_settings' => array(
						'forum_tags'		=> array(
							'default'	=> true,
							'type'		=> 'check',
							'inputlabel'	=> 'Show tags in sidebar?',
							'title'		=> 'Tag Cloud In Sidebar',
							'shortexp'	=> 'Including post tags on the forum sidebar.',
							'exp'		=> 'Tags are added by users and moderators on your forum and can help people locate posts.'
						),
						'forum_image_1'		=> array(
							'default'	=> '',
							'type'		=> 'image_upload',
							'inputlabel'	=> 'Upload Forum Image',
							'imagepreview'	=> 125,
							'title'		=> 'Forum Sidebar Image #1',
							'shortexp'	=> 'Add a 125px by 125px image to your forum sidebar',
							'exp'		=> "Spice up your forum with a promotional image in the forum sidebar."
						),
						'forum_image_link_1' => array(
							'default'	=> '',
							'type'		=> 'text',
							'inputlabel'	=> 'Image Link URL',
							'title'		=> 'Forum Image #1 Link',
							'shortexp'	=> 'Full URL for your forum image.',
							'exp'		=> "Add the full url for your forum image."
						),
						'forum_image_2' => array(
							'default'	=> '',
							'type'		=> 'image_upload',
							'imagepreview'	=> 125,
							'inputlabel'	=> 'Upload Forum Image',
							'title'		=> 'Forum Sidebar Image #2',
							'shortexp'	=> 'Add a 125px by 125px image to your forum sidebar',
							'exp'		=> "Spice up your forum with a promotional image in the forum sidebar."
						),
						'forum_image_link_2'	=> array(
							'default'	=> '',
							'type'		=> 'text',
							'inputlabel'	=> 'Image Link URL',
							'title'		=> 'Forum Image #2 Link',
							'shortexp'	=> 'Full URL for your forum image.',
							'exp'		=> "Add the full url for your forum image."
						),
						'forum_sidebar_link'	=> array(
							'default'	=> '#',
							'type'		=> 'text',
							'inputlabel'	=> 'Forum Image Caption URL',
							'title'		=> 'Forum Caption Link URL (Text Link)',
							'shortexp'	=> 'Add the URL for your forum caption (optional)',
							'exp'		=> "Text link underneath your forum images."
						),
						'forum_sidebar_link_text' => array(
							'default'	=> 'About '.get_bloginfo('name'),
							'type'		=> 'text',
							'inputlabel'	=> 'Forum Sidebar Link Text',
							'title'		=> 'Forum Sidebar Link Text',
							'shortexp'	=> 'The text of your image caption link',
							'exp'		=> "Change the text of the caption placed under your forum images."
						)
				)
		);
	}else{$forum_options = array();}
	
	/*
		Custom Options
	*/
	$custom_options = array( 'custom_options' => apply_filters('epanel_custom_options', array()) );
	
	/*
		Merge preset functions
	*/
	$optionarray = array_merge($welcome, $default_options, $forum_options);

	/*
		Load Section Options
	*/
	// Comes before, so you can load on to 'new' option sets
	$optionarray =  array_merge(load_section_options('new', 'top', $load_unavailable), $optionarray, load_section_options('new', 'bottom', $load_unavailable), $misc_settings, $custom_code);
	
	if(isset($custom_options['custom_options']) && !empty($custom_options['custom_options']))
		$optionarray = array_merge($optionarray, $custom_options);
	
	foreach($optionarray as $optionset => $options){
		$optionarray[$optionset] = array_merge( load_section_options($optionset, 'top', $load_unavailable), $options, load_section_options($optionset, 'bottom', $load_unavailable));
	}
	return apply_filters('epanel_options_array', $optionarray); 
}