<?php
/**
 ** WARNING! DO NOT EDIT!
 **
 ** UNLESS YOU KNOW WHAT YOU ARE DOING
 **
 **/
?>
<?php if (!ecart('customer','process','return=true')): ?>
<?php if(ecart('customer','errors-exist')) ecart('customer','errors'); ?>

<ul class="ecart account">
<?php while (ecart('customer','menu')): $counter++; $counter_last++;?>
	<li class="<?php ecart('customer','management'); ?>"><a href="<?php ecart('customer','management','url'); ?>"><?php ecart('customer','management'); ?></a>
		
		<div class="area-description">
		<?php if($counter_last == 1) { echo epanel_option('first_link_desc');  } else if($counter_last == 2) { echo epanel_option('second_link_desc'); } else if($counter_last == 3) { echo epanel_option('third_link_desc'); } else if($counter_last == 4) { echo epanel_option('fourth_link_desc'); } else if($counter_last == 5) { echo epanel_option('fifth_link_desc'); }  ?></div>	
		
	</li>
<?php endwhile; ?>
</ul>

<?php return true; endif; ?>

<form action="<?php ecart('customer','action'); ?>" method="post" class="ecart validate" autocomplete="off">

<?php if ("account" == ecart('customer','process','return=true')): ?>

	<?php if(!epanel_option('enable_customer_area_link')) { } else { ?>
	
	<p class="customer-link"><a href="<?php ecart('customer','url'); ?>">&laquo; <?php _e('Return to the Customer Area','Ecart'); ?></a></p>
	
	<?php } ?>
	
	<?php if(ecart('customer','errors-exist')) ecart('customer','errors'); ?>
	<?php if(ecart('customer','password-changed')): ?>
		
		<div class="notice"><?php _e('Your password has been changed successfully.','Ecart'); ?></div>
	
	<?php endif; ?>

	<?php if(ecart('customer','profile-saved')): ?>
		<div class="notice"><?php _e('Your account has been updated.','Ecart'); ?></div>
	<?php endif; ?>
	
	<div class="clearfix"></div>
	
	<div id="profile-update-form">
	
	<?php echo stripslashes(epanel_option('profile_edit_additional_content'));?>
	
	<div class="one_half">
		
		<h5><?php _e('Your Account Details','Ecart'); ?></h5>
		
		<label for="firstname"><?php _e('First Name','Ecart'); ?></label>
		<span><?php ecart('customer','firstname','required=true&minlength=2&size=8&title='.__('First Name','Ecart')); ?></span>
		
		<label for="lastname"><?php _e('Last Name','Ecart'); ?></label>
		<span><?php ecart('customer','lastname','required=true&minlength=3&size=14&title='.__('Last Name','Ecart')); ?></span>
		
	</div>
	
	<div class="one_half last">
		
		<h5><?php _e('Change Your Password','Ecart'); ?></h5>
		
		<label for="password"><?php _e('Your New Password','Ecart'); ?></label>
		<span><?php ecart('customer','password','size=14&title='.__('New Password','Ecart')); ?></span>
		
		<label for="confirm-password"><?php _e('Confirm Your New Password','Ecart'); ?></label>
		<span><?php ecart('customer','confirm-password','&size=14&title='.__('Confirm Password','Ecart')); ?></span>

	</div>
	<div class="clearfix"></div>
	
	<div class="one_half">
		
		<label for="company"><?php _e('Company Name (if applicable)','Ecart'); ?></label>
		<span><?php ecart('customer','company','size=20&title='.__('Company','Ecart')); ?></span>
		
		<label for="phone"><?php _e('Phone Number','Ecart'); ?></label>
		<span><?php ecart('customer','phone','format=phone&size=15&title='.__('Phone','Ecart')); ?></span>
		
	</div>
	
	<div class="one_half last">
		
		<label for="email"><?php _e('Email Address','Ecart'); ?></label>
		<span><?php ecart('customer','email','required=true&format=email&size=30&title='.__('Email','Ecart')); ?></span>
		
	</div>
	
	<div class="clearfix"></div>
	
	<p><?php ecart('customer','save-button','label='.__('Update My Profile','Ecart')); ?></p>
	
	</div>
	
<?php endif; // end account ?>

