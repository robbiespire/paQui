<?php
/*
Template Name: Homepage With Store
*/
get_header();
?>

<div id="slider-wrapper">
		
		<?php if (epanel_option('home_slider') == 'static') { ?>
			
			<div class="container" id="static-slider">
			
				<img src="<?php echo epanel_option('slide_static_picture');?>" alt="static-home"/>
			
			</div>
		
		<?php } else if (epanel_option('home_slider') == 'nivo') { ?>
			
			<div id="nivo-wrapper" class="container">
				<div id="nivo-slider">
				
					<?php $loop = new WP_Query( array( 'post_type' => 'slides', 'posts_per_page' => 100, 'order' => 'ASC', 'orderby' => 'menu_order' ) ); ?>
					
					<?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
	
					<?php $thumb = get_post_thumbnail_id(); $image_static = vt_resize( $thumb,'' , 960, epanel_option('slider_height'), true ); ?>
						
						<?php if (get_post_meta($post->ID, 'nivo_url', true ) !=='') { ?>
						
						 <a href="<?php echo get_post_meta($post->ID, 'nivo_url', true );?>"><img src="<?php echo $image_static[url]; ?>" width="<?php echo $image_static[width]; ?>" height="<?php echo $image_static[height]; ?>" alt="<?php the_title();?>"/></a>
						 
						 <?php } else if (get_post_meta($post->ID, 'nivo_url', true ) =='') { ?>
						 	
						 	<img src="<?php echo $image_static[url]; ?>" width="<?php echo $image_static[width]; ?>" height="<?php echo $image_static[height]; ?>" alt="<?php the_title();?>" <?php if (get_post_meta($post->ID, 'nivo_caption', true ) !=='') { ?> title="<?php echo get_post_meta($post->ID, 'nivo_caption', true ); ?>" <?php } ?> />
						 	
						 <?php } ?>
						 
					<?php endwhile; ?>
				
				</div>
			</div>
		
		<?php } else if (epanel_option('home_slider') == 'bx') { ?>
		
		<div id="bxslider" class="container">
		
			<ul id="bx">
				<?php $loop = new WP_Query( array( 'post_type' => 'slides', 'posts_per_page' => 100, 'order' => 'ASC', 'orderby' => 'menu_order' ) ); ?>
				
					<?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
					
					
					
						<?php $thumb = get_post_thumbnail_id(); $image_static = vt_resize( $thumb,'' , 960, epanel_option('slider_height'), true ); ?>
	
							<li>
							<?php if (get_post_meta($post->ID, 'bx_type', true ) =='image') { ?>
							<img src="<?php echo $image_static[url]; ?>" width="<?php echo $image_static[width]; ?>" height="<?php echo $image_static[height]; ?>" alt="<?php the_title();?>" title="<?php the_title()?>" />
							<?php } else if (get_post_meta($post->ID, 'bx_type', true ) =='image_link') { ?>
							<a href="<?php echo get_post_meta($post->ID, 'bx_link_code', true ); ?>"><img src="<?php echo $image_static[url]; ?>" width="<?php echo $image_static[width]; ?>" height="<?php echo $image_static[height]; ?>" alt="<?php the_title();?>" title="<?php the_title()?>" /></a>
							<?php } else if (get_post_meta($post->ID, 'bx_type', true ) =='caption_right') { ?>
								
								<img src="<?php echo $image_static[url]; ?>" width="<?php echo $image_static[width]; ?>" height="<?php echo $image_static[height]; ?>" alt="<?php the_title();?>" title="<?php the_title()?>" />
								<div class="bx-caption slide-caption-right"><?php the_content();?></div>
							
							<?php } else if (get_post_meta($post->ID, 'bx_type', true ) =='caption_left') { ?>
								
								<img src="<?php echo $image_static[url]; ?>" width="<?php echo $image_static[width]; ?>" height="<?php echo $image_static[height]; ?>" alt="<?php the_title();?>" title="<?php the_title()?>" />
								<div class="bx-caption slide-caption-left"><?php the_content();?></div>
							
							<?php } else if (get_post_meta($post->ID, 'bx_type', true ) =='title_left') { ?>
								
								<img src="<?php echo $image_static[url]; ?>" width="<?php echo $image_static[width]; ?>" height="<?php echo $image_static[height]; ?>" alt="<?php the_title();?>" title="<?php the_title()?>" />
								<div class="bx-caption slide-title-left"><?php the_content();?></div>
							
							<?php } else if (get_post_meta($post->ID, 'bx_type', true ) =='title_right') { ?>
								
								<img src="<?php echo $image_static[url]; ?>" width="<?php echo $image_static[width]; ?>" height="<?php echo $image_static[height]; ?>" alt="<?php the_title();?>" title="<?php the_title()?>" />
								<div class="bx-caption slide-title-right"><?php the_content();?></div>
							
							<?php } else if (get_post_meta($post->ID, 'bx_type', true ) =='center') { ?>
								
								<img src="<?php echo $image_static[url]; ?>" width="<?php echo $image_static[width]; ?>" height="<?php echo $image_static[height]; ?>" alt="<?php the_title();?>" title="<?php the_title()?>" />
								<div class="bx-caption slide-title-center"><?php the_content();?></div>
							
							<?php } else if (get_post_meta($post->ID, 'bx_type', true ) =='video_mode') { ?>
								
								<div class="bx-video-mode">
								
									<?php echo get_post_meta($post->ID, 'bx_video_code', true ); ?>
								
								</div>
							
							<?php } ?>
							</li>
					
				<?php endwhile; ?>

			</ul>
			
			<div id="my-pager"></div>
		
		</div>
		
		<?php } else if (epanel_option('home_slider') == 'mini') { ?>
			
			<div id="bxslider" class="container">
			
				<ul id="bx-mini">
					
					<?php $loop = new WP_Query( array( 'post_type' => 'slides', 'posts_per_page' => 100, 'order' => 'ASC' ) ); ?>
					
						<?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
						
						<?php $thumb_minibx = get_post_thumbnail_id(); $image_mini = vt_resize( $thumb_minibx,'' , 450, 180, true ); ?>
						
							<li>
								
								<?php if (get_post_meta($post->ID, 'bxmini_mode', true ) =='image_right') { ?>
							
								<div class="one_half mini-content">
									
									<h2><?php the_title();?></h2>
									
									<?php  if (get_post_meta($post->ID, 'bxmini_sub', true ) !=='') { echo '<h4>'.get_post_meta($post->ID, 'bxmini_sub', true ).'</h4>'; } ?>
									
									
									<?php the_content();?>
								
								</div>
								
								<div class="one_half last mini-thumb">
								
									<img src="<?php echo $image_mini[url]; ?>" width="<?php echo $image_mini[width]; ?>" height="<?php echo $image_mini[height]; ?>" alt="<?php the_title();?>" title="<?php the_title()?>" />
								
								</div>
								
								
								<?php } else if (get_post_meta($post->ID, 'bxmini_mode', true ) =='image_left') { ?>
							
									<div class="one_half mini-thumb">
									
										<img src="<?php echo $image_mini[url]; ?>" width="<?php echo $image_mini[width]; ?>" height="<?php echo $image_mini[height]; ?>" alt="<?php the_title();?>" title="<?php the_title()?>" />
									
									</div>
									
									<div class="one_half last mini-content">
										
										<h2><?php the_title();?></h2>
										
										<?php  if (get_post_meta($post->ID, 'bxmini_sub', true ) !=='') { echo '<h4>'.get_post_meta($post->ID, 'bxmini_sub', true ).'</h4>'; } ?>
										
										<?php the_content();?>
									
									</div>
								
								<?php } else if (get_post_meta($post->ID, 'bxmini_mode', true ) =='video_right') { ?>
								
									<div class="one_half mini-content">
											
										<h2><?php the_title();?></h2>
											
										<?php  if (get_post_meta($post->ID, 'bxmini_sub', true ) !=='') { echo '<h4>'.get_post_meta($post->ID, 'bxmini_sub', true ).'</h4>'; } ?>
											
										<?php the_content();?>
										
									</div>
										
									<div class="one_half last mini-thumb">
										
										<?php echo get_post_meta($post->ID, 'bxmini_video_code', true ); ?>
									
									</div>									
								
								<?php } else if (get_post_meta($post->ID, 'bxmini_mode', true ) =='video_left') { ?>
								
									<div class="one_half mini-thumb">
									
										<?php echo get_post_meta($post->ID, 'bxmini_video_code', true ); ?>
									
									</div>
									
									<div class="one_half mini-content last">
											
										<h2><?php the_title();?></h2>
											
										<?php  if (get_post_meta($post->ID, 'bxmini_sub', true ) !=='') { echo '<h4>'.get_post_meta($post->ID, 'bxmini_sub', true ).'</h4>'; } ?>
											
										<?php the_content();?>
										
									</div>
									
								<?php } ?>
								
								<div class="clearfix"></div>
								
							</li>
					
						<?php endwhile; ?>
					
				</ul>
				
			</div>
			
		<?php } ?>

