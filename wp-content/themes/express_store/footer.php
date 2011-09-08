<div class="clearfix"></div>
<div id="footer-wrapper">
	
	<div class="container" id="footer-inner">
	<?php if (epanel_option('footer_columns') == 'two' ) { ?>
		
		<div class="one_half">
			<?php if ( !function_exists('dynamic_sidebar') || ! generated_dynamic_sidebar('Footer Column 1') ) ; ?>
		</div>
		
		<div class="one_half last">
			<?php if ( !function_exists('dynamic_sidebar') || ! generated_dynamic_sidebar('Footer Column 2') ) ; ?>
		</div>
		
	<?php } else if (epanel_option('footer_columns') == 'three' ) { ?>
	
		<div class="one_third">
			<?php if ( !function_exists('dynamic_sidebar') || ! generated_dynamic_sidebar('Footer Column 1') ) ; ?>
		</div>
		
		<div class="one_third">
			<?php if ( !function_exists('dynamic_sidebar') || ! generated_dynamic_sidebar('Footer Column 2') ) ; ?>
		</div>
		
		<div class="one_third last">
			<?php if ( !function_exists('dynamic_sidebar') || ! generated_dynamic_sidebar('Footer Column 3') ) ; ?>
		</div>
	
	<?php } else if (epanel_option('footer_columns') == 'four' ) { ?>
	
		<div class="one_fourth">
			<?php if ( !function_exists('dynamic_sidebar') || ! generated_dynamic_sidebar('Footer Column 1') ) ; ?>
		</div>
		
		<div class="one_fourth">
			<?php if ( !function_exists('dynamic_sidebar') || ! generated_dynamic_sidebar('Footer Column 2') ) ; ?>
		</div>
		
		<div class="one_fourth">
			<?php if ( !function_exists('dynamic_sidebar') || ! generated_dynamic_sidebar('Footer Column 3') ) ; ?>
		</div>
		
		<div class="one_fourth last">
			<?php if ( !function_exists('dynamic_sidebar') || ! generated_dynamic_sidebar('Footer Column 4') ) ; ?>
		</div>
	
	<?php } ?>
	
		
	</div>
	<div class="clearfix"></div>
	
	<div id="footer-bottom" class="footer_wrap">
		
		<div class="container">
		
			<div class="footer-left">
				<?php if (epanel_option('footer_left_content') == 'content' ) { echo stripslashes(epanel_option('custom_footer_left_content')); } 
					
					else if (epanel_option('footer_left_content') == 'menu' ) { 
						
						if ( has_nav_menu( 'ep-bottomleft-navigation' ) ) {
						
							wp_nav_menu(  array( 'theme_location' => 'ep-bottomleft-navigation', 'container' =>'', 'menu' =>'ep-bottomleft-navigation', 'menu_class'  => 'bottom-menu', 'menu_id' => 'bottom-left-menu'  ));
							
						} else { epanel_no_menu_location(); } } 
				?>
			</div>
		
			<div class="footer-right">
				<?php if (epanel_option('footer_right_content') == 'content' ) { echo stripslashes(epanel_option('custom_footer_right_content')); } 
					
					else if (epanel_option('footer_right_content') == 'menu' ) { 
						
						if ( has_nav_menu( 'ep-bottomright-navigation' ) ) {
						
							wp_nav_menu(  array( 'theme_location' => 'ep-bottomright-navigation', 'container' =>'', 'menu' =>'ep-bottomright-navigation', 'menu_class'  => 'bottom-menu', 'menu_id' => 'bottom-right-menu'  ));
							
						} else { epanel_no_menu_location(); } } 
				?>
			</div>
		
		</div>
		
	</div>
	<div class="clearfix"></div>
	
</div>
</div><!-- end wrapper -->
</body>
<?php if(!epanel_option('fontreplacement')){ ?>
<?php }else{ ?> 
<script type="text/javascript">
	Cufon.replace('#top-left-menu li a, #top-right-menu li a, #main-menu2 > li > a',{hover: true	}); 
	Cufon.replace('h6,h5,h4,h3,h2,h1'); 
</script>
<script type="text/javascript"> Cufon.now(); </script>
<?php } ?>
<script> 
 
flowplayer(".video_player", "<?php echo get_template_directory_uri() ?>/js/flowplayer-3.2.5.swf",  {
	clip: {
		
		// these two configuration variables does the trick
		autoPlay: false, 
		autoBuffering: true // <- do not place a comma here  
	}
});
</script>

<?php wp_footer();?>