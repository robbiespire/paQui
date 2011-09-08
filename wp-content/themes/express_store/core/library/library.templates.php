<?php

if(__FILE__ == $_SERVER['SCRIPT_FILENAME'])
{
    header('HTTP/1.0 404 Not Found');
      exit;
}

/*-----------------------------------------------------------------------------------*/
/* Options Framework Common functions for theme development
/*-----------------------------------------------------------------------------------*/

remove_action ('wp_head', 'rsd_link');

remove_action ('wp_head', 'wlwmanifest_link');

remove_action ('wp_head', 'wp_generator');

remove_filter ('term_description','wpautop');

add_filter('widget_text', 'do_shortcode');


remove_filter ('comment_text', 'wpautop');

function mySearchFilter($query) {
	if ($query->is_search) {
		$query->set('post_type', 'post');
	}
	return $query;
}

add_filter('pre_get_posts','mySearchFilter');

function epanel_remove_recent_comments_style() {
	global $wp_widget_factory;
	remove_action( 'wp_head', array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ) );
}
add_action( 'widgets_init', 'epanel_remove_recent_comments_style' );

/*-----------------------------------------------------------------------------------*/
/* Options Framework Sidebar Setup
/*-----------------------------------------------------------------------------------*/

/**
 * Sidebar - Call & Markup
 *
 */

function epanel_draw_sidebar($id, $name, $default = null){?>
	<ul id="<?php echo 'list_'.$id; ?>" class="sidebar_widgets fix">
		<?php if (!dynamic_sidebar($name)){ epanel_default_widget( $id, $name, $default); } ?>
	</ul>
<?php }

/**
 * Sidebar - Default Widget
 *
 */
function epanel_default_widget($id, $name, $default){
	if(isset($default) && !epanel('sidebar_no_default')):
	
		get_template_part( $default ); 
		
	elseif(!epanel('sidebar_no_default')):
	?>	

	<li class="widget-default no_<?php echo $id;?>">
			<h3 class="widget-title">Add Widgets (<?php echo $name;?>)</h3>
			<p>This is your <?php echo $name;?>. Edit this content that appears here in the <a href="<?php echo admin_url('widgets.php');?>">widgets panel</a> by adding or removing widgets in the <?php echo $name;?> area.
			</p>
	</li>

<?php endif;
	}

/**
 * Sidebar - Standard Sidebar Setup
 *
 */
function epanel_standard_sidebar($name, $description){
	return array(
		'name'=> $name,
		'description' => $description,
	    'before_widget' => '<li id="%1$s" class="%2$s widget fix"><div class="widget-pad">',
	    'after_widget' => '</div></li>',
	    'before_title' => '<h3 class="widget-title">',
	    'after_title' => '</h3>'
	);
}


/*-----------------------------------------------------------------------------------*/
/* javascript confirmations
/*-----------------------------------------------------------------------------------*/

function pl_action_confirm($name, $text){ ?>
	<script language="jscript" type="text/javascript">
		function <?php echo $name;?>(){	
			var a = confirm ("<?php echo esc_js( $text );?>");
			if(a) {
				jQuery("#input-full-submit").val(1);
				return true;
			} else return false;
		}
	</script>
<?php }


