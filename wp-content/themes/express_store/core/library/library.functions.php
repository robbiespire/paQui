<?php

if(__FILE__ == $_SERVER['SCRIPT_FILENAME'])
{
    header('HTTP/1.0 404 Not Found');
      exit;
}

/*-----------------------------------------------------------------------------------*/
/* Options Framework Check If the theme is installed in the right directory
/*-----------------------------------------------------------------------------------*/

function correct_path() {

	if( file_exists(get_theme_root().'/express_store/style.css' ) ) {
	    // do nothing
	} else {
	
		echo '<div id="message" class="error" style="width: 97%; font-size:11px; line-height:1.6em;"> ERROR : You have successfully activated this theme but you may have problems. <br/> It looks like that the theme path of the Express Store theme is wrong! Currently this is your theme path : '. TEMPLATEPATH .', but it <br/><strong>MUST BE '.get_theme_root().'/express_store/'.'</strong>, having the express_store theme folder inside another folder will cause problems, make sure that the express_store folder is inside the wp-content/themes/ folder and not in any other subfolder</div>';
} }

add_action('admin_head','correct_path');

// E-Panel Framework Update Interface

/*----------------------------------------------------------------------------------------*/
/* Framework Update Interface Component - update_notifier_menu + get_latest_theme_version */
/*----------------------------------------------------------------------------------------*/

function update_notifier_menu() {  
	$xml = get_latest_theme_version(21600); // This tells the function to cache the remote call for 21600 seconds (6 hours)
	$theme_data = get_theme_data(TEMPLATEPATH . '/style.css'); // Get theme data from style.css (current version is what we want)
	
	if(version_compare($theme_data['Version'], $xml->latest) == -1) {
		global $theme_update_notice, $pagenow;
		if ( $pagenow == "index.php") { ?>
		
		<?php $theme_data = get_theme_data(TEMPLATEPATH . '/style.css'); ?>
				
				<div id="message" class="updated fade" style="width: 97%;">
					<p>A new version of <?php echo $theme_data['Title']; ?> theme is available for download. To update the Theme, login to <a target="_blank" href="http://www.themeforest.net/">ThemeForest</a>, head over to your <strong>downloads</strong> section and re-download the theme like you did when you bought it.</p>
				</div>
				
				<div id="message" class="updated metabox-holder" style="background: none; border:none; width:98%;">
					
					<div class="postbox-container-new">
						
						<div id="dashboard_right_now" class="postbox " style="background: #fffddf;">
						<div class="handlediv" title="Click to toggle"><br></div><h3 class="hndle"><span><?php echo $theme_data['Title'];?> Theme Update Available</span></h3>
							<div class="inside">
							
								<div id="notice">
									
									
									<div class="table table_content">
										<p class="sub">Update Instruction And Information</p>
										<p>Hello <strong><?php global $current_user; get_currentuserinfo(); echo $current_user->user_login . "\n"; ?></strong>, a new version of <?php echo $theme_data['Title']; ?> theme is available for download.  </p>
										
										<p>You are running <?php echo '<strong>'.$theme_data['Title'] . '</strong> <strong style="color:red;">' . $theme_data['Version'] .'</strong>';?>, the latest available theme version is <strong style="color:green;"><?php echo $xml->latest; ?></strong> . <p>To update the Theme, login to <a href="http://www.themeforest.net/">ThemeForest</a>, head over to your <strong>downloads</strong> section and re-download the theme like you did when you bought it.</p>
											<p>Extract the zip's contents, look for the extracted theme folder, and after you have all the new files upload them using FTP to the <strong>/wp-content/themes/express_store</strong> folder overwriting the old ones (this is why it's important to backup any changes you've made to the theme files).</p>
											<p>If you didn't make any changes to the theme files, you are free to overwrite them with the new ones without the risk of losing theme settings, pages, posts, etc, and backwards compatibility is guaranteed.</p></p>
									
									</div>
									
									<div class="table table_discussion">
										<p class="sub"><?php echo $theme_data['Title'];?> Theme Updates Changelog</p>
										
											<p>Take a look at changes made to <?php echo $theme_data['Title']; ?></p> 
												
												<p><a href="#" onClick="jQuery(this).parent().parent().parent().find('.fullchange').slideToggle();">View Full Changelog</a></p>
												
												<div class="fullchange">
													<p><?php echo $xml->changelog; ?></p>
												</div>
											
										</div>
										
										<br class="clear">
									
								</div>
								
								
								
							</div>
						</div>
						
					</div>
					
					
					
				</div>
		<?php }
		 
		 if ( $pagenow !== "index.php") { ?>
		 
		 	<div id="message" class="updated fade" style="width: 97%; line-height:1.6em;">
		 		<p style="font-size:11px !important;">A new version of <?php echo $theme_data['Title']; ?> theme is available for download. To update the Theme, login to <a target="_blank" href="http://www.themeforest.net/">ThemeForest</a>, head over to your <strong>downloads</strong> section and re-download the theme like you did when you bought it, please for more detailed instructions and update informations take a look at your <a href="<?php echo  get_admin_url();?>">wordpress dashboard</a>.</p>
		 	</div>
		 
		 <?php 
		 
		 } }
	}


