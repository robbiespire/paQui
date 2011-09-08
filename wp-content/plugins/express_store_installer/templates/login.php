<?php
/**
 ** WARNING! DO NOT EDIT!
 **
 ** UNLESS YOU KNOW WHAT YOU ARE DOING
 **
 **/
?>
<?php if (ecart('customer','accounts','return=true') == "none"): ?>
	<?php ecart('customer','order-lookup'); ?>
<?php return; endif; ?>
<?php ecart('customer','login-errors'); ?>
<form action="<?php ecart('customer','url'); ?>" method="post" class="ecart" id="login">

<?php if (ecart('customer','process','return=true') == "recover"): ?>

	<ul>
		<li><h3><?php _e('Recover your password','Ecart'); ?></h3></li>
		<li><?php ecart('customer','login-errors'); ?></li>
		<li>
		<span><?php ecart('customer','account-login','size=20&title='.__('Login','Ecart')); ?><label for="login"><?php ecart('customer','login-label'); ?></label></span>
		<span><?php ecart('customer','recover-button'); ?></span>
		</li>
		<li></li>
	</ul>

<?php else: ?>
	
<ul>
	<?php if (ecart('customer','notloggedin')): ?>
	<?php ecart('customer','login-errors'); ?>
	<li id="login-inputs">
		<label for="login"><?php _e('Account Login','Ecart'); ?></label>
		<span><?php ecart('customer','account-login','size=20&title='.__('Login','Ecart')); ?></span>
		
		<label for="password"><?php _e('Password','Ecart'); ?></label>
		<span><?php ecart('customer','password-login','size=20&title='.__('Password','Ecart')); ?></span>
		
		<span><?php ecart('customer','login-button'); ?></span>
	</li>
	
	<?php endif; ?>
</ul>

<?php endif; ?>

</form>
<div class="clearfix"></div><br/>
