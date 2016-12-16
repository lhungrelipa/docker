(function ($) {
	/* SCMS : SNEEIT COMPACT MENU STICKY*/
	var SCMS_Index = -1;
	
	function scms_sticky_menu_enable(holder, holder_clone, scroller) {
		if (scroller.is('.sneeit-compact-menu-sticky')) {
			return;
		}
		
		holder_clone.show();
		holder_clone.css({
			'width': holder.css('width'),
			'height': holder.css('height'),
			'padding': holder.css('padding'),
			'margin': holder.css('margin'),
			'position': holder.css('position'),
			'top': holder.css('top'),
			'left': holder.css('left'),
			'bottom': holder.css('bottom'),
			'right': holder.css('right')
		});
		
		
		scroller.css('left', holder.offset().left);
		scroller.css('width', holder.width()+'px');
		if (!holder.is(scroller)) {
			holder.hide();
		}
		
		scroller.addClass('sneeit-compact-menu-sticky');
	}
	function scms_sticky_menu_disable(holder, holder_clone, scroller) {
		if (!scroller.is('.sneeit-compact-menu-sticky')) {
			return;
		}
		scroller.removeClass('sneeit-compact-menu-sticky');
		scroller.css('left', '').css('width', '');		
		holder.css('height', '');
		
		holder_clone.hide();
		holder.show();
	}
	
	$('.sneeit-compact-menu[data-sticky_menu]').each(function(){		
		/* INIT */
		SCMS_Index++;
		
		// collect data
		var SCMS_Sticky_Menu = $(this).attr('data-sticky_menu');
		var SCMS_Sticky_Menu_Holder = $($(this).attr('data-sticky_menu_holder'));
		var SCMS_Sticky_Menu_Scroller = $($(this).attr('data-sticky_menu_scroller'));
		if (SCMS_Sticky_Menu_Holder.length == 0) {
			SCMS_Sticky_Menu_Holder = $(this);
		}
		if (SCMS_Sticky_Menu_Scroller.length == 0) {
			SCMS_Sticky_Menu_Scroller = $(this);
		}
		
		// create cloner of place holder		
		$('<div class="sneeit-compact-menu-holder-'+SCMS_Index+'"></div>').insertBefore(SCMS_Sticky_Menu_Holder); // !important, use before only
		var SCMS_Sticky_Menu_Holder_Clone = $('.sneeit-compact-menu-holder-'+SCMS_Index);
		SCMS_Sticky_Menu_Holder_Clone.css({
			'width': SCMS_Sticky_Menu_Holder.css('width'),
			'height': SCMS_Sticky_Menu_Holder.css('height'),
			'padding': SCMS_Sticky_Menu_Holder.css('padding'),
			'margin': SCMS_Sticky_Menu_Holder.css('margin'),
			'position': SCMS_Sticky_Menu_Holder.css('position'),
			'top': SCMS_Sticky_Menu_Holder.css('top'),
			'left': SCMS_Sticky_Menu_Holder.css('left'),
			'bottom': SCMS_Sticky_Menu_Holder.css('bottom'),
			'right': SCMS_Sticky_Menu_Holder.css('right'),
			'display' : 'none'
		});
		var SCMS_Last_Window_Scroll_Top = 0;
		
		/* When Scrolling */
		$(window).scroll(function() {			
			
			var holder_bottom = 0;
			if (SCMS_Sticky_Menu_Scroller.is('.sneeit-compact-menu-sticky')) {
				holder_bottom = SCMS_Sticky_Menu_Holder_Clone.offset().top + SCMS_Sticky_Menu_Holder_Clone.height();
			} else {
				holder_bottom = SCMS_Sticky_Menu_Holder.offset().top + SCMS_Sticky_Menu_Holder.height();
			}
			var window_top = $(window).scrollTop();
			
			if (window_top > holder_bottom) {
				switch (SCMS_Sticky_Menu) {
				case 'up':
					if (window_top < SCMS_Last_Window_Scroll_Top) {
						scms_sticky_menu_enable(SCMS_Sticky_Menu_Holder, SCMS_Sticky_Menu_Holder_Clone, SCMS_Sticky_Menu_Scroller);
					} else {
						scms_sticky_menu_disable(SCMS_Sticky_Menu_Holder, SCMS_Sticky_Menu_Holder_Clone, SCMS_Sticky_Menu_Scroller);		
					}
					break;

				case 'down':
					if (window_top > SCMS_Last_Window_Scroll_Top) {
						scms_sticky_menu_enable(SCMS_Sticky_Menu_Holder, SCMS_Sticky_Menu_Holder_Clone, SCMS_Sticky_Menu_Scroller);
					} else {
						scms_sticky_menu_disable(SCMS_Sticky_Menu_Holder, SCMS_Sticky_Menu_Holder_Clone, SCMS_Sticky_Menu_Scroller);		
					}
					break;

				default:
					scms_sticky_menu_enable(SCMS_Sticky_Menu_Holder, SCMS_Sticky_Menu_Holder_Clone, SCMS_Sticky_Menu_Scroller);
					break;
				}	
			} else {
				scms_sticky_menu_disable(SCMS_Sticky_Menu_Holder, SCMS_Sticky_Menu_Holder_Clone, SCMS_Sticky_Menu_Scroller);
			}
			SCMS_Last_Window_Scroll_Top = window_top;
		});
	});
}) (jQuery);