<?php if ("downloads" == ecart('customer','process','return=true')): ?>
	
	<h5 class="form-title"><?php _e('Downloads','Ecart'); ?></h5>
	<p><a href="<?php ecart('customer','url'); ?>">&laquo; <?php _e('Return to Account Management','Ecart'); ?></a></p>
	<?php if (ecart('customer','has-downloads')): ?>
	<table class="productcart" id="account-table">
		<thead>
			<tr class="firstrow">
				<th scope="col"><?php _e('Product','Ecart'); ?></th>
				<th scope="col"><?php _e('Order','Ecart'); ?></th>
				<th scope="col"><?php _e('Amount','Ecart'); ?></th>
			</tr>
		</thead>
		<?php while(ecart('customer','downloads')): ?>
		<tr class="product_row">
			<td class="item-column"><?php ecart('customer','download','name'); ?> <?php ecart('customer','download','variation'); ?><br />
				<small><a href="<?php ecart('customer','download','url'); ?>"><?php _e('Download File','Ecart'); ?></a> (<?php ecart('customer','download','size'); ?>)</small></td>
			<td><?php ecart('customer','download','purchase'); ?><br />
				<small><?php ecart('customer','download','date'); ?></small></td>
			<td><?php ecart('customer','download','total'); ?><br />
				<small><?php ecart('customer','download','downloads'); ?> <?php _e('Downloads','Ecart'); ?></small></td>
		</tr>
		<?php endwhile; ?>
	</table>
	<?php else: ?>
	<p><?php _e('You have no digital product downloads available.','Ecart'); ?></p>
	<?php endif; // end 'has-downloads' ?>

<?php endif; // end downloads ?>

<?php if ("history" == ecart('customer','process','return=true')): ?>
	
	<?php if (ecart('customer','has-purchases')): ?>
	
	<h5 class="form-title"><?php _e('Order History','Ecart');?></h5>
	
		<p><a href="<?php ecart('customer','url'); ?>">&laquo; <?php _e('Return to Account Management','Ecart'); ?></a></p>
		<table class="productcart" id="account-table">
			<thead>
				<tr class="firstrow">
					<th scope="col"><?php _e('Date','Ecart'); ?></th>
					<th scope="col"><?php _e('Order','Ecart'); ?></th>
					<th scope="col"><?php _e('Status','Ecart'); ?></th>
					<th scope="col"><?php _e('Total','Ecart'); ?></th>
					<th scope="col"><?php _e('View','Ecart'); ?></th>
				</tr>
			</thead>
			<?php while(ecart('customer','purchases')): ?>
			<tr class="product_row">
				<td class="item-column"><?php ecart('purchase','date'); ?></td>
				<td><?php ecart('purchase','id'); ?></td>
				<td><?php ecart('purchase','status'); ?></td>
				<td><?php ecart('purchase','total'); ?></td>
				<td><a href="<?php ecart('customer','order'); ?>"><?php _e('View Order','Ecart'); ?></a></td>
			</tr>
			<?php endwhile; ?>
		</table>
		
	<?php else: ?>
	<p><?php _e('You have no orders, yet.','Ecart'); ?></p>
	<?php endif; // end 'has-purchases' ?>
		
<?php endif; // end history ?>

<?php if ("order" == ecart('customer','process','return=true')): ?>
	<p><a href="<?php ecart('customer','url'); ?>">&laquo; <?php _e('Return to Account Management','Ecart'); ?></a></p>

	<?php ecart('purchase','receipt'); ?>
	
	<p><a href="<?php ecart('customer','url'); ?>">&laquo; <?php _e('Return to Account Management','Ecart'); ?></a></p>
<?php endif; ?>

<?php if ("custom" == ecart('customer','process','return=true')): ?>

	<?php if(!epanel_option('enable_customer_area_link')) { } else { ?>
	
	<p class="customer-link"><a href="<?php ecart('customer','url'); ?>">&laquo; <?php _e('Return to the Customer Area','Ecart'); ?></a></p>
	
	<?php } ?>
	
	<?php $showcase_id = epanel_option('custom_page_content'); $showcase_content_id = ep_get_ID_by_page_name($showcase_id);
		
		$post_id = get_post($showcase_content_id); $content = $post_id->post_content;
		
		$content = apply_filters('the_content', $content); $content = str_replace(']]>', ']]>', $content);
		
		echo $content;
	?>		

<?php endif; ?>

</form>
