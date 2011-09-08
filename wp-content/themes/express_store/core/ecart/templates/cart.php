<?php
/**
 ** WARNING! DO NOT EDIT!
 **
 ** UNLESS YOU KNOW WHAT YOU ARE DOING
 **
 **/
?>
<?php if (ecart('cart','hasitems')): ?>
<form id="shopping-cart-form" action="<?php ecart('cart','url'); ?>" method="post">
<div id="cart-navigation">
	<a class="link-left" href="<?php ecart('cart','referrer'); ?>">&laquo; <?php _e('Continue Shopping','Ecart'); ?></a>
	<a class="link-right" href="<?php ecart('checkout','url'); ?>"><?php _e('Proceed to Checkout','Ecart'); ?> &raquo;</a>
</div>
<br/>
<div id="shopping-cart-wrapper">
	<?php ecart('cart','function'); ?>
	<table id="shopping-table" class="productcart">
		<tr class="firstrow">
			<th scope="col" class="firstcol"><?php _e('Product(s)','Ecart'); ?></th>
			<th scope="col"><?php _e('Quantity','Ecart'); ?></th>
			<th scope="col" class="money"><?php _e('Item Price','Ecart'); ?></th>
			<th scope="col" class="money"><?php _e('Item Total','Ecart'); ?></th>
			<th scope="col" class="money"></th>
		</tr>
	
		<?php while(ecart('cart','items')): ?>
			<tr class="product_row">
				<td class="item-column">
					<a href="<?php ecart('cartitem','url'); ?>"><?php ecart('cartitem','name'); ?></a>
					<?php ecart('cartitem','options'); ?>
					<?php ecart('cartitem','addons-list'); ?>
					<?php ecart('cartitem','inputs-list'); ?>
				</td>
				<td class="right-row"><?php ecart('cartitem','quantity','input=text'); ?></td>
				<td class="money right-row"><?php ecart('cartitem','unitprice'); ?></td>
				<td class="money right-row"><?php ecart('cartitem','total'); ?></td>
				<td class="money right-row"><?php ecart('cartitem','remove','label=Remove Product&class=remove-product'); ?></td>
			</tr>
		<?php endwhile; ?>
	
	</table>
	
	
	<div id="shopping-cart-shipping-total" class="one_half">
	
		<?php if (ecart('cart','needs-shipping-estimates')): ?>
	
		<div class="shippingcart">
			
			<span id="shipping-heading"><?php _e('Estimate shipping &amp; taxes for:','Ecart'); ?></span>
				
			<div class="inner-form">
				<?php _e('Please choose your country :','Ecart');?><?php ecart('cart','shipping-estimates'); ?>
			</div>
			
		</div>
		
		<?php endif;?>
		
		<div class="promocart">
			
			<span id="promo-heading"><?php _e('Coupons &amp; Promotions','Ecart'); ?></span>
				
			<div class="inner-form-coupon">
				<?php _e('Coupon Code :','Ecart');?><?php ecart('cart','promo-code','value=Apply Coupon Code'); ?>
			</div>
			
		</div>
	
	</div>
	
	<div class="one_half last" id="shopping-cart-subtotal">
	
		<div class="cart-subtotal">
			
			<span id="subtotal-heading"><?php _e('Shopping Cart Summary','Ecart'); ?></span>
				
			<div class="inner-form" id="total-form">
				<p><?php _e('Subtotal :','Ecart'); ?> <span><?php ecart('cart','subtotal'); ?></span></p>
				
				<?php if (ecart('cart','hasdiscount')): ?>
				<p><?php _e('Discount :','Ecart'); ?> <span>-<?php ecart('cart','discount'); ?></span></p>
				<?php endif; ?>
				
				<?php if (ecart('cart','needs-shipped')): ?>
				<p><?php ecart('cart','shipping','label='.__('Shipping :','Ecart')); ?> <span><?php ecart('cart','shipping'); ?></span></p>
				<?php endif; ?>
				
				<?php if(ecart('cart','hastaxes')) : ?>
				<p><?php ecart('cart','tax','label='.__('Taxes :','Ecart')); ?> <span><?php ecart('cart','tax'); ?></span></p>
				<?php endif; ?>
				
				<p class="last-total"><?php _e('Total','Ecart'); ?> <span><?php ecart('cart','total'); ?></span></p>
				
			</div>
			
		</div>
		
		<div id="update-cart" class="alignright"><?php ecart('cart','update-button','value=Update Shopping Cart'); ?></div>
	
	</div>

</div>
</form>

<?php else: ?>
	<p class="warning"><?php _e('There are currently no items in your shopping cart.','Ecart'); ?></p>
	<p><a href="<?php ecart('catalog','url'); ?>">&laquo; <?php _e('Continue Shopping','Ecart'); ?></a></p>
<?php endif; ?>
