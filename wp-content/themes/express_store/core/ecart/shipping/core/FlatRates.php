<?php
/**
 * Flat Rates
 *
 * Provides flat rate shipping calculations
 *
 * @version 1.1
 * @package ecart
 * @since 1.1 dev
 * @subpackage FlatRates
 *
 * $Id: FlatRates.php 1613 2010-12-30 16:48:55Z jdillick $
 **/

class FlatRates extends ShippingFramework implements ShippingModule {

	function methods () {
		return array('order' => __("Flat Rate on order","Ecart"),
					 'item' => __("Flat Rate per item","Ecart"));
	}

	function init () {
		$rate['items'] = array();
	}

	function calculate ($options,$Order) {
		foreach ($this->rates as $rate) {
			$column = $this->ratecolumn($rate);
			list($module,$method) = explode("::",$rate['method']);

			if ($method == 'item')
				$rate['amount'] = array_sum($rate['items']);

			if ($method == 'order' && isset($rate[$column][0])) {
				$rate['amount'] = $rate[$column][0];
			}

			if (isset($rate['name']) && isset($rate['amount']))
				$options[$rate['name']] = new ShippingOption($rate);

		}

		return $options;
	}

	function calcitem ($id,$Item) {
		foreach ($this->rates as &$rate) {
			list($module,$method) = explode("::",$rate['method']);
			if ($method != 'item') continue; // Only work on item shipping methods
			$column = $this->ratecolumn($rate);
			if (!isset($rate[$column][0])) continue;
			$rate['items'][$id] = $Item->quantity * $rate[$column][0];
		}
	}

	function ui () {
		?>
var FlatRates = function (methodid,table,rates) {
	table.empty();
	var headingsRow = $('<tr class="headings"/>').appendTo(table);

	$('<th scope="col">').appendTo(headingsRow);

	var domesticHeading = new Array();
	$.each(domesticAreas,function(key,area) {
		domesticHeading[key] = $('<th scope="col"><label for="'+area+'['+methodid+']">'+area+'</label></th>').appendTo(headingsRow);
	});
	var regionHeading = $('<th scope="col"><label for="'+region+'['+methodid+']">'+region+'</label></th>').appendTo(headingsRow);
	var worldwideHeading = $('<th scope="col"><label for="worldwide['+methodid+']"><?php echo addslashes(__('Worldwide','Ecart')); ?></label></th>').appendTo(headingsRow);
	$('<th scope="col">').appendTo(headingsRow);

	var row = $('<tr/>').appendTo(table);

	$('<td/>').appendTo(row);

	$.each(domesticAreas,function(key,area) {
		var inputCell = $('<td/>').appendTo(row);
		if (!isNaN(key)) key = area;
		if (rates && rates[key] && rates[key][0]) var value = rates[key][0];
		else value = 0;
		$('<input type="text" name="settings[shipping_rates]['+methodid+']['+key+'][]" id="'+area+'['+methodid+']" class="selectall right" size="7" tabindex="'+(methodid+1)+'04" />').change(function() {
			this.value = asMoney(this.value);
		}).val(asMoney(new Number(value))).appendTo(inputCell);
	});

	var inputCell = $('<td/>').appendTo(row);
	if (rates && rates[region] && rates[region][0]) value = rates[region][0];
	else value = 0;
	$('<input type="text" name="settings[shipping_rates]['+methodid+']['+region+'][]"  id="'+region+'['+methodid+']" class="selectall right" size="7" tabindex="'+(methodid+1)+'05" />').change(function() {
		this.value = asMoney(this.value);
	}).val(asMoney(new Number(value))).appendTo(inputCell);

	var inputCell = $('<td/>').appendTo(row);
	if (rates && rates['Worldwide'] && rates['Worldwide'][0]) value = rates['Worldwide'][0];
	else value = 0;
	intlInput = $('<input type="text" name="settings[shipping_rates]['+methodid+'][Worldwide][]" id="worldwide['+methodid+']" class="selectall right" size="7" tabindex="'+(methodid+1)+'06" />').change(function() {
		this.value = asMoney(this.value);
	}).val(asMoney(new Number(value))).appendTo(inputCell);

	$('<td/>').appendTo(row);

	quickSelects();
}

methodHandlers.register('<?php echo $this->module; ?>::order',FlatRates);
methodHandlers.register('<?php echo $this->module; ?>::item',FlatRates);

		<?php
	}

} // END class FlatRates

?>