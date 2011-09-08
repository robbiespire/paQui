<?php
header("Content-type: text/javascript");
if (file_exists("./wp-config.php")){include("./wp-config.php");}
elseif (file_exists("../wp-config.php")){include("../wp-config.php");}
elseif (file_exists("../../wp-config.php")){include("../../wp-config.php");}
elseif (file_exists("../../../wp-config.php")){include("../../../wp-config.php");}
elseif (file_exists("../../../../wp-config.php")){include("../../../../wp-config.php");}
elseif (file_exists("../../../../../wp-config.php")){include("../../../../../wp-config.php");}
elseif (file_exists("../../../../../../wp-config.php")){include("../../../../../../wp-config.php");}
elseif (file_exists("../../../../../../../wp-config.php")){include("../../../../../../../wp-config.php");}
elseif (file_exists("../../../../../../../../wp-config.php")){include("../../../../../../../../wp-config.php");}
include("../variables.sl.php");
$zl=(trim(get_option('sl_zoom_level'))!="")? get_option('sl_zoom_level') : 4;
$mt=(trim(get_option('sl_map_type'))!="")? get_option('sl_map_type') : "G_NORMAL_MAP";
$wl=(trim(get_option('sl_website_label'))!="")? parseToXML(get_option('sl_website_label')) : "Website";
$du=(trim(get_option('sl_distance_unit'))!="")? get_option('sl_distance_unit') : "miles";
$oc=(trim(get_option('sl_map_overview_control'))!="")? get_option('sl_map_overview_control') : 0;
print "if (document.getElementById('map')){window.onunload = function (){ GUnload(); }}
var add_base='".$sl_base."';
var add_upload_base='".$sl_upload_base."';
var sl_map_home_icon='".get_option('sl_map_home_icon')."';
var sl_map_end_icon='".get_option('sl_map_end_icon')."';
var sl_google_map_country='".parseToXML(get_option('sl_google_map_country'))."';
var sl_google_map_domain='".get_option('sl_google_map_domain')."';
var sl_zoom_level=$zl;
var sl_map_type=$mt;
var sl_website_label='$wl';
var sl_load_locations_default='".get_option('sl_load_locations_default')."';
var sl_distance_unit='$du';
var sl_map_overview_control='$oc';\n";

if (ereg($sl_upload_base, get_option('sl_map_home_icon'))){
	$home_icon_path=ereg_replace($sl_upload_base, $sl_upload_path, get_option('sl_map_home_icon'));
}
else {
	$home_icon_path=ereg_replace($sl_base, $sl_path, get_option('sl_map_home_icon'));
}
$home_size=(function_exists(getimagesize) && file_exists($home_icon_path))? getimagesize($home_icon_path) : array(0 => 20, 1 => 34);
//$home_size=($home_size[0]=="")? array(0 => 20, 1 => 34) : $home_size;
print "var sl_map_home_icon_width=$home_size[0];\n";
print "var sl_map_home_icon_height=$home_size[1];\n";

if (ereg($sl_upload_base, get_option('sl_map_end_icon'))){
	$end_icon_path=ereg_replace($sl_upload_base, $sl_upload_path, get_option('sl_map_end_icon'));
}
else {
	$end_icon_path=ereg_replace($sl_base, $sl_path, get_option('sl_map_end_icon'));
}
$end_size=(function_exists(getimagesize) && file_exists($end_icon_path))? getimagesize($end_icon_path) : array(0 => 20, 1 => 34);
//$end_size=($end_size[0]=="")? array(0 => 20, 1 => 34) : $end_size;
print "var sl_map_end_icon_width=$end_size[0];\n";
print "var sl_map_end_icon_height=$end_size[1];\n";
?>