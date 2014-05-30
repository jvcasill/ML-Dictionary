<?php

/**
 * Custom Query Strings (Pemalinks) for ML Dictionary
 * 
 * Build the custom URL for: http://www.site.com/dictionary/source-language/translation-language/term
 * This URL will redirect to the /dictionary/ page, wherein we will check to see if there is a translation for the specifid languages and term
 */ 

// ADD IN: Create Dictionary Page automatically and setting for different page to be used in admin

/**
 * Query Variables
 */

function mld_query_vars( $variables ) {
	$variables[] = "mld_source_language";
	$variables[] = "mld_translation_language";
	$variables[] = "mld_term";
	return $variables;
}
 
// hook add_query_vars function into query_vars
add_filter('query_vars', 'mld_query_vars');

/**
 * Rewrite Rules
 */

function mld_rewrite_rules( $rules ) {
	$rewrite_rules = array('dictionary/([^/]+)/([^/]+)/([^/]+)/?$' => 'index.php?pagename=dictionary&mld_source_language=$matches[1]&mld_translation_language=$matches[2]&mld_term=$matches[3]');
	$rules = $rewrite_rules + $rules;
	return $rules;
}
 
add_filter('rewrite_rules_array', 'mld_rewrite_rules');