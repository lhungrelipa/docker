// http://stackoverflow.com/questions/14199681/jquery-ui-sortable-move-clone-but-keep-original
// http://jsfiddle.net/v265q/
var sneeit_page_builder_editing = false;
jQuery(function($) {	
		
	
	function sneeit_page_builder_come_in () {
		sneeit_page_builder_editing = true;		

		// update content before scanning
		if (typeof(switchEditors) != 'undefined') {
			switchEditors.go('content', 'html');			
		}

		$('#wp-content-wrap')
			.removeClass('html-active')
			.removeClass('tmce-active')
			.addClass('sneeit-page-builder-active');
	
		setTimeout(function () {
			$('#wp-content-wrap').removeClass('html-active');
		}, 100);

		var processor_selector = '#sneeit-page-builder-workspace';
		
		// move content to work space to process
		$(processor_selector).html(sneeit_page_builder_shortcode_to_html($('#content').val(), Sneeit_Shortcodes.declaration, 0));

		// mark all raw shortcode div with an attribute, we will remove it after wrapped
		$(processor_selector+' div[data-sneeit_shortcode_id]').each(function() {
			$(this).attr('data-sneeit_shortcode_id_pending', $(this).attr('data-sneeit_shortcode_id'));
		});
		// apply wrapper for the converted shortcode		
		$.each(Sneeit_Shortcodes.declaration, function (shortcode_id, shortcode_declaration) {
			// allow scanning nested max levels
			for (var i = 0; i < Sneeit_PageBuilder_Options.max_nested_level; i++) {
				if (!$(processor_selector+' div[data-sneeit_shortcode_id_pending="'+shortcode_id+'"]').length) {
					// scanned all shortcode with this id within max levels
					return;
				}
				$(processor_selector+' div[data-sneeit_shortcode_id_pending="'+shortcode_id+'"]').each(function () {
					// mark this was processed
					$(this).removeAttr('data-sneeit_shortcode_id_pending');

					// then process it
					var shortcode_content = this.outerHTML;
					shortcode_content = sneeit_page_builder_workspace_wrapper_html(
						shortcode_id, 
						shortcode_declaration, 
						shortcode_content
					);
					
					// and remove it when done
					$(this).replaceWith(shortcode_content);
				});
			}		
		});

		// add inner sortable handler inside column
		// mark all column element with pending status
		$(processor_selector+' div[data-sneeit_shortcode_id="column"]').attr('data-sneeit_shortcode_id_pending', 'column');

		// scan with max levels for all columns 
		for (var i = 0; i < Sneeit_PageBuilder_Options.max_nested_level; i++) {
			if (!$(processor_selector+' div[data-sneeit_shortcode_id_pending="column"]').length) {
				// processed all
				break;
			}

			// still have? wrap it content for ui-sortable
			$(processor_selector+' div[data-sneeit_shortcode_id_pending="column"]').each(function () {
				// mark this was processed
				$(this).removeAttr('data-sneeit_shortcode_id_pending');

				// then process it
				var shortcode_inner_content = $(this).html();
				$(this).html('<div class="sneeit-page-builder-workspace-box-content-column-linked-sortable sneeit-page-builder-linked-sortable">'+shortcode_inner_content+'</div>');
			});
		}
		

		// apply sortable wrapper for column shortcodes
		sneeit_page_builder_apply_sortable();
	}
	
	// interact with the default UI of wordpress post editor toolbar
	$('#wp-content-wrap .wp-editor-tabs').prepend('<button type="button" id="content-sneeit-page-builder" class="wp-switch-editor switch-sneeit-page-builder">'+Sneeit_PageBuilder_Options.text.Page_builder+'</button>');

	// page builder come in
	// ####################
	// when start, if has shortcode list, just enter mode
//	var sneeit_content_has_shortcode = false;
//	var start_content = $('#content').val();	
//	$.each(Sneeit_Shortcodes.declaration, function (shortcode_id, shortcode_declaration) {
//		if (sneeit_content_has_shortcode || !start_content) {
//			return;
//		}
//		if (start_content.indexOf(shortcode_id) != -1) {
//			sneeit_content_has_shortcode = true;
//			sneeit_page_builder_come_in();
//		}
//	});
	
	// when click tab
	$('#content-sneeit-page-builder').click(function () {
		sneeit_page_builder_come_in();
	});
	

	// page builder come out when click other editor tab
	// ################################################
	$('#content-tmce, #content-html').click(function () {
		$('#wp-content-wrap').removeClass('sneeit-page-builder-active');
		if ($(this).is('#content-tmce')) {
			$('#wp-content-wrap').addClass('tmce-active');
		} else {
			$('#wp-content-wrap').addClass('html-active');
		}
		sneeit_page_builder_editing = false;
	});

	// init start ui for sneeit page builder box
	var toolbar_content = '<div id="sneeit-page-builder-toolbar-tab"><ul>'
	+sneeit_page_builder_box_toolbar_tab_columns('header')
	+sneeit_page_builder_box_toolbar_tab_shortcodes('header')
	+'</ul>'
	+sneeit_page_builder_box_toolbar_tab_columns('content')
	+sneeit_page_builder_box_toolbar_tab_shortcodes('content')
	+'</div>'

	var workspace_content = '';

	sneeit_page_builder_box(toolbar_content, workspace_content);

	// apply tab ui to toolbar tab
	$('#sneeit-page-builder-toolbar-tab').tabs();



	// insert column element
	// #####################
	$('.sneeit-page-builder-toolbar-tab-content-columns-button').click(function () {		
		$('#sneeit-page-builder-workspace').prepend(
			sneeit_page_builder_workspace_wrapper_html(
				'column', 
				Sneeit_Shortcodes.declaration['column'], 
				'<div data-sneeit_shortcode_id="column" width="'+$(this).attr('data-width')+'"><div class="sneeit-page-builder-workspace-box-content-column-linked-sortable sneeit-page-builder-linked-sortable"></div></div>'
			)
		);	
		sneeit_page_builder_apply_sortable();
	});

	// insert shortcode element
	// #####################
	$('.sneeit-page-builder-toolbar-tab-shortcodes-button').click(function () {
		var shortcode_id = $(this).attr('data-shortcode_id');
		var shortcode_declaration = Sneeit_Shortcodes.declaration[shortcode_id];
		var shortcode_content = '<div data-sneeit_shortcode_id="'+shortcode_id+'"">';
		if ('nested' in shortcode_declaration) {
			$.each(shortcode_declaration['nested'], function (nested_shortcode_id, nested_shortcode_declaration) {
				shortcode_content += '<div data-sneeit_shortcode_id="'+nested_shortcode_id+'""></div>';
			});
		}
		shortcode_content += '</div>';
		$('#sneeit-page-builder-workspace').prepend(
			sneeit_page_builder_workspace_wrapper_html (
				shortcode_id, 
				Sneeit_Shortcodes.declaration[shortcode_id],
				shortcode_content
			)
		);
	});
	
	// fixed tool bar when scrolling page
	if (0) {
		var sneeit_pbtb = $('#sneeit-page-builder-toolbar');		
		var sneeit_wp_editor_tab = $('.wp-editor-tabs');
		var sneeit_pbtb_wrapper = $('#sneeit-page-builder-workspace-wrapper');
		if (sneeit_pbtb.length && 
			sneeit_wp_editor_tab.length && 
			sneeit_pbtb_wrapper.length) {		
			$(window).scroll(function () {
				$('#wp-content-editor-tools').css('display','block');
				var sneeit_pbtb_top = (sneeit_wp_editor_tab.offset().top + sneeit_wp_editor_tab.height()) - $(window).scrollTop();

				var sneeit_pbtb_wrapper_bottom = sneeit_pbtb_wrapper.offset().top + sneeit_pbtb_wrapper.height();
				var sneeit_pbtb_bottom = sneeit_pbtb_top + sneeit_pbtb.height();
				var sneeit_pbtb_width = sneeit_pbtb.width();
				var sneeit_window_top = $(window).scrollTop();
				var sneeit_window_bottom_half = sneeit_window_top + $(window).height() / 2;
				if (sneeit_window_top > sneeit_pbtb_bottom) {
					if (sneeit_pbtb_wrapper_bottom > sneeit_window_bottom_half) {
						$('#wp-content-editor-tools').css('display','block');
						sneeit_pbtb.css({
							'position' : 'fixed',
							'top' : sneeit_pbtb_top + 'px',
							'width' : sneeit_pbtb_width + 'px'
						});					
					} else {
						$('#wp-content-editor-tools').css('display','none');
						sneeit_pbtb.css({
							'position' : 'absolute',
							'top' : sneeit_pbtb.offset().top + 'px',
							'width' : sneeit_pbtb_width + 'px'
						});					
					}				
				} else {
					$('#wp-content-editor-tools').css('display','block');
					sneeit_pbtb.css({
						'position' : 'static',
						'top' : 'auto',
						'width' : 'auto'
					});

				}
			});
			$(window).resize(function() {
				if (sneeit_pbtb.css('position') == 'fixed') {
					sneeit_pbtb.css('width', $('#sneeit-page-builder-box').width());				
				}
			});		
		}
	}
	
	if (Sneeit_PageBuilder_Declaration.nested) {
		$('#sneeit-page-builder-box').addClass('enabled-nested');
	} else {
		$('#sneeit-page-builder-box').addClass('disabled-nested');
	}
	
	

	// increase/decrease width
	$(document).on('click', '.sneeit-page-builder-workspace-box-column-change-width', function () {
		var column_box = $(this).parents('.sneeit-page-builder-workspace-box').first();
		var column_width = Number(column_box.attr('data-width'));		
		var column_width_pattern = new Array();
		// convert text column pattern to number pattern
		for (var i = 0; i < Sneeit_PageBuilder_Options.column_pattern.length; i++) {
			column_width_pattern.push(sneeit_page_builder_column_label_to_width_percent(Sneeit_PageBuilder_Options.column_pattern[i]));
		}

		// sort from small to big
		for (var i = 0; i < column_width_pattern.length - 1; i++) {
			for (var j = i+1; j < column_width_pattern.length; j++) {
				if (column_width_pattern[i] > column_width_pattern[j]) {
					var temp = column_width_pattern[i];
					column_width_pattern[i] = column_width_pattern[j];
					column_width_pattern[j] = temp;
				}				
			}
		}
		
		// scan and find the suitable value
		var found_index = -1;
		for (var i = 0; i < column_width_pattern.length; i++) {
			if (column_width == column_width_pattern[i]) {
				if ($(this).is('.sneeit-page-builder-workspace-box-column-increase-width')) {
					if (i < column_width_pattern.length - 1) {
						found_index = i+1;
					}					
				} else if (i > 0) {
					found_index = i - 1;
				}
				break;
			} else if (column_width < column_width_pattern[i]) {
				if ($(this).is('.sneeit-page-builder-workspace-box-column-increase-width')) {
					found_index = i;			
				} else if (i > 0) {
					found_index = i - 1;
				}
				break;
			}
		}
		
		// update data when increase / decrease
		if (found_index != -1) {
			column_width = column_width_pattern[found_index];
			column_box.css('width', column_width+'%').attr('data-width', column_width);
			column_box.find('div[data-sneeit_shortcode_id="column"]').first().attr('width', column_width);
			column_box.find('.sneeit-page-builder-workspace-box-header-column-value').first().text(' '+sneeit_page_builder_width_percent_to_column_label(column_width, '%')+' ');
		}
	});

	// delete boxes
	$(document).on('click', '.sneeit-page-builder-workspace-box-header-button-delete', function () {
		$(this).parents('.sneeit-page-builder-workspace-box').first().remove();
	});

	// clone boxes
	$(document).on('click', '.sneeit-page-builder-workspace-box-header-button-duplicate', function () {		
		var box_container = $(this).parents('.sneeit-page-builder-workspace-box').first();
		if (box_container.length) {
			$(box_container).clone().insertAfter($(box_container));
			sneeit_page_builder_apply_sortable();	
		}
	});

	// when editing boxes
	$(document).on('click', '.sneeit-page-builder-workspace-box-header-button-edit', function () {		
		var box_container = $(this).parents('.sneeit-page-builder-workspace-box').first();
		if (box_container.length) {	
			// get data
			var shortcode_id = box_container.attr('data-shortcode_id');
			var shortcode_declaration = Sneeit_Shortcodes.declaration[shortcode_id];
			var shortcode_holder = box_container.find('div[data-sneeit_shortcode_id="'+shortcode_id+'"]');
			var shortcode_content = '';
			var editor = new Object();

			if ('nested' in shortcode_declaration) {
				// init editor nested fields
				editor.nested = new Array();
				$.each(shortcode_declaration['nested'], function(nested_shortcode_id, nested_shortcode_declaration) {
					var index = 0;

					// scan all nested shortcodes inside
					shortcode_holder.find('div[data-sneeit_shortcode_id="'+nested_shortcode_id+'"]').each(function () {			
						var temp = new Object();
						var nested_shortcode_holder = $(this);
						$.each(nested_shortcode_declaration['fields'], function(nested_shortcode_field_id, nested_shortcode_field_declaration) {
							// collect attribute
							if ('type' in nested_shortcode_field_declaration && nested_shortcode_field_declaration['type'] == 'content') {
								// if content
								temp[nested_shortcode_field_id] = nested_shortcode_holder.html();
							} else {
								// if field value
								var field_value = nested_shortcode_holder.attr(nested_shortcode_field_id);
								if (typeof(field_value) != 'undefined') {									
									temp[nested_shortcode_field_id] = field_value;
								} else {
									if ('default' in nested_shortcode_field_declaration) {
										temp[nested_shortcode_field_id] = nested_shortcode_field_declaration['default'];
									} else {
										temp[nested_shortcode_field_id] = '';
									}
								}
							}
						});

						if (typeof(editor.nested[index]) == 'undefined') {
							editor.nested[index] = new Object();
						}

						// append to editor
						editor.nested[index][nested_shortcode_id] = temp;
						index++;

					});					
				});				
			} else {
				shortcode_content = sneeit_page_builder_workspace_html_to_shortcode(
					shortcode_holder.html(),
					Sneeit_Shortcodes.declaration,
					'.sneeit-page-builder-workspace-box',
					0
				);
				

				// remove sortable list from column
				$('#sneeit-page-builder-dummy').html(shortcode_content);
				$('#sneeit-page-builder-dummy .sneeit-page-builder-workspace-box-content-column-linked-sortable').each(function () {
					var inner_html = $(this).html();
					$(this).replaceWith(inner_html);
				});
				shortcode_content = $('#sneeit-page-builder-dummy').html();
			}
			
			// modify delacration
			if ('fields' in shortcode_declaration) {
				$.each(shortcode_declaration['fields'], function (field_id, field_declaration) {
					if ('type' in field_declaration && field_declaration['type'] == 'content') {
						shortcode_declaration['fields'][field_id]['value'] = shortcode_content;						
					} else {
						var field_value = shortcode_holder.attr(field_id);
						if (typeof(field_value) != 'undefined') {
							shortcode_declaration['fields'][field_id]['value'] = field_value;
						}
					}					
				});	
			}
			
			// create editor			
			editor.selection = new Object();
			editor.selection.getContent = function () {
				return shortcode_content;	
			}
			// mceInsertContent
			editor.execCommand = function (action, position, shortcode_result) {

				var processor_selector = '#sneeit-page-builder-dummy';

				// add to dummy to pre-process before apply to workspace				
				$(processor_selector).html(sneeit_page_builder_shortcode_to_html(shortcode_result, Sneeit_Shortcodes.declaration, 0));

				// mark all raw shortcode div with an attribute, we will remove it after wrapped
				$(processor_selector+' div[data-sneeit_shortcode_id]').each(function() {
					$(this).attr('data-sneeit_shortcode_id_pending', $(this).attr('data-sneeit_shortcode_id'));
				});

				$.each(Sneeit_Shortcodes.declaration, function (shortcode_id, shortcode_declaration) {
					// allow scanning nested max levels
					for (var i = 0; i < Sneeit_PageBuilder_Options.max_nested_level; i++) {
						if (!$(processor_selector+' div[data-sneeit_shortcode_id_pending="'+shortcode_id+'"]').length) {
							// scanned all shortcode with this id within max levels
							return;
						}
						$(processor_selector+' div[data-sneeit_shortcode_id_pending="'+shortcode_id+'"]').each(function () {
							// mark this was processed
							$(this).removeAttr('data-sneeit_shortcode_id_pending');

							// then process it
							var shortcode_content = this.outerHTML;
							shortcode_content = sneeit_page_builder_workspace_wrapper_html(
								shortcode_id, 
								shortcode_declaration, 
								shortcode_content
							);
							
							// and remove it when done
							$(this).replaceWith(shortcode_content);
						});
					}
				});

				// add inner sortable handler inside column
				// mark all column element with pending status
				$(processor_selector+' div[data-sneeit_shortcode_id="column"]').attr('data-sneeit_shortcode_id_pending', 'column');

				// scan with max levels for all columns 
				for (var i = 0; i < Sneeit_PageBuilder_Options.max_nested_level; i++) {
					if (!$(processor_selector+' div[data-sneeit_shortcode_id_pending="column"]').length) {
						// processed all
						break;
					}

					// still have? wrap it content for ui-sortable
					$(processor_selector+' div[data-sneeit_shortcode_id_pending="column"]').each(function () {
						// mark this processed
						$(this).removeAttr('data-sneeit_shortcode_id_pending');

						// then process it
						var shortcode_inner_content = $(this).html();
						$(this).html('<div class="sneeit-page-builder-workspace-box-content-column-linked-sortable sneeit-page-builder-linked-sortable">'+shortcode_inner_content+'</div>');
					});
				}

				box_container.replaceWith($(processor_selector).html());

				// box_container.replaceWith
				sneeit_page_builder_apply_sortable();
				$('html,body').removeClass('disabled-scroll');
			}
			sneeit_shortcodes_box(editor, shortcode_id, shortcode_declaration);			
		}		
	});

	/*UPDATE CONTENT TEXT AREA*/
	// replace shortcode if press below buttons:
	$('#save-post, #post-preview, #publish, #content-tmce, #content-html').mouseenter(function () {
		if (sneeit_page_builder_editing) {
			sneeit_page_builder_workspace_to_content_textarea();	
		}
	});
	$('#save-post, #post-preview, #publish, #content-tmce, #content-html').click(function () {		
		if (sneeit_page_builder_editing) {
			sneeit_page_builder_workspace_to_content_textarea();			
		}
	});
	
	$('#wpwrap').click(function () {
		$('html,body').remove('disabled-scroll'); // just in case the shortcode not work properly
	})
	$('#sneeit-page-builder-toolbar').mouseenter(function () {
		$('html,body').remove('disabled-scroll'); // just in case the shortcode not work properly	
	});
});