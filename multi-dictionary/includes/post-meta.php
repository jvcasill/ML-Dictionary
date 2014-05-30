<?php

/**
 * Add Post Meta
 * 
 * Add the following post meta values for the "Language" Post Type
 * - Native Spelling (string)
 *
 * Add the following post meta values for the "Translation" Post Type
 * - Source Language ID (int)
 * - Translation Language ID (int)
 * - Translation (string)
 * - Notes (string)
 * - Field ID (int)
 * - Source Type ID (int, multiple, joined with Source)
 * - Source (string, multiple, joined with Sourcy Type ID)
 * - Usage example (string, multiple)
 */ 

/**
 * Languages Meta Box // Create
 */
function mld_languages_add_meta_box() {

	add_meta_box(
		'mld_', // HTML meta box ID
		__( 'Language Details', 'multilingual-dictionary' ), // Meta box title
		'mld_languages_meta_box_callback', // Callback function
		'mld_language' // Post type
	);
	
}
add_action( 'add_meta_boxes', 'mld_languages_add_meta_box' );

/**
 * Languages Meta Box // Display
 */
function mld_languages_meta_box_callback( $post ) {

	wp_nonce_field( 'mld_languages_meta_box', 'mld_languages__meta_box_nonce' );

	$native_spelling = get_post_meta( $post->ID, '_mld_native_spelling', true );

	echo '<label for="native_spelling">';
	_e( 'Native spelling of the language name: ', 'multilingual-dictionary' );
	echo '</label> ';
	echo '<input type="text" id="native_spelling" name="native_spelling" value="' . esc_attr( $native_spelling ) . '" size="25" />';
}

/**
 * Languages Meta Box // Save
 */
