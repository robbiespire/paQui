<div class="wrap ecart">

	<div class="icon32"></div>
	<h2><?php _e('Store Products Management','Ecart'); ?> <a href="<?php echo esc_url( add_query_arg(array('page'=>$this->Admin->pagename('products'),'id'=>'new'),admin_url('admin.php'))); ?>" class="button add-new"><?php _e('Add New Product','Ecart'); ?></a></h2>

	<?php if (!empty($Ecart->Flow->Notice)): ?><div id="message" class="updated fade"><p><?php echo $Ecart->Flow->Notice; ?></p></div><?php endif; ?>

	<form action="" method="get" id="products-manager">
	<?php include("navigation.php"); ?>

	<div>
		<input type="hidden" name="page" value="<?php echo $this->Admin->pagename('products'); ?>" />
	</div>

	<p id="post-search" class="search-box" style="display:none !important;">
		<input type="text" id="products-search-input" class="search-input" name="s" value="<?php echo stripslashes(esc_attr($s)); ?>" />
		<input type="submit" value="<?php _e('Search Products','Ecart'); ?>" class="button" />
	</p>


	<div class="tablenav">
		<?php if ($page_links) echo "<div class='tablenav-pages'>$page_links</div>"; ?>
		<div class="alignleft actions filters">
		<button type="submit" id="delete-button" name="deleting" value="product" class="button-secondary"><?php _e('Delete','Ecart'); ?></button>
		<select name="cat" class="filters">
		<?php echo $categories_menu; ?>
		</select>
		<select name="sl" class="filters" style="display:none !important;">
		<?php echo $inventory_menu; ?>
		</select>
		<input type="submit" id="filter-button" value="<?php _e('Filter','Ecart'); ?>" class="button-secondary" />
		</div>
		<div class="clear"></div>
	</div>
	<div class="clear"></div>

	<table class="widefat" cellspacing="0">
		<thead>
		<tr><?php print_column_headers('ecart_page_ecart-products'); ?><th scope="col" id="id" class="manage-column column-id" style="">Product ID</th></tr>
		</thead>
		<tfoot>
		<tr><?php print_column_headers('ecart_page_ecart-products',false); ?><th scope="col" id="id" class="manage-column column-id" style="">Product ID</th></tr>
		</tfoot>
	<?php if (sizeof($Products) > 0): ?>
		<tbody id="products" class="list products">
		<?php
		$hidden = get_hidden_columns('ecart_page_ecart-products');

		$even = false;
		foreach ($Products as $key => $Product):
		$editurl = esc_url(add_query_arg(array_merge(stripslashes_deep($_GET),
			array('page'=>'ecart-products',
					'id'=>$Product->id)),
					admin_url('admin.php')));

		$delurl = esc_url(esc_attr(add_query_arg(array_merge(stripslashes_deep($_GET),
			array('page'=>'ecart-products',
					'delete[]'=>$Product->id,
					'deleting'=>'product')),
					admin_url('admin.php'))));

		$dupurl = esc_url(esc_attr(add_query_arg(array_merge(stripslashes_deep($_GET),
			array('page'=>'ecart-products',
					'duplicate'=>$Product->id)),
					admin_url('admin.php'))));


		$ProductName = empty($Product->name)?'('.__('no product name','Ecart').')':$Product->name;
		?>
		<tr<?php if (!$even) echo " class='alternate'"; $even = !$even; ?>>
			<th scope='row' class='check-column'><input type='checkbox' name='delete[]' value='<?php echo $Product->id; ?>' /></th>
			<td class="name column-name"><a class='row-title' href='<?php echo $editurl; ?>' title='<?php _e('Edit','Ecart'); ?> &quot;<?php echo esc_attr($ProductName); ?>&quot;'><?php echo esc_html($ProductName); ?></a>
				<div class="row-actions">
					<span class='edit'><a href="<?php echo $editurl; ?>" title="<?php _e('Edit','Ecart'); ?> &quot;<?php echo esc_attr($ProductName); ?>&quot;"><?php _e('Edit','Ecart'); ?></a> | </span>
					<span class='edit'><a href="<?php echo $dupurl; ?>" title="<?php _e('Duplicate','Ecart'); ?> &quot;<?php echo esc_attr($ProductName); ?>&quot;"><?php _e('Duplicate','Ecart'); ?></a> | </span>
					<span class='delete'><a class="submitdelete" title="<?php _e('Delete','Ecart'); ?> &quot;<?php echo esc_attr($ProductName); ?>&quot;" href="<?php echo $delurl; ?>" rel="<?php echo $Product->id; ?>"><?php _e('Delete','Ecart'); ?></a> | </span>
					<span class='view'><a href="<?php echo ecarturl(ECART_PRETTYURLS?$Product->slug:array('ecart_pid'=>$Product->id)); ?>" title="<?php _e('View','Ecart'); ?> &quot;<?php echo esc_attr($ProductName); ?>&quot;" rel="permalink" target="_blank"><?php _e('View','Ecart'); ?></a></span>
				</div>
				</td>
			<td class="category column-category<?php echo in_array('category',$hidden)?' hidden':''; ?>"><?php echo esc_html($Product->categories); ?></td>
			<td class="price column-price<?php echo in_array('price',$hidden)?' hidden':''; ?>"><?php
				if ($Product->variations == "off") echo money($Product->mainprice);
				elseif ($Product->maxprice == $Product->minprice) echo money($Product->maxprice);
				else echo money($Product->minprice)."&mdash;".money($Product->maxprice);
			?></td>
			<td class="inventory column-inventory<?php echo in_array('inventory',$hidden)?' hidden':''; ?>"><?php if ($Product->inventory == "on") echo $Product->stock; ?></td>
			<td class="featured column-featured<?php echo in_array('featured',$hidden)?' hidden':''; ?>"><button type="button" name="feature" value="<?php echo $Product->id; ?>" class="<?php echo ($Product->featured == "on")?' feature featured':'feature'; ?>">&nbsp;</button></td>
			<td class="col-id column-id"><?php echo $Product->id; ?></td>

		</tr>
		<?php endforeach; ?>
		</tbody>
	<?php else: ?>
		<tbody><tr><td colspan="6"><?php _e('No products found.','Ecart'); ?></td></tr></tbody>
	<?php endif; ?>
	</table>
	</form>
	<div class="tablenav">
		<?php if ($page_links) echo "<div class='tablenav-pages'>$page_links</div>"; ?>
		<div class="clear"></div>
	</div>
