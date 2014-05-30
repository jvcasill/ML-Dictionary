<?php

/**
 * Create Post Types
 * 
 * Create Custom Post Types for the following:
 * - Language
 * - Translation
 * - Field
 */ 

/**
 * Register "Language" Post Type
 */

add_action( 'init', 'mld_language_init' );

function mld_language_init() {
	$labels = array(
		'name'               => _x( 'Languages', 'post type general name', 'multilingual-dictionary' ),
		'singular_name'      => _x( 'Language', 'post type singular name', 'multilingual-dictionary' ),
		'menu_name'          => _x( 'Languages', 'admin menu', 'multilingual-dictionary' ),
		'name_admin_bar'     => _x( 'Language', 'add new on admin bar', 'multilingual-dictionary' ),
		'add_new'            => _x( 'Add New', 'language', 'multilingual-dictionary' ),
		'add_new_item'       => __( 'Add New Language', 'multilingual-dictionary' ),
		'new_item'           => __( 'New Language', 'multilingual-dictionary' ),
		'edit_item'          => __( 'Edit Language', 'multilingual-dictionary' ),
		'view_item'          => __( 'View Language', 'multilingual-dictionary' ),
		'all_items'          => __( 'All Languages', 'multilingual-dictionary' ),
		'search_items'       => __( 'Search Languages', 'multilingual-dictionary' ),
		'parent_item_colon'  => __( 'Parent Languages:', 'multilingual-dictionary' ),
		'not_found'          => __( 'No languages found.', 'multilingual-dictionary' ),
		'not_found_in_trash' => __( 'No languages found in Trash.', 'multilingual-dictionary' ),
	);

	$args = array(
		'labels'             => $labels,
		'public'             => false,
		'publicly_queryable' => false,
		'show_ui'            => false,
		'show_in_menu'       => false,
		'query_var'          => false,
		'rewrite'            => array( 'slug' => 'language', 'with_front' => false ),
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title', )
	);

	register_post_type( 'mld_language', $args );
}

/**
* "Translation" Custom Post Type
*/

add_action( 'init', 'mld_translation_init' );

function mld_translation_init() {
	$labels = array(
		'name'               => _x( 'Translations', 'post type general name', 'multilingual-dictionary' ),
		'singular_name'      => _x( 'Translation', 'post type singular name', 'multilingual-dictionary' ),
		'menu_name'          => _x( 'Translations', 'admin menu', 'multilingual-dictionary' ),
		'name_admin_bar'     => _x( 'Translation', 'add new on admin bar', 'multilingual-dictionary' ),
		'add_new'            => _x( 'Add New', 'mld_translation', 'multilingual-dictionary' ),
		'add_new_item'       => __( 'Add New Translation', 'multilingual-dictionary' ),
		'new_item'           => __( 'New Translation', 'multilingual-dictionary' ),
		'edit_item'          => __( 'Edit Translation', 'multilingual-dictionary' ),
		'view_item'          => __( 'View Translation', 'multilingual-dictionary' ),
		'all_items'          => __( 'All Translations', 'multilingual-dictionary' ),
		'search_items'       => __( 'Search Translation', 'multilingual-dictionary' ),
		'parent_item_colon'  => __( 'Parent Translation:', 'multilingual-dictionary' ),
		'not_found'          => __( 'No translation found.', 'multilingual-dictionary' ),
		'not_found_in_trash' => __( 'No translations found in Trash.', 'multilingual-dictionary' ),
	);

	$args = array(
		'labels'             => $labels,
		'public'             => false,
		'publicly_queryable' => false,
		'show_ui'            => false,
		'show_in_menu'       => false,
		'query_var'          => false,
		'rewrite'            => array( 'slug' => 'translation', 'with_front' => false ),
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title', 'editor', )
	);

	register_post_type( 'mld_translation', $args );
}

/**
 * Register "Field" Post Type
 */

add_action( 'init', 'mld_field_init' );

function mld_field_init() {
	$labels = array(
		'name'               => _x( 'Fields', 'post type general name', 'multilingual-dictionary' ),
		'singular_name'      => _x( 'Field', 'post type singular name', 'multilingual-dictionary' ),
		'menu_name'          => _x( 'Fields', 'admin menu', 'multilingual-dictionary' ),
		'name_admin_bar'     => _x( 'Field', 'add new on admin bar', 'multilingual-dictionary' ),
		'add_new'            => _x( 'Add New', 'mld_field', 'multilingual-dictionary' ),
		'add_new_item'       => __( 'Add New Field', 'multilingual-dictionary' ),
		'new_item'           => __( 'New Field', 'multilingual-dictionary' ),
		'edit_item'          => __( 'Edit Field', 'multilingual-dictionary' ),
		'view_item'          => __( 'View Field', 'multilingual-dictionary' ),
		'all_items'          => __( 'All Fields', 'multilingual-dictionary' ),
		'search_items'       => __( 'Search Fields', 'multilingual-dictionary' ),
		'parent_item_colon'  => __( 'Parent Fields:', 'multilingual-dictionary' ),
		'not_found'          => __( 'No fields found.', 'multilingual-dictionary' ),
		'not_found_in_trash' => __( 'No fields found in Trash.', 'multilingual-dictionary' ),
	);

	$args = array(
		'labels'             => $labels,
		'public'             => false,
		'publicly_queryable' => false,
		'show_ui'            => false,
		'show_in_menu'       => false,
		'query_var'          => false,
		'rewrite'            => array( 'slug' => 'field', 'with_front' => false ),
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title', )
	);

	register_post_type( 'mld_field', $args );
}

/**
 * Register "Soure Type" Post Type
 */

add_action( 'init', 'mld_source_type_init' );

function mld_source_type_init() {
	$labels = array(
		'name'               => _x( 'Source Types', 'post type general name', 'multilingual-dictionary' ),
		'singular_name'      => _x( 'Source Type', 'post type singular name', 'multilingual-dictionary' ),
		'menu_name'          => _x( 'Source Types', 'admin menu', 'multilingual-dictionary' ),
		'name_admin_bar'     => _x( 'Source Type', 'add new on admin bar', 'multilingual-dictionary' ),
		'add_new'            => _x( 'Add New', 'mld_source_type', 'multilingual-dictionary' ),
		'add_new_item'       => __( 'Add New Source Type', 'multilingual-dictionary' ),
		'new_item'           => __( 'New Source Type', 'multilingual-dictionary' ),
		'edit_item'          => __( 'Edit Source Type', 'multilingual-dictionary' ),
		'view_item'          => __( 'View Source Type', 'multilingual-dictionary' ),
		'all_items'          => __( 'All Source Types', 'multilingual-dictionary' ),
		'search_items'       => __( 'Search Source Types', 'multilingual-dictionary' ),
		'parent_item_colon'  => __( 'Parent Source Types:', 'multilingual-dictionary' ),
		'not_found'          => __( 'No source types found.', 'multilingual-dictionary' ),
		'not_found_in_trash' => __( 'No source types found in Trash.', 'multilingual-dictionary' ),
	);

	$args = array(
		'labels'             => $labels,
		'public'             => false,
		'publicly_queryable' => false,
		'show_ui'            => false,
		'show_in_menu'       => false,
		'query_var'          => false,
		'rewrite'            => array( 'slug' => 'source-type', 'with_front' => false ),
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title', )
	);

	register_post_type( 'mld_source_type', $args );
}