add_action('admin_head','update_notifier_menu');

function get_latest_theme_version($interval) {
	// remote xml file location
	$notifier_file_url = 'http://enovastudio.org/updates/express22_log.xml';
	
	$db_cache_field = 'express-theme-notifier-cache2';
	$db_cache_field_last_updated = 'express-theme-notifier-last-updated2';
	$last = get_option( $db_cache_field_last_updated );
	$now = time();
	// check the cache
	if ( !$last || (( $now - $last ) > $interval) ) {
		// cache doesn't exist, or is old, so refresh it
		if( function_exists('curl_init') ) { // if cURL is available, use it...
			$ch = curl_init($notifier_file_url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			$cache = curl_exec($ch);
			curl_close($ch);
		} else {
			$cache = file_get_contents($notifier_file_url); // ...if not, use the common file_get_contents()
		}
		
		if ($cache) {			
			// we got good results
			update_option( $db_cache_field, $cache );
			update_option( $db_cache_field_last_updated, time() );			
		}
		// read from the cache file
		$notifier_data = get_option( $db_cache_field );
	}
	else {
		// cache file is fresh enough, so read from it
		$notifier_data = get_option( $db_cache_field );
	}
	
	$xml = simplexml_load_string($notifier_data); 
	
	return $xml;
}

/*-----------------------------------------------------------------------------------*/
/* Dynamic sidebars parameters
/*-----------------------------------------------------------------------------------*/

function widget_first_last_classes($params) {

	global $my_widget_num; // Global a counter array
	$this_id = $params[0]['id']; // Get the id for the current sidebar we're processing
	$arr_registered_widgets = wp_get_sidebars_widgets(); // Get an array of ALL registered widgets	

	if(!$my_widget_num) {// If the counter array doesn't exist, create it
		$my_widget_num = array();
	}

	if(!isset($arr_registered_widgets[$this_id]) || !is_array($arr_registered_widgets[$this_id])) { // Check if the current sidebar has no widgets
		return $params; // No widgets in this sidebar... bail early.
	}

	if(isset($my_widget_num[$this_id])) { // See if the counter array has an entry for this sidebar
		$my_widget_num[$this_id] ++;
	} else { // If not, create it starting with 1
		$my_widget_num[$this_id] = 1;
	}

	$class = 'class="widget-' . $my_widget_num[$this_id] . ' '; // Add a widget number class for additional styling options

	if($my_widget_num[$this_id] == 1) { // If this is the first widget
		$class .= 'widget-first ';
	} elseif($my_widget_num[$this_id] == count($arr_registered_widgets[$this_id])) { // If this is the last widget
		$class .= 'last ';
	}

	$params[0]['before_widget'] = str_replace('class="', $class, $params[0]['before_widget']); // Insert our new classes into "before widget"

	return $params;

}
add_filter('dynamic_sidebar_params','widget_first_last_classes');

/*-----------------------------------------------------------------------------------*/
/* hook ajax into default wordpress menu action
/*-----------------------------------------------------------------------------------*/

if(!function_exists('epanel_ajax_switch_menu_walker'))
{
	function epanel_ajax_switch_menu_walker()
	{	
		if ( ! current_user_can( 'edit_theme_options' ) )
		die('-1');

		check_ajax_referer( 'add-menu_item', 'menu-settings-column-nonce' );
	
		require_once ABSPATH . 'wp-admin/includes/nav-menu.php';
	
		$item_ids = wp_save_nav_menu_items( 0, $_POST['menu-item'] );
		if ( is_wp_error( $item_ids ) )
			die('-1');
	
		foreach ( (array) $item_ids as $menu_item_id ) {
			$menu_obj = get_post( $menu_item_id );
			if ( ! empty( $menu_obj->ID ) ) {
				$menu_obj = wp_setup_nav_menu_item( $menu_obj );
				$menu_obj->label = $menu_obj->title; // don't show "(pending)" in ajax-added items
				$menu_items[] = $menu_obj;
			}
		}
	
		if ( ! empty( $menu_items ) ) {
			$args = array(
				'after' => '',
				'before' => '',
				'link_after' => '',
				'link_before' => '',
				'walker' => new epanel_backend_walker,
			);
			echo walk_nav_menu_tree( $menu_items, 0, (object) $args );
		}
		
		die('end');
	}
	
	//hook into wordpress admin.php
	add_action('wp_ajax_epanel_ajax_switch_menu_walker', 'epanel_ajax_switch_menu_walker');
}

/*-----------------------------------------------------------------------------------*/
/* Options Framework Functions Library handler
/*-----------------------------------------------------------------------------------*/

// Additional classes for layout mode - not required at this time 
function epanel_body_classes(){
	$design_mode = (epanel_option('site_design_mode')) ? epanel_option('site_design_mode') : 'full_width';
		
	$body_classes = '';
	$body_classes .= $design_mode;
	if(epanel_is_buddypress_active() && !epanel_bbpress_forum()){
		$body_classes .= ' buddypress';
	}

	global $epanel_template;
	$body_classes .= ' ttype-'.$epanel_template->template_type.' tmain-'.$epanel_template->main_type;
	
	return $body_classes;
}


// globalize page ID
function epanel_id_setup(){
	global $post;
	global $epanel_ID;
	global $epanel_post;

	if(isset($post) && is_object($post)){
		$epanel_ID = $post->ID;
		$epanel_post = $post;	
	}
	else {
		$epanel_post = '';
		$epanel_ID = '';
	}
	
}

/*-----------------------------------------------------------------------------------*/
/* Options Framework Hook register
/*-----------------------------------------------------------------------------------*/
function epanel_register_hook( $hook_name, $hook_area_id){
	

	if(is_admin()){
		/*
			Register The Hook
		*/
		
		global $registered_hooks;
		
		if( !isset($registered_hooks[$hook_area_id]) ) $registered_hooks[$hook_area_id] = array();
	
		if( isset($registered_hooks[$hook_area_id]) && is_array($registered_hooks[$hook_area_id]) ){
			$flipped_hooks = array_flip($registered_hooks[$hook_area_id]);
		}
	
		if( !isset($flipped_hooks[$hook_name]) ){
			$registered_hooks[$hook_area_id][] = $hook_name;
		}
		
	} else {
		
		/*
			Do The Hook
		*/
		do_action( $hook_name );
		
	}

}

// Check logged in user level - just for fun
function checkauthority(){
	if (!current_user_can('edit_themes'))
	wp_die('Sorry, but you don&#8217;t have the administrative privileges needed to do this.');
}

// IE Sux - Microsoft = Xbox360
function ie_version() {
  $match=preg_match('/MSIE ([0-9]\.[0-9])/',$_SERVER['HTTP_USER_AGENT'],$reg);
  if($match==0)
    return false;
  else
    return floatval($reg[1]);
}

// Tinyurl - just for fun
function getTinyUrl($url) {   
	$response = wp_remote_get("http://tinyurl.com/api-create.php?url=".$url);
	
	if( is_wp_error( $response ) )
		return $url;
	else
		return wp_remote_retrieve_body($response);

}

/*-----------------------------------------------------------------------------------*/
/* Options Framework layout model
/*-----------------------------------------------------------------------------------*/

function epanel_layout_mode() {

	global $epanel_layout;
	global $post;

	if(!epanel_is_posts_page() && isset($post) && get_post_meta($post->ID, '_epanel_layout_mode', true)){
		$epanel_layout->build_layout(get_post_meta($post->ID, '_epanel_layout_mode', true));
		return get_post_meta($post->ID, '_epanel_layout_mode', true);
	} elseif(epanel_is_posts_page() && epanel_option('posts_page_layout')){
		$epanel_layout->build_layout(epanel_option('posts_page_layout'));
		return epanel_option('posts_page_layout');
	} else {
		return $epanel_layout->layout_mode;
	}

}

// Content width for dynamic layout - only for development not for production
function epanel_current_page_content_width() {

	global $epanel_layout;
	global $content_width;
	global $post;

	$mode = epanel_layout_mode();
	
	$c_width = $epanel_layout->layout_map[$mode]['maincolumn_width'];
	
	if ( !isset( $content_width ) ) $content_width = $c_width - 45;

}

// Check for more pages - not for production

function show_posts_nav() {
	global $wp_query;
	return ($wp_query->max_num_pages > 1);
}


// Only for development
function show_query_analysis(){
	if (current_user_can('administrator')){
	    global $wpdb;
	    echo "<pre>";
	    print_r($wpdb->queries);
	    echo "</pre>";
	}
}

// Add page callback
function epanel_add_page($file, $name){
	global $epanel_user_pages;
	
	$epanel_user_pages[$file] = array('name' => $name);
	
}

function epanel_remove_page($file){
	global $epanel_user_pages;
	
	
}

// used for wp 3.0 callback 
function epanel_setup_menu() {
	echo 'Add links using WordPress menus in your site admin.';
}

//fallback wp 3 menus for theme locations
function epanel_no_menu_location() {
	echo '<p class="no-menu">Please select the menu theme location from your <a href="'.admin_url().'">WordPress Menu Page</a> and save it.';
}

//support url
function support_url() { 
	echo 'http://themeforest.net/user/Enova#from';
}

//Theme Url
function theme_url() {
	echo 'http://themeforest.net/item/';
}

//Documentation Url
function doc_url() {
	echo TEMPLATEPATH . '/documentation/';
}

// Load dynamic parts - not for production now
function setup_epanel_template() {
	get_template_part( 'template.load' );
}

// Callback for childthemes
function epanel_add_page_callback($page_array){
	global $epanel_user_pages;
	
	if( is_array($epanel_user_pages) ){
		foreach($epanel_user_pages as $file => $pageinfo){
			$page_array[$file] = array('name'=>$pageinfo['name']);
		}
	}
	
	return $page_array;
}

// Create the upload folder
function epanel_make_uploads(){
	if (!is_dir(MAIN_UPLOAD_DIR)){
		if(@mkdir(MAIN_UPLOAD_DIR) && @chmod(MAIN_UPLOAD_DIR, 0777)) $return = true;
	}
	if (!is_dir(EPANEL_UPLOAD_DIR)){
		if(@mkdir(EPANEL_UPLOAD_DIR) && @chmod(EPANEL_UPLOAD_DIR, 0777)) $return = true;
	}
	
	if(@file_put_contents(EPANEL_DCSS, 'Load') && @chmod(EPANEL_DCSS, 0777)) $return = true;
	
	return $return;
}

/* ------------------------------------------------------------------------*
 * Breadcumb
 * ------------------------------------------------------------------------*/
 
 function dimox_breadcrumbs() {
  
   $delimiter = '&raquo;';
   $home = 'Home'; // text for the 'Home' link
   $before = '<span class="current">'; // tag before the current crumb
   $after = '</span>'; // tag after the current crumb
  
   if ( !is_home() && !is_front_page() || is_paged() ) {
  
     echo '<div id="crumbs">';
     _e('<span class="you">You Are Here : </span>','epanel');
  
     global $post;
     $homeLink = get_bloginfo('url');
     echo '<a href="' . $homeLink . '">' . $home . '</a> ' . $delimiter . ' ';
  
     if ( is_category() ) {
       global $wp_query;
       $cat_obj = $wp_query->get_queried_object();
       $thisCat = $cat_obj->term_id;
       $thisCat = get_category($thisCat);
       $parentCat = get_category($thisCat->parent);
       if ($thisCat->parent != 0) echo(get_category_parents($parentCat, TRUE, ' ' . $delimiter . ' '));
       echo $before . 'Archive by category "' . single_cat_title('', false) . '"' . $after;
  
     } elseif ( is_day() ) {
       echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
       echo '<a href="' . get_month_link(get_the_time('Y'),get_the_time('m')) . '">' . get_the_time('F') . '</a> ' . $delimiter . ' ';
       echo $before . get_the_time('d') . $after;
  
     } elseif ( is_month() ) {
       echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
       echo $before . get_the_time('F') . $after;
  
     } elseif ( is_year() ) {
       echo $before . get_the_time('Y') . $after;
  
     } elseif ( is_single() && !is_attachment() ) {
       if ( get_post_type() != 'post' ) {
         $post_type = get_post_type_object(get_post_type());
         $slug = $post_type->rewrite;
         echo '<a href="' . $homeLink . '/' . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a> ' . $delimiter . ' ';
         echo $before . get_the_title() . $after;
       } else {
         $cat = get_the_category(); $cat = $cat[0];
         echo get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
         echo $before . get_the_title() . $after;
       }
  
     } elseif ( !is_single() && !is_page() && get_post_type() != 'post' ) {
       $post_type = get_post_type_object(get_post_type());
       echo $before . $post_type->labels->singular_name . $after;
  
     } elseif ( is_attachment() ) {
       $parent = get_post($post->post_parent);
       $cat = get_the_category($parent->ID); $cat = $cat[0];
       echo get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
       echo '<a href="' . get_permalink($parent) . '">' . $parent->post_title . '</a> ' . $delimiter . ' ';
       echo $before . get_the_title() . $after;
  
     } elseif ( is_page() && !$post->post_parent ) {
       echo $before . get_the_title() . $after;
  
     } elseif ( is_page() && $post->post_parent ) {
       $parent_id  = $post->post_parent;
       $breadcrumbs = array();
       while ($parent_id) {
         $page = get_page($parent_id);
         $breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
         $parent_id  = $page->post_parent;
       }
       $breadcrumbs = array_reverse($breadcrumbs);
       foreach ($breadcrumbs as $crumb) echo $crumb . ' ' . $delimiter . ' ';
       echo $before . get_the_title() . $after;
  
     } elseif ( is_search() ) {
       echo $before . 'Search results for "' . get_search_query() . '"' . $after;
  
     } elseif ( is_tag() ) {
       echo $before . 'Posts tagged "' . single_tag_title('', false) . '"' . $after;
  
     } elseif ( is_author() ) {
        global $author;
       $userdata = get_userdata($author);
       echo $before . 'Articles posted by ' . $userdata->display_name . $after;
  
     } elseif ( is_404() ) {
       echo $before . 'Error 404' . $after;
     }
  
     if ( get_query_var('paged') ) {
       if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ' (';
       echo __(' Page') . ' ' . get_query_var('paged');
       if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ')';
     }
  
     echo '</div>';
  
   }
 } // end dimox_breadcrumbs()

/*-----------------------------------------------------------------------------------*/
/* Resize images dynamically using wp built in functions */
/*-----------------------------------------------------------------------------------*/

/*
 * Resize images dynamically using wp built in functions
 * Victor Teixeira
 *
 * php 5.2+
 *
 * Exemple use:
 * 
 * <?php 
 * $thumb = get_post_thumbnail_id(); 
 * $image = vt_resize( $thumb,'' , 140, 110, true );
 * ?>
 * <img src="<?php echo $image[url]; ?>" width="<?php echo $image[width]; ?>" height="<?php echo $image[height]; ?>" />
 *
 * @param int $attach_id
 * @param string $img_url
 * @param int $width
 * @param int $height
 * @param bool $crop
 * @return array
 */
function vt_resize( $attach_id = null, $img_url = null, $width, $height, $crop = false ) {

	// this is an attachment, so we have the ID
	if ( $attach_id ) {
	
		$image_src = wp_get_attachment_image_src( $attach_id, 'full' );
		$file_path = get_attached_file( $attach_id );
	
	// this is not an attachment, let's use the image url
	} else if ( $img_url ) {
		
		$file_path = parse_url( $img_url );
		$file_path = ltrim( $file_path['path'], '/' );
		//$file_path = rtrim( ABSPATH, '/' ).$file_path['path'];
		
		$orig_size = getimagesize( $file_path );
		
		$image_src[0] = $img_url;
		$image_src[1] = $orig_size[0];
		$image_src[2] = $orig_size[1];
	}
	
	$file_info = pathinfo( $file_path );
	$extension = '.'. $file_info['extension'];

	// the image path without the extension
	$no_ext_path = $file_info['dirname'].'/'.$file_info['filename'];

	$cropped_img_path = $no_ext_path.'-'.$width.'x'.$height.$extension;

	// checking if the file size is larger than the target size
	// if it is smaller or the same size, stop right here and return
	if ( $image_src[1] > $width || $image_src[2] > $height ) {

		// the file is larger, check if the resized version already exists (for crop = true but will also work for crop = false if the sizes match)
		if ( file_exists( $cropped_img_path ) ) {

			$cropped_img_url = str_replace( basename( $image_src[0] ), basename( $cropped_img_path ), $image_src[0] );
			
			$vt_image = array (
				'url' => $cropped_img_url,
				'width' => $width,
				'height' => $height
			);
			
			return $vt_image;
		}

		// crop = false
		if ( $crop == false ) {
		
			// calculate the size proportionaly
			$proportional_size = wp_constrain_dimensions( $image_src[1], $image_src[2], $width, $height );
			$resized_img_path = $no_ext_path.'-'.$proportional_size[0].'x'.$proportional_size[1].$extension;			

			// checking if the file already exists
			if ( file_exists( $resized_img_path ) ) {
			
				$resized_img_url = str_replace( basename( $image_src[0] ), basename( $resized_img_path ), $image_src[0] );

				$vt_image = array (
					'url' => $resized_img_url,
					'width' => $new_img_size[0],
					'height' => $new_img_size[1]
				);
				
				return $vt_image;
			}
		}

		// no cached files - let's finally resize it
		$new_img_path = image_resize( $file_path, $width, $height, $crop );
		$new_img_size = getimagesize( $new_img_path );
		$new_img = str_replace( basename( $image_src[0] ), basename( $new_img_path ), $image_src[0] );

		// resized output
		$vt_image = array (
			'url' => $new_img,
			'width' => $new_img_size[0],
			'height' => $new_img_size[1]
		);
		
		return $vt_image;
	}

	// default output - without resizing
	$vt_image = array (
		'url' => $image_src[0],
		'width' => $image_src[1],
		'height' => $image_src[2]
	);
	
	return $vt_image;
}

/*-----------------------------------------------------------------------------------*/
/* Add browser informations to body tag
/*-----------------------------------------------------------------------------------*/

function browser_body_class($classes) {
 
    // A little Browser detection shall we?
    $browser = $_SERVER[ 'HTTP_USER_AGENT' ];
 
    // Mac, PC ...or Linux
    if ( preg_match( "/Mac/", $browser ) ){
        $classes[] = 'mac';
 
    } elseif ( preg_match( "/Windows/", $browser ) ){
        $classes[] = 'windows';
 
    } elseif ( preg_match( "/Linux/", $browser ) ) {
        $classes[] = 'linux';
 
    } else {
        $classes[] = 'unknown-os';
    }
 
    // Checks browsers in this order: Chrome, Safari, Opera, MSIE, FF
    if ( preg_match( "/Chrome/", $browser ) ) {
        $classes[] = 'chrome';
 
        preg_match( "/Chrome\/(\d.\d)/si", $browser, $matches);
        $classesh_version = 'ch' . str_replace( '.', '-', $matches[1] );
        $classes[] = $classesh_version;
 
        } elseif ( preg_match( "/Safari/", $browser ) ) {
            $classes[] = 'safari';
 
            preg_match( "/Version\/(\d.\d)/si", $browser, $matches);
            $sf_version = 'sf' . str_replace( '.', '-', $matches[1] );
            $classes[] = $sf_version;
 
         } elseif ( preg_match( "/Opera/", $browser ) ) {
            $classes[] = 'opera';
 
            preg_match( "/Opera\/(\d.\d)/si", $browser, $matches);
            $op_version = 'op' . str_replace( '.', '-', $matches[1] );
            $classes[] = $op_version;
 
         } elseif ( preg_match( "/MSIE/", $browser ) ) {
            $classes[] = 'msie';
 
            if( preg_match( "/MSIE 6.0/", $browser ) ) {
                $classes[] = 'ie6';
            } elseif ( preg_match( "/MSIE 7.0/", $browser ) ){
                $classes[] = 'ie7';
            } elseif ( preg_match( "/MSIE 8.0/", $browser ) ){
                $classes[] = 'ie8';
            } elseif ( preg_match( "/MSIE 9.0/", $browser ) ){
                $classes[] = 'ie9';
            }
 
            } elseif ( preg_match( "/Firefox/", $browser ) && preg_match( "/Gecko/", $browser ) ) {
                $classes[] = 'firefox';
 
                preg_match( "/Firefox\/(\d)/si", $browser, $matches);
                $ff_version = 'ff' . str_replace( '.', '-', $matches[1] );
                $classes[] = $ff_version;
 
            } else {
                $classes[] = 'unknown-browser';
            }
 
    return $classes;
}
 
add_filter('body_class','browser_body_class');