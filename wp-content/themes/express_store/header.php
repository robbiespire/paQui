<?php 
/*
	
	HEADER
	
	This file controls the HTML <head> and top graphical sections of Express Store
	
*/ 	
?><!DOCTYPE html><!-- HTML 5 -->
<html <?php language_attributes(); ?>>
<meta http-equiv="X-UA-Compatible" content="IE=7, IE=9">
<head>
<?php epanel_head_common(); ?>
<meta name="description" content="<?php bloginfo('description'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<!-- Skin Builder -->
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
<?php if(epanel_option('epanel_theme') =='default_skin') { ?>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo get_template_directory_uri() ?>/css/default_skin.css" />
<?php } else if (epanel_option('epanel_theme') =='skin2') { ?>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo get_template_directory_uri() ?>/css/skin2.css" />
<?php } else if (epanel_option('epanel_theme') =='skin3') { ?>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo get_template_directory_uri() ?>/css/skin3.css" />
<?php } else if (epanel_option('epanel_theme') =='skin4') { ?>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo get_template_directory_uri() ?>/css/skin4.css" />
<?php } else if (epanel_option('epanel_theme') =='skin5') { ?>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo get_template_directory_uri() ?>/css/skin5.css" />
<?php } else if (epanel_option('epanel_theme') =='skin6') { ?>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo get_template_directory_uri() ?>/css/skin6.css" />
<?php } else if (epanel_option('epanel_theme') =='skin7') { ?>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo get_template_directory_uri() ?>/css/skin7.css" />
<?php } else if (epanel_option('epanel_theme') =='skin8') { ?>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo get_template_directory_uri() ?>/css/skin8.css" />
<?php } else if (epanel_option('epanel_theme') =='skin9') { ?>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo get_template_directory_uri() ?>/css/skin9.css" />
<?php } else if (epanel_option('epanel_theme') =='skin10') { ?>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo get_template_directory_uri() ?>/css/skin10.css" />
<?php } else if (epanel_option('epanel_theme') =='skin11') { ?>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo get_template_directory_uri() ?>/css/default_skin_normal.css" />
<?php } ?>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo get_template_directory_uri() ?>/css/skin_builder.php" />
<link rel="stylesheet" type="text/css" media="all" href="<?php echo get_template_directory_uri() ?>/css/shortcodes.css" />
<?php if (!epanel_option('fontreplacement')); { ?>
<?php require_once 'font_switcher.php'; ?>
<?php } ?>
<!-- End skins -->

<!--[if IE 7]>
<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/ie7.css" media="screen"/>
<![endif]-->
<!--[if IE 8]>
<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/ie8.css" media="screen"/>
<script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script>
<![endif]-->
<!--[if IE 9]>
<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/ie9.css" media="screen"/>
<![endif]-->

<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>

<?php wp_head(); ?>
<?php if(epanel_option('home_slider') == 'nivo') { ?>
<script type="text/javascript">
jQuery(window).load(function() {
    jQuery('#nivo-slider').nivoSlider({
    	directionNav:true,
    	effect:'<?php echo epanel_option('nivo_fx');?>',
    	slices:<?php echo epanel_option('nivo_slices');?>,
    	animSpeed:<?php echo epanel_option('nivo_anim_spd');?>,
    	pauseTime:<?php echo epanel_option('nivo_pause_time');?>,
    	pauseOnHover:<?php if(epanel_option('nivo_pause_hover')) { echo 'true'; } else { echo 'false'; } ?>
    	
    });
});
</script>
<?php } else if (epanel_option('home_slider') == 'bx' OR 'mini') { ?>
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery('#<?php if(epanel_option('home_slider') == 'bx') { echo 'bx'; } else { echo 'bx-mini'; } ?>').bxSlider({
            auto: true,
            autoControls: false,
            autoHover: <?php if(epanel_option('bx_pause')) { echo 'true'; } else { echo 'false'; } ?>,
            pager: true,
            pagerSelector: '#my-pager',
            controls: false,
            mode: '<?php echo epanel_option('bx_mode');?>',
            speed: <?php echo epanel_option('bx_fx_spd');?>,
            easing: '<?php echo epanel_option('bx_easing');?>',
            captions: false,
           pause: <?php echo epanel_option('bx_delay');?>,
           nextSelector: '#next-arrow',
           prevSelector: '#prev-arrow' 
        });
    });
</script>
<?php } ?>
<script type="text/javascript"> 
    // unblock when ajax activity stops 
    jQuery(document).ajaxStop(jQuery.unblockUI); 
  
    jQuery(document).ready(function() { 
        jQuery('.addtocart.ajax').click(function() { 
            
            jQuery.blockUI({ 
            
            message:  '<p>Processing your request...</p>',
            
            css: { 
                border: 'none', 
                padding: '15px', 
                backgroundColor: '#000', 
                '-webkit-border-radius': '10px', 
                '-moz-border-radius': '10px', 
                opacity: .5, 
                color: '#fff' 
            } }); 
     		 
            setTimeout(jQuery.unblockUI, 1000); 
        }); 
    });  
</script>
</head>
<body <?php body_class();?>>
<div id="wrapper">

	<div id="header">
	
		<div id="main-header" class="container">
		
			<div>
				<div id="logo">
					<a href="<?php bloginfo('url'); ?>"><img  src="<?php echo epanel_option('epanel_custom_logo');?>" alt="<?php echo bloginfo( 'name' ); ?>"/></a>
				</div>
				
				<div id="main-menu-wrapper" class="main_menu">
					
					<?php if ( has_nav_menu( 'ep-main-navigation' ) ) { ?>
					
						<?php wp_nav_menu(  array( 'theme_location' => 'ep-main-navigation', 'container' =>'', 'menu' =>'ep-main-navigation', 'menu_id' => 'main-menu', 'fallback_cb' => 'epanel_fallback_menu', 'max_columns'=>4  )); ?>
						
					<?php } else { epanel_no_menu_location(); } ?>
					
				</div>
				
			</div>
			
		
		</div><!-- main header -->
		
	</div><!-- end header -->