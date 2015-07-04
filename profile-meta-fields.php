<?php

/*
Plugin Name: Profile Meta Fields
Plugin URI: http://github.com/limikael/profile-meta-fields
Description: Let users without admin access edit meta information about themselves.
Version: 0.0.1
*/

/**
 * Show a rich text edit field.
 */
function pm_richtext($p) {
	$user=wp_get_current_user();

	if (!$user || !$user->ID)
		return "<i>Not logged in.</i>";

	ob_start();
	$value=get_user_meta($user->ID,$p["field"],TRUE);
	if (!$value)
		$value="";

	wp_editor(
		$value,
		"pmfield_".$p["field"],
		array(
			"editor_height"=>300,
			"media_buttons"=>FALSE
		)
	);

	$content=ob_get_contents();
	ob_end_clean();

	return $content;
}

add_shortcode("pm_richtext", "pm_richtext");
add_shortcode("pm-richtext", "pm_richtext");

/**
 * Render the form.
 */
function pm_form($p, $content) {
	global $pm_have_submit;

	$user=wp_get_current_user();

	if (!$user || !$user->ID)
		return "<i>Not logged in.</i>";

	if (isset($_REQUEST["profile-meta-form"])) {
		foreach ($_REQUEST as $key=>$value) {
			if (substr($key,0,8)=="pmfield_")
				update_user_meta($user->ID,substr($key,8),$value);
		}

		echo "<div class='updated'><p><strong>Profile updated.</strong></p></div>";
	}

	$s="<form class='profile-meta-form' method='post'>";
	$s.="<input type='hidden' name='profile-meta-form' value='1'/>";
	$s.=do_shortcode($content);

	if (!$pm_have_submit)
		$s.=pm_submit(array());

	$s.="</form>";

	return $s;

	return "nothing...";
}

add_shortcode("pm_form", "pm_form");
add_shortcode("pm-form", "pm_form");

/**
 * Render the form.
 */
function pm_submit($p) {
	global $pm_have_submit;

	$pm_have_submit=TRUE;

	return "<input type='submit' value='Save'/>";
}

add_shortcode("pm_submit", "pm_submit");
add_shortcode("pm-submit", "pm_submit");
