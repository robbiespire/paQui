<?php
/**
 * Scripts.php
 *
 * Controller for browser script queueing and delivery
 *
 * @version 1.0
 * @package ecart
 * @since 1.0
 * @subpackage scripts
 **/

/**
 * Scripts
 * 
 * @since 1.1
 * @package ecart
 **/
/** From BackPress */
require_once( ABSPATH . WPINC . '/class.wp-dependencies.php' );
require_once( ABSPATH . WPINC . '/class.wp-scripts.php' );

class EcartScripts extends WP_Scripts {

	function __construct() {
		do_action_ref_array( 'ecart_default_scripts', array(&$this) );

		add_action('wp_enqueue_scripts', array(&$this,'wp_dependencies'),1);
		add_action('admin_head', array(&$this,'wp_dependencies'),1);

		add_action('wp_head', array(&$this,'print_head_scripts'),15);
		add_action('admin_head', array(&$this,'print_head_scripts'),15);
		add_action('wp_footer', array(&$this,'print_footer_scripts'),15);
		add_action('admin_footer', array(&$this,'print_footer_scripts'),15);

	}

	function do_item( $handle, $group = false ) {
		if(parent::do_item($handle,$group))
			$this->print_code .= $this->print_script_custom($handle);
	}

	function print_head_scripts() {
		global $concatenate_scripts;

		if ( ! did_action('ecart_print_scripts') )
			do_action('ecart_print_scripts');

		script_concat_settings();
		$this->do_concat = $concatenate_scripts;
		$this->do_head_items();

		if ( apply_filters('ecart_print_head_scripts', true) )
			$this->print_script_request();

		$this->reset();
		return $this->done;
	}

	function print_footer_scripts() {
		global $concatenate_scripts;

		if ( ! did_action('ecart_print_footer_scripts') )
			do_action('ecart_print_footer_scripts');

		script_concat_settings();
		$concatenate_scripts = defined('CONCATENATE_SCRIPTS') ? CONCATENATE_SCRIPTS : true;
		$this->do_concat = $concatenate_scripts;
		$this->do_footer_items();

		if ( apply_filters('ecart_print_footer_scripts', true) )
			$this->print_script_request();

		$this->reset();
		return $this->done;
	}

	function print_script_request () {
		global $compress_scripts;
		$Settings =& EcartSettings();

		$zip = $compress_scripts ? 1 : 0;
		if ( $zip && defined('ENFORCE_GZIP') && ENFORCE_GZIP )
			$zip = 'gzip';

		if ( !empty($this->concat) ) {
			$ver = md5("$this->concat_version");
			if ($Settings->get('script_server') == 'plugin') {
				$src = trailingslashit(get_bloginfo('url')) . "?sjsl=" . trim($this->concat, ', ') . "&c={$zip}&ver=$ver";
				if (is_ssl()) $src = str_replace('http://','https://',$src);
			} else $src = $this->base_url . "scripts.php?c={$zip}&load=" . trim($this->concat, ', ') . "&ver=$ver";
			echo "<script type='text/javascript' src='" . esc_attr($src) . "'></script>\n";
		}

		if ( !empty($this->print_code) ) {
			echo "<script type='text/javascript'>\n";
			echo "/* <![CDATA[ */\n";
			echo $this->print_code;
			echo "/* ]]> */\n";
			echo "</script>\n";
		}

		if ( !empty($this->print_html) )
			echo $this->print_html;
	}
	function print_scripts_l10n( $handle, $echo = true ) {
		if ( empty($this->registered[$handle]->extra['l10n']) || empty($this->registered[$handle]->extra['l10n'][0]) || !is_array($this->registered[$handle]->extra['l10n'][1]) )
			return false;

		$object_name = $this->registered[$handle]->extra['l10n'][0];

		$data = "var $object_name = {";
		$eol = '';
		foreach ( $this->registered[$handle]->extra['l10n'][1] as $var => $val ) {
			if ( 'l10n_print_after' == $var ) {
				$after = $val;
				continue;
			}
			$data .= "$eol$var: \"" . esc_js( $val ) . '"';
			$eol = ",";
		}
		$data .= "};\n";
		$data .= isset($after) ? "$after\n" : '';

		if ( $echo ) {
			echo "<script type='text/javascript'>\n";
			echo "/* <![CDATA[ */\n";
			echo $data;
			echo "/* ]]> */\n";
			echo "</script>\n";
			return true;
		} else {
			return $data;
		}
	}


