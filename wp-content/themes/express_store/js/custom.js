jQuery.noConflict();

jQuery(function() {
	
	if(jQuery.fn.jCarouselLite)

    jQuery("#scroller-wrapper, #related-wrapper").jCarouselLite({
        btnNext: ".next",
        btnPrev: ".prev",
        visible: 4,
        auto: 800,
        speed: 1500
    });
});	


jQuery(document).ready(function(){
	
	if(jQuery.fn.epanelMegamenu)		
	jQuery("").epanelMegamenu({modify_position:true});
	
	jQuery('#main-menu ul>li>a, .sub-menu a').hover(function() { //mouse in
			jQuery(this).stop().animate({ paddingLeft: '10px' }, 200);
		}, function() { //mouse out
		jQuery(this).stop().animate({ paddingLeft: 0 }, 200);
		});
	
});

jQuery(document).ready(function() { 
    jQuery('ul#main-menu, #store-home-menu').superfish({ 
        delay:       1000,                            // one second delay on mouseout 
        animation:   {opacity:'show',height:'show'},  // fade-in and slide-down animation 
        speed:       'fast',                          // faster animation speed 
        autoArrows:  false,                           // disable generation of arrow mark-up 
        dropShadows: false                            // disable drop shadows 
    }); 
    
    
    jQuery('#buttonsend').click( function() {
    	
    		var name    = jQuery('#name').val();
    		var subject = jQuery('#subject').val();
    		var email   = jQuery('#email').val();
    		var message = jQuery('#message').val();
    		var siteurl = jQuery('#siteurl').val();
    		
    		jQuery('.loading').fadeIn('fast');
    		
    		if (name != "" && subject != "" && email != "" && message != "")
    			{
    
    				jQuery.ajax(
    					{
    						url: siteurl+'/sendemail.php',
    						type: 'POST',
    						data: "name=" + name + "&subject=" + subject + "&email=" + email + "&message=" + message,
    						success: function(result) 
    						{
    							jQuery('.loading').fadeOut('slow');
    							if(result == "email_error") {
    								jQuery('#email').css({"border":"1px solid #FF8C8C"});
    							} else {
    								jQuery('#name, #subject, #email, #message').val("");
    								jQuery('.success-contact').show().fadeOut(5000, function(){ jQuery(this).remove(); });								
    							}
    						}
    					}
    				);
    				return false;
    				
    			} 
    		else 
    			{
    				jQuery('.loading').fadeOut('slow');
    				if( name == "") jQuery('#name').css({"background":"#FFFCFC","border":"2px solid #d06e6e"});
    				if(subject == "") jQuery('#subject').css({"background":"#FFFCFC","border":"2px solid #d06e6e"});
    				if(email == "" ) jQuery('#email').css({"background":"#FFFCFC","border":"2px solid #d06e6e"});
    				if(message == "") jQuery('#message').css({"background":"#FFFCFC","border":"2px solid #d06e6e"});
    				return false;
    			}
    	});
    	
    		jQuery('#name, #subject, #email,#message').focus(function(){
    			jQuery(this).css({"background":"#ffffff","border":"2px solid #d6d6d6"});
    		});     
    
    /* additional shortcodes settings */
    
    
    jQuery(".toggle_body").hide(); 
    
    	jQuery("h4.toggle").toggle(function(){
    		jQuery(this).addClass("toggle_active");
    		}, function () {
    		jQuery(this).removeClass("toggle_active");
    	});
    	
    	jQuery("h4.toggle").click(function(){
    		jQuery(this).next(".toggle_body").slideToggle();
    
    	});
    	
    
    jQuery(".tabs_container").each(function(){
    	jQuery("ul.tabs",this).tabs("div.panes > div", {tabs:'li',effect: 'fade', fadeOutSpeed: -400});
    });
    jQuery(".mini_tabs_container").each(function(){
    	jQuery("ul.mini_tabs",this).tabs("div.panes > div", {tabs:'li',effect: 'fade', fadeOutSpeed: -400});
    });
    jQuery.tools.tabs.addEffect("slide", function(i, done) {
    	this.getPanes().slideUp();
    	this.getPanes().eq(i).slideDown(function()  {
    		done.call();
    	});
    });
    jQuery(".accordion").tabs("div.pane", {tabs: '.tab', effect: 'slide'});	
     
    
}); 

