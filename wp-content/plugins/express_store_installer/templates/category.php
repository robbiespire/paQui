<?php
/**
 ** WARNING! DO NOT EDIT!
 **
 ** UNLESS YOU KNOW WHAT YOU ARE DOING
 **
 **/
?>
<?php if(ecart('category','hasproducts','load=prices,images')): ?>
	<div id="catalog-wrapper" class="category">
		
		<?php if(epanel_option('show_category_name')) { ?>
		<div class="catalog-cat-name"><h4><?php ecart('category','name'); ?></h4></div>
		<?php } ?>
		
		<?php if(epanel_option('show_product_sorter')) { ?>
		<div class="catalog-dropdown"><?php ecart('catalog','orderby-list','dropdown=on'); ?></div>
		<?php } ?>
		
		<div class="clearfix"></div>
		
		<div id="catalog-products">
		
			<?php while(ecart('category','products')): $products_counter_last++;?>
			
				<div id="prd-id-<?php ecart('product','id');?>" class="catalog-product one_third <?php if($products_counter_last == 3) { echo " last"; $products_counter_last = 0; } ?>">
					
					<?php $image_resize_method = epanel_option('catalog_pic_resize_method'); ?>
					
					<a href="<?php ecart('product','link');?>"><?php ecart('product','coverimage','width=205&height=141&fit='.$image_resize_method.'&sharpen=100&alt=product-picture'); ?></a>
					
					<div class="product-details">
						<h3 class="product-name"><?php ecart('product','name');?></h3>
						
						<?php if(epanel_option('show_product_summary')) { ?>
							<?php ecart('product','summary');?>
						<?php } ?>
						
					</div>
					
					<div class="price-details">
						
						 <?php if(ecart('product','has-savings')): ?>
						 	
						 	<div class="discounted-price">
						 		<p class="old-price"><?php ecart('product','price'); ?></p>
						 		
						 		<div class="new-price">
							 		<p><?php if (epanel_option('store_home_sale_show_text')) { echo epanel_option('store_home_sale_text'); } ?></p>
							 		<p><?php ecart('product','saleprice'); ?></p>
						 		</div>
						 	</div>
						 	
						 	<a class="catalog-link-discount" href="<?php ecart('product','link');?>"><?php _e('Read Details','epanel');?></a>
						 
						 <?php else: ?>
						 	
						 	<div class="catalog-price">
						 	 <?php ecart('product','price'); ?>
						 	</div>
						 	
						 	<a class="catalog-link" href="<?php ecart('product','link');?>"><?php _e('Read Details','epanel');?></a>
						 	
						 <?php endif; ?>
					
							
					</div>
				
				</div>
				
				<?php if($products_counter_last == 3) { echo '<div class="clearfix"></div>'; $products_counter_last = 0; } ?>
			
			<?php endwhile; ?>
		
		</div>
		
	</div>
	
	<div id="catalog-pagination"><?php ecart('category','pagination','label=&after=&before=&previous=Previous Page&next=Next Page'); ?></div>
	
	<?php else: ?>
	<?php if (!ecart('catalog','is-landing')): ?>
	<h3><?php _e('Ops Something Went Wrong.....','Ecart'); ?></h3>
	<p><?php _e('No products were found.','Ecart'); ?></p>
	<?php endif; ?>
<?php endif; ?>
