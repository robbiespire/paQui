<div class='wrap'>
<!--h2>About Lots of Locales (LoL) Store Locator Plugin</h2><br-->
<?php

//print "<span style='font-size:14px; font-family:Helvetica'>";
ob_start();
include('readme.txt');
$txt=ob_get_contents();
ob_clean();
//print ereg_replace("(http://.*\..*\..{2,3}\n)", "<a href='\\1' target='_blank'>\\1</a>", $txt);
//$txt=ereg_replace("(http://www.viadat.com)", "<a href='\\1' target='_blank'>\\1</a>", $txt);

$txt=ereg_replace("\=\=\= ", "<h2>", $txt);
$txt=ereg_replace(" \=\=\=", "</h2>", $txt);
$txt=ereg_replace("\=\= ", "<div id='wphead' style='color:white'><h1 id='site-heading'><span id='site-title'>", $txt);
$txt=ereg_replace(" \=\=", "</h1></span></div>", $txt);
$txt=ereg_replace("\= ", "<b><u>", $txt);
$txt=ereg_replace(" \=", "</u></b>", $txt);

//$txt=ereg_replace("\[(.*)\]\((a-z\.\:\/)\)", "<a href='\\2'>\\1</a>", $txt);

//$txt=ereg_replace("\[(.*)\]\(([a-zA-Z]+://[.]?[a-zA-Z0-9_/?&amp;%20,=-\+-])*\)", "<a href=\"\\2\" target=_blank>\\1</a>", $txt);

$txt=do_hyperlink($txt);

print nl2br($txt);
//print "</span>";

?>
</div>