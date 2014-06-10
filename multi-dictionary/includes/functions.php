<?php

/**
 * General ML Dictionary Functions
 * 
 */ 
 
 
/**
 * Add Selected HTML Attribute if Match
 */ 

function mld_selected_if( $value, $check_value ) {
	if ( ( is_string($value) || is_numeric($value) ) && $value == $check_value ) {
		echo ' selected ';
	}
}

/**
 * Return a trailing slash if permalinks are set to have one
 */ 
 
function mld_check_trailing_slash() {
	$permalink_last_char = substr(get_option('permalink_structure'), -1); 
	if ( $permalink_last_char == '/' ) {
		return '/';
	} else {
		return '';
	}
}

/**
 * Create a page in Wordpress
 */ 

function mld_create_page($page_name, $content = false, $post_parent = 0) {
	$create_page = array(
	  'post_title'    => $page_name,
	  'post_content'  => $content,
	  'post_status'   => 'publish',
	  'post_author'   => 1,
	  'post_type'     => 'page',
	  'post_name'     => $page_name,
	  'post_parent'   => $post_parent,
	);

	// Insert the post into the database
	wp_insert_post( $create_page );
}

/**
 * Import Translations CSV
 */ 

function mld_upload_csv( $file, $has_heading = false ) {

	
  ini_set('memory_limit', '64M');
  // Set UTF-8 to allow special characters
  setlocale(LC_ALL, "en_US.UTF-8");
  echo setlocale(LC_ALL, 0);
  echo 'test';
  set_time_limit(0);

  if ( $file ) {
  
      // look only for uploaded files
      if ($file['error'] == 0) {
	  
        $filetmp = $file['tmp_name'];
		
        if (($handle = fopen($filetmp, "r")) !== FALSE) {

          $translations = explode("\n", file_get_contents(utf8_encode($filetmp)));
		  
          $count = count( $translations );
		  $total_count = $count;
			if( $has_heading ) {
				$total_count = $count - 1;
			}
		  
          unset($translations);
          //echo "Total item count: " . $total_count . "<br />";

          while (($data = fgetcsv($handle, 1000, ',', '"')) !== FALSE) {

            // Skip the first entry in the csv containing colmn info
            if($has_heading) {
			
              $has_heading = false; 
              $count--; 
              continue; 
			  
            }
            // insert the current post and relevant info into the database
            $currently_processed = mld_import_translation($data, $count);

          }
          fclose($handle);

		  $count--;

        }
		
        unlink($filetmp); // delete the temp csv file

			wp_redirect( $_SERVER['REQUEST_URI'].'&mld_imported='.$count );
			exit;

		
      }
	  
    }
  
} 

/**
 * Import a Translation Row from CSV File
 */ 