	function all_deps( $handles, $recursion = false, $group = false ) {
		$r = parent::all_deps( $handles, $recursion );
		if ( !$recursion )
			$this->to_do = apply_filters( 'ecart_print_scripts_array', $this->to_do );
		return $r;
	}

	function wp_dependencies () {
		global $wp_scripts;

		if ( !is_a($wp_scripts, 'WP_Scripts') )
			$wp_scripts = new WP_Scripts();

		$wpscripts = array_keys($wp_scripts->registered);

		$deps = array();
		foreach ($this->queue as $handle) {
			if (!isset($this->registered[$handle]) || !isset($this->registered[$handle]->deps)) continue;
			$wpdeps = array_intersect($this->registered[$handle]->deps,$wpscripts);
			$mydep = array_diff($this->registered[$handle]->deps,$wpdeps);
			$this->registered[$handle]->deps = $mydep;
		}

		if (!empty($wpdeps)) foreach ((array)$wpdeps as $handle) wp_enqueue_script($handle);

	}

	function print_script_custom ($handle) {
		return !empty($this->registered[$handle]->extra['code'])?$this->registered[$handle]->extra['code']:false;
	}


} // END class EcartScripts

function ecart_default_scripts (&$scripts) {

	$script = basename(__FILE__);
	$schema = ( !empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) != 'off' ) ? 'https://' : 'http://';
	if (defined('ECART_PLUGINURI')) $url = ECART_PLUGINURI.'/core'.'/';
	else $url = preg_replace("|$script.*|i", '', $schema . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);

	$scripts->base_url = $url;
	$scripts->default_version = mktime(false,false,false,1,1,2010);
	$scripts->default_dirs = array('/ui/behaviors/','/ui/products');

	// $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '_dev' : '';

	$scripts->add('ecart', "/ui/behaviors/ecart.js", array('jquery'), '20100101');
	$scripts->add_data('ecart', 'group', 1);

	$scripts->add('cart', "/ui/behaviors/cart.js", array('jquery','ecart'), '20100101');
	$scripts->add_data('cart', 'group', 1);

	$scripts->add('catalog', "/ui/behaviors/catalog.js", array('jquery','ecart'), '20100101');
	$scripts->add_data('catalog', 'group', 1);

	$scripts->add('calendar', "/ui/behaviors/calendar.js", array('jquery','ecart'), '20100101');
	$scripts->add_data('calendar', 'group', 1);

	$scripts->add('checkout', "/ui/behaviors/checkout.js", array('jquery','ecart'), '20100101');
	$scripts->add_data('checkout', 'group', 1);

	$scripts->add('colorbox', "/ui/behaviors/colorbox.js", array('jquery'), '20100101');
	$scripts->add_data('colorbox', 'group', 1);

	$scripts->add('ocupload', "/ui/behaviors/ocupload.js", array('jquery'), '20100101');
	$scripts->add_data('ocupload', 'group', 1);

	$scripts->add('scalecrop', "/ui/behaviors/scalecrop.js", array('jquery','jquery-ui-core','jquery-ui-draggable'), '20100101');
	$scripts->add_data('scalecrop', 'group', 1);

	$scripts->add('priceline', "/ui/behaviors/priceline.js", array('jquery','ecart'), '20100101');
	$scripts->add_data('priceline', 'group', 1);

	$scripts->add('editors', "/ui/behaviors/editors.js", array('jquery','jquery-ui-sortable'), '20100101');
	$scripts->add_data('editors', 'group', 1);

	$scripts->add('product-editor', "/ui/products/editor.js", array('jquery','priceline'), '20100101');
	$scripts->add_data('product-editor', 'group', 1);

	$scripts->add('category-editor', "/ui/categories/category.js", array('jquery','priceline'), '20100101');
	$scripts->add_data('category-editor', 'group', 1);

	$scripts->add('category-arrange', "/ui/categories/arrange.js", array('jquery','ecart'), '20100101');
	$scripts->add_data('category-arrange', 'group', 1);

	$scripts->add('products-arrange', "/ui/categories/products.js", array('jquery'), '20100101');
	$scripts->add_data('products-arrange', 'group', 1);

	$scripts->add('settings', "/ui/behaviors/settings.js", array('jquery'), '20100101');
	$scripts->add_data('settings', 'group', 1);

	$scripts->add('taxes', "/ui/behaviors/taxes.js", array('jquery'), '20100101');
	$scripts->add_data('taxes', 'group', 1);

	$scripts->add('setup', "/ui/behaviors/setup.js", array('jquery'), '20100101');
	$scripts->add_data('setup', 'group', 1);

	$scripts->add('ecart-swfobject', "/ui/behaviors/swfupload/plugins/swfupload.swfobject.js", array(), '2202');
	$scripts->add_data('ecart-swfobject', 'group', 1);

	$scripts->add('ecart-swfupload-queue', "/ui/behaviors/swfupload/plugins/swfupload.queue.js", array(), '2202');
	$scripts->add_data('ecart-swfupload-queue', 'group', 1);

	$scripts->add('swfupload', "/ui/behaviors/swfupload/swfupload.js", array('jquery','ecart-swfobject'), '2202');
	$scripts->add_data('swfupload', 'group', 1);

}

