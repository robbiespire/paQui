<?php
	global $Ecart;
	$setting_pages = array_filter(array_keys($Ecart->Flow->Admin->Pages),array(&$Ecart->Flow->Admin,'get_settings_pages'));
?>
	<?php $i = 0; foreach ($setting_pages as $screen): ?>
		<li class="ui-state-default ui-corner-top <?php if ($_GET['page'] == $screen) echo ' ui-tabs-selected ui-state-active'; ?>"><a class="general_settings tabnav-element" href="?page=<?php echo $screen; ?>"><span><?php
			echo $Ecart->Flow->Admin->Pages[$screen]->label;
		?></span></a></li>
	<?php endforeach; ?>