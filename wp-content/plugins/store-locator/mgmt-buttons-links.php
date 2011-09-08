<?php

print "<table width='100%' cellpadding='5px' cellspacing='0' style='border:solid silver 1px' id='rightnow' class='widefat'>
<thead><tr>
<td style='/*background-color:#000;*/ width:20%'><input class='button' type='button' value='".__("Delete Selected", $text_domain)."' onclick=\"if(confirm('".__("You sure", $text_domain)."?')){LF=document.forms['locationForm'];LF.act.value='delete';LF.submit();}else{return false;}\"></td>";
	if (file_exists($sl_upload_path."/addons/csv-xml-importer-exporter/export-links.php")) {
		print "<td style='width:13%; text-align:center; color:black; /*background-color:white*/' class='youhave'>";
		$sl_real_base=$sl_base; $sl_base=$sl_upload_base;
		include($sl_upload_path."/addons/csv-xml-importer-exporter/export-links.php");
		$sl_base=$sl_real_base;
		print "</td>";
	}
print "<td style='/*background-color:#000;*/ width:73%; text-align:right; color:white'>";
	if (file_exists($sl_upload_path."/addons/multiple-field-updater/multiple-field-update-form.php") && get_option('sl_location_updater_type')=="Multiple Fields") {
		include($sl_upload_path."/addons/multiple-field-updater/multiple-field-update-form.php");
	}
	else {
		print "<strong>".__("Tags", $text_domain)."</strong>&nbsp;<input name='sl_tags'>&nbsp;<input class='button' type='button' value='".__("Tag Selected", $text_domain)."' onclick=\"LF=document.forms['locationForm'];LF.act.value='add_tag';LF.submit();\">&nbsp;<input class='button' type='button' value='".__("Remove Tag From Selected", $text_domain)."' onclick=\"if(confirm('".__("You sure", $text_domain)."?')){LF=document.forms['locationForm'];LF.act.value='remove_tag';LF.submit();}else{return false;}\">";
	}
print "</td></tr></thead></table>
";

?>