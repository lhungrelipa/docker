jQuery(document).ready(function($){
	
	// CUSTOM SIDEBARs
	//////////////////////
	if (sneeit_widgets.support_sidebar) {
		// create form to input new custom sidebars
		if (typeof('ajaxurl') != 'undefined') {
			$('.widgets-php h1').append(
				'<a id="sneeit-add-sidebar" class="page-title-action hide-if-no-widgets" href="javascript:void(0)">' +
					sneeit_widgets.text['+ Add New Sidebar'] +
				'</a>'
			);

			var sidebar_format_list = '<select id="sneeit-add-sidebar-format" name="sneeit-add-sidebar-format"><option value="">'+sneeit_widgets.text['Default']+'</option>';
			$.each(sneeit_widgets.sidebar_declaration, function (sidebar_id, sidebar_declaration) {
				sidebar_format_list += '<option value="'+sidebar_id+'">'+sidebar_declaration['name']+'</div>';
			});
			sidebar_format_list += '</select>';


			$('.widgets-php .widget-liquid-left').prepend(
				'<div id="sneeit-add-sidebar-form">'+
					'<div class="sneeit-add-sidebar-error"></div>' +
					'<div class="sneeit-add-sidebar-form-inner">' +
						'<div class="left-col">'+
							'<label for="sneeit-add-sidebar-input">' +
								'<span class="widget-description">'+sneeit_widgets.text['Input Your Sidebar Name']+'</span>'+
								'<input id="sneeit-add-sidebar-input" type="text" placeholder="'+sneeit_widgets.text['Input Your Sidebar Name']+'"/>'+
							'</label>' +
						'</div>' +
						'<div class="right-col">'+
							'<label for="sneeit-add-sidebar-format">'+
								'<span class="widget-description">'+sneeit_widgets.text['Follow Format Of']+'</span>'+
								sidebar_format_list+
							'</label>' +
						'</div>' +
						
						'<div class="clear"></div>' +
					'</div>'+
					'<div class="clear"></div>' +
					'<a id="sneeit-add-sidebar-submit" class="page-title-action hide-if-no-widgets" href="javascript:void(0)">' +
						sneeit_widgets.text['+ Add New Sidebar'] +
					'</a>' +
					'<a id="sneeit-add-sidebar-cancel" class="page-title-action hide-if-no-widgets" href="javascript:void(0)">' +
						sneeit_widgets.text['Cancel'] +
					'</a>' +
				'<div class="clear"></div></div>'
			);

			$('#sneeit-add-sidebar').click(function () {
				$(this).hide();
				$('#sneeit-add-sidebar-form').stop().slideDown();
			});
			$('#sneeit-add-sidebar-cancel').click(function () {
				$('#sneeit-add-sidebar').show();
				$('#sneeit-add-sidebar-form').stop().slideUp();
			});

			$('#sneeit-add-sidebar-submit').click(function () {
				// validate
				var sidebar_name = $('#sneeit-add-sidebar-input').val();
				if (!sidebar_name) {
					$('#sneeit-add-sidebar-error').html('<span>'+sneeit_widgets.text['Your Side Name Is Not Valid']+'</span>');
					return;
				}
				var sidebar_format = $('#sneeit-add-sidebar-format').val();

				// if good, do it
				$.post(ajaxurl, { 
					action: 'sneeit_add_custom_sidebar', 
					name: sidebar_name,
					format: sidebar_format
				}).done(function( data ) {
					if (!data || ((data.indexOf('Warning: ') != -1 || data.indexOf('Fatal error: ') != -1) && data.indexOf(' on line ') != -1)) {
						$('#sneeit-add-sidebar-form').html(
							'<div class="sneeit-add-sidebar-error"><span>'+
								sneeit_widgets.text['Sever Responded an Error Message!']+
								'<br/><br/>' + 
								data+
							'</span></div>'
						);
					} else {						
						location.reload();
					}					
				});
				$('#sneeit-add-sidebar-form').html('<i class="fa fa-cog fa-spin sneeit-custom-sidebar-action-loading-icon"></i>');
			});
		}

		// init UI for new custom sidebars (delete, edit)
		$.each(sneeit_widgets.custom_sidebars, function (sidebar_id, sidebar_declaration) {
			$('#'+sidebar_id).each(function () {
				$(
				'<div class="sidebar-actions">'+
					'<a href="javascript:void(0)" class="sneeit-delete-sidebar" data-id="'+sidebar_id+'">'+
						'<i class="fa fa-trash-o"></i> '+sneeit_widgets.text['Delete Sidebar']+
					'</a>'+
					'<a href="javascript:void(0)" class="sneeit-rename-sidebar" data-id="'+sidebar_id+'">'+
						'<i class="fa fa-i-cursor"></i> '+sneeit_widgets.text['Rename Sidebar']+
					'</a>'+
				'</div>').insertAfter($(this).find('.sidebar-name'));
			});			
		});
		$('.sneeit-delete-sidebar').click(function () {
			var sidebar_id = $(this).attr('data-id');
			if (confirm(sneeit_widgets.text['Are You Sure?'])) {
				$.post(ajaxurl, { 
					action: 'sneeit_delete_custom_sidebar',
					id: sidebar_id
				}).done(function( data ) {						
					if (!data || ((data.indexOf('Warning: ') != -1 || data.indexOf('Fatal error: ') != -1) && data.indexOf(' on line ') != -1)) {
						alert(sneeit_widgets.text['Sever Responded an Error Message!']);
					} else {
						$('#'+sidebar_id).html('<i class="fa fa-cog fa-spin sneeit-custom-sidebar-action-loading-icon"></i>');
						location.reload();
					}						
				});
			}
		});

		$('.sneeit-rename-sidebar').click(function () {
			var sidebar_id = $(this).attr('data-id');
			var sidebar_name = $('#'+sidebar_id).find('.sidebar-name h3').text();
			var new_sidebar_name = prompt(sneeit_widgets.text['Rename Sidebar'], sidebar_name);
			if (new_sidebar_name) {	
				$.post(ajaxurl, { 
					action: 'sneeit_rename_custom_sidebar',
					name: new_sidebar_name,
					id: sidebar_id
				}).done(function( data ) {						
					if (!data || ((data.indexOf('Warning: ') != -1 || data.indexOf('Fatal error: ') != -1) && data.indexOf(' on line ') != -1)) {
						alert(sneeit_widgets.text['Sever Responded an Error Message!']);
					} else {
						$('#'+sidebar_id).html('<i class="fa fa-cog fa-spin sneeit-custom-sidebar-action-loading-icon"></i>');
						location.reload();
					}
				});					
			}				
		});
	}
	
	
	// effect to stick Save and other actions on bottom when scrolling
	function sneeit_widget_action_on_scrolling() {
		$('.widget.open .widget-control-actions').each(function(){
			var w_b = $(window).scrollTop() + $(window).height();
			var par = $(this).parents('.widget.open');
			var w_content = par.find('.widget-content');
			var wc_t = w_content.offset().top;
			var wc_b = wc_t + w_content.height() + 30;
			
			if (w_b < wc_b && w_b > wc_t) {
				$(this).css('width', $(this).width()+'px');
				$(this).addClass('fixed');
			} else {
				$(this).removeClass('fixed');
				$(this).css('width', 'auto');
			}
		});
	}
	$(window).scroll(function() {    	
		sneeit_widget_action_on_scrolling();
	});
	$(document).on('click', '.widget-top *', function(){		
		sneeit_widget_action_on_scrolling();
	});
	
	// effect when click close button
	$(document).on('click', '.widget-control-actions .alignleft a', function(){
		var par = $(this).parents('.widget');
		var w_top = $(window).scrollTop();
		var par_top = par.offset().top;
		var html_pad_top = Number($('html').css('padding-top').replace('px', ''));
		if (par_top - html_pad_top < w_top) {
			$(window).scrollTop(par_top - html_pad_top);
		}
	});

	
});
