<?php

/**
 * Vote
 * 
 * Add Up or Down vote if user is logged in
 * Post Meta: _mld_upvote, user_id
 *            _mld_downvote, user_id
 */ 

global $wp_query;

if ( is_user_logged_in() && isset($_GET['mld_upvote']) || isset($_GET['mld_downvote']) ) { 

	$mld_user_id = get_current_user_id();
	
	if( isset($_GET['mld_upvote']) ) {
	// Up vote
		$mld_translation_id_to_vote = (int) $_GET['mld_upvote'];
		delete_post_meta($mld_translation_id_to_vote, '_mld_upvote', $mld_user_id );
		delete_post_meta($mld_translation_id_to_vote, '_mld_downvote', $mld_user_id );
		add_post_meta($mld_translation_id_to_vote, '_mld_upvote', $mld_user_id, false );
	} else {
	// Down vote
		$mld_translation_id_to_vote = (int) $_GET['mld_downvote'];
		delete_post_meta($mld_translation_id_to_vote, '_mld_upvote', $mld_user_id );
		delete_post_meta($mld_translation_id_to_vote, '_mld_downvote', $mld_user_id );
		add_post_meta($mld_translation_id_to_vote, '_mld_downvote', $mld_user_id, false );
	}

}