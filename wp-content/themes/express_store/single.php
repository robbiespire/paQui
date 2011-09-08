<?php
get_header();
?>
<div id="breadcumb">

	<div class="container">
		<?php if (function_exists('dimox_breadcrumbs')) dimox_breadcrumbs();?>
	</div>

</div>

<div id="inner-page" <?php post_class(); ?>>

	<div class="container" id="inner-wrapper">
	
		<?php if (get_post_meta($post->ID, 'sidebar_position', true ) =='left') { ?>
		
			<div class="one_fourth" id="sidebar">
				<?php if ( !function_exists('dynamic_sidebar') || ! generated_dynamic_sidebar('Page Sidebar') ) ; ?>
			</div>
			
			<div class="three_fourth last" id="page-content">
				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
					 	
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
					 			<?php global $more; $more = false; ?><?php the_content(); ?><?php $more = true; ?>
					 		</div>
					 	
					 	<div class="post-meta">
					 		<ul class="post-meta-info">
					 			<li class="post-author"><strong><?php _e('Posted By : ','epanel');?></strong><?php the_author();?></li>
					 		
					 			<li class="post-time"><strong><?php _e('On : ','epanel');?></strong><?php the_time(get_option('ep_blog_date_format')); ?></li>
					 			
					 			<li class="post-comments"><a href="<?php the_permalink();?>"><?php comments_number( __(' Leave a comment', 'epanel'), __(' One comment', 'epanel'), __(' % comments', 'epanel')); ?></a></li>
					 			
					 		</ul>

					 	</div>
						
						<?php wp_link_pages(); ?>
						
					 	</div><!-- End Post layout -->
	 			<?php endwhile; endif; ?>
	 			
	 			<div id="comments">
	 				<?php comments_template(); ?>
	 			</div>
	 			
				</div>
				
		<div class="clearfix"></div>
		
		
		<?php } else if (get_post_meta($post->ID, 'sidebar_position', true ) =='right') { ?>
		
					
					<div class="three_fourth" id="page-content">
						<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
							 	
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
							 			<?php global $more; $more = false; ?><?php the_content(); ?><?php $more = true; ?>
							 		</div>
							 	
							 	<div class="post-meta">
							 		<ul class="post-meta-info">
							 			<li class="post-author"><strong><?php _e('Posted By : ','epanel');?></strong><?php the_author();?></li>
							 		
							 			<li class="post-time"><strong><?php _e('On : ','epanel');?></strong><?php the_time(get_option('ep_blog_date_format')); ?></li>
							 			
							 			<li class="post-comments"><a href="<?php the_permalink();?>"><?php comments_number( __(' Leave a comment', 'epanel'), __(' One comment', 'epanel'), __(' % comments', 'epanel')); ?></a></li>
							 			
							 		</ul>
		
							 	</div>
								
								<?php wp_link_pages(); ?>
								
							 	</div><!-- End Post layout -->
			 			<?php endwhile; endif; ?>
			 			
			 			<div id="comments">
			 				<?php comments_template(); ?>
			 			</div>
			 			
						</div>
						
						<div class="one_fourth last" id="sidebar">
							<?php if ( !function_exists('dynamic_sidebar') || ! generated_dynamic_sidebar('Page Sidebar') ) ; ?>
						</div>
						
				<div class="clearfix"></div>
			
			
			
		<?php } else { ?>
				
				<p>Please edit this post and select a sidebar position</p>
				
		<?php } ?>

	</div>

</div>

<?php get_footer();?>