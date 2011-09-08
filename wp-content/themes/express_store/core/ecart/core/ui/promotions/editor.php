	<div class="wrap ecart">
		<?php if (!empty($this->Notice)): ?><div id="message" class="updated fade"><p><?php echo $this->Notice; ?></p></div><?php endif; ?>

		<div class="icon32"></div>
		<h2><?php _e('Add or Edit Coupon','Ecart'); ?></h2>

		<div id="ajax-response"></div>
		<form name="promotion" id="promotion" action="<?php echo add_query_arg('page','ecart-promotions',admin_url('admin.php')); ?>" method="post">
			<?php wp_nonce_field('ecart-save-promotion'); ?>

			<div class="hidden"><input type="hidden" name="id" value="<?php echo $Promotion->id; ?>" /></div>

			<div id="poststuff" class="metabox-holder has-right-sidebar">

				<div id="side-info-column" class="inner-sidebar">
				<?php
				do_action('submitpage_box');
				$side_meta_boxes = do_meta_boxes('ecart_page_ecart-promotions', 'side', $Promotion);
				?>
				</div>

				<div id="post-body" class="<?php echo $side_meta_boxes ? 'has-sidebar' : 'has-sidebar'; ?>">
				<div id="post-body-content" class="has-sidebar-content">

					<div id="titlediv">
						<div id="titlewrap">
							<small><?php _e('Coupon Code');?></small>
							<input name="name" id="title" type="text" value="<?php echo esc_attr($Promotion->name); ?>" size="30" tabindex="1" autocomplete="off" />
						</div>
					</div>

				<?php
				do_meta_boxes('ecart_page_ecart-promotions', 'normal', $Promotion);
				do_meta_boxes('ecart_page_ecart-promotions', 'advanced', $Promotion);
				wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
				?>

				</div>
				</div>

			</div> <!-- #poststuff -->
		</form>
	</div>

<div id="starts-calendar" class="calendar"></div>
<div id="ends-calendar" class="calendar"></div>

<script type="text/javascript">

