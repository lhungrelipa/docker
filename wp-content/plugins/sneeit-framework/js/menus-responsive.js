(function ($) {
	if (typeof(Sneeit_Responsive_Menus) == 'undefined') {
		return;
	}
		
	function sneeit_responsive_menus() {
		$.each(Sneeit_Responsive_Menus, function (id, declare){
			// check if we have all responsive data
			if (!('selector' in declare)) {
				return;
			}
			var selector = $(declare.selector);
			if (selector.length == 0) {
				// already responsive or not exist
				return;
			}
			
			var max_width = 1024;
			if ('max_width' in declare) {
				max_width = declare.max_width;
			}
			
			if (!isNaN(max_width)) {
				max_width = Number(max_width);
			}
			
			// check if we need to init responsive or not
			if ($('.sneeit-responsive-menu-toggle-'+id).length == 0) {
				// add toggle button
				selector.addClass('sneeit-responsive-menu-'+id);
				var btn_html = '<a href="javascript:void(0)" data-id="'+id+'" class="sneeit-responsive-menu-toggle sneeit-responsive-menu-toggle-'+id+' ' + id + '-responsive-menu-toggle">';
				if ('text' in declare) {
					btn_html += declare.text;
				} else {					
					btn_html += '<i class="fa fa-bars"></i>';
				}				
				btn_html += '</a>';
				
				$(btn_html).insertBefore(selector);
				
				// responsive menu effects
				$('.sneeit-responsive-menu-toggle-'+id).click(function(){					
					var id = $(this).attr('data-id');
					var selector = $('.sneeit-responsive-menu-'+id);
					if (selector.is('.sneeit-responsive-menu-expanded')) {						
						selector
							.removeClass('sneeit-responsive-menu-expanded')
							.addClass('sneeit-responsive-menu-collapsed');
					} else {
						console.log('expanded');
						selector
							.removeClass('sneeit-responsive-menu-collapsed')
							.addClass('sneeit-responsive-menu-expanded');
					}
				});
			}
			
			// check if we need to enable or disable responsive			
			if ($(window).width() < max_width) {
				sneeit_responsive_menus_enable(id);
			} else {
				sneeit_responsive_menus_disable(id);
			}			
		});
	}
	function sneeit_responsive_menus_enable(id) {
		var declare = Sneeit_Responsive_Menus[id];
		var selector = $(declare.selector);
		selector.addClass('sneeit-responsive-menu ' + id + '-responsive-menu').removeClass('sneeit-responsive-menu-collapsed sneeit-responsive-menu-expanded');
		$('.sneeit-responsive-menu-toggle-'+id).show();		
	}
	function sneeit_responsive_menus_disable(id) {
		var declare = Sneeit_Responsive_Menus[id];
		var selector = $(declare.selector);
		selector.removeClass('sneeit-responsive-menu sneeit-responsive-menu-collapsed sneeit-responsive-menu-expanded ' + id + '-responsive-menu');
		$('.sneeit-responsive-menu-toggle-'+id).hide();
	}
	
	$(window).resize(function() {
		sneeit_responsive_menus();
	});
	sneeit_responsive_menus();
}) (jQuery);