// Title and External Script Integration
function epanel_head_common(){
	?>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<?php 
		/*
		 * Print the <title> tag based on what is being viewed.
		 */
		 
		echo '<title>';
		global $page, $paged;
	
		wp_title( '|', true, 'right' );
	
		// Add the blog name.
		bloginfo( 'name' );
	
		// Add the blog description for the home/front page.
		$site_description = get_bloginfo( 'description', 'display' );
		if ( $site_description && ( is_home() || is_front_page() ) )
			echo " | $site_description";
	
		// Add a page number if necessary:
		if ( $paged >= 2 || $page >= 2 )
			echo ' | ' . sprintf( __( 'Page %s', 'epanel' ), max( $paged, $page ) );
		echo '</title>';
	/*
		Meta Images
	*/
	if(epanel_option('epanel_favicon')){
		echo '<link rel="shortcut icon" href="'.epanel_option('epanel_favicon').'" type="image/x-icon" />';
	}
	if(epanel_option('epanel_touchicon')){
		echo '<link rel="apple-touch-icon" href="'.epanel_option('epanel_touchicon').'" />';
	}
	if(epanel_option('gfonts')): ?>
		<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=<?php print_epanel_option('gfonts_families', 'molengo');?>">
	<?php endif;
	echo ( !apply_filters( 'epanel_xfn', '' ) )  ? "\r\n<link rel=\"profile\" href=\"http://gmpg.org/xfn/11\" />\r\n" : '';
	
}
	

	
// Fix IE issues to the extent possible...
function epanel_fix_ie($imagestofix = ''){?>
<?php if(epanel('google_ie')):?>
<!--[if lt IE 8]> <script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE8.js"></script> <![endif]-->
<?php endif;?>
<!--[if IE 6]>
<script src="<?php echo CORE_JS . '/ie.belatedpng.js';?>"></script> 
<script>DD_belatedPNG.fix('<?php echo $imagestofix;?>');</script>
<![endif]-->
<?php 

/*
	IE File Setting up with conditionals
	TODO Why doesnt WP allow you to conditionally enqueue scripts?
*/

// If IE6 add the Internet Explorer 6 specific stylesheet
	global $wp_styles;
	wp_enqueue_style('ie6-style', THEME_CSS  . '/ie6.css', array(), CORE_VERSION);
	$wp_styles->add_data( 'ie6-style', 'conditional', 'lte IE 6' );
	
	wp_enqueue_style('ie7-style', THEME_CSS  . '/ie7.css', array(), CORE_VERSION);
	$wp_styles->add_data( 'ie7-style', 'conditional', 'IE 7' );
	
} 

function do_dynamic_css(){
	
	// Function Temporary disabled not needed for production	
	
	
	if(is_multisite()){
		get_dynamic_css();
	} if(file_exists(EPANEL_DCSS)){
	
		echo '<link rel="stylesheet" id="epanel-dynamic-css" href="';

		echo EPANEL_DCSS_URI . '?ver=' . CORE_VERSION;

		echo '" type="text/css" media="all" />'."\n";
		
		
	} else {
		// Deprecated location, remove by 1.5.0
		echo '<link rel="stylesheet" id="epanel-dynamic-css" href="';
		echo CORE_CSS.'/dynamic.css?ver='.CORE_VERSION;
		echo '" type="text/css" media="all" />'."\n";
	}
}	

function epanel_font_replacement( $default_font = ''){
	
	if(epanel_option('typekit_script')){
		echo epanel_option('typekit_script');
	}
	
	if(epanel_option('fontreplacement')){
		global $cufon_font_path;
		
		if(epanel_option('font_file')) $cufon_font_path = epanel_option('font_file');
		elseif($default_font) $cufon_font_path = THEME_JS.'/'.$default_font;
		else $cufon_font_path = null;
		
		// ===============================
		// = Hook JS Libraries to Footer =
		// ===============================
		add_action('wp_footer', 'font_replacement_scripts');
		function font_replacement_scripts(){
			
			global $cufon_font_path;

			wp_register_script('cufon', CORE_JS.'/type.cufon.js', 'jquery', '1.09', true);
			wp_print_scripts('cufon');
			
			if(isset($cufon_font_path)){
				wp_register_script('cufon_font', $cufon_font_path, 'cufon');
				wp_print_scripts('cufon_font');
			}
		
		}
		
		add_action('wp_head', 'cufon_inline_script');
		function cufon_inline_script(){
			?><script type="text/javascript"><?php 
			if(epanel('replace_font')): 
				?>jQuery(document).ready(function () {
					Cufon.replace('<?php echo epanel("replace_font"); ?>', {hover: true});
				});<?php 
			endif;
			?></script><?php
		 }
 	}
}

/**
 * Adds the metabar or byline under the post title
 *
 * @since 4.1.0
 */