</div><!-- end slider wrapper -->

<?php if(epanel_option('enabled_intro_text')) { ?>

<div id="intro-content" class="container">

<h2><?php echo stripslashes(epanel_option('intro_text'));?></h2>

</div>

<?php } ?>

<?php if (epanel_option('enabled_product_slider') && epanel_option('home_scroller_position')  == 'top') { ?>
	
	<?php if(epanel_option('home_product_slider') == 'custom' ) { ?>

	<div id="product-slider">
		
		<h5 class="container"><?php _e('Latest Featured Products','epanel');?></h5>
		
		<div id="scroller-wrapper" class="container">
		
		<ul id="products-scroller">
		
		<?php $scroller_loop = new WP_Query( array( 'post_type' => 'scroller', 'posts_per_page' => 100, 'orderby' => 'menu_order', 'order' => 'ASC' ) ); ?>
		
			<?php while ( $scroller_loop->have_posts() ) : $scroller_loop->the_post(); ?>
			
			<?php $thumb_scroll = get_post_thumbnail_id(); $image_scroll = vt_resize( $thumb_scroll,'' , 205, 140, true ); ?>
				
				<?php if (get_post_meta($post->ID, 'item_url', true ) == '') { ?>
				
				<li><img src="<?php echo $image_scroll[url]; ?>" width="<?php echo $image_scroll[width]; ?>" height="<?php echo $image_scroll[height]; ?>" alt="<?php the_title();?>" title="<?php the_title()?>" />
					<p><?php the_title();?></p>
				</li>
				
				<?php } else { ?>
				
				<li><a href="<?php echo get_post_meta($post->ID, 'item_url', true ); ?>"><img src="<?php echo $image_scroll[url]; ?>" width="<?php echo $image_scroll[width]; ?>" height="<?php echo $image_scroll[height]; ?>" alt="<?php the_title();?>" title="<?php the_title()?>" /></a>
					<p><?php the_title();?></p>
				</li>
				
		<?php } endwhile;?>
			
		</ul>
		</div>
			
	</div><!-- end product slider -->
	
	<?php } else if(epanel_option('home_product_slider') == 'store_scroll_mode') { ?>
	
		<div id="product-slider">
		
			<h5 class="container"><?php _e('Latest Featured Products','epanel');?></h5>
			
			<div id="scroller-wrapper" class="container">
			
			<?php 
						
			ecart('catalog','featured-products','order='.$shop_catalog_order.'&load=true'); 
							
			// START SHOP SYSTEM LOOP - PLEASE DO NOT EDIT THIS UNLESS YOU KNOW WHAT YOU ARE DOING 
			
			if(ecart('category','hasproducts','load=prices,specs,categories&limit='.$shop_home_limit)) : ?>
				
				<ul id="products-scroller">
					
					<?php while(ecart('category','products')) : $counter++; $counter_last++;?>
					
						<li>
							<a href="<?php ecart('product','link'); ?>"><?php ecart('product','coverimage','width=205&height=140&fit=matte&sharpen=100&alt=product-picture'); ?></a>
							<p class="scroller-product-details"><a href="<?php ecart('product','link'); ?>"><?php ecart('product','name'); ?></a></p>
						</li>
					
					<?php endwhile; ?>
					
				</ul>
		
			<?php
			
			//END SHOP SYSTEM LOOP 
			
			endif; ?>
				
			</div>
			
		</div>
	
	<?php } ?>
	
<?php } ?>

