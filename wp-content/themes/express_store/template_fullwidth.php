<?php
/*
Template Name: Fullwidth Page
*/
get_header();
?>
<!--
<div id="breadcumb">

	<div class="container">
		<?php ecart('catalog','breadcrumb'); ?>
	</div>

</div>
-->

<div id="inner-page">

	<div class="container fullwidth" id="inner-wrapper">
	
		<div id="page-content">
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		
		<?php the_content();?>
		
		<?php endwhile; endif; ?>
		</div>
		
		<div class="clearfix"></div>
		
	</div>

</div>

<?php get_footer();?>