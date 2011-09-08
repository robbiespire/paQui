<?php 
//ADD PRODUCTS BUTTON SHORTCODE MANAGER
add_action('init', 'add_button_product');  
function add_button_product() {
	if ( current_user_can('edit_posts') &&  current_user_can('edit_pages') )
	{
		add_filter('mce_external_plugins', 'add_plugin_product');
		add_filter('mce_buttons', 'register_button_product');
	}
}

function register_button_product($buttons) {
	array_push($buttons, "product");
	return $buttons;
}

function add_plugin_product($plugin_array) {  
	$plugin_array['product'] = get_bloginfo('template_url').'/core/components/shortcode-manager/shortcodes/product/product-plugin.js';  
	return $plugin_array;  
}

?>