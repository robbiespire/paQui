<div class="wrap ecart">
	<div class="icon32"></div>
	<h2><?php _e('Store Registered Customers','Ecart'); ?></h2>

	<form action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" id="orders-list" method="get">
	<div>
		<input type="hidden" name="page" value="<?php echo $page; ?>" />
		<input type="hidden" name="status" value="<?php echo $status; ?>" />
	</div>

	<br class="clear" />
	<p id="post-search" class="search-box" style="display:none !important;">
		<input type="text" id="customers-search-input" class="search-input" name="s" value="<?php echo esc_attr($s); ?>" />
		<input type="submit" value="<?php _e('Search','Ecart'); ?>" class="button" />
	</p>

	<div class="tablenav">
		<div class="alignleft actions inline">
		<?php if(current_user_can('ecart_delete_customers')): ?><button type="submit" id="delete-button" name="deleting" value="customer" class="button-secondary"><?php _e('Delete','Ecart'); ?></button><?php endif; ?>
			<div class="filtering" style="display:none !important;">
				<select name="range" id="range">
					<?php echo menuoptions($ranges,$range,true); ?>
				</select>
				<div id="dates">
					<div id="start-position" class="calendar-wrap"><input type="text" id="start" name="start" value="<?php echo $startdate; ?>" size="10" class="search-input selectall" /></div>
					<small><?php _e('to','Ecart'); ?></small>
					<div id="end-position" class="calendar-wrap"><input type="text" id="end" name="end" value="<?php echo $enddate; ?>" size="10" class="search-input selectall" /></div>
				</div>
				<button type="submit" id="filter-button" name="filter" value="customers" class="button-secondary"><?php _e('Filter','Ecart'); ?></button>
			</div>
			</div>
			<?php if ($page_links) echo "<div class='tablenav-pages'>$page_links</div>"; ?>
		<div class="clear"></div>
	</div>
	<div class="clear"></div>

	<table class="widefat" cellspacing="0">
		<thead>
		<tr><?php print_column_headers('ecart_page_ecart-customers'); ?></tr>
		</thead>
		<tfoot>
		<tr><?php print_column_headers('ecart_page_ecart-customers',false); ?></tr>
		</tfoot>
	<?php if (sizeof($Customers) > 0): ?>
		<tbody id="customers-table" class="list orders">
		<?php
			$hidden = get_hidden_columns('ecart_page_ecart-customers');

			$even = false;
			foreach ($Customers as $Customer):
			$CustomerName = (empty($Customer->firstname) && empty($Customer->lastname))?'('.__('no contact name','Ecart').')':"{$Customer->firstname} {$Customer->lastname}";
			?>
		<tr<?php if (!$even) echo " class='alternate'"; $even = !$even; ?>>
			<th scope='row' class='check-column'><input type='checkbox' name='selected[]' value='<?php echo $Customer->id; ?>' /></th>
			<td class="name column-name"><a class='row-title' href='<?php echo esc_url( add_query_arg(array('page'=>'ecart-customers','id'=>$Customer->id),admin_url('admin.php'))); ?>' title='<?php _e('Edit','Ecart'); ?> &quot;<?php echo esc_attr($CustomerName); ?>&quot;'><?php echo esc_html($CustomerName); ?></a><?php echo !empty($Customer->company)?"<br />".esc_html($Customer->company):""; ?></td>
			<td class="login column-login<?php echo in_array('login',$hidden)?' hidden':''; ?>"><?php echo esc_html($Customer->user_login); ?></td>
			<td class="email column-email<?php echo in_array('email',$hidden)?' hidden':''; ?>"><a href="mailto:<?php echo esc_attr($Customer->email); ?>"><?php echo esc_html($Customer->email); ?></a></td>

			<td class="location column-location<?php echo in_array('location',$hidden)?' hidden':''; ?>"><?php
				$location = '';
				$location = $Customer->city;
				if (!empty($location) && !empty($Customer->state)) $location .= ', ';
				$location .= $Customer->state;
				if (!empty($location) && !empty($Customer->country))
					$location .= ' &mdash; ';
				$location .= $Customer->country;
				echo esc_html($location);
				 ?></td>
			<td class="total column-total<?php echo in_array('total',$hidden)?' hidden':''; ?>"><a href="<?php echo esc_url( add_query_arg(array('page'=>'ecart-orders','customer'=>$Customer->id),admin_url('admin.php'))); ?>"><?php echo $Customer->orders; ?> &mdash; <?php echo money($Customer->total); ?></a></td>
			<td class="date column-date<?php echo in_array('date',$hidden)?' hidden':''; ?>"><?php echo date("Y/m/d",mktimestamp($Customer->created)); ?></td>
		</tr>
		<?php endforeach; ?>
		</tbody>
	<?php else: ?>
		<tbody><tr><td colspan="6"><?php _e('No','Ecart'); ?> <?php _e('customers, yet.','Ecart'); ?></td></tr></tbody>
	<?php endif; ?>
	</table>

	</form>
	<div class="tablenav" style="display:none !important;">
		<?php if(current_user_can('ecart_export_customers')): ?>
		<div class="alignleft actions">
			<form action="<?php echo esc_url(add_query_arg(array_merge($_GET,array('src'=>'export_customers')),admin_url("admin.php"))); ?>" id="log" method="post">
			<button type="button" id="export-settings-button" name="export-settings" class="button-secondary"><?php _e('Export Options','Ecart'); ?></button>
			<div id="export-settings" class="hidden">
			<div id="export-columns" class="multiple-select">
				<ul>
					<li<?php $even = true; if ($even) echo ' class="odd"'; $even = !$even; ?>><input type="checkbox" name="selectall_columns" id="selectall_columns" /><label for="selectall_columns"><strong><?php _e('Select All','Ecart'); ?></strong></label></li>
					<li<?php if ($even) echo ' class="odd"'; $even = !$even; ?>><input type="hidden" name="settings[customerexport_headers]" value="off" /><input type="checkbox" name="settings[customerexport_headers]" id="purchaselog_headers" value="on" /><label for="purchaselog_headers"><strong><?php _e('Include column headings','Ecart'); ?></strong></label></li>

					<?php $even = true; foreach ($columns as $name => $label): ?>
						<li<?php if ($even) echo ' class="odd"'; $even = !$even; ?>><input type="checkbox" name="settings[customerexport_columns][]" value="<?php echo $name; ?>" id="column-<?php echo $name; ?>" <?php echo in_array($name,$selected)?' checked="checked"':''; ?> /><label for="column-<?php echo $name; ?>" ><?php echo $label; ?></label></li>
					<?php endforeach; ?>

				</ul>
			</div><br />
			<select name="settings[customerexport_format]">
				<?php echo menuoptions($exports,$formatPref,true); ?>
			</select></div>
			<button type="submit" id="download-button" name="download" value="export" class="button-secondary"><?php _e('Download','Ecart'); ?></button>
			</form>
		</div>
		<?php endif; ?>
		<?php if ($page_links) echo "<div class='tablenav-pages'>$page_links</div>"; ?>
		<div class="clear"></div>
	</div>
