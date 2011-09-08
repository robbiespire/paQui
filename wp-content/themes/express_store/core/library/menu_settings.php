<?php
/******************************************************************
	Menu Settings
******************************************************************/
?>
<!--
<script type="text/javascript">
jQuery(document).ready(function() {
  jQuery('.sf-menu').sooperfish({
	hoverClass  : 'sfHover',
	delay    : <?php echo epanel_option('menu_hide_speed');?>, //make sure menus only disappear when intended, 500ms is advised by Jacob Nielsen
	animationShow  : {width:'show',height:'show',opacity:'show'},
	speedShow    : <?php echo epanel_option('menu_show_speed'); ?>,
	easingShow      : '<?php echo epanel_option('menu_show_easing'); ?>',
	animationHide  : {width:'hide',height:'hide',opacity:'hide'},
	speedHide    : <?php echo epanel_option('menu_effect_hide_speed'); ?>,
	easingHide      : '<?php echo epanel_option('menu_hide_easing'); ?>',
	autoArrows  : false
  });
});
</script>-->

<script> 
 
    jQuery(document).ready(function() { 
        jQuery('ul#main-menu').superfish({ 
            delay:       1000,                            // one second delay on mouseout 
            animation:   {opacity:'show',height:'show'},  // fade-in and slide-down animation 
            speed:       'fast',                          // faster animation speed 
            autoArrows:  false,                           // disable generation of arrow mark-up 
            dropShadows: false                            // disable drop shadows 
        }); 
    }); 
 
</script>