<div class="clearfix"></div>

<!-- start normal content area -->
<div id="homepage-content" class="container">
	
	
	<div class="one_fourth" id="sidebar">
		<?php if ( !function_exists('dynamic_sidebar') || ! generated_dynamic_sidebar('Page Sidebar') ) ; ?>
	</div><!-- end inner page sidebar -->
	
	<div class="three_fourth last" id="page-content">
	
		<?php // START SHOP SYSTEM LOOP - PLEASE DO NOT EDIT THIS UNLESS YOU KNOW WHAT YOU ARE DOING 
			
			// PREPARE THE SHOP LOOP TO DISPLAY PRODUCTS
			
			$shop_category_loop = epanel_option('store_home_catid');
			$shop_catalog_order = epanel_option('store_home_catalog_order');
			$shop_catalog_smart = epanel_option('store_home_catalog_mode');
			$shop_home_limit = epanel_option('store_home_catalog_limit');
			
			if($shop_catalog_smart == 'default') {
			
			ecart('catalog','category','order='.$shop_catalog_order.'&load=true'); 
			
			} else if($shop_catalog_smart == 'latest') {
			
			ecart('catalog','new-products','order='.$shop_catalog_order.'&load=true'); 
			
			} else if($shop_catalog_smart == 'featured') {
			
			ecart('catalog','featured-products','order='.$shop_catalog_order.'&load=true'); 
				
			} else if($shop_catalog_smart == 'on_sale') {
			
			ecart('catalog','onsale-products','order='.$shop_catalog_order.'&load=true');
			
			} else if($shop_catalog_smart == 'singular') {
				
			ecart('catalog','category','id='.$shop_category_loop.'&order='.$shop_catalog_order.'&load=true'); 
			
			}
						 
			
			if(ecart('category','hasproducts','load=prices,specs,categories&limit='.$shop_home_limit)) : ?>
			
			
			<div id="homepage-catalog">
				
				 <?php while(ecart('category','products')) : $products_counter_last++; $clear_row_home++;?>
				 
					 <div id="prd-id-<?php ecart('product','id');?>" class="home-product one_third <?php if($products_counter_last == 3) { echo " last"; $products_counter_last = 0; } ?>">
					 	
					 	<div class="home-product-image">
					 		<a href="<?php ecart('product','link');?>"><?php ecart('product','coverimage','width=205&height=141&fit=crop&sharpen=100&alt=product-picture'); ?></a>
					 	</div>
					 	
					 	<div class="home-product-details">
					 		<h3 class="product-name"><?php ecart('product','name'); ?></h3>
					 		
					 		<?php if (epanel_option('store_home_summary')) { ecart('product','summary'); } ?>	
					 	</div>
						
						<div class="home-product-price">
		                
			                <?php if(ecart('product','has-savings')): ?>
			                
			                	<span class="price home-discount">
			                		
			                		<span class="no-sale-price"><?php ecart('product','price'); ?></span>
	
			                		<span class="sale-price">
			                		<?php if (epanel_option('store_home_sale_show_text')) { echo epanel_option('store_home_sale_text'); } ?>
			                		
			                		<?php ecart('product','saleprice'); ?>
			                		
			                		</span>
			                	</span>
			                
			                <?php else: ?>
			                	
			                	<span class="price">
			                	 <?php ecart('product','price'); ?>
			                	</span>
			                
			                <?php endif; ?>
			            
			                 <?php if (epanel_option('store_home_read_more')) { ?>
			                 
			                 <span class="read-more">
			                	<a href="<?php ecart('product','link');?>"><?php _e('Read Details','epanel');?></a>
			                 </span>
			                 
			                 <?php } ?>
	                	</div>
					 	
					 </div>
					 
					 <?php if($clear_row_home == 3) { echo '<div class="clearfix"></div>'; $clear_row_home = 0; } ?>
				
				<?php endwhile; ?>
			
			</div>
			
			<?php ecart('category','pagination'); ?>
		
		<?php
		
		//END SHOP SYSTEM LOOP 
		
		endif; ?>
			
	
	</div><!-- end page content -->
	

	
