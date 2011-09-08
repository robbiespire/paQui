<?php
/**
 ** WARNING! DO NOT EDIT!
 **
 ** UNLESS YOU KNOW WHAT YOU ARE DOING
 **
 **/
?>
<div id="ecart-cart-ajax">
<?php if (ecart('cart','hasitems')): ?>
	<p class="status">
		<span id="ecart-sidecart-items"><?php ecart('cart','totalitems'); ?></span> <strong><?php _e('Product(s)','Ecart'); ?></strong><br />
		<span id="ecart-sidecart-total" class="money"><?php ecart('cart','total'); ?></span> <strong><?php _e('Total','Ecart'); ?></strong>
	</p>
	<ul>
		<li><a href="<?php ecart('cart','url'); ?>"><?php _e('View Your Shopping Cart','Ecart'); ?></a></li>
		<?php if (ecart('checkout','local-payment')): ?>
		<li><a href="<?php ecart('checkout','url'); ?>"><?php _e('Proceed to Checkout','Ecart'); ?></a></li>
		<?php endif; ?>
	</ul>
<?php else: ?>
	<p class="status"><?php _e('Currently your shopping cart is empty.','Ecart'); ?></p>
<?php endif; ?>
</div>
