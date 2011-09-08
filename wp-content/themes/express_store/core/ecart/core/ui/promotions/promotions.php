<div class="wrap ecart">

	<div class="icon32"></div>
	<h2><?php _e('Shopping Cart Coupons','Ecart'); ?> <a href="<?php echo esc_url(add_query_arg(array_merge($_GET,array('page'=>'ecart-promotions','id'=>'new')),admin_url('admin.php'))); ?>" class="button add-new"><?php _e('Add New Coupon','Ecart'); ?></a></h2>

	<form action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" id="promotions" method="get">
	<div>
		<input type="hidden" name="page" value="ecart-promotions" />
	</div>

	<p id="post-search" class="search-box" style="display:none;">
		<input type="text" id="promotions-search-input" name="s" class="search-input" value="<?php echo esc_attr($s); ?>" />
		<input type="submit" value="<?php _e('Search Promotions','Ecart'); ?>" class="button" />
	</p>

	<div class="tablenav">
		<?php if ($page_links) echo "<div class='tablenav-pages'>$page_links</div>"; ?>
		<div class="alignleft actions"><button type="submit" id="delete-button" name="deleting" value="promotion" class="button-secondary"><?php _e('Delete','Ecart'); ?></button></div>
		<div class="clear"></div>
	</div>
	<div class="clear"></div>

	<table class="widefat" cellspacing="0">
		<thead>
		<tr><?php print_column_headers('ecart_page_ecart-promotions'); ?></tr>
		</thead>
		<tfoot>
		<tr><?php print_column_headers('ecart_page_ecart-promotions',false); ?></tr>
		</tfoot>
	<?php if (sizeof($Promotions) > 0): ?>
		<tbody class="list promotions">
		<?php
			$hidden = get_hidden_columns('ecart_page_ecart-promotions');

			$even = false;
			foreach ($Promotions as $Promotion):
			$editurl = add_query_arg(array_merge($_GET,array('page'=>'ecart-promotions','id'=>$Promotion->id)),admin_url('admin.php'));
			$deleteurl = add_query_arg(array_merge($_GET,array('page'=>'ecart-promotions','delete[]'=>$Promotion->id,'deleting'=>'promotion')),admin_url('admin.php'));
			$PromotionName = empty($Promotion->name)?'('.__('no promotion name').')':$Promotion->name;
		?>
		<tr<?php if (!$even) echo " class='alternate'"; $even = !$even; ?>>
			<th scope='row' class='check-column'><input type='checkbox' name='delete[]' value='<?php echo $Promotion->id; ?>' /></th>
			<td width="33%" class="name column-name"><a class='row-title' href='<?php echo esc_url($editurl); ?>' title='<?php _e('Edit','Ecart'); ?> &quot;<?php echo esc_attr($PromotionName); ?>&quot;'><?php echo esc_html($PromotionName); ?></a>
				<div class="row-actions">
					<span class='edit'><a href="<?php echo esc_url($editurl); ?>" title="<?php _e('Edit','Ecart'); ?> &quot;<?php echo esc_attr($PromotionName); ?>&quot;"><?php _e('Edit','Ecart'); ?></a> | </span>
					<span class='delete'><a class='submitdelete' title='<?php _e('Delete','Ecart'); ?> &quot;<?php echo esc_attr($PromotionName); ?>&quot;' href="<?php echo esc_url($deleteurl); ?>" rel="<?php echo $Promotion->id; ?>"><?php _e('Delete','Ecart'); ?></a></span>
				</div>

			</td>
			<td class="discount column-discount<?php echo in_array('discount',$hidden)?' hidden':''; ?>"><?php
				if ($Promotion->type == "Percentage Off") echo percentage($Promotion->discount);
				if ($Promotion->type == "Amount Off") echo money($Promotion->discount);
				if ($Promotion->type == "Free Shipping") echo $this->Settings->get("free_shipping_text");
				if ($Promotion->type == "Buy X Get Y Free") echo __('Buy','Ecart').' '.$Promotion->buyqty.' '.__('Get','Ecart').' '.$Promotion->getqty.' '.__('Free','Ecart');
			?></td>
			<td class="applied column-applied<?php echo in_array('applied',$hidden)?' hidden':''; ?>"><?php echo $Promotion->target; ?></td>
			<td class="eff column-eff<?php echo in_array('eff',$hidden)?' hidden':''; ?>"><strong><?php echo $status[$Promotion->status]; ?></strong><?php
				$starts = (mktimestamp($Promotion->starts) > 1) ?
				                 _d(get_option('date_format'),mktimestamp($Promotion->starts)) :
				                 _d(get_option('date_format'),mktimestamp($Promotion->created));
				$ends = (mktimestamp($Promotion->ends) > 1) ?
				               " — " . _d(get_option('date_format'),mktimestamp($Promotion->ends)) :
				               ", " . __('does not expire','Ecart');
				echo "<br />".$starts.$ends;
			?></td>
		</tr>
		<?php endforeach; ?>
		</tbody>
	<?php else: ?>
		<tbody><tr><td colspan="5"><?php _e('No promotions found.','Ecart'); ?></td></tr></tbody>
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
	var $=jQuery.noConflict();

	$('#selectall').change( function() {
		$('#promotions th input').each( function () {
			if (this.checked) this.checked = false;
			else this.checked = true;
		});
	});

	$('a.submitdelete').click(function () {
		if (confirm("<?php _e('You are about to delete this promotion!\n \'Cancel\' to stop, \'OK\' to delete.','Ecart'); ?>")) {
			$('<input type="hidden" name="delete[]" />').val($(this).attr('rel')).appendTo('#promotions');
			$('<input type="hidden" name="deleting" />').val('promotion').appendTo('#promotions');
			$('#promotions').submit();
			return false;
		} else return false;
	});

	$('#delete-button').click(function() {
		if (confirm("<?php echo addslashes(__('Are you sure you want to delete the selected promotions?','Ecart')); ?>")) {
			$('<input type="hidden" name="promotions" value="list" />').appendTo($('#promotions'));
			return true;
		} else return false;
	});

	pagenow = 'ecart_page_ecart-promotions';
	columns.init(pagenow);

});

</script>