<form action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="post">
<?php if(ecart('customer','errors-exist')) ecart('customer','errors'); ?>
<ul>
	<li>
		<span><input type="text" name="purchaseid" size="12" /><label><?php _e('Order Number','Ecart'); ?></label></span>
		<span><input type="text" name="email" size="32" /><label><?php _e('E-mail Address','Ecart'); ?></label></span>
		<span><input type="submit" name="vieworder" value="<?php _e('View Order','Ecart'); ?>" /></span>
	</li>
</ul>
<br class="clear" />
</form>