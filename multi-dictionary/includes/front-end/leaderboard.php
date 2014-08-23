<?php

/**
 * Leaderboard Shortcode
 * 
 */
 
// Initialize the dictionary
$mld_leaderboard = new mld_Dictionary();	
$mld_leaderboard->get_translations();

$leaderboard = $mld_leaderboard->create_leaderboard();