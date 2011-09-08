<?php
/**
 * scripts.php
 *
 * Provides script concatenation and compression
 *
 * @version 1.0
 * @package ecart
 * @since 1.1
 * @subpackage scripts
 **/


$load = isset($_GET['load'])?$_GET['load']:$_GET['sjsl'];
$load = preg_replace( '/[^a-z0-9,_-]+/i', '', $load );
$load = explode(',', $load);
if (empty($load)) exit();

/**
 * @ignore
 */
if (!function_exists('add_action')) {
	function add_action() {}
}

/**
 * @ignore
 */
if (!function_exists('do_action_ref_array')) {
	function do_action_ref_array() {}
}

function get_file($path) {

	if ( function_exists('realpath') )
		$path = realpath($path);

	if ( ! $path || ! @is_file($path) )
		return '';

	return @file_get_contents($path);
}


require_once('functions.php');
if (!defined('ABSPATH')) load_ecarts_wpconfig();
if (!defined('WPINC')) define('WPINC', 'wp-includes');
require_once('flow/Scripts.php');

$EcartScripts = new EcartScripts();
ecart_default_scripts($EcartScripts);

$compress = ( isset($_GET['c']) && $_GET['c'] );
$force_gzip = ( $compress && 'gzip' == $_GET['c'] );
$expires_offset = 31536000;
$out = '';

foreach( $load as $handle ) {
	if ( !array_key_exists($handle, $EcartScripts->registered) )
		continue;

	$path = dirname(__FILE__) . $EcartScripts->registered[$handle]->src;
	$out .= get_file($path) . "\n";
}

header('Content-Type: application/x-javascript; charset=UTF-8');
header('Expires: ' . gmdate( "D, d M Y H:i:s", time() + $expires_offset ) . ' GMT');
header("Cache-Control: public, max-age=$expires_offset");

if ( $compress && ! ini_get('zlib.output_compression') && 'ob_gzhandler' != ini_get('output_handler') ) {
	header('Vary: Accept-Encoding'); // Handle proxies
	if ( false !== strpos( strtolower($_SERVER['HTTP_ACCEPT_ENCODING']), 'deflate') && function_exists('gzdeflate') && ! $force_gzip ) {
		header('Content-Encoding: deflate');
		$out = gzdeflate( $out, 3 );
	} elseif ( false !== strpos( strtolower($_SERVER['HTTP_ACCEPT_ENCODING']), 'gzip') && function_exists('gzencode') ) {
		header('Content-Encoding: gzip');
		$out = gzencode( $out, 3 );
	}
}

echo $out;
exit();

?>