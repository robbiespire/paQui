<?php

if(__FILE__ == $_SERVER['SCRIPT_FILENAME'])
{
    header('HTTP/1.0 404 Not Found');
      exit;
}

// automatically add posts to custom post types - still under construction


function get_default_banners(){
	return array();
}

function get_default_slides(){
	return array();
}

function get_default_scrolls(){
	return array();
}