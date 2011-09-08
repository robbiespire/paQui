<?php get_header(); ?>
<div id="breadcumb">

	<div class="container">
		<?php if (function_exists('dimox_breadcrumbs')) dimox_breadcrumbs();?>
	</div>

</div>

<div id="inner-page">

	<div class="container" id="inner-wrapper">
	
	<?php if (is_page()) { ?>
		
		<?php if (get_post_meta($post->ID, 'sidebar_position', true ) =='left') { ?>
		
			<div class="one_fourth" id="sidebar">
				<?php if ( !function_exists('dynamic_sidebar') || ! generated_dynamic_sidebar('Page Sidebar') ) ; ?>
			</div>
			
			<div class="three_fourth last non-fullwidth" id="page-content">
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			
			<?php the_content();?>
			
			<?php endwhile; endif; ?>
			</div>
			
			<div class="clearfix"></div>
		
		<?php } else if (get_post_meta($post->ID, 'sidebar_position', true ) =='right') { ?>
		
			<div class="three_fourth" id="page-content">
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			
			<?php the_content();?>
			
			<?php endwhile; endif; ?>
			</div>
			
			<div class="one_fourth last" id="sidebar">
				<?php if ( !function_exists('dynamic_sidebar') || ! generated_dynamic_sidebar('Page Sidebar') ) ; ?>
			</div>
			
			<div class="clearfix"></div>
			
		<?php } ?>
	
	<?php } else if (is_home()) { ?>
		
		<div class="one_fourth" id="sidebar">
				<?php if ( !function_exists('dynamic_sidebar') || ! generated_dynamic_sidebar('Page Sidebar') ) ; ?>
			</div>
			
			<div class="three_fourth last" id="page-content">
				 <?php
					 $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
					 $excats = get_option('ep_exclude_categories');
					 $args=array(
					    'cat'=> $excats,
					    'paged'=>$paged,
					    );
					 query_posts($args);
					 while (have_posts()) : the_post(); ?>
					 	
					 	<div class="post-layout post-<?php the_ID();?>">
					 	<h2 class="post-title"><a href="<?php the_permalink();?>"><?php the_title();?></a></h2>
					 	<?php if(has_post_thumbnail()) { ?>
					 		<?php $post_thumb = get_post_thumbnail_id(); $post_image = vt_resize( $post_thumb,'' , 700, 220, true );?>
					 		
					 		<div class="post-image">
					 			<a href="<?php the_permalink();?>" class="post-load"><img src="<?php echo $post_image[url]; ?>" width="<?php echo $post_image[width]; ?>" height="<?php echo $post_image[height]; ?>" alt="<?php the_title();?>" /></a>
					 		</div>
					 		
					 	<?php } else { ?>
					 	<?php } ?>	
					 		
					 		<div class="post-content">
					 			<?php global $more; $more = false; ?><?php the_excerpt(''); ?><?php $more = true; ?>
					 		</div>
					 	
					 	<div class="post-meta">
					 		<ul class="post-meta-info">
					 			<?php if (get_option('ep_blog_show_author') != 'false') { ?>
					 			<li class="post-author"><strong><?php _e('Posted By : ','epanel');?></strong><?php the_author();?></li>
					 			<?php } ?>
					 			<?php if (get_option('ep_blog_show_time') != 'false') { ?>
					 			<li class="post-time"><strong><?php _e('On : ','epanel');?></strong><?php the_time(get_option('ep_blog_date_format')); ?></li>
					 			<?php } ?>
					 			<?php if (get_option('ep_blog_show_comments') != 'false') { ?>
					 			<li class="post-comments"><a href="<?php the_permalink();?>"><?php comments_number( __(' Leave a comment', 'epanel'), __(' One comment', 'epanel'), __(' % comments', 'epanel')); ?></a></li>
					 			<?php } ?>
					 		</ul>
					 		<a class="read-more-link" href="<?php the_permalink();?>"><?php _e('Continue Reading &raquo;', 'epanel');?></a>
					 	</div>
				
					 	</div><!-- End Post layout -->
		
					<?php endwhile; ?> 
					
					<?php if(function_exists('wp_pagenavi')) { wp_pagenavi(); } ?>
				</div>
		
		
	<?php } ?>
		
	</div>

</div>

<?php get_footer();?>