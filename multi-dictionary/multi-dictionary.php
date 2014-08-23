<?php
   /*
   Plugin Name: Multilingual Dictionary
   Plugin URI: http://www.lingreference.org
   Description: A multilingual dictionary providing translations of terms between languages along with a user-based voting system for ranking the most appropriate translations.
   Version: 1.1.3
   Author: Joseph Casillas and Zachary Coyne
   Author URI: http://www.lingreference.org
   License: GPL2
   
   Text Domain: multilingual-dictionary
   Function & Post Type Prefix: mld_
   */   
   
   /**
    * Release Notes
	*/ 
   
   /* 1.1.2
    *
	* 1. Include "Reverse" translation for front-end display (ex. for Eng/Span Green->Verde also include Span/Eng Verde->Green submissions.
	*
	*/

   /* 1.1.3
    *
	* 1. Added part of speech dropdown (noun, adjective, verb, adverb)
	* 2. Added 'Source language definition' and 'Target language definition' (optional entries)
	* 3. Added Submitted by option (checkbox user can click to show their name with the entry)
	* 4. Added Total terms count shortcode: [mld_total_term_count /]
	* 5. Added TSV Import
	*
	*/
   

/**
* Activate Multilingual Dictionary
*/ 
function mld_install() {

	// Create the 'dictionary' and 'add-translation' pages
	if( get_page_by_title( 'Dictionary' ) == NULL ) {
		$mld_page_content = '[mld_search_form]<br/><br/>[mld_search_results]';
		mld_create_page( 'Dictionary', $mld_page_content );
	}
	if( get_page_by_title( 'Add Translation' ) == NULL ) {
		$mld_post_parent = get_page_by_title( 'Dictionary' );
		$mld_page_content = '[mld_add_translation]';
		mld_create_page( 'Add Translation', $mld_page_content, $mld_post_parent->ID );
	}		

	mld_create_post_types();
	
	flush_rewrite_rules();	
	
}

/**
* Deactivate Multilingual Dictionary
*/ 
function mld_deactivate() {
	flush_rewrite_rules();
}

/**
* Uninstall Multilingual Dictionary
*/ 
function mld_uninstall() {
	flush_rewrite_rules();
}

/**
* Create Post Types
*/
function mld_create_post_types() {
	include_once ( dirname(__FILE__) . '/includes/post-types.php' );
}
mld_create_post_types();

/**
* Add Post Meta
*/ 
include_once ( dirname(__FILE__) . '/includes/post-meta.php' );

/**
* Create Post Types & Taxonomies
*/ 
function mld_create_taxonomies() {
	//include_once ( dirname(__FILE__) . '/includes/taxonomies.php' );
}

/**
* Create Moderator Role
*/ 

add_action( 'plugins_loaded', 'mld_add_roles' );

function mld_add_roles() {
	
	add_role( 
		'mld_moderator', 
		__( 'Dictionary Moderator'),
		array(
			'read'         => true,  
			'edit_posts'   => false,
			'delete_posts' => false,
			'mld_moderate' => true,
		) 
	);
	
	// Add moderator capability to Super Admin
	$role = get_role( 'administrator' );
	$role->add_cap( 'mld_moderate' );

}

/**
* Create Admin Page(s) -- login Reserved for Moderators and Super Admin
*/   

add_action( 'plugins_loaded', 'mld_user_check' );

function mld_user_check() {

	global $current_user;
	get_currentuserinfo();

	if ( is_super_admin($current_user->ID) || current_user_can('mld_moderate') ) {
		include_once ( dirname(__FILE__) . '/includes/admin-pages.php' );
	}
}

/**
* Dictionary Query Strings (Pernalinks)
*/ 
include_once ( dirname(__FILE__) . '/includes/query-strings.php' );

/**
* Wordpress Alerts
*/ 
include_once ( dirname(__FILE__) . '/includes/alerts.php' );

/**
* Dictionary Functions
*/ 
include_once ( dirname(__FILE__) . '/includes/functions.php' );

