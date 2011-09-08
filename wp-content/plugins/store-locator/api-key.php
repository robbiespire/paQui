<div class='wrap'>
<?php 

if ($_POST) {
update_option('store_locator_api_key', $_POST[store_locator_api_key]);
update_option('sl_language', $_POST[sl_language]);

$sl_google_map_arr=explode(":", $_POST[google_map_domain]);
update_option('sl_google_map_country', $sl_google_map_arr[0]);
update_option('sl_google_map_domain', $sl_google_map_arr[1]);

update_option('sl_map_character_encoding', $_POST[sl_map_character_encoding]);


print "<div class='highlight'>".__("Successful Update", $text_domain)."</div> <!--meta http-equiv='refresh' content='0'-->";
}

print "<h2>".__("Update Your Google API Key", $text_domain)."</h2><br><form action='' method='post'><table class='widefat'><thead><tr ><th colspan='2'>".__("Your API Key Status", $text_domain).":&nbsp;&nbsp;<!--/th><th-->";
//load_plugin_textdomain('api-key');
//$text_domain='api=key';
$slak=get_option('store_locator_api_key');

if (!$slak || trim($slak)=='') {
	print "<div style='border:solid red 0px; padding:3px; background-color:pink; width:400px; color:black; display:inline;'>".__("Please Update Your Google API Key", $text_domain)."</div>";
}
else {
	print "<div style='border:solid green 0px; padding:3px; background-color:LightGreen; width:400px; color:black; display:inline;'>".__("API Key Submitted", $text_domain)."</div>";
}

print "</th></tr></thead><tr><td style='width:40%;' class='left_side'><h2>".__("Google API Key", $text_domain)."</h2><!--/td><td--><input name='store_locator_api_key' value='$slak' size='70'><br><br><div class=''><strong><a href='http://code.google.com/apis/maps/signup.html' target='_blank'>".__("Get your Google API Key", $text_domain)."</a></strong><br>(".__("You'll need to log in with your Google account on the page that opens up. If you don't have an account", $text_domain).", <a href='https://www.google.com/accounts/' target='_blank'>".__("sign up here", $text_domain)."</a>.)</div></td><!--/tr-->";

/*print "<tr><td>".__("Choose Your Language", $text_domain).":</td><td><select name='sl_language'>";
$the_lang["United States"]="en_EN";
$the_lang["France"]="fr_FR";

foreach ($the_lang as $key=>$value) {
	$selected=(get_option('sl_language')==$value)?" selected " : "";
	print "<option value='$value' $selected>$key ($value)</option>\n";
}
print "</td></tr>";*/