jQuery(document).ready(function($) {

		jQuery(".colorbox").each(function(){
				var $iframe = jQuery(this).attr('data-iframe');
				if($iframe == undefined || $iframe == 'false'){
					$iframe = false;
				}else{
					$iframe = true;
				}
				var $href = false;
				var $inline = jQuery(this).attr('data-inline');
				if($inline == undefined || $inline == 'false'){
					$inline = false;
				}else{
					$inline = true;
					$href = jQuery(this).attr('data-href');
				}
				var $width = jQuery(this).attr('data-width');
				if($width == undefined){
					if($iframe == true || $inline == true ){
						$width = '80%';
					}else{
						$width = '';
					}
				}
				var $height = jQuery(this).attr('data-height');
				if($height == undefined){
					if($iframe == true || $inline == true ){
						$height = '80%';
					}else{
						$height = '';
					}
				}
				var $photo = jQuery(this).attr('data-photo');
				if($photo == undefined || $photo == 'false'){
					$photo = false;
				}else{
					$photo = true;
				}
				var $close = jQuery(this).attr('data-close');
				if($close == undefined || $close == 'true'){
					$close = true;
				}else{
					$close = false;
				}
				jQuery(this).colorbox({
					opacity:0.7,
					innerWidth:$width,
					innerHeight:$height,
					iframe:$iframe,
					inline:$inline,
					href:$href,
					photo:$photo,
					onLoad: function(){
						if(!$close){
							jQuery("#cboxClose").css("visibility", "hidden");
						}else{
							jQuery("#cboxClose").css("visibility", "visible");
						}
						jQuery("#colorbox").removeClass('withVideo');
					},
					onComplete: function(){	
						if (typeof Cufon !== "undefined"){
							Cufon.replace('#cboxTitle');
						}
					}
				});
			});
			
			/* enable lightbox */
			var enable_lightbox = function(parent){
				
				jQuery("a.lightbox[href*='http://www.vimeo.com/']",parent).each(function() {
					jQuery(this).attr('href',this.href.replace("www.vimeo.com/", "player.vimeo.com/video/"));
				});
				jQuery("a.lightbox[href*='http://vimeo.com/']",parent).each(function() {
					jQuery(this).attr('href',this.href.replace("vimeo.com/", "player.vimeo.com/video/"));
				});
				jQuery("a.lightbox[href*='http://www.youtube.com/watch?']",parent).each(function() {
					jQuery(this).attr('href',this.href.replace(new RegExp("watch\\?v=", "i"), "v/"));
				});
				jQuery("a.lightbox[href*='http://player.vimeo.com/']",parent).each(function() {
					jQuery(this).addClass("fancyVimeo").removeClass('lightbox');
				});
				jQuery("a.lightbox[href*='http://www.youtube.com/v/']",parent).each(function() {
					jQuery(this).addClass("fancyVideo").removeClass('lightbox');
				});
				jQuery("a.lightbox[href$='.swf']",parent).each(function() {
					jQuery(this).addClass("fancyVideo").removeClass('lightbox');
				});
				jQuery(".fancyVimeo",parent).each(function(){
					var $width = jQuery(this).attr('data-width');
					if($width == undefined){
						$width = '640';
					}
					var $height = jQuery(this).attr('data-height');
					if($height == undefined){
						$height = '408';
					}
					jQuery(this).colorbox({
						opacity:0.7,
						innerWidth:$width,
						innerHeight:$height,
						iframe:true,
						scrolling:false,
						current:"{current} of {total}",
						onLoad: function(){
							jQuery("#cboxClose").css("visibility", "hidden");
							jQuery("#colorbox").addClass('withVideo');
						},
						onComplete: function(){
							if (typeof Cufon !== "undefined"){
								Cufon.replace('#cboxTitle');
							}
						}
					});
				});
		
				jQuery(".fancyVideo",parent).each(function(){
					var $width = jQuery(this).attr('data-width');
					if($width == undefined){
						$width = '640';
					}
					var $height = jQuery(this).attr('data-height');
					if($height == undefined){
						$height = '390';
					}
					var lightbox_html = jQuery.flash.create({swf:this.href,width:$width,height:$height,play:true});
					
					jQuery(this).colorbox({
						opacity:0.7,
						innerWidth:$width,
						innerHeight:$height,
						html:lightbox_html,
						scrolling:false,
						current:"{current} of {total}",
						//rel:'nofollow',
						onLoad: function(){
							jQuery("#cboxClose").css("visibility", "hidden");
							jQuery("#colorbox").addClass('withVideo');
						},
						onComplete: function(){
							if (typeof Cufon !== "undefined"){
								Cufon.replace('#cboxTitle');
							}
						}
					});
				});
				jQuery(".lightbox",parent).colorbox({
					opacity:0.7,
					maxWidth:"100%",
					maxHeight:"100%",
					current:"{current} of {total}",
					onLoad: function(){
						jQuery("#cboxClose").css("visibility", "visible");
						jQuery("#colorbox").removeClass('withVideo');
					},
					onComplete: function(){
						if (typeof Cufon !== "undefined"){
							Cufon.replace('#cboxTitle');
						}
					}
				});
			};
			enable_lightbox(document);
			
}); 