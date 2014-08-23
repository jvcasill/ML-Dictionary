<?php

/**
 * ML Dictionary Shortcodes for Front-end Dictionary Display
 * 
 */ 

/**
 * Search Form Shortcode
 */
 
function mld_search_form_shortcode() {
	include ABSPATH . 'wp-content/plugins/multi-dictionary/includes/front-end/search-form.php';
}
 
add_shortcode('mld_search_form', 'mld_search_form_shortcode');

/**
 * Search Form Shortcode: Homepage
 */
 
function mld_search_form_homepage_shortcode() {
	include ABSPATH . 'wp-content/plugins/multi-dictionary/includes/front-end/search-form-homepage.php';
}
 
add_shortcode('mld_search_form_home', 'mld_search_form_homepage_shortcode');

/**
 * Search Form Shortcode: Header
 */
 
function mld_search_form_header_shortcode() {
	include ABSPATH . 'wp-content/plugins/multi-dictionary/includes/front-end/search-form-header.php';
}
 
add_shortcode('mld_search_form_header', 'mld_search_form_header_shortcode');

/**
 * Search Results Shortcode
 */
 
function mld_search_results_shortcode(  ) {
	include ABSPATH . 'wp-content/plugins/multi-dictionary/includes/front-end/search-results.php';
}
 
add_shortcode('mld_search_results', 'mld_search_results_shortcode');

/**
 * Add Translation Shortcode
 */
 
function mld_add_translation_shortcode(  ) {
	include ABSPATH . 'wp-content/plugins/multi-dictionary/includes/front-end/add-translation.php';
}
 
add_shortcode('mld_add_translation', 'mld_add_translation_shortcode');

/**
 * Add Translation Shortcode (Styled for Lingreference.org)
 */
 
function mld_add_translation_styled_shortcode(  ) {
	include ABSPATH . 'wp-content/plugins/multi-dictionary/includes/front-end/add-translation-styled.php';
}
 
add_shortcode('mld_add_translation_ling', 'mld_add_translation_styled_shortcode');

/**
 * Total Term Count Shortcode
 */
 
function mld_total_term_count_shortcode(  ) {
	include ABSPATH . 'wp-content/plugins/multi-dictionary/includes/front-end/total-term-count.php';
	return $total_term_count;
}
 
add_shortcode('mld_total_term_count', 'mld_total_term_count_shortcode');

/**
 * Leaderboard Shortcode
 */
 
function mld_leaderboard_shortcode(  ) {
	include ABSPATH . 'wp-content/plugins/multi-dictionary/includes/front-end/leaderboard.php';
	return $leaderboard;
}
 
add_shortcode('mld_leaderboard', 'mld_leaderboard_shortcode');