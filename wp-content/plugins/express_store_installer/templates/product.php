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
					<span><?php _e('Quantity','epanel');?> :</span><span class="form"><?php ecart('product','quantity','class=selectall&options=1&input=text'); ?></span><span class="addtocart"><?php ecart('product','addtocart','ajax=on'); ?></span>
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
<?php else: ?>
<h3><?php _e('Product Not Found','Ecart'); ?></h3>
<p><?php _e('Sorry! The product you requested is not found in our catalog!','Ecart'); ?></p>
<?php endif; ?>
