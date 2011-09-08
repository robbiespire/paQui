<?php

if(__FILE__ == $_SERVER['SCRIPT_FILENAME'])
{
    header('HTTP/1.0 404 Not Found');
      exit;
}

/*
	Theme Introduction 
*/

function get_theme_intro(){
	
	$theme_data = get_theme_data(TEMPLATEPATH . '/style.css');
	
	define('THEMEDESC', $theme_data['Description']);
		
	$intro = '<div class="admin_billboard fix"><div class="admin_theme_screenshot"><img class="" src="'.THEME_ROOT.'/screenshot.png" /></div>' .
		'<div class="admin_billboard_content"><div class="admin_header"><h3 class="admin_header_main">Welcome to '.THEMENAME.'!</h3></div>'.
		'<div class="admin_billboard_text" style="width:400px;">'.THEMEDESC.'</div>'.
		'</div></div>'.
		'<ul class="admin_feature_list">'.
		'<li class="feature_settings"><div class="feature_icon"></div><strong>E-Panel Theme Configuration &amp; Setup</strong> <p>This panel is where you will start the customization of your website. Any options applied through this interface will make changes to your website.</p></li>' .
		'<li class="feature_requirements"><div class="feature_icon"></div><strong>Hosting Requirements</strong> <p>In order to get this theme up an running without problems, your host needs these requirements.</p>
		<ul>
			<li>MySQL 5+</li>
			<li>PHP 5.2 or better</li>
			<li>PHP cURL Library w/SSL support</li>
			<li>PHP GD 2 Library</li>
		</ul>
		</li>'.
		'</ul>'
		;

		return apply_filters('epanel_theme_intro', $intro);
	}
	
