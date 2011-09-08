<?php
/**
 ** WARNING! DO NOT EDIT!
 **
 ** UNLESS YOU KNOW WHAT YOU ARE DOING
 **
 **/
?>
<form action="<?php ecart('checkout','url'); ?>" method="post" class="ecart validate" id="checkout">
<?php ecart('checkout','cart-summary'); ?>

<?php if (ecart('cart','hasitems')): ?>
	<?php ecart('checkout','function'); ?>
	<ul id="customer-form">
		<?php if (ecart('customer','notloggedin')): ?>
		<li class="reg-input">
			<h5><?php _e('Login to Your Account','Ecart'); ?></h5>
			<span><?php ecart('customer','account-login','size=20&title='.__('Login','Ecart')); ?><label for="account-login"><?php _e('Email','Ecart'); ?></label></span>
			<span><?php ecart('customer','password-login','size=20&title='.__('Password','Ecart')); ?><label for="password-login"><?php _e('Password','Ecart'); ?></label></span>
			<span><?php ecart('customer','login-button','context=checkout&value=Login'); ?></span>
		</li>
		<li></li>
		<?php endif; ?>
		<li class="reg-input">
			<h5><?php _e('Contact Information','Ecart'); ?></h5>
			<span><?php ecart('checkout','firstname','required=true&minlength=2&size=8&title='.__('First Name','Ecart')); ?><label for="firstname"><?php _e('First Name','Ecart'); ?></label></span>
			<span><?php ecart('checkout','lastname','required=true&minlength=3&size=14&title='.__('Last Name','Ecart')); ?><label for="lastname"><?php _e('Last Name','Ecart'); ?></label></span>
			<span><?php ecart('checkout','company','size=22&title='.__('Company/Organization','Ecart')); ?><label for="company"><?php _e('Company Organization','Ecart'); ?></label></span>
		</li>
		<li>
		</li>
		<li class="reg-input">
			<span><?php ecart('checkout','phone','format=phone&size=15&title='.__('Phone','Ecart')); ?><label for="phone"><?php _e('Phone','Ecart'); ?></label></span>
			<span><?php ecart('checkout','email','required=true&format=email&size=30&title='.__('Email','Ecart')); ?>
			<label for="email"><?php _e('Email Address','Ecart'); ?></label></span>
		</li>
		<?php if (ecart('customer','notloggedin')): ?>
		<li class="reg-input">
			<span><?php ecart('checkout','password','required=true&format=passwords&size=16&title='.__('Password','Ecart')); ?>
			<label for="password"><?php _e('Password','Ecart'); ?></label></span>
			<span><?php ecart('checkout','confirm-password','required=true&format=passwords&size=16&title='.__('Password Confirmation','Ecart')); ?>
			<label for="confirm-password"><?php _e('Confirm Password','Ecart'); ?></label></span>
		</li>
		<?php endif; ?>
		<li></li>
		<?php if (ecart('cart','needs-shipped')): ?>
			<li class="half" id="billing-address-fields">
		<?php else: ?>
			<li>
		<?php endif; ?>
			<h5 class="space-head"><?php _e('Billing Address','Ecart'); ?></h5>
			<div>
				<?php ecart('checkout','billing-address','required=true&title='.__('Billing street address','Ecart')); ?>
				<label for="billing-address"><?php _e('Street Address','Ecart'); ?></label>
			</div>
			<div>
				<?php ecart('checkout','billing-xaddress','title='.__('Billing address line 2','Ecart')); ?>
				<label for="billing-xaddress"><?php _e('Address Line 2','Ecart'); ?></label>
			</div>
			<div class="left">
				<?php ecart('checkout','billing-city','required=true&title='.__('City billing address','Ecart')); ?>
				<label for="billing-city"><?php _e('City','Ecart'); ?></label>
			</div>
			<div class="right">
				<?php ecart('checkout','billing-state','required=true&title='.__('State/Provice/Region billing address','Ecart')); ?>
				<label for="billing-state"><?php _e('State / Province','Ecart'); ?></label>
			</div>
			<div class="left">
				<?php ecart('checkout','billing-postcode','required=true&title='.__('Postal/Zip Code billing address','Ecart')); ?>
				<label for="billing-postcode"><?php _e('Postal / Zip Code','Ecart'); ?></label>
			</div>
			<div class="right">
				<?php ecart('checkout','billing-country','required=true&title='.__('Country billing address','Ecart')); ?>
				<label for="billing-country"><?php _e('Country','Ecart'); ?></label>
			</div>
		<?php if (ecart('cart','needs-shipped')): ?>
			
			</li>
			<li class="half right" id="shipping-address-fields">
				<h5 class="space-head"><?php _e('Shipping Address','Ecart'); ?></h5>
				<div>
					<?php ecart('checkout','shipping-address','required=true&title='.__('Shipping street address','Ecart')); ?>
					<label for="shipping-address"><?php _e('Street Address','Ecart'); ?></label>
				</div>
				<div>
					<?php ecart('checkout','shipping-xaddress','title='.__('Shipping address line 2','Ecart')); ?>
					<label for="shipping-xaddress"><?php _e('Address Line 2','Ecart'); ?></label>
				</div>
				<div class="left">
					<?php ecart('checkout','shipping-city','required=true&title='.__('City shipping address','Ecart')); ?>
					<label for="shipping-city"><?php _e('City','Ecart'); ?></label>
				</div>
				<div class="right">
					<?php ecart('checkout','shipping-state','required=true&title='.__('State/Provice/Region shipping address','Ecart')); ?>
					<label for="shipping-state"><?php _e('State / Province','Ecart'); ?></label>
				</div>
				<div class="left">
					<?php ecart('checkout','shipping-postcode','required=true&title='.__('Postal/Zip Code shipping address','Ecart')); ?>
					<label for="shipping-postcode"><?php _e('Postal / Zip Code','Ecart'); ?></label>
				</div>
				<div class="right">
					<?php ecart('checkout','shipping-country','required=true&title='.__('Country shipping address','Ecart')); ?>
					<label for="shipping-country"><?php _e('Country','Ecart'); ?></label>
				</div>
			</li>
		<?php else: ?>
			</li>
		<?php endif; ?>
		<?php if (ecart('checkout','billing-localities')): ?>
			<li class="half locale hidden">
				<div>
				<?php ecart('checkout','billing-locale'); ?>
				<label for="billing-locale"><?php _e('Local Jurisdiction','Ecart'); ?></label>
				</div>
			</li>
		<?php endif; ?>
		
		<li></li>
		<li>
			<h5 class="space-head"><?php _e('Payment Information','Ecart'); ?></h5>
			<?php ecart('checkout','payment-options'); ?>
			<?php ecart('checkout','gateway-inputs'); ?>
		</li>
		<?php if (ecart('checkout','card-required')): ?>
		<li class="payment reg-input">
			<span><?php ecart('checkout','billing-card','required=true&size=30&title='.__('Credit/Debit Card Number','Ecart')); ?><label for="billing-card"><?php _e('Credit/Debit Card Number','Ecart'); ?></label></span>
			<span><?php ecart('checkout','billing-cardexpires-mm','size=4&required=true&minlength=2&maxlength=2&title='.__('Card\'s 2-digit expiration month','Ecart')); ?> /<label for="billing-cardexpires-mm"><?php _e('MM','Ecart'); ?></label></span>
			<span><?php ecart('checkout','billing-cardexpires-yy','size=4&required=true&minlength=2&maxlength=2&title='.__('Card\'s 2-digit expiration year','Ecart')); ?><label for="billing-cardexpires-yy"><?php _e('YY','Ecart'); ?></label></span>
			<span><?php ecart('checkout','billing-cardtype','required=true&title='.__('Card Type','Ecart')); ?></span>
		</li>
		<li class="payment reg-input">
			<span><?php ecart('checkout','billing-cardholder','required=true&size=30&title='.__('Card Holder\'s Name','Ecart')); ?><label for="billing-cardholder"><?php _e('Name on Card','Ecart'); ?></label></span>
			<span><?php ecart('checkout','billing-cvv','size=7&minlength=3&maxlength=4&title='.__('Card\'s security code (3-4 digits on the back of the card)','Ecart')); ?><label for="billing-cvv"><?php _e('Security ID','Ecart'); ?></label></span>
		</li>
		<?php if (ecart('checkout','billing-xcsc-required')): // Extra billing security fields ?>
		<li class="payment reg-input">
		<span><?php ecart('checkout','billing-xcsc','input=start&size=7&minlength=5&maxlength=5&title='.__('Card\'s start date (MM/YY)','Ecart')); ?><label for="billing-xcsc-start"><?php _e('Start Date','Ecart'); ?></label></span>
			<span><?php ecart('checkout','billing-xcsc','input=issue&size=7&minlength=3&maxlength=4&title='.__('Card\'s issue number','Ecart')); ?><label for="billing-xcsc-issue"><?php _e('Issue #','Ecart'); ?></label></span>
		</li>
		<?php endif; ?>
		
		<?php endif; ?>
		<li></li>
		<li>
		</li>
	</ul>
	<p class="submit"><?php ecart('checkout','submit','value='.__('Confirm This Order','Ecart')); ?></p>

<?php endif; ?>
</form>
