<div class='wrap'>
<?php 

if (!$_POST) {move_upload_directories();}
if ($_POST) {
$_POST[height]=ereg_replace("[^0-9]", "", $_POST[height]);
$_POST[width]=ereg_replace("[^0-9]", "", $_POST[width]);
update_option('sl_map_height', $_POST[height]);
update_option('sl_map_width', $_POST[width]);
update_option('sl_map_radii', $_POST[radii]);
update_option('sl_map_height_units', $_POST[height_units]);
update_option('sl_map_width_units', $_POST[width_units]);
update_option('sl_map_home_icon', $_POST[icon]);
update_option('sl_map_end_icon', $_POST[icon2]);
update_option('sl_map_theme', $_POST[theme]);
update_option('sl_search_label', $_POST[search_label]);
update_option('sl_radius_label', $_POST[sl_radius_label]);
update_option('sl_website_label', $_POST[sl_website_label]);
update_option('sl_instruction_message', $_POST[sl_instruction_message]);
update_option('sl_zoom_level', $_POST[zoom_level]);
$_POST[sl_use_city_search]=($_POST[sl_use_city_search]=="")? 0 : $_POST[sl_use_city_search];
update_option('sl_use_city_search', $_POST[sl_use_city_search]);
//$_POST[sl_use_name_search]=($_POST[sl_use_name_search]=="")? 0 : $_POST[sl_use_name_search];
//update_option('sl_use_name_search', $_POST[sl_use_name_search]);
$_POST[sl_remove_credits]=($_POST[sl_remove_credits]=="")? 0 : $_POST[sl_remove_credits];
update_option('sl_remove_credits', $_POST[sl_remove_credits]);
$_POST[sl_load_locations_default]=($_POST[sl_load_locations_default]=="")? 0 : $_POST[sl_load_locations_default];
update_option('sl_load_locations_default', $_POST[sl_load_locations_default]);
update_option('sl_map_type', $_POST[sl_map_type]);
update_option('sl_num_initial_displayed', $_POST[sl_num_initial_displayed]);
update_option('sl_map_overview_control', $_POST[sl_map_overview_control]);
update_option('sl_distance_unit', $_POST[sl_distance_unit]);

print "<div class='highlight'>".__("Successful Update", $text_domain)." $view_link</div> <!--meta http-equiv='refresh' content='0'-->";
}
print "<h2>".__("Map Designer", $text_domain)."</h2><br><form method='post' name='mapDesigner'><table class='widefat'><thead><tr><th colspan='2'>".__("Map Designer", $text_domain)."</th><!--td><".__("Designer", $text_domain)."--></td--></tr></thead>";
initialize_variables();

$icon_dir=opendir($sl_path."/icons/"); 
while (false !== ($an_icon=readdir($icon_dir))) {
	if (!ereg("^\.{1,2}$", $an_icon) && !ereg("shadow", $an_icon) && !ereg("\.db", $an_icon)) {

		$icon_str.="<img style='height:25px; cursor:hand; cursor:pointer; border:solid white 2px; padding:2px' src='$sl_base/icons/$an_icon' onclick='document.forms[\"mapDesigner\"].icon.value=this.src;document.getElementById(\"prev\").src=this.src;' onmouseover='style.borderColor=\"red\";' onmouseout='style.borderColor=\"white\";'>";
	}
}
if (is_dir($sl_upload_path."/custom-icons/")) {
	$icon_upload_dir=opendir($sl_upload_path."/custom-icons/");
	while (false !== ($an_icon=readdir($icon_upload_dir))) {
		if (!ereg("^\.{1,2}$", $an_icon) && !ereg("shadow", $an_icon) && !ereg("\.db", $an_icon)) {

			$icon_str.="<img style='height:25px; cursor:hand; cursor:pointer; border:solid white 2px; padding:2px' src='$sl_upload_base/custom-icons/$an_icon' onclick='document.forms[\"mapDesigner\"].icon.value=this.src;document.getElementById(\"prev\").src=this.src;' onmouseover='style.borderColor=\"red\";' onmouseout='style.borderColor=\"white\";'>";
		}
	}
}

$icon_dir=opendir($sl_path."/icons/");
while (false !== ($an_icon=readdir($icon_dir))) {
	if (!ereg("^\.{1,2}$", $an_icon) && !ereg("shadow", $an_icon) && !ereg("\.db", $an_icon)) {

		$icon2_str.="<img style='height:25px; cursor:hand; cursor:pointer; border:solid white 2px; padding:2px' src='$sl_base/icons/$an_icon' onclick='document.forms[\"mapDesigner\"].icon2.value=this.src;document.getElementById(\"prev2\").src=this.src;' onmouseover='style.borderColor=\"red\";' onmouseout='style.borderColor=\"white\";'>";
	}
}
if (is_dir($sl_upload_path."/custom-icons/")) {
	$icon_upload_dir=opendir($sl_upload_path."/custom-icons/");
	while (false !== ($an_icon=readdir($icon_upload_dir))) {
		if (!ereg("^\.{1,2}$", $an_icon) && !ereg("shadow", $an_icon) && !ereg("\.db", $an_icon)) {

			$icon2_str.="<img style='height:25px; cursor:hand; cursor:pointer; border:solid white 2px; padding:2px' src='$sl_upload_base/custom-icons/$an_icon' onclick='document.forms[\"mapDesigner\"].icon2.value=this.src;document.getElementById(\"prev2\").src=this.src;' onmouseover='style.borderColor=\"red\";' onmouseout='style.borderColor=\"white\";'>";
		}
	}
}

