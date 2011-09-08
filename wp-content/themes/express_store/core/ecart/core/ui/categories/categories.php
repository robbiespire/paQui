<div class="wrap ecart">
	<div class="icon32"></div>
	<h2><?php _e('Categories Manager','Ecart'); ?> <a href="<?php echo esc_url(add_query_arg(array_merge(stripslashes_deep($_GET),array('page'=>$this->Admin->pagename('categories'),'id'=>'new')),admin_url('admin.php'))); ?>" class="button add-new"><?php _e('Add New Category','Ecart'); ?></a></h2>
	<?php if (!empty($this->Notice)): ?><div id="message" class="updated fade"><p><?php echo $this->Notice; ?></p></div><?php endif; ?>

	<form action="" id="categories" method="get">
	<div>
		<input type="hidden" name="page" value="<?php echo $this->Admin->pagename('categories'); ?>" />
	</div>

	<p id="post-search" class="search-box"  style="display:none !important;">
		<input type="text" id="categories-search-input" class="search-input" name="s" value="<?php echo esc_attr(stripslashes($s)); ?>" />
		<input type="submit" value="<?php _e('Search Categories','Ecart'); ?>" class="button" />
	</p>

	<div class="tablenav">
		<?php if ($page_links) echo "<div class='tablenav-pages'>$page_links</div>"; ?>
		<div class="alignleft actions">
			<button type="submit" id="delete-button" name="deleting" value="category" class="button-secondary"><?php _e('Delete','Ecart'); ?></button>
			&nbsp;
			<a style="display:none !important;" href="<?php echo esc_url(add_query_arg(array_merge(stripslashes_deep($_GET),array('page'=>$this->Admin->pagename('categories'),'a'=>'arrange')),admin_url('admin.php'))); ?>" class="button add-new"><?php _e('Arrange','Ecart'); ?></a>
		</div>
		<div class="clear"></div>
	</div>
	<div class="clear"></div>

	<table class="widefat" cellspacing="0">
		<thead>
		<tr><?php print_column_headers('ecart_page_ecart-categories'); ?><th scope="col" id="id" class="manage-column column-id num" style="">Category ID</th></tr>
		</thead>
		<tfoot>
		<tr><?php print_column_headers('ecart_page_ecart-categories',false); ?><th scope="col" class="manage-column column-id num" style="">Category ID</th></tr>
		</tfoot>
	<?php if (sizeof($Categories) > 0): ?>
		<tbody id="categories-table" class="list categories">
		<?php
		$hidden = array();
		$hidden = get_hidden_columns('ecart_page_ecart-categories');

		$even = false;
		foreach ($Categories as $Category):

		$editurl = esc_url(add_query_arg(array_merge($_GET,
			array('page'=>$this->Admin->pagename('categories'),
					'id'=>$Category->id)),
					admin_url('admin.php')));

		$deleteurl = esc_url(add_query_arg(array_merge($_GET,
			array('page'=>$this->Admin->pagename('categories'),
					'delete[]'=>$Category->id,
					'deleting'=>'category')),
					admin_url('admin.php')));

		$CategoryName = empty($Category->name)?'('.__('no category name','Ecart').')':$Category->name;
		?>
		<tr<?php if (!$even) echo " class='alternate'"; $even = !$even; ?>>
			<th scope='row' class='check-column'><input type='checkbox' name='delete[]' value='<?php echo $Category->id; ?>' /></th>
			<td><a class='row-title' href='<?php echo $editurl; ?>' title='<?php _e('Edit','Ecart'); ?> &quot;<?php echo esc_attr($CategoryName); ?>&quot;'><?php echo str_repeat("&#8212; ",$Category->depth); echo esc_html($CategoryName); ?></a>
				<div class="row-actions">
					<span class='edit'><a href="<?php echo $editurl; ?>" title="<?php _e('Edit','Ecart'); ?> &quot;<?php echo esc_attr($CategoryName); ?>&quot;"><?php _e('Edit','Ecart'); ?></a> | </span>
					<span class='delete'><a class='submitdelete' title='<?php _e('Delete','Ecart'); ?> &quot;<?php echo esc_attr($CategoryName); ?>&quot;' href="<?php echo $deleteurl; ?>" rel="<?php echo $Category->id; ?>"><?php _e('Delete','Ecart'); ?></a> | </span>
					<span class='view'><a href="<?php echo ecarturl(ECART_PRETTYURLS?"category/$Category->uri":array('ecart_category'=>$Category->id)); ?>" title="<?php _e('View','Ecart'); ?> &quot;<?php echo esc_attr($CategoryName); ?>&quot;" rel="permalink" target="_blank"><?php _e('View','Ecart'); ?></a></span>
				</div>
			</td>
			<td width="5%" class="num links column-links<?php echo in_array('links',$hidden)?' hidden':''; ?>"><?php echo $Category->total; ?></td>
			<td width="5%" class="templates column-templates<?php echo ($Category->spectemplate == "on")?' spectemplates':''; echo in_array('templates',$hidden)?' hidden':''; ?>">&nbsp;</td>
			<td width="5%" class="menus column-menus<?php echo ($Category->facetedmenus == "on")?' facetedmenus':''; echo in_array('menus',$hidden)?' hidden':''; ?>">&nbsp;</td>
			<td width="5%" style="text-align:center;" class="menus column-id"><?php echo $Category->id; ?></td>
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
jQuery(document).ready( function() {

	var $ = jQuery.noConflict();

	$('#selectall').change( function() {
		$('#categories-table th input').each( function () {
			if (this.checked) this.checked = false;
			else this.checked = true;
		});
	});

	$('a.submitdelete').click(function () {
		if (confirm("<?php _e('You are about to delete this category!\n \'Cancel\' to stop, \'OK\' to delete.','Ecart'); ?>")) {
			$('<input type="hidden" name="delete[]" />').val($(this).attr('rel')).appendTo('#categories');
			$('<input type="hidden" name="deleting" />').val('category').appendTo('#categories');
			$('#categories').submit();
			return false;
		} else return false;
	});

	$('#delete-button').click(function() {
		if (confirm("<?php echo addslashes(__('Are you sure you want to delete the selected categories?','Ecart')); ?>")) {
			$('<input type="hidden" name="categories" value="list" />').appendTo($('#categories'));
			return true;
		} else return false;
	});

	pagenow = 'ecart_page_ecart-categories';
	columns.init(pagenow);

});
</script>