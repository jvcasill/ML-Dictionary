<?php

/**
 * Process the Search
 * 
 * If a search has been performed, grab the variables and reset the form values
 */ 

global $wp_query;

if( isset($wp_query->query_vars['mld_source_language']) && 
    isset($wp_query->query_vars['mld_translation_language']) && 
	isset($wp_query->query_vars['mld_term']) &&
	strpos($_SERVER['REQUEST_URI'], 'dictionary/add-translation') == false ) {

	$GLOBALS['mld_dictionary']->set_source_language( urldecode($wp_query->query_vars['mld_source_language']) );
	$GLOBALS['mld_dictionary']->set_translation_language( urldecode($wp_query->query_vars['mld_translation_language']) );
	$GLOBALS['mld_dictionary']->set_term( urldecode($wp_query->query_vars['mld_term']) );
	
	$GLOBALS['mld_dictionary']->search = true;
	
	if ( !array_key_exists( $GLOBALS['mld_dictionary']->source_language['id'], $GLOBALS['mld_dictionary']->languages['id_index'] ) ||
	     !array_key_exists( $GLOBALS['mld_dictionary']->translation_language['id'], $GLOBALS['mld_dictionary']->languages['id_index'] ) ) {
		$wp_query->set_404();
	} elseif ( $GLOBALS['mld_dictionary']->translation_language['id'] == $GLOBALS['mld_dictionary']->source_language['id'] ) {
		// Can't translate between the same languages
		$wp_query->set_404();
	} elseif ( !$GLOBALS['mld_dictionary']->get_translations('1') ) {
		// Proper language selection but term not found -- send 404 header and still show dictionary page (for adding term or showing similar terms)
		//header("HTTP/1.0 404 Not Found");
		status_header(404);
	}
	
}