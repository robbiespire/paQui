<?php
/**
 ** WARNING! DO NOT EDIT!
 **
 ** UNLESS YOU KNOW WHAT YOU ARE DOING
 **
 **/
?>
<h3><?php _e('Thank you for your order!','Ecart'); ?></h3>

<?php if (ecart('checkout','completed')): ?>

<?php if (ecart('purchase','notpaid')): ?>
	<p><?php _e('Your order has been received but the payment has not yet completed processing.','Ecart'); ?></p>
	
	<?php if (ecart('checkout','offline-instructions','return=1')): ?>
	<p><?php ecart('checkout','offline-instructions'); ?></p>
	<?php endif; ?>
	
	<?php if (ecart('purchase','hasdownloads')): ?>
	<p><?php _e('The download links on your order receipt will not work until the payment is received.','Ecart'); ?></p>
	<?php endif; ?>

	<?php if (ecart('purchase','hasfreight')): ?>
	<p><?php _e('Your items will not ship out until the payment is received.','Ecart'); ?></p>
	<?php endif; ?>

<?php endif; ?>

<?php ecart('checkout','receipt'); ?>

<?php if (ecart('customer','wpuser-created')): ?>
	<p><?php _e('An email was sent with account login information to the email address provided for your order.','Ecart'); ?>  <a href="<?php ecart('customer','url'); ?>"><?php _e('Login to your account','Ecart'); ?></a> <?php _e('to access your orders, change your password and manage your checkout information.','Ecart'); ?></p>
<?php endif; ?>

<?php else: ?>

<p><?php _e('Your order is still in progress and has not yet been received from the payment processor. You will receive an email notification when your payment has been verified and the order has been completed.','Ecart'); ?></p>

<?php endif; ?>
