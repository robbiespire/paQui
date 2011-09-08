/**
 * This file holds the main javascript functions needed to improve the epanel mega menu backend
 *
 * @author		Christian "Kriesi" Budschedl
 * @copyright	Copyright ( c ) Christian Budschedl
 * @link		http://kriesi.at
 * @link		http://epanelthemes.com
 * @since		Version 1.0
 * @package 	epanelFramework
 */

(function($)
{
	var epanel_mega_menu = {
	
		recalcTimeout: false,
	
		// bind the click event to all elements with the class epanel_uploader 
		bind_click: function()
		{
			var megmenuActivator = $('.menu-item-epanel-megamenu', '#menu-to-edit');
				
				megmenuActivator.live('click', function()
				{	
					var checkbox = $(this),
						container = checkbox.parents('.menu-item:eq(0)');
				
					if(checkbox.is(':checked'))
					{
						container.addClass('epanel_mega_active');
					}
					else
					{
						container.removeClass('epanel_mega_active');
					}
					
					//check if anything in the dom needs to be changed to reflect the (de)activation of the mega menu
					epanel_mega_menu.recalc();
					
				});
		},
		
		recalcInit: function()
		{
			$( ".menu-item-bar" ).live( "mouseup", function(event, ui) 
			{
				if(!$(event.target).is('a'))
				{
					clearTimeout(epanel_mega_menu.recalcTimeout);
					epanel_mega_menu.recalcTimeout = setTimeout(epanel_mega_menu.recalc, 500);  
				}
			});
		},
		
		
		recalc : function()
		{
			menuItems = $('.menu-item', '#menu-to-edit');
			
			menuItems.each(function(i)
			{
				var item = $(this),
					megaMenuCheckbox = $('.menu-item-epanel-megamenu', this);
				
				if(!item.is('.menu-item-depth-0'))
				{
					var checkItem = menuItems.filter(':eq('+(i-1)+')');
					if(checkItem.is('.epanel_mega_active'))
					{
						item.addClass('epanel_mega_active');
						megaMenuCheckbox.attr('checked','checked');
					}
					else
					{
						item.removeClass('epanel_mega_active');
						megaMenuCheckbox.attr('checked','');
					}
				}				
				
				
				
				
				
			});
			
		},
		
		//clone of the jqery menu-item function that calls a different ajax admin action so we can insert our own walker
		addItemToMenu : function(menuItem, processMethod, callback) {
			var menu = $('#menu').val(),
				nonce = $('#menu-settings-column-nonce').val();

			processMethod = processMethod || function(){};
			callback = callback || function(){};

			params = {
				'action': 'epanel_ajax_switch_menu_walker',
				'menu': menu,
				'menu-settings-column-nonce': nonce,
				'menu-item': menuItem
			};

			$.post( ajaxurl, params, function(menuMarkup) {
				var ins = $('#menu-instructions');
				processMethod(menuMarkup, params);
				if( ! ins.hasClass('menu-instructions-inactive') && ins.siblings().length )
					ins.addClass('menu-instructions-inactive');
				callback();
			});
		}

};
	

	
	$(function()
	{
		epanel_mega_menu.bind_click();
		epanel_mega_menu.recalcInit();
		epanel_mega_menu.recalc();
		if(typeof wpNavMenu != 'undefined'){ wpNavMenu.addItemToMenu = epanel_mega_menu.addItemToMenu; }
 	});

	
})(jQuery);	 