</div>

<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready( function() {
	var $=jqnc(),
		featureurl = '<?php echo wp_nonce_url(admin_url('admin-ajax.php'),'wp_ajax_ecart_feature_product'); ?>';

	$('#selectall').change( function() {
		$('#products th input').each( function () {
			if (this.checked) this.checked = false;
			else this.checked = true;
		});
	});

	$('a.submitdelete').click(function () {
		var name = $(this).attr('title');
		if ( confirm(<?php _jse('You are about to delete this product!\n \'Cancel\' to stop, \'OK\' to delete.','Ecart'); ?>)) {
			$('<input type="hidden" name="delete[]" />').val($(this).attr('rel')).appendTo('#products-manager');
			$('<input type="hidden" name="deleting" />').val('product').appendTo('#products-manager');
			$('#products-manager').submit();
			return false;
		} else return false;
	});

	$('#delete-button').click(function() {
		if (confirm("<?php echo addslashes(__('Are you sure you want to delete the selected products?','Ecart')); ?>")) return true;
		else return false;
	});

	$('button.feature').click(function () {
		var $this = $(this);
		$.get(featureurl,{'feature':$this.val(),'action':'ecart_feature_product'},function (result) {
			if (result == "on") $this.addClass('featured');
			else $this.removeClass('featured');
		});
	});

	pagenow = 'ecart_page_ecart-products';
	columns.init(pagenow);

});
/* ]]> */
</script>