</div>

<div id="start-calendar" class="calendar"></div>
<div id="end-calendar" class="calendar"></div>

<script type="text/javascript">
var lastexport = new Date(<?php echo date("Y,(n-1),j",$Ecart->Settings->get('customerexport_lastexport')); ?>);

jQuery(document).ready( function() {
	var $=jqnc();

$('#selectall').change( function() {
	$('#customers-table th input').each( function () {
		if (this.checked) this.checked = false;
		else this.checked = true;
	});
});

$('#delete-button').click(function() {
	if (confirm("<?php echo addslashes(__('Are you sure you want to delete the selected customers?','Ecart')); ?>")) return true;
	else return false;
});

function formatDate (e) {
	if (this.value == "") match = false;
	if (this.value.match(/^(\d{6,8})/))
		match = this.value.match(/(\d{1,2}?)(\d{1,2})(\d{4,4})$/);
	else if (this.value.match(/^(\d{1,2}.{1}\d{1,2}.{1}\d{4})/))
		match = this.value.match(/^(\d{1,2}).{1}(\d{1,2}).{1}(\d{4})/);
	if (match) {
		date = new Date(match[3],(match[1]-1),match[2]);
		$(this).val((date.getMonth()+1)+"/"+date.getDate()+"/"+date.getFullYear());
		range.val('custom');
	}
}

var range = $('#range'),
	start = $('#start').change(formatDate),
	StartCalendar = $('#start-calendar').PopupCalendar({
		scheduling:false,
		input:start
	}).bind('calendarSelect',function () {
		range.val('custom');
	}),
	end = $('#end').change(formatDate),
	EndCalendar = $('#end-calendar').PopupCalendar({
		scheduling:true,
		input:end,
		scheduleAfter:StartCalendar
	}).bind('calendarSelect',function () {
		range.val('custom');
	});

range.change(function () {
	if (this.selectedIndex == 0) {
		start.val(''); end.val('');
		$('#dates').addClass('hidden');
		return;
	} else $('#dates').removeClass('hidden');
	var today = new Date(),
		startdate = new Date(today.getFullYear(),today.getMonth(),today.getDate()),
		enddate = new Date(today.getFullYear(),today.getMonth(),today.getDate());
	today = new Date(today.getFullYear(),today.getMonth(),today.getDate());

	switch($(this).val()) {
		case 'week':
			startdate.setDate(today.getDate()-today.getDay());
			enddate = new Date(startdate.getFullYear(),startdate.getMonth(),startdate.getDate()+6);
			break;
		case 'month':
			startdate.setDate(1);
			enddate = new Date(startdate.getFullYear(),startdate.getMonth()+1,0);
			break;
		case 'quarter':
			quarter = Math.floor(today.getMonth()/3);
			startdate = new Date(today.getFullYear(),today.getMonth()-(today.getMonth()%3),1);
			enddate = new Date(today.getFullYear(),startdate.getMonth()+3,0);
			break;
		case 'year':
			startdate = new Date(today.getFullYear(),0,1);
			enddate = new Date(today.getFullYear()+1,0,0);
			break;
		case 'yesterday':
			startdate.setDate(today.getDate()-1);
			enddate.setDate(today.getDate()-1);
			break;
		case 'lastweek':
			startdate.setDate(today.getDate()-today.getDay()-7);
			enddate.setDate((today.getDate()-today.getDay()+6)-7);
			break;
		case 'last30':
			startdate.setDate(today.getDate()-30);
			enddate.setDate(today.getDate());
			break;
		case 'last90':
			startdate.setDate(today.getDate()-90);
			enddate.setDate(today.getDate());
			break;
		case 'lastmonth':
			startdate = new Date(today.getFullYear(),today.getMonth()-1,1);
			enddate = new Date(today.getFullYear(),today.getMonth(),0);
			break;
		case 'lastquarter':
			startdate = new Date(today.getFullYear(),(today.getMonth()-(today.getMonth()%3))-3,1);
			enddate = new Date(today.getFullYear(),startdate.getMonth()+3,0);
			break;
		case 'lastyear':
			startdate = new Date(today.getFullYear()-1,0,1);
			enddate = new Date(today.getFullYear(),0,0);
			break;
		case 'lastexport':
			startdate = lastexport;
			enddate = today;
			break;
		case 'custom': return; break;
	}
	StartCalendar.select(startdate);
	EndCalendar.select(enddate);
}).change();

$('#export-settings-button').click(function () { $('#export-settings-button').hide(); $('#export-settings').removeClass('hidden'); });
$('#selectall_columns').change(function () {
	if ($(this).attr('checked')) $('#export-columns input').not(this).attr('checked',true);
	else $('#export-columns input').not(this).attr('checked',false);
});

pagenow = 'ecart_page_ecart-customers';
columns.init(pagenow);

});

</script>