add_filter('epanel_post_metabar', 'do_shortcode', 20);
function epanel_get_post_metabar( $format = '' ) {
	
	$metabar = '';
	
	if ( is_page() )
		return; // don't do post-info on pages
	
	if( $format == 'clip'){
		
		$metabar .= sprintf( '<span class="sword">%s</span> [post_date] ', __('On','epanel') );
		$metabar .= sprintf( '<span class="sword">%s</span> [post_author_posts_link] ', __('By','epanel') );
			
	} else {
		
		if(epanel_option('byline_author')){
			$metabar .= sprintf( '<span class="sword">%s</span> [post_author_posts_link] ', __('By','epanel') );
		}

		if(epanel_option('byline_date')){
			$metabar .= sprintf( '<span class="sword">%s</span> [post_date] ', __('On','epanel') );
		}

		if(epanel_option('byline_comments')){
			$metabar .= '&middot; [post_comments] ';
		}

		if(epanel_option('byline_categories')){
			$metabar .= sprintf( '&middot; <span class="sword">%s</span> [post_categories]', __('In','epanel') );
		}
		
	}
	
	$metabar .= ' [post_edit]';
	
	printf( '<div class="metabar"><em>%s</em></div>', apply_filters('epanel_post_metabar', $metabar) );
	
}

/*-----------------------------------------------------------------------------------*/
/* Template Main Functions
/*-----------------------------------------------------------------------------------*/

if (function_exists('add_theme_support')) {
	add_theme_support( 'post-thumbnails' );
		//set_post_thumbnail_size( 595, 190, true ); // default thumbnail size
	add_theme_support( 'menus' ); 
	add_theme_support( 'automatic-feed-links' );
}

function ep_get_ID_by_page_name($page_name)
{
	global $wpdb;
	$page_name_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name = '".$page_name."'");
	return $page_name_id;
}

function get_root_page($page_id) 
{
	global $wpdb;
	
	$parent = $wpdb->get_var("SELECT post_parent FROM $wpdb->posts WHERE post_type='page' AND ID = '$page_id'");
	
	if ($parent == 0) 
		return $page_id;
	else 
		return get_root_page($parent);
}


/**
 * Get page name by ID
 */
function get_page_name_by_ID($page_id)
{
	global $wpdb;
	$page_name = $wpdb->get_var("SELECT post_title FROM $wpdb->posts WHERE ID = '$page_id'");
	return $page_name;
}


/**
 * Get page ID by Page Template
 */
function get_page_ID_by_page_template($template_name)
{
	global $wpdb;
	$page_ID = $wpdb->get_var("SELECT post_id FROM $wpdb->postmeta WHERE meta_value = '$template_name' AND meta_key = '_wp_page_template'");
	return $page_ID;
}


/**
 * Get page ID by Custom Field Value
 */