if (is_dir($sl_upload_path."/themes/")) {
	$theme_dir=opendir($sl_upload_path."/themes/"); 

	while (false !== ($a_theme=readdir($theme_dir))) {
		if (!ereg("^\.{1,2}$", $a_theme) && !ereg("\.(php|txt|htm(l)?)", $a_theme)) {

			$selected=($a_theme==get_option('sl_map_theme'))? " selected " : "";
			$theme_str.="<option value='$a_theme' $selected>$a_theme</option>\n";
		}
	}
}
	
$zl[]=0;$zl[]=1;$zl[]=2;$zl[]=3;$zl[]=4;$zl[]=5;$zl[]=6;$zl[]=7;$zl[]=8;
$zl[]=9;$zl[]=10;$zl[]=11;$zl[]=12;$zl[]=13;$zl[]=14;$zl[]=15;$zl[]=16;
$zl[]=17;$zl[]=18;$zl[]=19;

$zoom="<select name='zoom_level'>";
foreach ($zl as $value) {
	$zoom.="<option value='$value' ";
	if (get_option('sl_zoom_level')==$value){ $zoom.=" selected ";}
	$zoom.=">$value</option>";
}
$zoom.="</select>";

$checked=(get_option('sl_use_city_search')==1)? " checked " : "";
//$checked2=(get_option('sl_use_name_search')==1)? " checked " : "";
$checked3=(get_option('sl_remove_credits')==1)? " checked " : "";
$checked4=(get_option('sl_load_locations_default')==1)? " checked " : "";
$checked5=(get_option('sl_map_overview_control')==1)? " checked " : "";
/*
$map_type["".__("Show Default Street Map Only", $text_domain).""]="G_NORMAL_MAP";
$map_type["".__("Show Satellite Imagery Only", $text_domain).""]="G_SATELLITE_MAP";
$map_type["".__("Show Satellite Imagery + Street Map", $text_domain).""]="G_HYBRID_MAP";
$map_type["".__("Show Terrain + Street Map", $text_domain).""]="G_PHYSICAL_MAP";*/

$map_type["".__("Normal", $text_domain).""]="G_NORMAL_MAP";
$map_type["".__("Satellite", $text_domain).""]="G_SATELLITE_MAP";
$map_type["".__("Hybrid", $text_domain).""]="G_HYBRID_MAP";
$map_type["".__("Physical", $text_domain).""]="G_PHYSICAL_MAP";

foreach($map_type as $key=>$value) {
	$selected2=(get_option('sl_map_type')==$value)? " selected " : "";
	$map_type_options.="<option value='$value' $selected2>$key</option>\n";
}
$icon_notification_msg=((ereg("wordpress-store-locator-location-finder", get_option('sl_map_home_icon')) && ereg("^store-locator", $sl_dir)) || (ereg("wordpress-store-locator-location-finder", get_option('sl_map_end_icon')) && ereg("^store-locator", $sl_dir)))? "<div class='highlight' style='background-color:LightYellow;color:red'><span style='color:red'>".__("You have switched from <strong>'wordpress-store-locator-location-finder'</strong> to <strong>'store-locator'</strong> --- great!<br>Now, please re-select your <b>'Home Icon'</b> and <b>'Destination Icon'</b> below, so that they show up properly on your store locator map.", $text_domain)."</span></div>" : "" ;
	
print "
<tr><td colspan='1' width='40%' class='left_side'><h2>".__("Defaults", $text_domain)."</h2>
<table class='map_designer_section'><tr><td>".__("Choose Default Map Type Shown to Visitors", $text_domain).":</td>
<td><select name='sl_map_type'>\n".$map_type_options."</select></td></tr>
<tr><td>".__("Allow User Search By City?", $text_domain).":</td>
<td><input name='sl_use_city_search' value='1' type='checkbox' $checked></td></tr>
<tr><td>".__("Show Map Inset Box?", $text_domain).":</td>
<td><input name='sl_map_overview_control' value='1' type='checkbox' $checked5></td></tr>
<tr><td>".__("Show Locations By Default When Map Loads?", $text_domain).":</td>
<td><input name='sl_load_locations_default' value='1' type='checkbox' $checked4></td></tr>
<tr><td>".__("Number of Locations Shown By Default", $text_domain).":</td>
<td><input name='sl_num_initial_displayed' value='$sl_num_initial_displayed'><br><span style='font-size:80%'>(".__("Recommended Max: 50", $text_domain).")</span></td></tr></table>

