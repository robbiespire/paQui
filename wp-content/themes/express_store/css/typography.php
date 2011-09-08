<?php header("Content-type: text/css; charset: UTF-8"); ?>
<?php require_once( '../../../../wp-load.php' );?>

<?php if (!epanel_option('fontreplacement')); { ?>
h1, h2, h3, h4, h5, h6, #intro-content p { 
	font-family: '<?php echo epanel_option('google_font_url_headings');?>', Lucida Sans Unicode, Lucida Grande, Trebuchet MS,Helvetica,Arial,sans-serif;
}

.top-menu li a, #top-left-menu.epanel_mega li a, #top-right-menu.epanel_mega li a {
	font-family: '<?php echo epanel_option('google_font_url');?>', Lucida Sans Unicode, Lucida Grande, Trebuchet MS,Helvetica,Arial,sans-serif;
}

#main-menu-wrapper > ul > li > a {
	text-transform: uppercase;
	font-family: '<?php echo epanel_option('google_font_url');?>', Lucida Sans Unicode, Lucida Grande, Trebuchet MS,Helvetica,Arial,sans-serif;
	font-weight: normal;
}

#main-menu-wrapper ul ul li a, #main-menu-wrapper ul ul ul li a, .sub-menu a, .shopp.account a, .purchase-fields span { font-family: Lucida Sans Unicode, Lucida Grande, Trebuchet MS,Helvetica,Arial,sans-serif !important; }

#intro-text {
	font-family: '<?php echo epanel_option('google_font_url');?>', Lucida Sans Unicode, Lucida Grande, Trebuchet MS,Helvetica,Arial,sans-serif;
}

.mini-content h2 {
	font-family: '<?php echo epanel_option('google_font_url');?>', Lucida Sans Unicode, Lucida Grande, Trebuchet MS,Helvetica,Arial,sans-serif;
}

#products-scroller p {
	font-family: '<?php echo epanel_option('google_font_url');?>', Lucida Sans Unicode, Lucida Grande, Trebuchet MS,Helvetica,Arial,sans-serif;
}

.latest-post p.the-date {
	font-family: '<?php echo epanel_option('google_font_url');?>', Lucida Sans Unicode, Lucida Grande, Trebuchet MS,Helvetica,Arial,sans-serif;
}

#store-home-wrapper .epanel_mega a, #store-home-menu > li > a, #widget-login input[type=submit], #widget-login label, #recover-button, #customer-registration input[type=submit], #searchform input[type=submit], #cart-header, #cart-header ol, #cart-header-confirm, #wrapper {
	font-family: '<?php echo epanel_option('google_font_url');?>', Lucida Sans Unicode, Lucida Grande, Trebuchet MS,Helvetica,Arial,sans-serif;
}

<?php } ?>