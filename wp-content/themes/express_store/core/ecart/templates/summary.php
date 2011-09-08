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
	<a class="link-right" href="<?php ecart('cart','url'); ?>"><?php _e('Back To Your Shopping Cart','Ecart'); ?> &raquo;</a>
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
			</tr>
		<?php endwhile; ?>
	
	</table>
	
	<div class="one_half">
	<div id="shipping-container">
			
		<span id="shipping-heading"><?php _e('Shipping Method','Ecart'); ?></span>
		
		<?php if ((ecart('cart','has-shipping-methods'))): ?>
		
		<div class="inner-form">
		
		<form action="<?php ecart('shipping','url') ?>" method="post">
		
		<?php while(ecart('shipping','methods')): ?>
			<p><?php ecart('shipping','method-selector'); ?> <?php ecart('shipping','method-name'); ?> : <?php ecart('shipping','method-cost'); ?><span class="alignright"><strong><?php _e('Estimated Delivery: ','Ecart');?><?php ecart('shipping','method-delivery'); ?></strong></span></p>
		<?php endwhile; ?>
		</form>
		
		</div>
		<?php endif; ?>
		
	</div>
	</div>
	
	<div class="one_half last">
	
	<div class="cart-subtotal" id="summary-total">
		
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
	</div>

<div class="clearfix"></div>

</div>
</form>

<?php else: ?>
	<p class="warning"><?php _e('There are currently no items in your shopping cart.','Ecart'); ?></p>
	<p><a href="<?php ecart('catalog','url'); ?>">&laquo; <?php _e('Continue Shopping','Ecart'); ?></a></p>
<?php endif; ?>
