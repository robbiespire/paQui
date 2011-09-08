<div class="wrap ecart">
	<div class="icon32"></div>
	<h2><?php _e('Arrange Categories','Ecart'); ?></h2>
	<?php if (!empty($this->Notice)): ?><div id="message" class="updated fade"><p><?php echo $this->Notice; ?></p></div><?php endif; ?>

	<form action="" id="categories" method="get">
	<div>
		<input type="hidden" name="page" value="<?php echo $this->Admin->pagename('categories'); ?>" />
	</div>

	<div class="tablenav">
		<div class="alignleft actions">
			<a href="<?php echo esc_url(add_query_arg(array_merge(stripslashes_deep($_GET),array('page'=>$this->Admin->pagename('categories'),'a'=>null)),admin_url('admin.php'))); ?>" class="button add-new">&larr; <?php _e('Return to Manage Categories','Ecart'); ?></a>
		</div>
		<div class="clear"></div>
	</div>
	<div class="clear"></div>

	<table id="arrange-categories" class="widefat" cellspacing="0">
		<thead>
		<tr><?php print_column_headers('ecart_page_ecart-categories'); ?></tr>
		</thead>
		<tfoot>
		<tr><?php print_column_headers('ecart_page_ecart-categories',false); ?></tr>
		</tfoot>
	<?php if (sizeof($Categories) > 0): ?>
		<tbody id="categories-table" class="list categories">
		<?php
		$hidden = array();
		$hidden = get_hidden_columns('ecart_page_ecart-categories');

		$even = false;
		foreach ($Categories as $Category):

		$editurl = esc_url(esc_attr(add_query_arg(array_merge(stripslashes_deep($_GET),
			array('page'=>$this->Admin->pagename('categories'),
					'id'=>$Category->id)),
					admin_url('admin.php'))));

		$CategoryName = empty($Category->name)?'('.__('no category name','Ecart').')':$Category->name;

		$membership = explode('/',$Category->uri);

		if (count($membership) > 1) $membership[] = $membership[count($membership)-2].'-child';
		if ($membership[0] == $Category->slug) $membership[] = "top";
		$stripe = (!$even)?'alternate':''; $even = !$even;
		$classes = join(' ',$membership).(empty($stripe)?'':' '.$stripe);

		?>
		<tr class="<?php echo $classes; ?>" rel="<?php echo $Category->slug; ?>">
			<td><button type="button" name="top" alt="<?php _e('Move to the top','Ecart'); ?>&hellip;" class="moveto top">&nbsp;</button><button type="button" name="bottom" alt="<?php _e('Move to the bottom','Ecart'); ?>&hellip;" class="moveto bottom">&nbsp;</button><a class='row-title' href='<?php echo $editurl; ?>' title='<?php _e('Edit','Ecart'); ?> &quot;<?php echo $CategoryName; ?>&quot;'><?php echo str_repeat("&#8212; ",$Category->depth); echo $CategoryName; ?></a>
			<input type="hidden" name="id" value="<?php echo $Category->id; ?>" /><input type='hidden' name='position[<?php echo $Category->id; ?>]' value="<?php echo $Category->priority; ?>" size="4" class="num selectall" /></td>
			<th scope='row' width="48"><button type="button" name="collapse" class="collapsing closed">&nbsp;</button></th>
		</tr>
		<?php endforeach; ?>
		</tbody>
	<?php else: ?>
		<tbody><tr><td colspan="6"><?php _e('No categories found.','Ecart'); ?></td></tr></tbody>
	<?php endif; ?>
	</table>
	</form>
	<div class="tablenav">
		<?php if ($page_links) echo "<div class='tablenav-pages'>$page_links</div>"; ?>
		<div class="clear"></div>
	</div>
</div>

<script type="text/javascript">
var loadchildren_url = '<?php echo wp_nonce_url(admin_url('admin-ajax.php'),'wp_ajax_ecart_category_children'); ?>',
	updates_url = '<?php echo wp_nonce_url(admin_url('admin-ajax.php'),'wp_ajax_ecart_category_order'); ?>',
	SAVE_ERROR = "<?php _e('The category order could not be updated because of a communication error with the server.','Ecart'); ?>";
	LOAD_ERROR = "<?php _e('The child categories could not be loaded because of a communication error with the server.','Ecart'); ?>";
jQuery(document).ready( function() {
	pagenow = 'ecart_page_ecart-categories';
	columns.init(pagenow);
});
</script>