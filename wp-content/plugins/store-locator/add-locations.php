<?php

print "
<div class='wrap'>
<h2>".__("Add Locations", $text_domain)."</h2><br>";

global $wpdb;
initialize_variables();

//Inserting addresses by manual input
if ($_POST[sl_store] && $_GET[mode]!="pca") {
	foreach ($_POST as $key=>$value) {
		if (ereg("sl_", $key)) {
			$fieldList.="$key,";
			$value=comma($value);
			$valueList.="\"".stripslashes($value)."\",";
		}
	}
	$fieldList=substr($fieldList, 0, strlen($fieldList)-1);
	$valueList=substr($valueList, 0, strlen($valueList)-1);
	//$wpdb->query("INSERT into ". $wpdb->prefix . "store_locator (sl_store, sl_address, sl_city, sl_state, sl_zip) VALUES ('$_POST[sl_store]', '$_POST[sl_address]', '$_POST[sl_city]', '$_POST[sl_state]', '$_POST[sl_zip]')");
	//print "INSERT into ". $wpdb->prefix . "store_locator ($fieldList) VALUES ($valueList)"; exit;
	$wpdb->query("INSERT into ". $wpdb->prefix . "store_locator ($fieldList) VALUES ($valueList)");
	$address="$_POST[sl_address], $_POST[sl_city], $_POST[sl_state] $_POST[sl_zip]";
	do_geocoding($address);
	print "<div class='updated fade'>".__("Successful Addition",$text_domain).". $view_link</div> <!--meta http-equiv='refresh' content='0'-->"; //header("location:$_SERVER[HTTP_REFERER]");
}

//Importing addresses from an local or remote database
if ($_POST[remote] && trim($_POST[query])!="" || $_POST[finish_import]) {
	
	if (ereg(".*\..{2,}", $_POST[server])) {
		include($sl_upload_path."/addons/db-importer/remoteConnect.php");
	}
	else {
		/*if (file_exists("addons/db-importer/localImport.php")) {
			include($sl_upload_path."/addons/db-importer/localImport.php");
		}
		else {*/
			include($sl_path."/localImport.php");
		//}
	}
	//for intermediate step match column data to field headers
	if ($_POST[finish_import]!="1") {exit();}
}

//Importing CSV file of addresses
$newfile="temp-file.csv"; 
$target_path="$root/";
$root=ABSPATH."wp-content/plugins/".dirname(plugin_basename(__FILE__));
//print_r($_FILES);
if (move_uploaded_file($_FILES['csv_import']['tmp_name'], "$root/$newfile") && file_exists($sl_upload_path."/addons/csv-xml-importer-exporter/csvImport.php")) {
	include($sl_upload_path."/addons/csv-xml-importer-exporter/csvImport.php");
}
else{
		//echo "<div style='background-color:salmon; padding:5px'>There was an error uploading the file, please try again. </div>";
}

//If adding via the Point, Click, Add map (accepting AJAX)
if ($_GET[mode]=="pca") {
	include($sl_upload_path."/addons/point-click-add/pcaImport.php");
}
	
$base=get_option('siteurl');

print <<<EOQ
<!--h2>Copy and Paste Addresses into Text Area:</h2>
<form  method=post>
<textarea rows='20' cols='100'></textarea><br>
<input type='submit'>
</form-->
EOQ;

print "
<table cellpadding='10px' cellspacing='0' style='width:100%' class='manual_add_table'><tr>
<td style='/*border-right:solid silver 1px;*/ padding-top:0px;' valign='top'>

<form name='manualAddForm' method=post>
	<table cellpadding='0' class='widefat'>
	<thead><tr><th>".__("Type&nbsp;Address", $text_domain)."</th></tr></thead>
	<tr>
		<td>
		<b>".__("The General Address Format", $text_domain).": </b>(<a href=\"#\" onclick=\"show('format'); return false;\">".__("show/hide", $text_domain)."</a>)
		<span id='format' style='display:none'><br><i>".__("Name of Location", $text_domain)."<br>
		".__("Address (Street - Line1)", $text_domain)."<br>
		".__("Address (Street - Line2 - optional)", $text_domain)."<br>
		".__("City, State Zip", $text_domain)."</i></span><br><hr>
		".__("Name of Location", $text_domain)."<br><input name='sl_store' size=40><br><br>
		".__("Address", $text_domain)."<br><input name='sl_address' size=21>&nbsp;<small>(".__("Street - Line1", $text_domain).")</small><br>
		<input name='sl_address2' size=21>&nbsp;<small>(".__("Street - Line 2 - optional", $text_domain).")</small><br>
		<table cellpadding='0px' cellspacing='0px'><tr><td style='padding-left:0px' class='nobottom'><input name='sl_city' size='21'><br><small>".__("City", $text_domain)."</small></td>
		<td><input name='sl_state' size='7'><br><small>".__("State", $text_domain)."</small></td>
		<td><input name='sl_zip' size='10'><br><small>".__("Zip", $text_domain)."</small></td></tr></table><br>
		".__("Additional Information", $text_domain)."<br>
		<textarea name='sl_description' rows='5' cols='17'></textarea>&nbsp;<small>".__("Description", $text_domain)."</small><br>
		<input name='sl_tags'>&nbsp;<small>".__("Tags (seperate with commas)", $text_domain)."</small><br>		
		<input name='sl_url'>&nbsp;<small>".__("URL", $text_domain)."</small><br>
		<input name='sl_hours'>&nbsp;<small>".__("Hours", $text_domain)."</small><br>
		<input name='sl_phone'>&nbsp;<small>".__("Phone", $text_domain)."</small><br>
		<input name='sl_image'>&nbsp;<small>".__("Image URL (shown with location)", $text_domain)."</small><br><br>
	<input type='submit' value='".__("Add Location", $text_domain)."' class='button-primary'>
	</td>
		</tr>
	</table>
</form>

</td>
<td style='/*border-right:solid silver 1px;*/ padding-top:0px;' valign='top'>";

if (file_exists($sl_upload_path."/addons/csv-xml-importer-exporter/csv-import-form.php")) {
	include($sl_upload_path."/addons/csv-xml-importer-exporter/csv-import-form.php");
	print "<br>";
}
/*else {
	print "<A href='http://www.viadat.com/products-page/store-locator-add-ons/csv-importer--exporter--xml-exporter/' target='_blank'><center><b>Addon:</b> CSV Importer/Exporter & XML Exporter</center><br><br><img src='$sl_base/screenshot-3.jpg' border='0'></a>";
}*/
include($sl_path."/database-info.php");
if (file_exists($sl_upload_path."/addons/db-importer/db-import-form.php")) {
	include($sl_upload_path."/addons/db-importer/db-import-form.php");
}

print "
</td>
<td valign='top' style='padding-top:0px;'>
";

if (file_exists($sl_upload_path."/addons/point-click-add/point-click-add-form.php")) {
	include($sl_upload_path."/addons/point-click-add/point-click-add-form.php");
}
/*else {
	print "<A href='http://www.viadat.com/products-page/store-locator-add-ons/point-click--add-mapper/' target='_blank'><center><b>Addon:</b> Point, Click, Add Mapper</center><br><br><img src='$sl_base/screenshot-4.jpg' border='0'></a>";
}*/

print "</td>
</tr>
</table>
</div>";
?>