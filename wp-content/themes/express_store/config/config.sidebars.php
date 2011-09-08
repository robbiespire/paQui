<?php 
/*-----------------------------------------------------------------------------------*/
/* E-Panel Theme Framework Sidebars
/*-----------------------------------------------------------------------------------*/

if (function_exists("register_sidebar")) {
  register_sidebar(array(
    'name' => 'Page Sidebar',
    'description' => 'Here you can add and customize content for your page sidebar.',
    'before_widget' => '<div class="page-widget">',
    'after_widget' => '</div><div class="clearfix"></div>',
    'before_title' => '<h4 class="widget-title">',
    'after_title' => '</h4>',
));
}

if (function_exists("register_sidebar")) {
  register_sidebar(array(
    'name' => 'Footer Column 1',
    'description' => 'Here you can add and customize content for your footer.',
    'before_widget' => '<div class="footer-widget">',
    'after_widget' => '</div><div class="clearfix"></div>',
    'before_title' => '<h4 class="widget-title">',
    'after_title' => '</h4>',
));
}

if (function_exists("register_sidebar")) {
  register_sidebar(array(
    'name' => 'Footer Column 2',
    'description' => 'Here you can add and customize content for your footer.',
    'before_widget' => '<div class="footer-widget">',
    'after_widget' => '</div><div class="clearfix"></div>',
    'before_title' => '<h4 class="widget-title">',
    'after_title' => '</h4>',
));
}

if (function_exists("register_sidebar")) {
  register_sidebar(array(
    'name' => 'Footer Column 3',
    'description' => 'Here you can add and customize content for your footer.',
    'before_widget' => '<div class="footer-widget">',
    'after_widget' => '</div><div class="clearfix"></div>',
    'before_title' => '<h4 class="widget-title">',
    'after_title' => '</h4>',
));
}

if (function_exists("register_sidebar")) {
  register_sidebar(array(
    'name' => 'Footer Column 4',
    'description' => 'Here you can add and customize content for your footer.',
    'before_widget' => '<div class="footer-widget">',
    'after_widget' => '</div><div class="clearfix"></div>',
    'before_title' => '<h4 class="widget-title">',
    'after_title' => '</h4>',
));
}
?>