</div>
<!-- end homepage content -->

<?php if (epanel_option('enabled_product_slider') && epanel_option('home_scroller_position')  == 'bottom') { ?>
	
	<?php if(epanel_option('home_product_slider') == 'custom' ) { ?>

	<div id="product-slider">
		
		<h5 class="container"><?php _e('Latest Featured Products','epanel');?></h5>
		
		<div id="scroller-wrapper" class="container">
		
		<ul id="products-scroller">
		
		<?php $scroller_loop = new WP_Query( array( 'post_type' => 'scroller', 'posts_per_page' => 100, 'orderby' => 'menu_order', 'order' => 'ASC' ) ); ?>
		
			<?php while ( $scroller_loop->have_posts() ) : $scroller_loop->the_post(); ?>
			
			<?php $thumb_scroll = get_post_thumbnail_id(); $image_scroll = vt_resize( $thumb_scroll,'' , 205, 140, true ); ?>
				
				<?php if (get_post_meta($post->ID, 'item_url', true ) == '') { ?>
				
				<li><img src="<?php echo $image_scroll[url]; ?>" width="<?php echo $image_scroll[width]; ?>" height="<?php echo $image_scroll[height]; ?>" alt="<?php the_title();?>" title="<?php the_title()?>" />
					<p><?php the_title();?></p>
				</li>
				
				<?php } else { ?>
				
				<li><a href="<?php echo get_post_meta($post->ID, 'item_url', true ); ?>"><img src="<?php echo $image_scroll[url]; ?>" width="<?php echo $image_scroll[width]; ?>" height="<?php echo $image_scroll[height]; ?>" alt="<?php the_title();?>" title="<?php the_title()?>" /></a>
					<p><?php the_title();?></p>
				</li>
				
		<?php } endwhile;?>
			
		</ul>
		</div>
			
	</div><!-- end product slider -->
	
	<?php } else if(epanel_option('home_product_slider') == 'store_scroll_mode') { ?>
	
		<div id="product-slider">
		
			<h5 class="container"><?php _e('Latest Featured Products','epanel');?></h5>
			
			<div id="scroller-wrapper" class="container">
			
			<?php 
						
			ecart('catalog','featured-products','order='.$shop_catalog_order.'&load=true'); 
							
			// START SHOP SYSTEM LOOP - PLEASE DO NOT EDIT THIS UNLESS YOU KNOW WHAT YOU ARE DOING 
			
			if(ecart('category','hasproducts','load=prices,specs,categories&limit='.$shop_home_limit)) : ?>
				
				<ul id="products-scroller">
					
					<?php while(ecart('category','products')) : $counter++; $counter_last++;?>
					
						<li>
							<a href="<?php ecart('product','link'); ?>"><?php ecart('product','coverimage','width=205&height=140&fit=crop&sharpen=100&alt=product-picture'); ?></a>
							<p class="scroller-product-details"><a href="<?php ecart('product','link'); ?>"><?php ecart('product','name'); ?></a></p>
						</li>
					
					<?php endwhile; ?>
					
				</ul>
		
			<?php
			
			//END SHOP SYSTEM LOOP 
			
			endif; ?>
				
			</div>
			
		</div>
	
	<?php } ?>
	
<?php } ?>

<?php get_footer();?>