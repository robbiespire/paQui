<?php

if(__FILE__ == $_SERVER['SCRIPT_FILENAME'])
{
    header('HTTP/1.0 404 Not Found');
      exit;
}
/*-----------------------------------------------------------------------------------*/
/* Options Framework Dynamic Css handler - still under development
/*-----------------------------------------------------------------------------------*/
class EPANEL_WPCSS {

	
	function create() {
		
		$this->intro();
		$this->typography();
		$this->layout();
		$this->dynamic_grid();
		$this->options();
		$this->custom_css();
		
	}

	function intro(){
		$this->css .= "/* THIS COMPONENT IS STILL UNDER DEVELOPMENT - NOT FOR PRODUCTIONS */\n\n";
		if(is_multisite()) 	$this->css .= "/* Loaded inline inside of WP Multisite installations */\n\n";
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* Options Framework TYPOGRAPHY HANDLER
	/*-----------------------------------------------------------------------------------*/
	
	function typography(){
		
		$this->css .= '/* Typography --------------- */'."\n\n";
		
		foreach (get_option_array() as $mid){
			
			foreach($mid as $oid => $o){ 
				
				if($o['type'] == 'typography'){
					
					$type_foundry = new EPANEL_WPFoundry;

					$type = epanel_option($oid);
					
					$font_id = $type['font'];
					
					// Don't render if font isn't set.
					if(isset($font_id) && isset($type_foundry->foundry[$font_id]) ){
						
						if($type_foundry->foundry[$font_id]['google'])
							$google_fonts[] = $font_id;

						$type_selectors = $o['selectors']; 

						if( isset($type['selectors']) && !empty($type['selectors']) ) $type_selectors .=  ',' . trim(trim($type['selectors']), ",");

						$type_css = $type_foundry->get_type_css($type);
					
					
						$type_css_keys[] = $type_selectors . "{".$type_css."}"."\n";
					}
					
				}
				
			}
		}
		
		$this->css .= ( isset($google_fonts) && is_array($google_fonts ) ? '/* Import Google Fonts */'."\n". $type_foundry->google_import($google_fonts) . "\n" : '');
		
		$this->css .= '/* Set Type */'."\n";
		
		// Render the font CSS
		if(isset($type_css_keys) && is_array($type_css_keys)){
			foreach($type_css_keys as $typeface){
				$this->css .= $typeface ."\n";
			}
		}

		$this->css .= "\n"; // new line
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* Options Framework LAYOUT HANDLER - JUST FOR FUN WE SHOUND'T NEED THIS
	/* BUDDY PRESS TEST - BUDDYPRESS SUX :D
	/*-----------------------------------------------------------------------------------*/
	
	function layout(){
		
		global $epanel_layout; 
		global $post; 

		$this->css .= '/* Dynamic Layout --------------- */'."\n\n";
		
		/* Fixed Width Page */
		$fixed_page = $epanel_layout->content->width + 20;
		$this->css .= ".fixed_width #page, .fixed_width #footer, .canvas #page-canvas{width:".$fixed_page."px}\n";

		
		/* Content Width */
		$content_with_border = $epanel_layout->content->width + 2;
		$this->css .= "#page-main .content{width:".$content_with_border."px}\n";
		$this->css .= "#site{min-width:".$content_with_border."px}\n"; // Fix small horizontal scroll issue
		$this->css .= "#site .content, .wcontent, #primary-nav ul.main-nav.nosearch{width:".$epanel_layout->content->width."px}\n";
		
		/* Navigation Width */
		$nav_width = $epanel_layout->content->width - 220;
		$this->css .= "#primary-nav ul.main-nav{width:".$nav_width."px}\n";
		$this->css .= "\n";
		
		// For inline CSS in Multisite
		// TODO clean up layout variable handling
		$page_layout = $epanel_layout->layout_mode;
		
		/* Layout Modes */
		foreach(get_the_layouts() as $layout_mode){
			$epanel_layout->build_layout($layout_mode);
		
			//Setup for CSS
			$mode = '.'.$layout_mode.' ';
			$this->css .= $mode."#epanel_content #column-main, ".$mode.".wmain, ".$mode."#buddypress-page #container{width:". $epanel_layout->main_content->width."px}\n";
			$this->css .= $mode."#epanel_content #sidebar1, ".$mode."#buddypress-page #sidebar1{width:". $epanel_layout->sidebar1->width."px}\n";
			$this->css .= $mode."#epanel_content #sidebar2, ".$mode."#buddypress-page #sidebar2{width:". $epanel_layout->sidebar2->width."px}\n";
			$this->css .= $mode."#epanel_content #column-wrap, ".$mode."#buddypress-page #container{width:". $epanel_layout->column_wrap->width."px}\n";
			$this->css .= $mode."#epanel_content #sidebar-wrap, ".$mode."#buddypress-page #sidebar-wrap{width:". $epanel_layout->sidebar_wrap->width."px}\n\n";
		}
		
		// Put back to original mode for page layouts in multisite
		$epanel_layout->build_layout($page_layout);
		
	}
	
	function dynamic_grid(){
		global $epanel_layout; 
		
		// dynamic grid generator
		
		$this->css .= '/* Dynamic Grid --------------- */'."\n\n";
		for($i = 2; $i <= 5; $i++){
			$this->css .= '.dcol_container_'.$i.'{width: '.$epanel_layout->dcol[$i]->container_width.'px; float: right;}'."\n";
			$this->css .= '.dcol_'.$i.'{width: '.$epanel_layout->dcol[$i]->width.'px; margin-left: '.$epanel_layout->dcol[$i]->gutter_width.'px;}'."\n\n";
		}
		
	}
	
	function options(){
		// Color and other options handler
		$this->css .= '/* Options --------------- */'."\n\n";
		foreach (get_option_array() as $menuitem){

			foreach($menuitem as $optionid => $option_info){ 
				
				if($option_info['type'] == 'css_option' && epanel_option($optionid)){
					if(isset($option_info['css_prop']) && isset($option_info['selectors'])){
						
						$css_units = (isset($option_info['css_units'])) ? $option_info['css_units'] : '';
						
						$this->css .= $option_info['selectors'].'{'.$option_info['css_prop'].':'.epanel_option($optionid).$css_units.';}'."\n";
					}

				}
				
				if( $option_info['type'] == 'background_image' && epanel_option($optionid.'_url')){
					
					$bg_repeat = (epanel_option($optionid.'_repeat')) ? epanel_option($optionid.'_repeat'): 'no-repeat';
					$bg_pos_vert = (epanel_option($optionid.'_pos_vert') || epanel_option($optionid.'_pos_vert') == 0 ) ? (int) epanel_option($optionid.'_pos_vert') : '0';
					$bg_pos_hor = (epanel_option($optionid.'_pos_hor') || epanel_option($optionid.'_pos_hor') == 0 ) ? (int) epanel_option($optionid.'_pos_hor') : '50';
					$bg_selector = (epanel_option($optionid.'_selector')) ? epanel_option($optionid.'_selector') : $option_info['selectors'];
					$bg_url = epanel_option($optionid.'_url');
					
					$this->css .= $bg_selector ."{background-image:url('".$bg_url."');}"."\n";
					$this->css .= $bg_selector ."{background-repeat:".$bg_repeat.";}"."\n";
					$this->css .= $bg_selector ."{background-position:".$bg_pos_hor."% ".$bg_pos_vert."%;}"."\n";
					
					
				}
	
				
				if($option_info['type'] == 'colorpicker'){
					
					$this->_css_colors($optionid, $option_info['selectors'], $option_info['css_prop']);

				}
				
				elseif($option_info['type'] == 'color_multi'){
					
					foreach($option_info['selectvalues'] as $moption_id => $m_option_info){
						
						$the_css_selectors = (isset($m_option_info['selectors'])) ? $m_option_info['selectors'] : null ;
						$the_css_property = (isset($m_option_info['css_prop'])) ? $m_option_info['css_prop'] : null ;
						
						$this->_css_colors($moption_id, $the_css_selectors, $the_css_property);
					}
					
				}
			} 
		}
		$this->css .= "\n\n";
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* Options Framework main Color Handler
	/*-----------------------------------------------------------------------------------*/
	
	function _css_colors( $optionid, $selectors = null, $css_prop = null ){
		if( epanel_option($optionid) ){
			
			if(isset($css_prop)){
			
				if(is_array($css_prop)){
				
					foreach( $css_prop as $css_property => $css_selectors ){

						if($css_property == 'text-shadow'){
							$this->css .= $css_selectors . '{ text-shadow:'.epanel_option($optionid).' 0 1px 0;}'."\n";		
						} elseif($css_property == 'text-shadow-top'){
							$this->css .= $css_selectors . '{ text-shadow:'.epanel_option($optionid).' 0 -1px 0;}'."\n";		
						}else {
							$this->css .= $css_selectors . '{'.$css_property.':'.epanel_option($optionid).';}'."\n";		
						}
						
					}
				
				}else{
					$this->css .= $selectors.'{'.$css_prop.':'.epanel_option($optionid).';}'."\n";
				}
			
			} else {
				$this->css .= $selectors.'{color:'.epanel_option($optionid).';}'."\n";
			}
		}
	}
	
	function custom_css(){
		$this->css .= '/* Custom CSS */'."\n\n";
		$this->css .= epanel_option('customcss');
		$this->css .= "\n\n";
	}

}

/*-----------------------------------------------------------------------------------*/
/* Let's write our new awesome css file - still under dev
/*-----------------------------------------------------------------------------------*/

function epanel_build_dynamic_css( $trigger = 'N/A' ){
	
	$epanel_dynamic_css = new EPANEL_WPCSS;
	$epanel_dynamic_css->create();
	
	// Create directories and folders for storing dynamic files
	if(!file_exists(EPANEL_DCSS) ) epanel_make_uploads();
	
	
	// Write to dynamic files
	if ( is_writable(EPANEL_DCSS) && !is_multisite()){

		$lid = @fopen(EPANEL_DCSS, 'w');
		@fwrite($lid, $epanel_dynamic_css->css ."\n\n/* Dynamic css builder ". $trigger . ' -- Still under dev remote folder */');
		@fclose($lid);
		
	}
	
	// TODO - Deprecate this
	// Old way of doing dynamic CSS; deprecated.
	if (!file_exists(EPANEL_DCSS) && is_writable(CORE . '/css/dynamic.css') && !is_multisite()) {
		
		$lid = @fopen(CORE . '/css/dynamic.css', 'w');
		@fwrite($lid, $epanel_dynamic_css->css ."\n\n/* Triggered By ". $trigger . ' */');
		@fclose($lid);
	}
}

function get_dynamic_css(){
	$epanel_dynamic_css = new EPANEL_WPCSS;
	$epanel_dynamic_css->create();
	echo '<style type="text/css">'."\n\n". $epanel_dynamic_css->css . "\n".'</style>'. "\n";
}
/********** END OF CSS CLASS  **********/