function mld_import_translation($post, $count) {

	$mld_dictionary = new mld_Dictionary();

	$term =  (array_key_exists(0, $post) && $post[0] != "" ?  $post[0] : 'N/A');
	if ( strlen($term) == 0 ) {
		$term = false;
	}

	$mld_translation = (array_key_exists(1, $post) && $post[1] != ""  ?  $post[1] : 'N/A');
	if ( strlen($mld_translation) == 0 ) {
		$mld_translation = false;
	}

	$source_language = (array_key_exists(2, $post) && $post[2] != ""  ?  $post[2] : 'N/A');
	if ( strlen($source_language) == 0 ) {
		$source_language = false;
	}

	$translation_language = (array_key_exists(3, $post) && $post[3] != ""  ?  $post[3] : 'N/A');
	if ( strlen($translation_language) == 0 ) {
		$translation_language = false;
	}

	$field = (array_key_exists(4, $post) && $post[4] != ""  ?  $post[4] : 'N/A');
	if ( strlen($field) == 0 ) {
		$field = false;
	}

	$definition = (array_key_exists(5, $post) && $post[5] != ""  ?  $post[5] : 'N/A');
	if ( strlen($definition) <= 3 ) {
		$definition = false;
	}
	
	$sources_array = (array_key_exists(6, $post) && $post[6] != ""  ?  $post[6] : '');
	if ( strlen($sources_array) > 5 && strpos($sources_array, '||') !== false ) {
		$sources_array = explode('||',$sources_array);
		foreach ($sources_array as $source) {
			$single_source = explode('|', $source);
			$sources[] = trim($single_source[0]);
			$source_types[] = trim($single_source[1]);
		}
	} elseif (strlen($sources_array) > 5 && strpos($sources_array, '|') !== false ) {
		$single_source = explode('|', $sources_array);
		$sources = trim($single_source[0]);
		$source_types = trim($single_source[1]);
	} else {
		$sources = false;
		$source_types = false;
	}
	
	$usage_examples_array = (array_key_exists(7, $post) && $post[7] != ""  ?  $post[7] : '');
	if ( strlen($usage_examples_array) > 5 && strpos($usage_examples_array, '||') !== false ) {
		$usage_examples = explode('||', $usage_examples_array);
		foreach ($usage_examples as $usage_example) {
			$usage_example_trimmed[] = trim($usage_example);
		}
		$usage_examples = $usage_example_trimmed;
	} elseif (strlen($usage_examples_array) > 5 ) {
		$usage_examples = trim($usage_examples_array);
	} else {
		$usage_examples = false;
	}
	
	$notes = (array_key_exists(8, $post) && $post[8] != ""  ?  $post[8] : '');
	if ( strlen($notes) <= 3 ) {
		$notes = false;
	}
	
	if ( !$term || !$mld_translation || !$source_language || !$translation_language ) {
		return true;
	}
	
	// Create the translation
	$translation = array(
		'post_title' => $term,
		'post_content' => $definition,
		'post_type' => 'mld_translation',
		'post_status' => 'publish',
	);
	$translation_id = wp_insert_post( $translation );	
	
	$data = array(
		'_mld_translation' => trim($mld_translation),
		'_mld_source_language' => trim($source_language),
		'_mld_translation_language' => trim($translation_language),
		'_mld_notes' => trim($notes),
		'_mld_field' => trim($field),
		'_mld_source' => $sources,
		'_mld_source_type' => $source_types,
		'_mld_usage_example' => $usage_examples,
		'_mld_approved' => '1',
	);
	
	foreach ($data as $meta_key => $value) {
		if (is_null($value)) {
			continue;
		}

		if ( !is_array($value) ) {
		
			// Convert languages, fields and source types to their corresponding IDs
			if ( $meta_key == '_mld_source_language' || $meta_key == '_mld_translation_language' ) {
				if ( !is_numeric($value) ) {
					$value = trim(strtolower($value));
					$value = $mld_dictionary->languages['name_index'][$value]['id'];
				}
			}
			if ( $meta_key == '_mld_field' ) {
				if ( !is_numeric($value) ) {
					$value = trim(strtolower($value));
					$value = $mld_dictionary->fields['name_index'][$value];
				}
			}
			if ( $meta_key == '_mld_source_type' ) {
				if ( !is_numeric($value) ) {
					$value = trim(strtolower($value));
					$value = $mld_dictionary->source_types['name_index'][$value];
				}
			}
			
			if (strlen($value) > 0) {
				$value = sanitize_text_field($value);
				update_post_meta( $translation_id, $meta_key, $value );
			}
		} else {
			if (!empty($value) && is_array($value)) {
				delete_post_meta($translation_id, $meta_key);
				foreach ($value as $value) {
				
					// Convert source types to their corresponding IDs
					if ( $meta_key == '_mld_source_type' ) {
						if ( !is_numeric($value) ) {
							$value = trim(strtolower($value));
							$value = $mld_dictionary->source_types['name_index'][$value];
						}
					}
				
					if (strlen($value) > 3) {
						add_post_meta($translation_id, $meta_key, $value);
					}
				}
			}
		}
		
	}	
	
	// echo "Importing: <strong>" . $term  . "</strong> <i> (" . $count ." items remaining)...</i><br />";
	
	return true;
	
}