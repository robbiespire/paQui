<?php
/**
 ** WARNING! DO NOT EDIT!
 **
 ** UNLESS YOU KNOW WHAT YOU ARE DOING
 **
 **/
?>
<?php if (ecart('product','found')): ?>

<div id="single-product-wrapper">
	
	<div class="one_fourth">
		
		<div id="main-picture">
			<?php 
			
			$thumb_width = epanel_option('inner_width');
			$thumb_height = epanel_option('inner_height');
			
		 	$image_resize_method = epanel_option('catalog_pic_resize_method');
			
			ecart('product','coverimage','width='.$thumb_width.'&height='.$thumb_height.'&fit='.$image_resize_method.'&sharpen=100&alt=product-picture'); ?>
		</div>
		
		<div id="thumbs">
			<?php if(ecart('product','has-images')): $counter++; $counter_last++; ?>
				
				<ul id="thumbs-list">
					
					<?php while(ecart('product','images')): ?>
						<li class="one_third <?php if($counter_last == 3) { echo " last"; $counter_last = 0; } ?>"><?php ecart('product','image','alt=thumb&class=thumb-picture&width=64&height=64&fit='.$image_resize_method.'&zoom=true'); ?></li>
					<?php endwhile; ?>
					
				</ul><!-- end thumb list -->
				
			<?php else: ?>
				
			<?php endif; ?>
		</div><!-- end thumbs -->
		
		
	</div><!-- end pictures wrapper -->
	
	<div id="singular-product" class="three_fourth last">
		<h3><?php ecart('product','name'); ?></h3>
			
		<?php ecart('product','description'); ?>

		<?php if (epanel_option('show_free_shipping')) { 
			
			if(ecart('product','freeshipping')) { 
			
				echo '<span class="free-shipping">'; _e('Free Shipping Available on this product!','Ecart'); echo '</span>';
				
				} 
			} 
		?>
		
		<?php if(ecart('product','has-specs')): ?>
		<dl class="details">
			<?php while(ecart('product','specs')): ?>
			<dt><?php ecart('product','spec','name'); ?>:</dt><dd><?php ecart('product','spec','content'); ?></dd>
			<?php endwhile; ?>
		</dl>
		<?php endif; ?>
		
		<form action="<?php ecart('cart','url'); ?>" method="post" class="ecart product validate">
		
		<div class="product-prices">
			
			<div class="prices-show">
			<?php if (ecart('product','onsale')): ?>
				<p class="singular-price old-price"><?php _e('Product Price : ','Ecart'); ecart('product','price'); ?></p>
				<p class="newdiscounted"><?php _e('New Sale Price : ','Ecart'); ecart('product','saleprice'); ?></p>
			<?php else: ?>
				<p class="singular-price"><?php _e('Product Price : ','Ecart'); echo '<span>'; ecart('product','price'); echo '</span>';?></p>
			<?php endif; ?>
			</div>
			
			<div class="purchase-fields">
					<span><?php _e('Quantity','epanel');?> :</span><span class="form"><?php ecart('product','quantity','class=selectall&options=1&input=text'); ?></span><span class="addtocassaart"><?php ecart('product','addtocart','ajax=on'); ?></span>
			</div>
				
		</div><!-- end product prices -->
		
		<?php if(ecart('product','has-variations')): ?>
		
			<div class="product-settings">	
				<ul class="product-config">
					<?php ecart('product','variations','mode=multiple&label=true&defaults='.__('Select an option','Ecart').'&before_menu=<li>&after_menu=</li>'); ?>
				</ul>
			</div>
			
		<?php endif; ?>
		
		
		</form>
		
	</div>

</div>
<?php if (epanel_option('enable_related')) { ?>
<div class="clearfix"></div>
<div id="related-slider">

	<h5><?php echo epanel_option('related_title');?></h5>
	
	<div id="related-wrapper" class="container">
	
	<?php 
				
	ecart('catalog','featured-products','order='.$shop_catalog_order.'&load=true'); 
					
	// START SHOP SYSTEM LOOP - PLEASE DO NOT EDIT THIS UNLESS YOU KNOW WHAT YOU ARE DOING 
	
	$related_limit = epanel_option('related_limit');
	
	if(ecart('category','hasproducts','load=prices,specs,categories&limit='.$related_limit)) : ?>
		
		<ul id="products-scroller">
			
			<?php while(ecart('category','products')) : $counter++; $counter_last++;?>
			
				<li>
					<a href="<?php ecart('product','link'); ?>"><?php ecart('product','coverimage','width=150&height=120&fit=matte&sharpen=100&alt=product-picture'); ?></a>
					<p class="related-product-details"><a href="<?php ecart('product','link'); ?>"><?php ecart('product','name'); ?></a></p>
				</li>
			
			<?php endwhile; ?>
			
		</ul>

	<?php
	
	//END SHOP SYSTEM LOOP 
	
	endif; ?>
		
	</div>
	
</div>

<?php } ?>

<?php else: ?>
<h3><?php _e('Product Not Found','Ecart'); ?></h3>
<p><?php _e('Sorry! The product you requested is not found in our catalog!','Ecart'); ?></p>
<?php endif; ?>
