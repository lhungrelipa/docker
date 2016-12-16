if (typeof(Sneeit_Shortcodes) != 'undefined' && typeof(Sneeit_Shortcodes['declaration']) != 'undefined') {
	jQuery(function($) {	
				
		(function() {
						
			tinymce.create('tinymce.plugins.Sneeit_Shortcodes', {
				init : function(ed, url) {

					$.each(Sneeit_Shortcodes.declaration, function (shortcode_id, shorcode_declaration) {
						if (typeof(shorcode_declaration['icon']) == 'undefined') {
							return;
						}
						if (typeof(shorcode_declaration['display_callback']) == 'undefined') {
							return;
						}
						var shortcode_button_options = {
							title : shorcode_declaration['title']
						};
						if (sneeit_is_image_src(shorcode_declaration['icon'])) {
							shortcode_button_options['image'] = shorcode_declaration['icon'];
						} else {
							shortcode_button_options['icon'] = sneeit_valid_icon_code(shorcode_declaration['icon']) + ' sneeit-custom-shortcode-icon '+shortcode_id;
						}
						shortcode_button_options['onclick'] = function() {								
							if (!$.isEmptyObject(shorcode_declaration['fields']) || typeof(shorcode_declaration['nested']) != 'undefined') {																
								sneeit_shortcodes_box(ed, shortcode_id, shorcode_declaration);
							} else {										
								ed.execCommand('mceInsertContent', 0, '['+shortcode_id+']'+ed.selection.getContent()+'[/'+shortcode_id+']');
							}
						}
						ed.addButton(shortcode_id, shortcode_button_options);
					});				
				},
			});
			// Register plugin
			tinymce.PluginManager.add( 'sneeit_shortcodes', tinymce.plugins.Sneeit_Shortcodes );
		})();
		
		$('#wpwrap').click(function () {
			$('html,body').remove('disabled-scroll'); // just in case the shortcode not work properly
		})
	});
} /*end checking shortcode action*/

/*
console.log(Sneeit_Shortcodes['title']);			
			tinymce.create('tinymce.plugins.Sneeit_Shortcodes', {
				init : function(ed, url) {
					
					var sneeit_shortcode_menu = new Array();
					$.each(Sneeit_Shortcodes.declaration, function (shortcode_id, shorcode_declaration) {
						if ('column' == shortcode_id) {
							return;
						}
						var shortcode_button_options = {
							text : shorcode_declaration['title']
						};
						if (sneeit_is_image_src(shorcode_declaration['icon'])) {
							shortcode_button_options['image'] = shorcode_declaration['icon'];
						} else {
							shortcode_button_options['icon'] = sneeit_valid_icon_code(shorcode_declaration['icon']) + ' sneeit-custom-shortcode-icon '+shortcode_id;
						}
						shortcode_button_options['onclick'] = function() {								
							if (!$.isEmptyObject(shorcode_declaration['fields']) || typeof(shorcode_declaration['nested']) != 'undefined') {																
								sneeit_shortcodes_box(ed, shortcode_id, shorcode_declaration);
							} else {										
								ed.execCommand('mceInsertContent', 0, '['+shortcode_id+']'+ed.selection.getContent()+'[/'+shortcode_id+']');
							}
						}
						sneeit_shortcode_menu.push(shortcode_button_options);
					});
					
					console.log(sneeit_shortcode_menu);
				
					ed.addButton( 'sneeit_shortcodes', {
						type: 'listbox',
						text: 'shortcodes',
						icon: false,
						
					});		
				},
			});
			tinymce.PluginManager.add( 'sneeit_shortcodes', tinymce.plugins.Sneeit_Shortcodes );
			
			
			return;
 */