print "<!--tr--><td><h2>".__("Select Your Location", $text_domain)."</h2><!--/td><td--><select name='google_map_domain'>";
$the_domain["United States"]="maps.google.com";
$the_domain["Argentina"]="maps.google.com.ar"; //added 12/5/09
$the_domain["Australia"]="maps.google.com.au";
$the_domain["Austria"]="maps.google.at"; //updated 6/13/09
$the_domain["Belgium"]="maps.google.be";
//$the_domain["Bosnia and Herzegovina"]="maps.google.com.ba"; //removed 6/13/09
$the_domain["Brazil"]="maps.google.com.br";
$the_domain["Canada"]="maps.google.ca";
$the_domain["Chile"]="maps.google.cl"; //added 12/5/09
$the_domain["China"]="ditu.google.com"; //added 6/13/09
$the_domain["Czech Republic"]="maps.google.cz";
$the_domain["Denmark"]="maps.google.dk";
$the_domain["Finland"]="maps.google.fi";
$the_domain["France"]="maps.google.fr";
$the_domain["Germany"]="maps.google.de";
$the_domain["Hong Kong"]="maps.google.com.hk"; //added 6/13/09
//$the_domain["Hungary"]="maps.google.hu"; //added 12/5/09 //removed 12/17/09
$the_domain["India"]="maps.google.co.in"; //added 6/13/09
$the_domain["Italy"]="maps.google.it";
$the_domain["Japan"]="maps.google.co.jp"; //updated 6/13/09
//$the_domain["Kenya"]="maps.google.co.ke"; //added 6/13/09 //removed 12/17/09
$the_domain["Liechtenstein"]="maps.google.li"; //added 6/13/09
//$the_domain["Malaysia"]="maps.google.com.my"; //added 6/13/09 //removed 12/17/09
$the_domain["Mexico"]="maps.google.com.mx"; //added 12/5/09
$the_domain["Netherlands"]="maps.google.nl";
$the_domain["New Zealand"]="maps.google.co.nz";
$the_domain["Norway"]="maps.google.no";
$the_domain["Poland"]="maps.google.pl";
$the_domain["Portugal"]="maps.google.pt"; //added 12/5/09
$the_domain["Russia"]="maps.google.ru";
$the_domain["Singapore"]="maps.google.com.sg"; //added 12/5/09
//$the_domain["South Africa"]="maps.google.co.za"; //added 12/5/09 //removed 12/17/09
$the_domain["South Korea"]="maps.google.co.kr"; //added 6/13/09
$the_domain["Spain"]="maps.google.es";
$the_domain["Sweden"]="maps.google.se";
$the_domain["Switzerland"]="maps.google.ch";
$the_domain["Taiwan"]="maps.google.com.tw"; //updated 6/13/09
//$the_domain["Thailand"]="maps.google.co.th"; //added 6/13/09 //removed 12/17/09
$the_domain["United Kingdom"]="maps.google.co.uk";

$char_enc["Default (UTF-8)"]="utf-8";
$char_enc["Western European (ISO-8859-1)"]="iso-8859-1";
$char_enc["Western/Central European (ISO-8859-2)"]="iso-8859-2";
$char_enc["Western/Southern European (ISO-8859-3)"]="iso-8859-3";
$char_enc["Western European/Baltic Countries (ISO-8859-4)"]="iso-8859-4";
$char_enc["Russian (Cyrillic)"]="iso-8859-5";
$char_enc["Arabic (ISO-8859-6)"]="iso-8859-6";
$char_enc["Greek (ISO-8859-7)"]="iso-8859-7";
$char_enc["Hebrew (ISO-8859-8)"]="iso-8859-8";
$char_enc["Western European w/amended Turkish (ISO-8859-9)"]="iso-8859-9";
$char_enc["Western European w/Nordic characters (ISO-8859-10)"]="iso-8859-10";
$char_enc["Thai (ISO-8859-11)"]="iso-8859-11";
$char_enc["Baltic languages & Polish (ISO-8859-13)"]="iso-8859-13";
$char_enc["Celtic languages (ISO-8859-14)"]="iso-8859-14";
$char_enc["Japanese (Shift JIS)"]="shift_jis";
$char_enc["Simplified Chinese (China)(GB 2312)"]="gb2312";
$char_enc["Traditional Chinese (Taiwan)(Big 5)"]="big5";
$char_enc["Hong Kong (HKSCS)"]="hkscs";
$char_enc["Korea (EUS-KR)"]="eus-kr";

foreach ($the_domain as $key=>$value) {
	$selected=(get_option('sl_google_map_domain')==$value)?" selected " : "";
	print "<option value='$key:$value' $selected>$key ($value)</option>\n";
}
print "</select><!--/td></tr><tr><td--><h2>".__("Select Character Encoding", $text_domain)."</h2>
<select name='sl_map_character_encoding'>";

foreach ($char_enc as $key=>$value) {
	$selected=(get_option('sl_map_character_encoding')==$value)?" selected " : "";
	print "<option value='$value' $selected>$key</option>\n";
}
print "</select></td></tr>
<tr><td colspan='2'><input type='submit' value='".__("Update", $text_domain)."' class='button-primary'></td></tr></table></form>";

?></div>