/**
 * Register new JavaScript file.
 */
function ecart_register_script( $handle, $src, $deps = array(), $ver = false, $in_footer = false ) {
	global $EcartScripts;
	if ( !is_a($EcartScripts, 'EcartScripts') )
		$EcartScripts = new EcartScripts();

	$EcartScripts->add( $handle, $src, $deps, $ver );
	if ( $in_footer )
		$EcartScripts->add_data( $handle, 'group', 1 );
}

function ecart_localize_script( $handle, $object_name, $l10n ) {
	global $EcartScripts;
	if ( !is_a($EcartScripts, 'EcartScripts') )
		return false;

	return $EcartScripts->localize( $handle, $object_name, $l10n );
}

function ecart_custom_script ($handle, $code) {
	global $EcartScripts;
	if ( !is_a($EcartScripts, 'EcartScripts') )
		return false;

	$code = !empty($EcartScripts->registered[$handle]->extra['code'])?$EcartScripts->registered[$handle]->extra['code'].$code:$code;
	return $EcartScripts->add_data( $handle, 'code', $code );
}

/**
 * Remove a registered script.
 */
function ecart_deregister_script( $handle ) {
	global $EcartScripts;
	if ( !is_a($EcartScripts, 'EcartScripts') )
		$EcartScripts = new EcartScripts();

	$EcartScripts->remove( $handle );
}

/**
 * Enqueues script.
 *
 * Registers the script if src provided (does NOT overwrite) and enqueues.
*/
function ecart_enqueue_script( $handle, $src = false, $deps = array(), $ver = false, $in_footer = false ) {
	global $EcartScripts;
	if ( !is_a($EcartScripts, 'EcartScripts') )
		$EcartScripts = new EcartScripts();

	if ( $src ) {
		$_handle = explode('?', $handle);
		$EcartScripts->add( $_handle[0], $src, $deps, $ver );
		if ( $in_footer )
			$EcartScripts->add_data( $_handle[0], 'group', 1 );
	}
	$EcartScripts->enqueue( $handle );
}

/**
 * Check whether script has been added to WordPress Scripts.
 *
 * The values for list defaults to 'queue', which is the same as enqueue for
 * scripts.
 *
 * @param string $handle Handle used to add script.
 * @param string $list Optional, defaults to 'queue'. Others values are 'registered', 'queue', 'done', 'to_do'
 * @return bool
 */
function ecart_script_is( $handle, $list = 'queue' ) {
	global $EcartScripts;
	if ( !is_a($EcartScripts, 'EcartScripts') )
		$EcartScripts = new EcartScripts();

	$query = $EcartScripts->query( $handle, $list );

	if ( is_object( $query ) )
		return true;

	return $query;
}

/**
 * Handle Ecart script dependencies in the WP script queue
 *
 * @since 1.1
 *
 * @return void
 **/
function ecart_dependencies () {
	global $EcartScripts,$wp_scripts;
	if ( !is_a($EcartScripts, 'EcartScripts') )
		$EcartScripts = new EcartScripts();

	foreach ($wp_scripts->queue as $handle) {
		$deps = $wp_scripts->registered[$handle]->deps;
		$ecartdeps = array_intersect($deps,array_keys($EcartScripts->registered));
		foreach ($ecartdeps as $key => $s_handle) {
			ecart_enqueue_script($s_handle);
			array_splice($deps,$key,1);
		}
		$wp_scripts->registered[$handle]->deps = $deps;
	}
}

add_action('ecart_default_scripts', 'ecart_default_scripts');

?>