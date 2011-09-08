<?php

if ($_POST[finish_import]=="1") {
//Second step in importing locations - inserting into database

//var_dump ($_POST[field_map]); die();
insert_matched_data();
//	var_dump($_POST[field_map]);
	//exit();
	print "<div class='highlight'>".__("Successful Import", $text_domain).". $view_link</div>";
}
elseif (empty($_POST[finish_import])) {
//First step in importing address - mapping old table fields to the new table fields

if (!eregi("^select", trim($_POST[query]))) {
	print "<div class='highlight' style='border:solid red 1px; background-color:salmon'>".__("You May Only Use 'Select' Queries", $text_domain).". | <a href='$_SERVER[REQUEST_URI]'>".__("Try Again", $text_domain)."</a></div>";
	exit;
}


define("DB2_HOST","$_POST[host]");
define("DB2_USER","$_POST[user]");
define("DB2_PASS","$_POST[pass]");
define("DB2_NAME","$_POST[db]");
$query=$_POST[query];

//Doesn't use wpdb functionality because it is possible that datbase (or table)
$link=mysql_connect(DB2_HOST,DB2_USER,DB2_PASS);
mysql_select_db(DB2_NAME,$link);
mysql_query("SET NAMES utf8");
$sql=$query;
$result=mysql_query($sql,$link);
$rez=array();
while($row=mysql_fetch_assoc($result))
$rez[]=$row;
mysql_free_result($result);
mysql_close($link);

//echo base64_encode(serialize($rez));
//var_dump($rez);

match_imported_data($rez);
}

?>