/**
* Dictionary Classes
*/ 
include_once ( dirname(__FILE__) . '/includes/class/dictionary.php' );
include_once ( dirname(__FILE__) . '/includes/class/dictionary-users.php' );

/**
* Initialize the dictionary
*/   

add_action( 'wp', 'mld_load_dictionary' );

function mld_load_dictionary() {

	$GLOBALS['mld_dictionary'] = new mld_Dictionary();	
	include_once ( dirname(__FILE__) . '/includes/front-end/search-process.php' );
	include_once ( dirname(__FILE__) . '/includes/front-end/vote.php' );

}

add_action( 'plugins_loaded', 'mld_load_dictionary_voting' );

function mld_load_dictionary_voting() {

	include_once ( dirname(__FILE__) . '/includes/front-end/vote.php' );

}


/**
* Achievements Plugin Integration
*/ 
include_once ( dirname(__FILE__) . '/includes/achievements.php' );


/**
* Dictionary Shortcodes
*/ 
include_once ( dirname(__FILE__) . '/includes/shortcodes.php' );


/**
* Register Admin JS & CSS
*/ 
function mld_load_wp_admin_scripts() {
	wp_register_script( 'mld_general_admin', plugins_url() . '/multi-dictionary/admin/js/general-functions.js', false, '1.0.0' );
	wp_enqueue_script( 'mld_general_admin' );
	
	wp_register_style( 'mld_admin', plugins_url() . '/multi-dictionary/admin/css/styles.css', false, '1.0.0' );
	wp_enqueue_style( 'mld_admin' );
}
add_action( 'admin_enqueue_scripts', 'mld_load_wp_admin_scripts' );

/**
* Register Front-end JS & CSS
*/ 
function mld_load_wp_scripts() {

	wp_register_script( 'mld_general', plugins_url() . '/multi-dictionary/front-end/js/general-functions.js' );

	$logged_in = false;
	if ( is_user_logged_in() ) { $logged_in = true; }
	
	$variables = array( 
		'blog_url' => get_bloginfo('url') ,
		'trailing_slash' => mld_check_trailing_slash(),
		'logged_in' => $logged_in,
	);
	wp_localize_script( 'mld_general', 'mld_variables', $variables );
	wp_enqueue_script( 'mld_general', plugins_url() . '/multi-dictionary/front-end/js/general-functions.js', array( 'jquery' ),	true, '1.0.0' );

	wp_register_script( 'add_translation', plugins_url() . '/multi-dictionary/front-end/js/add-translation.js' );
	
	wp_localize_script( 'add_translation', 'mld_variables', $variables );
	wp_enqueue_script( 'add_translation', plugins_url() . '/multi-dictionary/front-end/js/add-translation.js', array( 'jquery' ),	true, '1.0.0' );

	wp_enqueue_style( 'mld_styles', plugins_url() . '/multi-dictionary/front-end/css/styles.css', false, '1.0.0' );
}
add_action( 'wp_enqueue_scripts', 'mld_load_wp_scripts' );

/**
* Dynamically set page title for search results
*/ 
function mld_custom_title($title) { 
	if ( isset($GLOBALS['mld_dictionary']) && $GLOBALS['mld_dictionary']->exact_match ) {
		return ucfirst($GLOBALS['mld_dictionary']->source_language['name']).' to '.ucfirst($GLOBALS['mld_dictionary']->translation_language['name']).' Translation of '.ucfirst($GLOBALS['mld_dictionary']->term['name']).' &raquo; '.get_bloginfo('name');
	} else {
		return $title;
	}
}
add_filter('wp_title', mld_custom_title, 100);

/**
* Allow shortcodes to work in sidebar
*/ 
add_filter('widget_text', 'do_shortcode');

/**
 * Install, Deactivate and Uninstall Hooks
 */ 

register_activation_hook( __FILE__, 'mld_install' ); 
register_deactivation_hook( __FILE__, 'mld_deactivate' ); 
register_uninstall_hook( __FILE__, 'mld_uninstall' ); 