function mld_languages_save_meta_box_data( $post_id ) {

	/*
	 * Make sure we should be updating the post meta (not autosave, etc)
	 */

	if ( ! isset( $_POST['mld_languages__meta_box_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( $_POST['mld_languages__meta_box_nonce'], 'mld_languages_meta_box' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( isset( $_POST['post_type'] ) && 'mld_language' == $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}

	if ( isset( $_POST['native_spelling'] ) ) {
		$native_spelling = sanitize_text_field( $_POST['native_spelling'] );
		update_post_meta( $post_id, '_mld_native_spelling', $native_spelling );
	}

}
add_action( 'save_post', 'mld_languages_save_meta_box_data' );



/**
 * Translations Meta Box // Create
 */
function mld_translations_add_meta_box() {

	add_meta_box(
		'mld_', // HTML meta box ID
		__( 'Translation Details', 'multilingual-dictionary' ), // Meta box title
		'mld_translations_meta_box_callback', // Callback function
		'mld_translation' // Post type
	);
	
}
add_action( 'add_meta_boxes', 'mld_translations_add_meta_box' );

/**
 * Translations Meta Box // Display
 */
function mld_translations_meta_box_callback( $post ) {

	wp_nonce_field( 'mld_translations_meta_box', 'mld_translations__meta_box_nonce' );

	$translation['source_language'] = get_post_meta( $post->ID, '_mld_source_language', true );
	$translation['translation_language'] = get_post_meta( $post->ID, '_mld_translation_language', true );
	$translation['translation'] = get_post_meta( $post->ID, '_mld_translation', true );
	$translation['notes'] = get_post_meta( $post->ID, '_mld_notes', true );
	$translation['field'] = get_post_meta( $post->ID, '_mld_field', true );
	$translation['source'] = get_post_meta( $post->ID, '_mld_source', false ); // Multiple entries possible
	$translation['source_type'] = get_post_meta( $post->ID, '_mld_source_type', false ); // Multiple entries possible
	$translation['usage_example'] = get_post_meta( $post->ID, '_mld_usage_example', false ); // Multiple entries possible
	
// Source Language			
	
	// Retrive the languages that have been set
	$args = array( 'post_type' => 'mld_language', 'orderby' => 'title', 'order' => 'ASC' );
	$languages_query = new WP_Query( $args );
				
	// Label
	echo '<label for="mld_source_language">';
	_e( 'Source Language: ', 'multilingual-dictionary' );
	echo '</label> ';
	
	// Drop down menu of (source) languages
	echo '<select id="mld_source_language" name="mld_source_language">';
	if ( $languages_query->have_posts() ) {
		while ( $languages_query->have_posts() ) {
			$languages_query->the_post();
			$selected = false;
			if ($languages_query->post->ID == esc_attr( $translation['source_language'] ) ) { 
				$selected = ' selected '; 
			}
			echo '<option '.$selected.' value="'.$languages_query->post->ID.'">'.get_the_title().'</option>';
		}
	}
    echo '</select><br/>';	
	
	// Rewind posts
	$languages_query->rewind_posts();

// Translation Language
	
	// Label
	echo '<label for="mld_translation_language">';
	_e( 'Translation Language: ', 'multilingual-dictionary' );
	echo '</label> ';
	
	// Drop down menu of (translation) languages
	echo '<select id="mld_translation_language" name="mld_translation_language">';
	if ( $languages_query->have_posts() ) {
		while ( $languages_query->have_posts() ) {
			$languages_query->the_post();
			$selected = false;
			if ($languages_query->post->ID == esc_attr( $translation['translation_language'] ) ) { 
				$selected = ' selected '; 
			}
			echo '<option '.$selected.' value="'.$languages_query->post->ID.'">'.get_the_title().'</option>';
		}
	}
    echo '</select><br/>';
		
// Field	
	
	// Retrive the fields that have been set
	$args = array( 'post_type' => 'mld_field', 'orderby' => 'title', 'order' => 'ASC' );
	$fields_query = new WP_Query( $args );
	
	// Label
	echo '<label for="mld_field">';
	_e( 'Field: ', 'multilingual-dictionary' );
	echo '</label> ';
	
	// Drop down menu of fields
	echo '<select id="mld_field" name="mld_field">';
	if ( $fields_query->have_posts() ) {
		while ( $fields_query->have_posts() ) {
			$fields_query->the_post();
			$selected = false;
			if ($fields_query->post->ID == esc_attr( $translation['field'] ) ) { 
				$selected = ' selected '; 
			}
			echo '<option '.$selected.' value="'.$fields_query->post->ID.'">'.get_the_title().'</option>';
		}
	}
    echo '</select><br/>';

// Translation

	echo '<label for="mld_translation">';
	_e( 'Translation: ', 'multilingual-dictionary' );
	echo '</label> ';
	echo '<input type="text" id="mld_translation" name="mld_translation" value="' . esc_attr( $translation['translation'] ) . '" size="25" /><br/>';

// Notes

	echo '<label for="mld_notes">';
	_e( 'Notes: ', 'multilingual-dictionary' );
	echo '</label> ';
	echo '<input type="text" id="mld_notes" name="mld_notes" value="' . esc_attr( $translation['notes'] ) . '" size="25" /><br/>';
	
// Source(s) (and Source Type(s))	
	
	// Retrive the source types that have been set
	$args = array( 'post_type' => 'mld_source_type', 'orderby' => 'title', 'order' => 'ASC' );
	$source_types_query = new WP_Query( $args );
	
	echo '<div class="mld_source-box">';
	
		echo '<label for="mld_source">';
		_e( 'Source(s): ', 'multilingual-dictionary' );
		echo '</label><br/>';
		for ($source_count=0; $source_count<count($translation["source"]);$source_count++) {
			echo "<input type=\"text\" name=\"mld_source[".$source_count."]\" value=\"".$translation["source"][$source_count]."\" />";
			
				// Drop down menu of source types
				echo '<select id="mld_source_type" name="mld_source_type['.$source_count.']">';
				echo '<option value="">Select Type</option>';
				if ( $source_types_query->have_posts() ) {
					while ( $source_types_query->have_posts() ) {
						$source_types_query->the_post();
						$selected = false;
						if ($source_types_query->post->ID == esc_attr( $translation['source_type'][$source_count] ) ) { 
							$selected = ' selected '; 
						}
						echo '<option '.$selected.' value="'.$source_types_query->post->ID.'">'.get_the_title().'</option>';
					}
				}
				echo '</select><br/>';
				
				$source_types_query->rewind_posts();
				
		}
		$source_count--;
		echo '<br/><a class="mld_add-source" href="" data-key="'.$source_count.'">Add another source.</a><br/>';
		
	echo '</div>';
		
// Usage Example(s)		
		
	echo '<div class="mld_usage-example-box">';
	
		echo '<label for="mld_usage_example">';
		_e( 'Usage Example(s): ', 'multilingual-dictionary' );
		echo '</label><br/>';
		for ($usage_example_count=0; $usage_example_count<count($translation["usage_example"]);$usage_example_count++) {
			echo "<input type=\"text\" name=\"mld_usage_example[".$usage_example_count."]\" value=\"".$translation["usage_example"][$usage_example_count]."\" /><br/>";
		}
		$i--;
		echo '<br/><a class="mld_add-usage-example" href="" data-key="'.$usage_example_count.'">Add another usage example.</a><br/>';
	
	echo '</div>';
	
	// Reset post
	wp_reset_postdata();
	
	?>
    
    <script>
	
	jQuery('document').ready(function(){
	
		// Add additional sources
		var source_count = <?php echo $source_count; ?>;
		var source_field = '';
		jQuery('a.mld_add-source').click(function(e){
			e.preventDefault();
			source_count++;
			if (source_count < 5) {
				source_field = '<input type="text" name="mld_source['+source_count+']" value="" /><?php 
				
				// Drop down menu of source types
				echo '<select id="mld_source_type" name="mld_source_type[';?>+source_count+<?php echo ']">';
				echo '<option value="">Select Type</option>';
				if ( $source_types_query->have_posts() ) {
					while ( $source_types_query->have_posts() ) {
						$source_types_query->the_post();
						echo '<option value="'.$source_types_query->post->ID.'">'.get_the_title().'</option>';
					}
				}
				echo '</select><br/>';
				
				?>';
				jQuery('div.mld_source-box').append(source_field);
			}
		});		
		
		// Add additional usage examples
		var usage_example_count = <?php echo $usage_example_count; ?>;
		var usage_example_field = '';
		jQuery('a.mld_add-usage-example').click(function(e){
			e.preventDefault();
			usage_example_count++;
			if (usage_example_count < 5) {
				usage_example_field = '<input type="text" name="mld_usage_example['+usage_example_count+']" value="" />';
				jQuery('div.mld_usage-example-box').append(usage_example_field);
			}
		});		
	
	});
	
	</script>
    
    <?php
	
}

/**
 * Translations Meta Box // Save
 */
function mld_translations_save_meta_box_data( $post_id ) {

	/*
	 * Make sure we should be updating the post meta (not autosave, etc)
	 */

	if ( ! isset( $_POST['mld_translations__meta_box_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( $_POST['mld_translations__meta_box_nonce'], 'mld_translations_meta_box' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( isset( $_POST['post_type'] ) && 'mld_translation' == $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}
	
	$data = array(
		// meta key => field name
		'_mld_translation' => $_POST['mld_translation'],
		'_mld_source_language' => $_POST['mld_source_language'],
		'_mld_translation_language' => $_POST['mld_translation_language'],
		'_mld_notes' => $_POST['mld_notes'],
		'_mld_field' => $_POST['mld_field'],
		'_mld_source' => $_POST['mld_source'],
		'_mld_source_type' => $_POST['mld_source_type'],
		'_mld_usage_example' => $_POST['mld_usage_example'],
	);
	
	foreach ($data as $meta_key => $value) {
		if (is_null($value)) {
			continue;
		}
		if ( !is_array($value) ) {
			$value = sanitize_text_field($value);
			update_post_meta( $post_id, $meta_key, $value );
		} else {
			if (!empty($value) && is_array($value)) {
				delete_post_meta($post_id, $meta_key);
				foreach ($value as $value) {
					if (strlen($value) > 0) {
						add_post_meta($post_id, $meta_key, $value);
					}
				}
			}
		}
		delete_post_meta($post_id, '_mld_approved');
		add_post_meta($post_id, '_mld_approved', 1);
	}
		
}
add_action( 'save_post', 'mld_translations_save_meta_box_data' );