</td><!--/tr-->
<!--tr><td>".__("Allow User Search By Name of Location?", $text_domain).":</td>
<td><input name='sl_use_name_search' value='1' type='checkbox' $checked2></td></tr-->
<!--tr--><td colspan='1' width='60%'><h2>".__("Labels", $text_domain)."</h2>
<table class='map_designer_section right_side'>
<tr><td>".__("Address Input Label", $text_domain).":</td>
<td><input name='search_label' value=\"$search_label\"></td></tr>
<tr><td>".__("Radius Dropdown Label", $text_domain).":</td>
<td><input name='sl_radius_label' value=\"$sl_radius_label\"></td></tr>
<tr><td>".__("Website URL Label", $text_domain).":</td>
<td><input name='sl_website_label' value=\"$sl_website_label\"></td>
<tr><td>".__("Instruction Message to Users", $text_domain).":</td>
<td><input name='sl_instruction_message' value=\"".$sl_instruction_message."\" size='50'></td>
</tr></table>

</td></tr>
<tr><td colspan='1' class='left_side'><h2>".__("Dimensions", $text_domain)."</h2>
<table class='map_designer_section'><tr><td><nobr>".__("Zoom Level", $text_domain).":</nobr></td>
<td>$zoom</td></tr>
<tr><td><nobr>".__("Map Height", $text_domain).":</nobr></td>
<td><input name='height' value='$height'>&nbsp;".choose_units($height_units, "height_units")."</td></tr>
<tr><td><nobr>".__("Map Width", $text_domain).":</nobr></td>
<td><input name='width' value='$width'>&nbsp;".choose_units($width_units, "width_units")."</td></tr>
<tr><td><nobr>".__("Radii Options<br>(in ".get_option('sl_distance_unit').")", $text_domain).":</nobr></td>
<td><input  name='radii' value='$radii' size='30'><br><span style='font-size:80%'>".__("Note: Seperate each number with a comma ',' , and put paratheseses '( )' around the default radius you would like to show</span>", $text_domain)."</td></tr>
<tr><td><nobr>".__("Distance Unit", $text_domain).":</td><td><select name='sl_distance_unit'>";
$the_distance_unit["".__("Kilometers", $text_domain).""]="km";
$the_distance_unit["".__("Miles", $text_domain).""]="miles";
foreach ($the_distance_unit as $key=>$value) {
	$selected=(get_option('sl_distance_unit')==$value)?" selected " : "";
	print "<option value='$value' $selected>$key</option>\n";
}
print "</select></td></tr>
</table>

</td><!--/tr>
<tr--><td colspan='1'><h2>".__("Design", $text_domain)."</h2>
$icon_notification_msg
<table class='map_designer_section right_side'><tr>
<tr><td valign='top'>".__("Choose Theme", $text_domain)."</td><td valign='top'> <select name='theme' onchange=\"\"><option value=''>".__("No Theme Selected", $text_domain)."</option>$theme_str</select>&nbsp;&nbsp;&nbsp;<a href='http://www.viadat.com/products-page/store-locator-themes/' target='_blank'>".__("Get&nbsp;Themes", $text_domain)." &raquo;</a></td></tr>
<tr><td>".__("Remove Credits", $text_domain).":</td>
<td><input name='sl_remove_credits' value='1' type='checkbox' $checked3></td></tr>
<tr><td valign='top'>".__("Home Icon", $text_domain).":</td>
<td valign='top'> <input name='icon' size='45' value='$icon' onchange=\"document.getElementById('prev').src=this.value\">&nbsp;&nbsp;<img id='prev' src='$icon' align='top'> <br><div style=''>$icon_str</div></td></tr>
<tr><td valign='top'>".__("Destination Icon", $text_domain).":</td>
<td valign='top'> <input name='icon2' size='45' value='$icon2' onchange=\"document.getElementById('prev2').src=this.value\">&nbsp;&nbsp;<img id='prev2' src='$icon2'align='top'> <br><div style=''>$icon2_str</div>
</td></tr>
<tr><td colspan='2'><div class=''><b>".__("Looking to create or find a unique icon? For ideas, visit", $text_domain)."<br> <a href='http://mapki.com/index.php?title=Icon_Image_Sets' target='_blank'>http://mapki.com/index.php?title=Icon_Image_Sets</a></b></div></td></tr></table>
</td></tr>
<tr><td colspan='2'><input type='submit' value='".__("Update", $text_domain)."' class='button-primary'></td></tr></table></form>";

?>
</div>