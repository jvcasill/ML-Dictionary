<?php

/**
 * Create Admin Pages
 * 
 * Create Admin Pages for the following:
 * - Main
 * - Settings
 * - Translations
 * --- More to come (voting, search, etc)
 */ 

/**
 * Create Main Admin Page
 */

add_action( 'admin_menu', 'register_mld_main_admin_page' );

function register_mld_main_admin_page(){
	add_menu_page( 'Multilingual Dictionary', 'ML Dictionary', 'mld_moderate', 'mld-admin', 'mld_main_admin_page', plugins_url().'/multi-dictionary/images/dictionary-icon.png', '16' );
}

function mld_main_admin_page() {
	//include_once (dirname(__FILE__) . '/admin-pages/admin.php');
	include_once (dirname(__FILE__) . '/admin-pages/translations.php');
}

/**
 * Create Moderate Page
 */

add_action( 'admin_menu', 'register_mld_moderate_admin_page' );

function register_mld_moderate_admin_page(){
	if ( !is_super_admin() ) {
		add_submenu_page( 'mld-admin', 'ML Dictionary Translations Moderation', 'Moderate', 'mld_moderate', 'mld-moderate', 'mld_moderate_admin_page' );
	}
}

function mld_moderate_admin_page() {
	include_once (dirname(__FILE__) . '/admin-pages/moderate.php');
}

/**
 * Create Add Translation Admin Page
 */

add_action( 'admin_menu', 'register_mld_manage_translation_admin_page' );

function register_mld_manage_translation_admin_page(){
	add_submenu_page( 'mld-admin', 'ML Dictionary Add Translation', 'Add Translation', 'mld_moderate', 'mld-manage-translation', 'mld_manage_translation_admin_page' );
}

function mld_manage_translation_admin_page() {
	include_once (dirname(__FILE__) . '/admin-pages/manage-translation.php');
}

/**
 * Create Settings Admin Page
 */

add_action( 'admin_menu', 'register_mld_settings_admin_page' );

function register_mld_settings_admin_page(){
	// Only show the settings page for the Super Admin
	if ( is_super_admin() ) {
		add_submenu_page( 'mld-admin', 'ML Dictionary Settings', 'Settings', 'mld_moderate', 'mld-settings', 'mld_settings_admin_page' );
	}
}

function mld_settings_admin_page() {
	include_once (dirname(__FILE__) . '/admin-pages/settings.php');
}

/**
 * Create Translations Admin Page
 */
 
/*

Remove this and use it as the main plugin page.

add_action( 'admin_menu', 'register_mld_translations_admin_page' );

function register_mld_translations_admin_page(){
	add_submenu_page( 'mld-admin', 'ML Dictionary Translations', 'View Translations', 'manage_options', 'mld-translations', 'mld_translations_admin_page' );
}

function mld_translations_admin_page() {
	include_once (dirname(__FILE__) . '/admin-pages/translations.php');
}
*/