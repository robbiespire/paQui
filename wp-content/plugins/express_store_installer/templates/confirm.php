<?php
/**
 ** WARNING! DO NOT EDIT!
 **
 ** UNLESS YOU KNOW WHAT YOU ARE DOING
 **
 **/
?>
<?php ecart('checkout','cart-summary'); ?>

<form action="<?php ecart('checkout','url'); ?>" method="post" class="ecart" id="checkout">
	<?php ecart('checkout','function','value=confirmed'); ?>
	<p class="submit"><?php ecart('checkout','confirm-button','value='.__('Confirm This Order','Ecart')); ?></p>
</form>
