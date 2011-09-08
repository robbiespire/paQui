<?php
/*
Template Name: Inner Page Store
*/
get_header();
?>
<div id="breadcumb">

	<div class="container">
		<?php ecart('catalog','breadcrumb'); ?>
	</div>

</div>

<div id="inner-page">

	<div class="container" id="inner-wrapper">
	
		<?php if (get_post_meta($post->ID, 'sidebar_position', true ) =='left') { ?>
		
			<div class="one_fourth" id="sidebar">
				<?php if ( !function_exists('dynamic_sidebar') || ! generated_dynamic_sidebar('Page Sidebar') ) ; ?>
			</div>
			
			<div class="three_fourth last" id="page-content">
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			
			<?php the_content();?>
			
			<?php endwhile; endif; ?>
			</div>
		
		<?php } else if (get_post_meta($post->ID, 'sidebar_position', true ) =='right') { ?>
		
			<div class="three_fourth" id="page-content">
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			
			<?php the_content();?>
			
			<?php endwhile; endif; ?>
			</div>
			
			<div class="one_fourth last" id="sidebar">
				<?php if ( !function_exists('dynamic_sidebar') || ! generated_dynamic_sidebar('Page Sidebar') ) ; ?>
			</div>
			
		<?php } ?>
		
		
		<div class="clearfix"></div>
		
	</div>

</div>

<?php get_footer();?>