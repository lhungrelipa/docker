<?php
// $code can be anything: full tag, full icon code, combined icon code, short icon code, ...
add_filter('sneeit_get_font_awesome_tag', 'sneeit_filter_get_font_awesome_tag', 1, 1);
add_filter('sneeit_font_awesome_tag', 'sneeit_filter_get_font_awesome_tag', 1, 1);
function sneeit_filter_get_font_awesome_tag($code) {
	// validate code
	$code = strtolower($code);
	$n0 = ord('0');
	$n9 = ord('9');
	$a  = ord('a');
	$z  = ord('z');
	$A  = ord('A');
	$Z  = ord('Z');
	$m  = ord('-');
	$s  = ord(' ');
//	$u  = ord('_');
	for ($i = 0; $i < strlen($code); $i++) {
		$c = ord((string) $code[$i]);
		if ($c >= $n0 && $c <= $n9 ||
			$c >= $a && $c <= $z ||
			$c >= $A && $c <= $Z ||
			$c == $m || $c == $s) {
			continue;
		}
		$code = substr($code, 0, $i).'_'.substr($code, $i+1);
	}
	
	$code = 'fa-'.implode(' fa-', explode(' ', trim(str_replace(array('_', 'fa-', 'fa'), '', $code))));
		
	// generate
	return '<i class="fa '.$code.'"></i>';
}




function sneeit_get_dashicons_tag($code) {
	// validate code
	$code = strtolower($code);
	$n0 = ord('0');
	$n9 = ord('9');
	$a  = ord('a');
	$z  = ord('z');
	$A  = ord('A');
	$Z  = ord('Z');
	$m  = ord('-');
	$u  = ord('_');
	for ($i = 0; $i < strlen($code); $i++) {
		$c = ord((string) $code[$i]);
		if ($c >= $n0 && $c <= $n9 ||
			$c >= $a && $c <= $z ||
			$c >= $A && $c <= $Z ||
			$c == $m || $c == $u) {
			continue;
		}
		$code = substr($code, 0, $i).'#'.substr($code, $i+1);
	}
	
	$code = str_replace('#', '', $code);
	$code = str_replace('dashicons-', '', $code);
	$code = 'dashicons-' . str_replace('dashicons', '', $code);
	
	// generate
	return '<i class="dashicons '.$code.'"></i>';
}