jQuery(document).ready( function() {
var $=jqnc(),
	currencyFormat = <?php $base = $this->Settings->get('base_operations'); echo json_encode($base['currency']['format']); ?>,
	rules = <?php echo json_encode($Promotion->rules); ?>,
	ruleidx = 1,
	itemidx = 1,
	promotion = <?php echo (!empty($Promotion->id))?$Promotion->id:'false'; ?>,
	loading = true,
	SCOPEPROP_LANG = {
		"Catalog":<?php _jse('price','Ecart'); ?>,
		"Cart":<?php _jse('subtotal','Ecart'); ?>,
		"Cart Item":<?php _jse('total, where:','Ecart'); ?>
	},
	TARGET_LANG = {
		"Catalog":<?php _jse('product','Ecart'); ?>,
		"Cart":<?php _jse('cart','Ecart'); ?>,
		"Cart Item":<?php _jse('cart','Ecart'); ?>
	},
	RULES_LANG = {
		"Name":<?php _jse('Name','Ecart'); ?>,
		"Category":<?php _jse('Category','Ecart'); ?>,
		"Variation":<?php _jse('Variation','Ecart'); ?>,
		"Price":<?php _jse('Price','Ecart'); ?>,
		"Sale price":<?php _jse('Sale price','Ecart'); ?>,
		"Type":<?php _jse('Type','Ecart'); ?>,
		"In stock":<?php _jse('In stock','Ecart'); ?>,

		"Tag name":<?php _jse('Tag name','Ecart'); ?>,
		"Unit price":<?php _jse('Unit price','Ecart'); ?>,
		"Total price":<?php _jse('Total price','Ecart'); ?>,
		"Input name":<?php _jse('Input name','Ecart'); ?>,
		"Input value":<?php _jse('Input value','Ecart'); ?>,
		"Quantity":<?php _jse('Quantity','Ecart'); ?>,

		"Any item name":<?php _jse('Any item name','Ecart'); ?>,
		"Any item amount":<?php _jse('Any item amount','Ecart'); ?>,
		"Any item quantity":<?php _jse('Any item quantity','Ecart'); ?>,
		"Total quantity":<?php _jse('Total quantity','Ecart'); ?>,
		"Shipping amount":<?php _jse('Shipping amount','Ecart'); ?>,
		"Subtotal amount":<?php _jse('Subtotal amount','Ecart'); ?>,
		"Discount amount":<?php _jse('Discount amount','Ecart'); ?>,

		"Customer type":<?php _jse('Customer type','Ecart'); ?>,
		"Ship-to country":<?php _jse('Ship-to country','Ecart'); ?>,

		"Promo code":<?php _jse('Promo code','Ecart'); ?>,
		"Promo use count":<?php _jse('Promo use count','Ecart'); ?>,

		"Is equal to":<?php _jse('Is equal to','Ecart'); ?>,
		"Is not equal to":<?php _jse('Is not equal to','Ecart'); ?>,
		"Contains":<?php _jse('Contains','Ecart'); ?>,
		"Does not contain":<?php _jse('Does not contain','Ecart'); ?>,
		"Begins with":<?php _jse('Begins with','Ecart'); ?>,
		"Ends with":<?php _jse('Ends with','Ecart'); ?>,
		"Is greater than":<?php _jse('Is greater than','Ecart'); ?>,
		"Is greater than or equal to":<?php _jse('Is greater than or equal to','Ecart'); ?>,
		"Is less than":<?php _jse('Is less than','Ecart'); ?>,
		"Is less than or equal to":<?php _jse('Is less than or equal to','Ecart'); ?>

	},
	conditions = {
		"Catalog":{
			"Name":{"logic":["boolean","fuzzy"],"value":"text"},
			"Category":{"logic":["boolean","fuzzy"],"value":"text"},
			"Variation":{"logic":["boolean","fuzzy"],"value":"text"},
			"Price":{"logic":["boolean","amount"],"value":"price"},
			"Sale price":{"logic":["boolean","amount"],"value":"price"},
			"Type":{"logic":["boolean"],"value":"text"},
			"In stock":{"logic":["boolean","amount"],"value":"text"}
		},
		"Cart":{
			
			"Promo use count":{"logic":["boolean","amount"],"value":"text"},
			"Promo code":{"logic":["boolean"],"value":"text"}
		},
		"Cart Item":{
			"Any item name":{"logic":["boolean","fuzzy"],"value":"text"},
			"Any item quantity":{"logic":["boolean","amount"],"value":"text"},
			"Any item amount":{"logic":["boolean","amount"],"value":"price"},
			"Total quantity":{"logic":["boolean","amount"],"value":"text"},
			"Shipping amount":{"logic":["boolean","amount"],"value":"price"},
			"Subtotal amount":{"logic":["boolean","amount"],"value":"price"},
			"Discount amount":{"logic":["boolean","amount"],"value":"price"},
			"Customer type":{"logic":["boolean"],"value":"text"},
			"Ship-to country":{"logic":["boolean","fuzzy"],"value":"text"},
			"Promo use count":{"logic":["boolean","amount"],"value":"text"},
			"Promo code":{"logic":["boolean"],"value":"text"}
		},
		"Cart Item Target":{
			"Name":{"logic":["boolean","fuzzy"],"value":"text"},
			"Category":{"logic":["boolean","fuzzy"],"value":"text"},
			"Tag name":{"logic":["boolean","fuzzy"],"value":"text"},
			"Variation":{"logic":["boolean","fuzzy"],"value":"text"},
			"Input name":{"logic":["boolean","fuzzy"],"value":"text"},
			"Input value":{"logic":["boolean","fuzzy"],"value":"text"},
			"Quantity":{"logic":["boolean","amount"],"value":"text"},
			"Unit price":{"logic":["boolean","amount"],"value":"price"},
			"Total price":{"logic":["boolean","amount"],"value":"price"},
			"Discount amount":{"logic":["boolean","amount"],"value":"price"}
		}
	},
	logic = {
		"boolean":["Is equal to","Is not equal to"],
		"fuzzy":["Contains","Does not contain","Begins with","Ends with"],
		"amount":["Is greater than","Is greater than or equal to","Is less than","Is less than or equal to"]
	},
	Conditional = function (type,settings,location) {
		var target = $('#promotion-target').val(),
			row = false, i = false;

		if (!type) type = 'condition';

		if (type == "cartitem") {
			i = itemidx;
			if (!location) row = $('<tr />').appendTo('#cartitem');
			else row = $('<tr></tr>').insertAfter(location);
		} else {
			i = ruleidx;
			if (!location) row = $('<tr />').appendTo('#rules');
			else row = $('<tr></tr>').insertAfter(location);
		}

		var cell = $('<td></td>').appendTo(row);
		var deleteButton = $('<button type="button" class="delete"></button>').html('<img src="<?php echo ECART_PLUGINURI; ?>/core/ui/icons/delete.png" alt=<?php _jse('Delete','Ecart'); ?> width="16" height="16" />').appendTo(cell).click(function () { if (i > 1) $(row).remove(); }).attr('opacity',0);

		var properties_name = (type=='cartitem')?'rules[item]['+i+'][property]':'rules['+i+'][property]';
		var properties = $('<select name="'+properties_name+'" class="ruleprops"></select>').appendTo(cell);

		if (type == "cartitem") target = "Cart Item Target";
		if (conditions[target])
			for (var label in conditions[target])
				$('<option></option>').html(RULES_LANG[label]).val(label).attr('rel',target).appendTo(properties);

		var operation_name = (type=='cartitem')?'rules[item]['+i+'][logic]':'rules['+i+'][logic]';
		var operation = $('<select name="'+operation_name+'" ></select>').appendTo(cell);
		var value = $('<span></span>').appendTo(cell);

		var addspan = $('<span></span>').appendTo(cell);
		$('<button type="button" class="add"></button>').html('Add new rule').appendTo(addspan).click(function () { new Conditional(type,false,row); });

		cell.hover(function () {
			if (i > 1) deleteButton.css({'opacity':100,'visibility':'visible'});
		},function () {
			deleteButton.animate({'opacity':0});
		});

		var valuefield = function (fieldtype) {
			value.empty();
			var name = (type=='cartitem')?'rules[item]['+i+'][value]':'rules['+i+'][value]';
			field = $('<input type="text" name="'+name+'" class="selectall" />').appendTo(value);
			if (fieldtype == "price") field.change(function () { this.value = asMoney(this.value); });
		}

		// Generate logic operation menu
		properties.change(function () {
			operation.empty();
			if (!$(this).val()) this.selectedIndex = 0;
			var property = $(this).val();
			var c = false;
			if (conditions[$(this).find(':selected').attr('rel')]);
				c = conditions[$(this).find(':selected').attr('rel')][property];

			if (c['logic'].length > 0) {
				operation.show();
				for (var l = 0; l < c['logic'].length; l++) {
					var lop = c['logic'][l];
					if (!lop) break;
					for (var op = 0; op < logic[lop].length; op++)
						$('<option></option>').html(RULES_LANG[logic[lop][op]]).val(logic[lop][op]).appendTo(operation);
				}
			} else operation.hide();

			valuefield(c['value']);
		}).change();

		// Load up existing conditional rule
		if (settings) {
			properties.val(settings.property).change();
			operation.val(settings.logic);
			if (field) field.val(settings.value);
		}

		if (type == "cartitem") itemidx++;
		else ruleidx++;
	};

$('.postbox a.help').click(function () {
	$(this).colorbox({iframe:true,open:true,innerWidth:768,innerHeight:480,scrolling:false});
	return false;
});


$('#discount-type').change(function () {
	$('#discount-row').hide();
	$('#beyget-row').hide();
	var type = $(this).val();

	if (type == "Percentage Off" || type == "Amount Off") $('#discount-row').show();
	if (type == "Buy X Get Y Free") {
		$('#beyget-row').show();
		$('#promotion-target').val('Cart Item').change();
		$('#promotion-target option:lt(2)').attr('disabled',true);
	} else {
		$('#promotion-target option:lt(2)').attr('disabled',false);
	}

	$('#discount-amount').unbind('change').change(function () {
		var value = this.value;
		if (loading) {
			value = new Number(this.value);
			loading = !loading;
		}
		if (type == "Percentage Off") this.value = asPercent(value);
		if (type == "Amount Off") this.value = asMoney(value);
	}).change();

}).change();

$('#promotion-target').change(function () {
	var target = $(this).val();
	var menus = $('#rules select.ruleprops');
	$('#target-property').html(SCOPEPROP_LANG[target]);
	$('#rule-target').html(TARGET_LANG[target]);
	$(menus).empty().each(function (id,menu) {
		for (var label in conditions[target])
			$('<option></option>').html(RULES_LANG[label]).val(label).attr('rel',target).appendTo($(menu));
	});
	if (target == "Cart Item") {
		if (rules['item']) for (var r in rules['item']) new Conditional('cartitem',rules['item'][r]);
		else new Conditional('cartitem');
	} else $('#cartitem').empty();

}).change();


if (rules) {
	for (var r in rules) if (r != 'item') new Conditional('condition',rules[r]);
} else new Conditional();

$('#starts-calendar').PopupCalendar({
	m_input:$('#starts-month'),
	d_input:$('#starts-date'),
	y_input:$('#starts-year')
}).bind('show',function () {
	$('#ends-calendar').hide();
});

$('#ends-calendar').PopupCalendar({
	m_input:$('#ends-month'),
	d_input:$('#ends-date'),
	y_input:$('#ends-year')
}).bind('show',function () {
	$('#starts-calendar').hide();
});

postboxes.add_postbox_toggles('ecart_page_ecart-promotions');
// close postboxes that should be closed
$('.if-js-closed').removeClass('if-js-closed').addClass('closed');

if (!promotion) $('#title').focus();

});

</script>