function get_page_ID_by_custom_field_value($custom_field, $value)
{
	global $wpdb;
	$page_ID = $wpdb->get_var("
	    SELECT wposts.ID
    	FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta
	    WHERE wposts.ID = wpostmeta.post_id 
    	AND wpostmeta.meta_key = '$custom_field' 
	    AND (wpostmeta.meta_value like '$value,%' OR wpostmeta.meta_value like '%,$value,%' OR wpostmeta.meta_value like '%,$value' OR wpostmeta.meta_value = '$value')		
    	AND wposts.post_status = 'publish' 
	    AND wposts.post_type = 'page'
		LIMIT 0, 1");

	return $page_ID;
}

/**
 * Url parameter function
 */

function getLink($url,$params=array(),$use_existing_arguments=false) {
    if($use_existing_arguments) $params = $params + $_GET;
    if(!$params) return $url;
    $link = $url;
    if(strpos($link,'?') === false) $link .= '?'; //If there is no '?' add one at the end
    elseif(!preg_match('/(\?|\&(amp;)?)$/',$link)) $link .= '&amp;'; //If there is no '&' at the END, add one.
    
    $params_arr = array();
    foreach($params as $key=>$value) {
        if(gettype($value) == 'array') { //Handle array data properly
            foreach($value as $val) {
                $params_arr[] = $key . '[]=' . urlencode($val);
            }
        } else {
            $params_arr[] = $key . '=' . urlencode($value);
        }
    }
    $link .= implode('&amp;',$params_arr);
    
    return $link;
}

/*-----------------------------------------------------------------------------------*/
/* Excerpt and Content trunc
/*-----------------------------------------------------------------------------------*/

function excerpt($limit) {
  $excerpt = explode(' ', get_the_excerpt(), $limit);
  if (count($excerpt)>=$limit) {
    array_pop($excerpt);
    $excerpt = implode(" ",$excerpt).'...';
  } else {
    $excerpt = implode(" ",$excerpt);
  }	
  $excerpt = preg_replace('`\[[^\]]*\]`','',$excerpt);
  return $excerpt;
}
 
function content($limit) {
  $content = explode(' ', get_the_content(), $limit);
  if (count($content)>=$limit) {
    array_pop($content);
    $content = implode(" ",$content).'...';
  } else {
    $content = implode(" ",$content);
  }	
  $content = preg_replace('/\[.+\]/','', $content);
  $content = apply_filters('the_content', $content); 
  $content = str_replace(']]>', ']]&gt;', $content);
  return $content;
}

/*-----------------------------------------------------------------------------------*/
/* WP Menus auto creation
/*-----------------------------------------------------------------------------------*/

function ep_theme_navigations() {
	register_nav_menu( 'ep-main-navigation', __( 'EP Main Navigation' ) );
	register_nav_menu( 'ep-bottomright-navigation', __( 'EP Bottom Footer Right Navigation' ) ); 
	register_nav_menu( 'ep-bottomleft-navigation', __( 'EP Bottom Footer Left Navigation' ) );	
}

add_action( 'init', 'ep_theme_navigations' );

wp_create_nav_menu( 'Main Navigation Menu', array( 'slug' => 'ep-main-menu' ) );  
wp_create_nav_menu( 'Bottom Right Navigation Menu', array( 'slug' => 'ep-bottomright-menu' ) );  
wp_create_nav_menu( 'Bottom Left Navigation Menu', array( 'slug' => 'ep-bottomleft-menu' ) );
 

/*-----------------------------------------------------------------------------------*/
/* Load Template Required Scripts
/*-----------------------------------------------------------------------------------*/

function epanel_template_scripts() {
	if (!is_admin()) {
		
		wp_deregister_script('jquery');
		wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js', false, '1.4');
		wp_enqueue_script('jquery');
		
		if (epanel_option('fontreplacement')) {
		
			wp_enqueue_script('cufon', get_template_directory_uri() . '/js/cufon.js', 'jquery');
			wp_enqueue_script('cufon-font', get_template_directory_uri() . '/js/cufon/'. epanel_option('cufon_font_file'), 'jquery');
		}
		wp_enqueue_script('menu', get_template_directory_uri() . '/js/superfish.js', 'jquery');
		wp_enqueue_script('menu-hover', get_template_directory_uri() . '/js/hoverIntent.js', 'jquery');
		wp_enqueue_script('menu-bgframe', get_template_directory_uri() . '/js/jquery.bgiframe.min.js', 'jquery');
		wp_enqueue_script('menu-super', get_template_directory_uri() . '/js/supersubs.js', 'jquery');

		wp_enqueue_script('easing', get_template_directory_uri() . '/js/easing.js', 'jquery');
		
		if (epanel_option('enabled_product_slider')) { 
		wp_enqueue_script('carousel', get_template_directory_uri() . '/js/carousel.js', 'jquery');
		}
		wp_register_script('flow', get_template_directory_uri() . '/js/flowplayer-3.2.4.min.js', 'jquery');
		wp_enqueue_script('flow');
		
		if (epanel_option('home_slider') == 'nivo') {
		
			wp_enqueue_style('nivo-slider-css', get_template_directory_uri() . '/css/nivo-slider.css');
			wp_enqueue_script('nivo-slider', get_template_directory_uri() . '/js/jquery.nivo.slider.js');

		} else if (epanel_option('home_slider') == 'bx' OR 'mini') {
			
			wp_enqueue_style('bx-css', get_template_directory_uri() . '/css/bx_styles/bx_styles.css');
			wp_enqueue_script('bx-slider', get_template_directory_uri() . '/js/jquery.bxSlider.min.js');
		
		}
		
		wp_enqueue_script('tabs', get_template_directory_uri() . '/js/jquery.tools.tabs.min.js', 'jquery');
		//wp_enqueue_script('colorbox', get_template_directory_uri() . '/js/jquery.colorbox-min.js', 'jquery');
		wp_enqueue_script('block', get_template_directory_uri() . '/js/block.js', 'jquery');		
		wp_enqueue_script('custom', get_template_directory_uri() . '/js/custom.js', 'jquery');
		wp_enqueue_style('fix-css', get_template_directory_uri() . '/css/fix.css');
		
	}
	
}
add_action('init', 'epanel_template_scripts');