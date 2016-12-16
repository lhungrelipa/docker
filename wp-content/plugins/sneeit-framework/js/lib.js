var DIV_CLEAR = '<div style="clear:both;float:none"></div>';

/*STRING LIB*/
// replace all
String.prototype.replaceAll = function(target, replacement) {
	return this.split(target).join(replacement);
};

/*SNEEIT LIB*/
function sneeit_is_image_src(src) {
    return(src.match(/\.(jpeg|jpg|gif|png)$/) != null);
}
function sneeit_slug_to_title(slug) {
	return slug.replace(/_/gi, ' ').replace(/-/gi, ' ').replace(/^[a-z]/, function(m){ return m.toUpperCase() });
}

function sneeit_valid_font_awesome_code(icon_code) {
	var n0 = '0'.charCodeAt(0);
	var n9 = '9'.charCodeAt(0);
	var a  = 'a'.charCodeAt(0);
	var z  = 'z'.charCodeAt(0);
	var A  = 'A'.charCodeAt(0);
	var Z  = 'Z'.charCodeAt(0);
	var m  = '-'.charCodeAt(0);
	var s  = ' '.charCodeAt(0);

	icon_code = icon_code.toLowerCase();
	for (i = 0; i < icon_code.length; i++) {
		c = icon_code.charCodeAt(i);
		if (c >= n0 && c <= n9 ||
			c >=  a && c <= z ||
			c >=  A && c <= Z ||
			c ==  m || c == s) {
			continue;
		}
		icon_code = icon_code.substring(0, i) + '_' + icon_code.substring(i+1);
	}
	
	icon_code = icon_code
					.replaceAll('_', '')
					.replaceAll('fa-', '')
					.replaceAll('fa', '');
	icon_code = icon_code.split(' ');
	return 'fa-'+icon_code.join(' fa-');
}

// include both font awesome and dashicons code
function sneeit_valid_icon_code(icon_code) {
	icon_code = icon_code.toLowerCase();
	if (typeof(jQuery) != 'undefined') {
		jQuery.trim(icon_code);
	}
	if (icon_code.indexOf('fa-') != -1) {
		icon_code = 'fa ' + sneeit_valid_font_awesome_code(icon_code);
	} else {
		if (icon_code.indexOf('dashicons-') == -1) {
			icon_code = 'dashicons-'+icon_code;
		}
		if (icon_code.indexOf('dashicons ') != 0) {
			icon_code = 'dashicons ' + icon_code;
		}		
	}
	if (icon_code.indexOf('icon ') != 0) {
		icon_code = 'icon ' + icon_code;
	}
	return icon_code;
}
function sneeit_is_variable_name_character(character) {
	var character = character.charCodeAt(0);
	if (character >= 'a'.charCodeAt(0) && 
		character <= 'z'.charCodeAt(0) ||
		character >= 'A'.charCodeAt(0) &&
		character <= 'Z'.charCodeAt(0) ||
		character >= '0'.charCodeAt(0) &&
		character <= '9'.charCodeAt(0) ||
		character == '_'.charCodeAt(0)) {
		return true;
	}

	return false;
}
function sneeit_is_slug_name_character(character) {
	var character = character.charCodeAt(0);
	if (character >= 'a'.charCodeAt(0) && 
		character <= 'z'.charCodeAt(0) ||
		character >= 'A'.charCodeAt(0) &&
		character <= 'Z'.charCodeAt(0) ||
		character >= '0'.charCodeAt(0) &&
		character <= '9'.charCodeAt(0) ||
		character == '_'.charCodeAt(0) || 
		character == '-'.charCodeAt(0)) {
		return true;
	}

	return false;
}
function sneeit_parse_json(data) {
	try {
		data = jQuery.parseJSON(data);
	} catch (e) {
		// not JSON
		return false;
	}
	return data;
}

function sneeit_included_cookie() {
	if ('cookie' in document) {
		return true;
	}
	return false;
}
function sneeit_get_cookie(c_name) {
	if (!sneeit_included_cookie()) {
		return '';
	}
    var i,x,y,ARRcookies=document.cookie.split(";");
    for (i=0;i<ARRcookies.length;i++)
    {
        x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
        y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
        x=x.replace(/^\s+|\s+$/g,"");
        if (x==c_name)
        {
            return unescape(y);
        }
    }
	return '';
}
function sneeit_set_cookie(c_name,value,exdays) {
	if (!sneeit_included_cookie()) {
		return false;
	}
    var exdate=new Date();
    exdate.setDate(exdate.getDate() + exdays);
    var c_value=escape(value) + ((exdays==null) ? '' : '; expires='+exdate.toUTCString())+'; path=/';
    document.cookie=c_name + "=" + c_value;
	if (sneeit_get_cookie(c_name) !== value) {
		return false;
	}
	return true;
}
function sneeit_delete_cookie(c_name) {
	if (!sneeit_included_cookie()) {
		return false;
	}
	document.cookie = c_name + '=;expires=Thu, 01 Jan 1970 00:00:01 GMT;';
	return true;
}
