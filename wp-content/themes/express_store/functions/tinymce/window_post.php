<?php
// look up for the path
require_once('ep_config.php');
// check for rights
if ( !current_user_can('edit_pages') && !current_user_can('edit_posts') ) 
	wp_die(__("You are not allowed to be here"));
    global $wpdb;
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>E-Panel Shortcodes</title>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo  get_template_directory_uri() ?>/functions/tinymce/tinymce.js"></script>
	<base target="_self" />
<style type="text/css">
<!-- 
select#epshortcode_tag optgroup { font:bold 11px Tahoma, Verdana, Arial, Sans-serif;}
select#epshortcode_tag optgroup option { font:normal 11px/18px Tahoma, Verdana, Arial, Sans-serif; padding-top:1px; padding-bottom:1px;}
-->
</style>
</head>
<body id="link" onLoad="tinyMCEPopup.executeOnLoad('init();');document.body.style.display='';
document.getElementById('epshortcode_tag').focus();" style="display: none;">
<!-- <form onsubmit="insertLink();return false;" action="#"> -->
	<form name="ep_tabs" action="#">
	<div class="tabs">
		<ul>
	<li id="ep_tab" class="current"><span><a href="javascript:mcTabs.displayTab('ep_tab','epshortcode_panel');" onMouseDown="return false;">Columns</a></span></li>
		

		</ul>
	</div>
	
	<div class="panel_wrapper">
		<!-- gallery panel -->
		<div id="epshortcode_panel" class="panel current">
		<br />
		<table border="0" cellpadding="4" cellspacing="0">
         
         <tr><td>Choose a shortcode and type your content into the shortcode.</td></tr>
         <tr>
            <td nowrap="nowrap"><label for="epshortcode_tag"><?php _e("Select Shortcodes", 'shortcodes'); ?></label></td>
            
            <td><select id="epshortcode_tag" name="epshortcode_tag" style="width: 200px">
                <option value="0">Choose One</option>
				<?php
					if(is_array($shortcode_tags)) 
					{
						$i=1;

						foreach ($shortcode_tags as $ep_shortcodekey => $short_code_value) 
						{
							if( stristr($short_code_value, 'ep_') ) 
							{
								$ep_shortcode_name = str_replace('ep_', '' ,$short_code_value);
								$ep_shortcode_names = str_replace('_', ' ' ,$ep_shortcode_name);
								$ep_shortcodenames = ucwords($ep_shortcode_names);
							
								echo '<option value="' . $ep_shortcodekey . '" >' . $ep_shortcodenames.'</option>' . "\n";
								echo '</optgroup>'; 

								$i++;
							}
						}
					}
			?>
            </select></td>
          </tr>
         
        </table>
		</div>
		
		<!-- pricing panel -->
				<div id="epshortcode_panel2" class="panel">
				<br />
				<table border="0" cellpadding="4" cellspacing="0">
		         
		         <tr><td>Choose a column shortcode and type your content into the shortcode.</td></tr>
		         <tr>
		            <td nowrap="nowrap"><label for="epshortcode_tag2"><?php _e("Select Shortcodes", 'shortcodes'); ?></label></td>
		            
		            <td><select id="epshortcode_tag2" name="epshortcode_tag2" style="width: 200px">
		                <option value="0">Choose One</option>
						<?php
							if(is_array($shortcode_tags)) 
							{
								$i=1;
		
								foreach ($shortcode_tags as $ep_shortcodekey => $short_code_value) 
								{
									if( stristr($short_code_value, 'pricing_') ) 
									{
										$ep_shortcode_name = str_replace('pricing_', '' ,$short_code_value);
										$ep_shortcode_names = str_replace('_', ' ' ,$ep_shortcode_name);
										$ep_shortcodenames = ucwords($ep_shortcode_names);
									
										echo '<option value="' . $ep_shortcodekey . '" >' . $ep_shortcodenames.'</option>' . "\n";
										echo '</optgroup>'; 
		
										$i++;
									}
								}
							}
					?>
		            </select></td>
		          </tr>
		         
		        </table>
				</div>
				
		
		

		
	</div>
		
	
	</div>

	<div class="mceActionPanel">
		<div style="float: left">
			<input type="button" id="cancel" name="cancel" value="Cancel" onClick="tinyMCEPopup.close();" />
		</div>

		<div style="float: right">
			<input type="submit" id="insert" name="insert" value="Insert" onClick="epshortcodesubmit();" />
		</div>
	</div>
</form>
</body>
</html>
