function sneeit_shortcodes_esc_attr(value) {
	if (value == null) {
		return value;
	}
	value = value.toString();
	return value
         .replace(/\[/gi, "&amp;#91;")
         .replace(/]/gi, "&amp;#93;")
         .replace(/"/gi, "&amp;quot;")
         /*.replace(/'/gi, "&amp;#039;")*/
         .replace(/>/gi, "&amp;gt;")
         .replace(/</gi, "&amp;lt;");
}
function sneeit_shortcodes_box_come_in() {		
	var header_height = $('#sneeit-shortcode-box > .header').height();
	var action_height = $('#sneeit-shortcode-box > .actions').height();
	
	$('#sneeit-shortcode-box').css('height', ($(window).height() - 2 * header_height) + 'px');
	$('#sneeit-shortcode-box > .content').css('height', ($(window).height() - 3 * header_height - action_height) + 'px');
	
	// disable scroll
	$('body').addClass('disabled-scroll');
}
function sneeit_shortcodes_box_go_out(fadeout_delay) {		
	if (typeof(fadeout_delay) == 'undefined') {
		fadeout_delay = 200;
	}
	// remove other things
	$('body').removeClass('disabled-scroll').css('height', '');		
	if (fadeout_delay == 0) {		
		$('#sneeit-shortcode-box').remove();
		$('#sneeit-shortcode-box-overlay').remove();
	} else {
		$('#sneeit-shortcode-box-overlay').fadeOut(fadeout_delay);
		$('#sneeit-shortcode-box').fadeOut(fadeout_delay, function () {
			$('#sneeit-shortcode-box').remove();
			$('#sneeit-shortcode-box-overlay').remove();
		});
	}	
}

// collect data to generate shortcode before out
function sneeit_shortcodes_collect_data(selected_content, container_selector, separator, shortcode_id, shortcode_fields) {
	var shortcode = '';
		
//	search in each container and collect data
	$(container_selector).first().each(function(){
		var the_container = $(this);
		var content = '';
		var attributes = new Object();		
				
		// insert shortcode		
		
		$.each(shortcode_fields, function(field_id, field_declaration) {
//			var field_selector = '#sneeit-shortcode-'+separator+'-'+field_id;
			var field_selector = '[name="sneeit-shortcode-'+separator+'-'+field_id+'"]';
			var field_selector_array = '[name="sneeit-shortcode-'+separator+'-'+field_id+'[]"]';
			
			switch (field_declaration['type']) {				
				case 'content':
				case 'textarea':
					$(the_container).find(field_selector).each(function(){
						content += $(this).val();
					});					
					break;
				case 'checkbox':
					$(the_container).find(field_selector).each(function(){						
						if ($(this).is(':checked')) {
							attributes[field_id] = 'on';
						} else {							
							attributes[field_id] = '';
						}						
					});	
					break;
					
				case 'radio':
					attributes[field_id] = '';
					$(the_container).find(field_selector).each(function(){						
						if ($(this).is(':checked')) {
							attributes[field_id] = $(this).val();
						}		
					});	
					
					break;
					
				case 'categories' :
				case 'tags' :
				case 'users' :
				case 'sidebars' :
				case 'selects' :
					
					$(the_container).find(field_selector).each(function(){
						var field_value = $(this).val();
						if (field_value === null) {
							field_value = '';
						}
						if (typeof(field_value) == 'Object' || typeof(field_value) == 'Array') {
							field_value = field_value.join(',');
						}
						
						attributes[field_id] = sneeit_shortcodes_esc_attr(field_value);						
					});	

					break;
				
				default:
					$(the_container).find(field_selector).each(function(){
						var field_value = $(this).val();
						if (field_value === null) {
							field_value = '';
						}
						
						attributes[field_id] = sneeit_shortcodes_esc_attr(field_value);						
					});
					break;
			}
		});
		
		if (content == '') {
			content = selected_content;
		}		
//		console.log(attributes);
		shortcode += '['+shortcode_id;
		if (!$.isEmptyObject(attributes)) {
			$.each(attributes, function(attribute_name, attribute_value) {
				shortcode += ' '+attribute_name+'="'+attribute_value+'"';
			});
		}
		shortcode += ']'+content + '[/'+shortcode_id+']';		
		$(this).remove();
	});
		
	return shortcode;
}

// main function for shortcode box, add wrapper and header
// the editor is for insert value when using page builder
var Sneeit_Shortcode_Nested_Index = 0;
function sneeit_shortcodes_box(editor, shortcode_id, shortcode_declaration) {
	var html = '';
		
	sneeit_shortcodes_box_go_out();
	
	// HTML for shortcode box
	// ######################
	// open box
	html += '<div id="sneeit-shortcode-box-overlay"></div>';
	html += '<div id="sneeit-shortcode-box" class="sneeit-shortcode-box">';
	
	// header
	html += '<div class="header">'+shortcode_declaration['title']+'<a href="javascript:void(0)" id="sneeit-shortcode-button-box-close"><span class="dashicons dashicons-no-alt"></span></a></div>';
	
	// body content with form
	html += '<div class="main content"><div class="inner">';
	
	html += '<div class="sneeit-shortcode-box-loading-icon"><i class="fa fa-spin fa-spinner"></i></div>';
	
	// close body content of form
	html += '</div></div>';
	
	// actions
	html += '<div class="actions">';
	html += '<a href="javascript:void(0)" id="sneeit-shortcode-button-insert" class="button button-large button-primary">'+Sneeit_Shortcodes.text.insert_shortcode+'</a>';
	html += '<a href="javascript:void(0)" id="sneeit-shortcode-button-cancel" class="button button-large">'+Sneeit_Shortcodes.text.cancel+'</a>';	
	html += '</div>';	
	
	// close box
	html += '</div>';
	
	$(html).appendTo($('body'));
	
	$.post(ajaxurl, {
		action: 'sneeit_shortcodes',
		sub_action: 'control_html',
		shortcode_id: shortcode_id
	}).done(function( data ) {
		$('#sneeit-shortcode-box .main.content .inner').html(data);
		
		$('#sneeit-shortcode-box .sneeit-control').first().addClass('first');
		$('#sneeit-shortcode-nested-box .sneeit-control').first().addClass('first');
			
		// if have nested, we need to create nested ui and actions
		if (typeof(shortcode_declaration['nested']) !== 'undefined') {
			var the_pattern_html = $('#sneeit-shortcode-nested-box').html();
			$('#sneeit-shortcode-nested-box .sneeit-shortcode-nested-box.pattern').remove();
			
			// clone
			if (typeof(editor) !== 'undefined' && typeof(editor['nested']) !== 'undefined') {
				for (var i = 0; i < editor['nested'].length; i++) {
					$('#sneeit-shortcode-nested-box')
					.append(the_pattern_html.replaceAll('__i__', Sneeit_Shortcode_Nested_Index));
					Sneeit_Shortcode_Nested_Index++;
				}
			} else { // just init default
				$('#sneeit-shortcode-nested-box')
					.append(the_pattern_html.replaceAll('__i__', Sneeit_Shortcode_Nested_Index));
				Sneeit_Shortcode_Nested_Index++;
			}
			
			
			
				
			// Apply effects and transition for shortcode box
			// ##############################################
			
			$('#sneeit-shortcode-nested-box').sortable().disableSelection();			
			
			// collaps / expand nested box
			$('#sneeit-shortcode-nested-box').on('click', '.sneeit-shortcode-nested-box-close-button', function(){				
				var par = $(this).parents('.sneeit-shortcode-nested-box');
				if (par.is('.collapsed')) {					
					par.removeClass('collapsed');
				} else {					
					par.addClass('collapsed');
				}
			});

			// remove nested
			$('#sneeit-shortcode-nested-box').on('click', '.sneeit-shortcode-button-remove-nested', function(){
				$(this).parents('.sneeit-shortcode-nested-box').remove();
			});
			
			// add new nested box
			$('#sneeit-shortcode-button-new-nested').click(function(){
				$('#sneeit-shortcode-nested-box').append(the_pattern_html.replaceAll('__i__', Sneeit_Shortcode_Nested_Index));
				Sneeit_Shortcode_Nested_Index++;
				$.event.trigger({type: 'sneeit_controls_init'});
				$('#sneeit-shortcode-nested-box').sortable().disableSelection();
			});
		}
		
		
		// fill up value
		if (typeof(editor) !== 'undefined') {
			$.each(shortcode_declaration['fields'], function(field_id, field_declaration) {
				if (field_declaration['type'] == 'content' && 
					editor.selection.getContent()) {
					field_declaration['value'] = editor.selection.getContent();
				}
				if ('value' in field_declaration) {			
					$('#sneeit-shortcode-box .sneeit-control[data-key="'+field_id+'"] .sneeit-control-value').each(function(){
						if ($(this).is('.sneeit-control-checkbox-value')) {
							if (field_declaration['value']) {
								$(this).prop('checked', true);
							} else {
								$(this).prop('checked', false);
							}
						} else if ($(this).is('.sneeit-control-radio-value')) {
							$(this).filter('[value=' + field_declaration['value'] +']').attr('checked', true);
						} else if ($(this).is('select[data-value]')) {
							$(this).attr('data-value', field_declaration['value']);
						} else {
							$(this).val(field_declaration['value']);
						}
					});
				}			
			});
		}
		
		// fill up value for nested
		if (typeof(editor) !== 'undefined' && typeof(editor['nested']) !== 'undefined' && typeof(shortcode_declaration['nested']) !== 'undefined') {
			for (var i = 0; i < editor['nested'].length; i++) {
				$.each(shortcode_declaration['nested'], function (nested_shortcode_id, nested_shortcode_declaration) {
					$.each(nested_shortcode_declaration['fields'], function (nested_shortcode_field_id, nested_shortcode_field_declaration) {
						$('#sneeit-shortcode-nested-box .sneeit-shortcode-nested-box').eq(i).find('.sneeit-shortcode-nested-box-item-'+nested_shortcode_id+' .sneeit-control[data-key="'+nested_shortcode_field_id+'"] .sneeit-control-value').each(function(){

							if ($(this).is('input[type="checkbox"]')) {
								if (editor['nested'][i][nested_shortcode_id][nested_shortcode_field_id]) {
									$(this).prop('checked', true);
								} else {
									$(this).prop('checked', false);
								}
							} else {
								$(this).val(editor['nested'][i][nested_shortcode_id][nested_shortcode_field_id]);
							}
						});
						
					});					
				});				
			}
		}
		
	
		// remake field ui
		$.event.trigger({type: 'sneeit_controls_init'});
		
		
		// shortcode box button actions
		// ############################
		$('#sneeit-shortcode-button-cancel, #sneeit-shortcode-box-overlay, #sneeit-shortcode-button-box-close').click(function(){		
			sneeit_shortcodes_box_go_out();
		});

		$('#sneeit-shortcode-button-insert').click(function() {
			// we will take action after 200ms, wating any iris color box finish toggle action
			$('#sneeit-shortcode-box').fadeOut(200, function () {
				var shortcode = '';
				var nested_shortcode = editor.selection.getContent();

				// get nested shortcode
				if (typeof(shortcode_declaration['nested']) != 'undefined') {
					nested_shortcode = '';
					var nested_length = $('.sneeit-shortcode-nested-box-item').length; // must hold nested length because we will remove elements
					for (var i = 0; i < nested_length; i++) {
						$.each(shortcode_declaration['nested'], function (nested_shortcode_id, nested_shortcode_declaration) {
							if (!$.isEmptyObject(nested_shortcode_declaration['fields'])) {						
								nested_shortcode += sneeit_shortcodes_collect_data(
									'', 
									'.sneeit-shortcode-nested-box-item-'+nested_shortcode_id,
									'nested-field',
									nested_shortcode_id, 
									nested_shortcode_declaration['fields']
								);
							} else {
								nested_shortcode += '['+nested_shortcode_id+']'+editor.selection.getContent()+'[/'+nested_shortcode_id+']';
							}					
						});	
					}
				}

				shortcode = sneeit_shortcodes_collect_data(
					nested_shortcode, 
					'#sneeit-shortcode-box',
					'field',
					shortcode_id, 
					shortcode_declaration['fields']
				);		
				editor.execCommand('mceInsertContent', 0, shortcode);
				sneeit_shortcodes_box_go_out(0);
			});
		});
		
		$('#sneeit-shortcode-box > .actions, #sneeit-shortcode-button-box-close').show();		

		
		// show the box
		sneeit_shortcodes_box_come_in();		
		$(window).resize(function () {
			if ($('#sneeit-shortcode-box').length) {
				sneeit_shortcodes_box_come_in();
			}		
		});
		
		
	});	
	
	
}