<?php
function save_meta_box ($Promotion) {
?>

<div id="misc-publishing-actions">
	<div class="misc-pub-section misc-pub-section-last">

	<label for="discount-status"><input type="hidden" name="status" value="disabled" /><input type="checkbox" name="status" id="discount-status" value="enabled"<?php echo ($Promotion->status == "enabled")?' checked="checked"':''; ?> /> &nbsp;<?php _e('Coupon Enabled ?','Ecart'); ?></label>
	</div>

	<div class="misc-pub-section misc-pub-section-last">

	<div id="start-position" class="calendar-wrap"><?php
		$dateorder = date_format_order(true);
		foreach ($dateorder as $type => $format):
			if ($previous == "s" && $type[0] == "s") continue;
	 		if ("month" == $type): ?><input type="text" name="starts[month]" id="starts-month" title="<?php _e('Month','Ecart'); ?>" size="3" maxlength="2" value="<?php echo ($Promotion->starts>1)?date("n",$Promotion->starts):''; ?>" class="selectall" /><?php elseif ("day" == $type): ?><input type="text" name="starts[date]" id="starts-date" title="<?php _e('Day','Ecart'); ?>" size="3" maxlength="2" value="<?php echo ($Promotion->starts>1)?date("j",$Promotion->starts):''; ?>" class="selectall" /><?php elseif ("year" == $type): ?><input type="text" name="starts[year]" id="starts-year" title="<?php _e('Year','Ecart'); ?>" size="5" maxlength="4" value="<?php echo ($Promotion->starts>1)?date("Y",$Promotion->starts):''; ?>" class="selectall" /><?php elseif ($type[0] == "s"): echo "/"; endif; $previous = $type[0];  endforeach; ?></div>
	<p><?php _e('Start promotion on this date.','Ecart'); ?></p>

	<div id="end-position" class="calendar-wrap"><?php
		foreach ($dateorder as $type => $format):
			if ($previous == "s" && $type[0] == "s") continue;
			if ("month" == $type): ?><input type="text" name="ends[month]" id="ends-month" title="<?php _e('Month','Ecart'); ?>" size="3" maxlength="2" value="<?php echo ($Promotion->ends>1)?date("n",$Promotion->ends):''; ?>" class="selectall" /><?php elseif ("day" == $type): ?><input type="text" name="ends[date]" id="ends-date" title="<?php _e('Day','Ecart'); ?>" size="3" maxlength="2" value="<?php echo ($Promotion->ends>1)?date("j",$Promotion->ends):''; ?>" class="selectall" /><?php elseif ("year" == $type): ?><input type="text" name="ends[year]" id="ends-year" title="<?php _e('Year','Ecart'); ?>" size="5" maxlength="4" value="<?php echo ($Promotion->ends>1)?date("Y",$Promotion->ends):''; ?>" class="selectall" /><?php elseif ($type[0] == "s"): echo "/"; endif; $previous = $type[0];  endforeach; ?></div>
	<p><?php _e('End the promotion on this date.','Ecart'); ?></p>

	</div>

</div>

<div id="major-publishing-actions">
	<input type="submit" class="button-primary" name="save" value="<?php _e('Save Coupon','Ecart'); ?>" />
</div>
<?php
}
add_meta_box('save-promotion', __('Save','Ecart').$Admin->boxhelp('promo-editor-save'), 'save_meta_box', 'ecart_page_ecart-promotions', 'side', 'core');

function discount_meta_box ($Promotion) {
	$types = array(
		'Percentage Off' => __('Percentage Off','Ecart'),
		'Amount Off' => __('Amount Off','Ecart')
	);

?>
<p><span>
<select name="type" id="discount-type">
	<?php echo menuoptions($types,$Promotion->type,true); ?>
</select></span>
<span id="discount-row">
	&mdash;
	<input type="text" name="discount" id="discount-amount" value="<?php echo $Promotion->discount; ?>" size="10" class="selectall" />
</span>
<span id="beyget-row">
	&mdash;
	&nbsp;<?php _e('Buy','Ecart'); ?> <input type="text" name="buyqty" id="buy-x" value="<?php echo $Promotion->buyqty; ?>" size="5" class="selectall" /> <?php _e('Get','Ecart'); ?> <input type="text" name="getqty" id="get-y" value="<?php echo $Promotion->getqty; ?>" size="5" class="selectall" />
</span></p>
<p><?php _e('Select the discount type and amount.','Ecart'); ?></p>

<?php
}
add_meta_box('promotion-discount', __('Coupon Discount Mode','Ecart').$Admin->boxhelp('promo-editor-discount'), 'discount_meta_box', 'ecart_page_ecart-promotions', 'normal', 'core');

function rules_meta_box ($Promotion) {
	$targets = array(
		'Cart' => __('shopping cart','Ecart'),

	);

	$target = '<select name="target" id="promotion-target" class="small">';
	$target .= menuoptions($targets,$Promotion->target,true);
	$target .= '</select>';

	if (empty($Promotion->search)) $Promotion->search = "all";

	$logic = '<select name="search" class="small">';
	$logic .= menuoptions(array('any'=>__('any','Ecart'),'all' => __('all','Ecart')),$Promotion->search,true);
	$logic .= '</select>';

?>

<div style="display:none;"><p><strong><?php printf(__('Apply discount to %s','Ecart'),$target,$logic); ?> <strong id="target-property"></strong></strong></p></div>
<table class="form-table" id="cartitem"></table>

<p><strong><?php printf(__('Apply this coupon when %s of these conditions match the','Ecart'),$logic); ?> <strong id="rule-target">:</strong></strong></p>

<table class="form-table" id="rules"></table>
<?php
}
add_meta_box('promotion-rules', __('Conditions','Ecart').$Admin->boxhelp('promo-editor-conditions'), 'rules_meta_box', 'ecart_page_ecart-promotions', 'normal', 'core');

?>