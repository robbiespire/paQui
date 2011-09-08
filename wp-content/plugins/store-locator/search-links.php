<table width=100% class='' cellpadding='3px'><tr><td valign=bottom width='33%' style='padding-left:0px'>
<?php
/*
if(1==1) {
	print "<a class='' href='#back' onclick='history.go(-1)' rel='nofollow'><img src='/images/spacer.gif' height=1 width=75 alt='' border=0><br>
Back</a>";
}
elseif ($_SERVER[HTTP_REFERER]!="") {
	print "<a class='' href='$_SERVER[HTTP_REFERER]' rel='nofollow'><img src='/images/spacer.gif' height=1 width=75 alt='' border=0><br>
Back</a>";
}
else {
	print "<a class='' href='..' rel='nofollow'><img src='/images/spacer.gif' height=1 width=75 alt='' border=0><br>
Back</a>";

}*/


//back();
$pos=0;
if ($start<0 || $start=="" || !isset($start) || empty($start)) {$start=0;}
if ($num_per_page<0 || $num_per_page=="") {$num_per_page=10;}
$prev=$start-$num_per_page;
$next=$start+$num_per_page;
if (ereg("&start=$start",$_SERVER[QUERY_STRING])) {
	$prev_page=str_replace("&start=$start","&start=$prev",$_SERVER[QUERY_STRING]);
	$next_page=str_replace("&start=$start","&start=$next",$_SERVER[QUERY_STRING]); //echo($next_page);
}
else {
	$prev_page=$_SERVER[QUERY_STRING]."&start=$prev";
	$next_page=$_SERVER[QUERY_STRING]."&start=$next";
}
if ($numMembers2>$num_per_page) {
	//print "  | ";

if ((($start/$num_per_page)+1)-5<1) {
	$beginning_link=1;
}
else {
	$beginning_link=(($start/$num_per_page)+1)-5;
}
if ((($start/$num_per_page)+1)+5>(($numMembers2/$num_per_page)+1)) {
	$end_link=(($numMembers2/$num_per_page)+1);
}
else {
	$end_link=(($start/$num_per_page)+1)+5;
}
$pos=($beginning_link-1)*$num_per_page;
	for ($k=$beginning_link; $k<$end_link; $k++) {
		if (ereg("&start=$start",$_SERVER[QUERY_STRING])) {
			$curr_page=str_replace("&start=$start","&start=$pos",$_SERVER[QUERY_STRING]);
		}
		else {
			$curr_page=$_SERVER[QUERY_STRING]."&start=$pos";
		}
		if (($start-($k-1)*$num_per_page)<0 || ($start-($k-1)*$num_per_page)>=$num_per_page) {
			print "<a class='' href=\"{$_SERVER[PHP_SELF]}?$curr_page\" rel='nofollow'>";
		}
		print $k;
		if (($start-($k-1)*$num_per_page)<0 || ($start-($k-1)*$num_per_page)>=$num_per_page) {
			print "</a>";
		}
		$pos=$pos+$num_per_page;
		print "&nbsp;&nbsp;";
	}
}
$cleared=ereg_replace("q=$_GET[q]", "", $_SERVER[REQUEST_URI]);
$extra_text=($_GET[q])? __("for your search of", $text_domain)." <strong>\"$_GET[q]\"</strong>&nbsp;|&nbsp;<a href='$cleared'>".__("Clear&nbsp;Results", $text_domain)."</a>" : "" ;
?>
</td>
<td align='center' valign='bottom' width='33%'><div class='' style='padding:5px; font-weight:normal'>
<?php 

	$end_num=($numMembers2<($start+$num_per_page))? $numMembers2 : ($start+$num_per_page) ;
	print "<nobr>".__("Results", $text_domain)." <strong>".($start+1)." - ".$end_num."</strong>"; 
	if (!ereg("doSearch", $_GET[u])) {
		print " ($numMembers2 ".__("total", $text_domain).")".$extra_text; 
	}
	print "</nobr>";

?>
</div>
</td>
<td align=right valign=bottom width='33%' style='padding-right:0px'>
<table><tr><td width=75><nobr><img src='/images/spacer.gif' height=1 width=75 alt='' border=0>
<?php 
if (($start-$num_per_page)>=0) { ?>
<a class='' href="<?php print "{$_SERVER[PHP_SELF]}?$prev_page"; ?>" rel='nofollow'><?php print __("Previous", $text_domain)."&nbsp;$num_per_page"; ?></a>
<?php } 
if (($start-$num_per_page)>=0 && ($start+$num_per_page)<$numMembers2) { ?>
&nbsp;&nbsp;|&nbsp;
<?php } ?>
</td>
<td width='85px' valign=bottom><img src='/images/spacer.gif' height=1 width=45 alt='' border=0>
<?php 
if (($start+$num_per_page)<$numMembers2) { ?>
<a class='' href="<?php print "{$_SERVER[PHP_SELF]}?$next_page"; ?>" rel='nofollow'><?php print __("Next", $text_domain)."&nbsp;$num_per_page"; ?></a><br>
<?php } ?>
</nobr>
</td></tr></table>
</td>
</tr>
</table>
<!--div style='margin:0px auto; position:relative; left:50px'><center><?php// if ($current_dir!="articles" && $current_dir!="groups") {include("$root/google/google_ads_728_90_2.php");} ?></center></div><br-->