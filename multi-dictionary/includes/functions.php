<?php

/**
 * General ML Dictionary Functions
 * 
 */ 


/**
 * Just a check to note that the plugin is active
 */ 

function mld_plugin_exists() {
	return true;
}
 
 
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

function mld_upload_csv( $file, $has_heading = false, $file_type = 'csv', $import_type = 'live' ) {
  
  $test_import = false;
  if ( $import_type == 'test' ) {
  	  $test_import = true;
  }
  
  if ($test_import) {
  	  echo '<div id="mld-admin">
	        	<h1>
				    <img src="'.plugins_url().'/multi-dictionary/images/dictionary-icon-large.png" alt="ML Dictionary Icon" />
					<span>Multilingual Dictionary:</span> Test Import
				</h1>';
  }
    
  // Set UTF-8 to allow special characters
  setlocale(LC_ALL, "en_US.UTF-8");
  
  // Increase script memory and time limit
  ini_set('memory_limit', '128M');
  set_time_limit(0);

  if ( $file ) {
  
      // look only for uploaded files
      if ($file['error'] == 0) {
	  
        $filetmp = $file['tmp_name'];
		
        if (($handle = fopen($filetmp, "r")) !== FALSE) {

          $translations = rtrim( file_get_contents( utf8_encode($filetmp) ), "\n");
		  $translations = explode("\n", $translations);
		  
		  $GLOBALS['row_count'] = 0;
          $GLOBALS['errored'] = 0;
		  if ( isset($_SESSION) ) {
			  unset($_SESSION['error_rows']);
		  }
		  
		  $count = count( $translations );
		  $total_count = $count;
			if( $has_heading ) {
				$total_count = $total_count - 1;
				$GLOBALS['row_count'] = 1;
			}
		  
          unset($translations);
          if ($test_import) {
		  	  echo '<h3 style="padding-bottom: 15px;">Terms Found in Document: <span style="color: #009900;">' . $total_count . '</span>
			            &mdash; <a href="'.$_SERVER['REQUEST_URI'].'">[Back]</a>
					</h3>
			       ';
		  }	
			
		  $delimeter = ',';
		  $enclosure = '"';
			  
		  if ( $file_type == 'tsv' ) {
		  
		  	  $delimeter = "\t";
			  while (($data = fgetcsv($handle, 1000, $delimeter)) !== FALSE) {
	
				// Skip the first entry in the csv containing colmn info
				if($has_heading) {
				
				  $has_heading = false; 
				  $count--; 
				  continue; 
				  
				}
				// insert the current post and relevant info into the database
				$currently_processed = mld_import_translation($data, $count, $test_import);
	
			  }
			  
		  } else {
		  
			  while (($data = fgetcsv($handle, 1000, $delimeter, $enclosure)) !== FALSE) {
	
				// Skip the first entry in the csv containing colmn info
				if($has_heading) {
				
				  $has_heading = false; 
				  $count--; 
				  continue; 
				  
				}
				// insert the current post and relevant info into the database
				$currently_processed = mld_import_translation($data, $count, $test_import);
	
			  }		  
		  
		  }

          fclose($handle);

		  $count--;

        }
		
        unlink($filetmp); // delete the temp csv file

			if ( !$test_import ) {
				
				// Store the batch import
				$imported_ids = '';
				foreach ($GLOBALS['import_batch_ids'] as $key => $imported_term_id) {
					$imported_ids.= $imported_term_id.',';
				}
				$imported_ids = rtrim($imported_ids, ',');
				
				$batch_import = array(
					'post_content' => $imported_ids,
					'post_type' => 'mld_import',
					'post_status' => 'publish',
				);
			
				$batch_id = wp_insert_post( $batch_import );	
				wp_redirect( $_SERVER['REQUEST_URI'].'&mld_imported='.$total_count.'&mld_errored='.$GLOBALS['errored'] );

			} else {
				// Test Import
				echo '</div>';
			}
			exit;

		
      }
	  
    }
  
} 

/**
 * Import a Translation Row from CSV File
 */ 

function mld_import_translation($post, $count, $test_import = false) {

	$row_has_error = false;
	$error_text = '';

	$import_html = '
	<div class="mld-test-import-box">
	<table cellpadding="0" cellspacing="0" class="mld-test-import">
		<tbody>
			<tr class="mld-test-import-heading-row">
				<td class="term">Term</td>
				<td class="term">Translation</td>
				<td class="language">Source Lang</td>
				<td class="language">Target Lang</td>
				<td class="speech">Part of Speech</td>
				<td class="field">Field</td>
				<td class="definition">EN Definition</td>
				<td class="definition">SL Definition</td>
				<td class="definition">TL Definition</td>
				<td class="sources">Sources</td>
				<td class="examples">Usage Examples</td>
				<td class="notes">Notes</td>
				<td class="user">User</td>
				<td class="user">Display Author?</td>
			</tr>
			<tr>';

	$GLOBALS['row_count']++;
	$mld_dictionary = new mld_Dictionary();

	$term =  (array_key_exists(0, $post) && $post[0] != "" ?  $post[0] : false);
	if ( !$term ) {
		$error_text.= '- Term Name missing (required)<br/>';
		$row_has_error = true;
		$import_html.= '<td class="center cell-error">Error (Required)</td>';
	} else {
		$term = trim($term);
		$import_html.= '<td class="center">'.$term.'</td>';
	}
	
	$mld_translation = (array_key_exists(1, $post) && $post[1] != ""  ?  $post[1] : false);
	if ( !$mld_translation ) {
		$import_html.= '<td class="center cell-error">Error (Required)</td>';
		$error_text.= '- Translation missing (required)<br/>';
		$row_has_error = true;
	} else {
		$mld_translation = trim($mld_translation);
		$import_html.= '<td class="center">'.$mld_translation.'</td>';
	}

	$source_language = (array_key_exists(2, $post) && $post[2] != ""  ?  $post[2] : false);
	if ( $source_language ) {
		$source_language = trim($source_language);

		if ( !is_numeric($source_language) ) {
			$source_language_lc = strtolower($source_language);
			if ( !array_key_exists($source_language_lc, $mld_dictionary->languages['name_index']) ) {
				$import_html.= '<td class="center cell-error">'.$source_language.'<br/>(Not Found)</td>';
				$error_text.= '- Source Language error - "'.$source_language.'" Language not found.<br/>';
				$row_has_error = true;
			} else {
				$import_html.= '<td class="center">'.$source_language.'</td>';
				$source_language = $mld_dictionary->languages['name_index'][$source_language_lc]['id'];
			}
		} else {
			if ( !array_key_exists($source_language, $mld_dictionary->languages['id_index']) ) {
				$import_html.= '<td class="center cell-error">'.$source_language.' <br/>(Not Found)</td>';
				$error_text.= '- Source Language error - Language with ID "'.$source_language.'" not found.<br/>';
				$row_has_error = true;
			} else {
				$import_html.= '<td class="center">'.$mld_dictionary->languages['id_index'][$source_language]['name'].'</td>';
			}
		}

	} else {
		$import_html.= '<td class="center cell-error">Error (Required)</td>';
		$error_text.= '- Source Language error - Language not specified (required).<br/>';
		$row_has_error = true;
	}
	
	$translation_language = (array_key_exists(3, $post) && $post[3] != ""  ?  $post[3] : false);
	if ( $translation_language ) {
		$translation_language = trim($translation_language);

		if ( !is_numeric($translation_language) ) {
			$translation_language_lc = strtolower($translation_language);
			if ( !array_key_exists($translation_language_lc, $mld_dictionary->languages['name_index']) ) {
				$import_html.= '<td class="center cell-error">'.$translation_language.'<br/>(Not Found)</td>';
				$error_text.= '- Translation Language error - "'.$translation_language.'" Language not found.<br/>';
				$row_has_error = true;
			} else {
				$import_html.= '<td class="center">'.$translation_language.'</td>';
				$translation_language = $mld_dictionary->languages['name_index'][$translation_language_lc]['id'];
			}
		} else {
			if ( !array_key_exists($translation_language, $mld_dictionary->languages['id_index']) ) {
				$import_html.= '<td class="center cell-error">'.$translation_language.' <br/>(Not Found)</td>';
				$error_text.= '- Translation Language error - Language with ID "'.$translation_language.'" not found.<br/>';
				$row_has_error = true;
			} else {
				$import_html.= '<td class="center">'.$mld_dictionary->languages['id_index'][$translation_language]['name'].'</td>';
			}
		}

	} else {
		$import_html.= '<td class="center cell-error">Error (Required)</td>';
		$error_text.= '- Translation Language error - Language not specified (required).<br/>';
		$row_has_error = true;
	}
	
	$part_speech = (array_key_exists(4, $post) && $post[4] != ""  ?  $post[4] : false);
	if ( $part_speech ) {
		$part_speech = trim($part_speech);
	}
	
	if ( $part_speech && !is_numeric($part_speech) ) {
		$part_speech_lc = strtolower($part_speech);
		if ( !array_key_exists($part_speech_lc, $mld_dictionary->parts_speech['name_index']) ) {
			$import_html.= '<td class="center cell-error">'.$part_speech.' (Not Found)</td>';
			$error_text.= '- Part of Speech error - "'.$part_speech.'" Part of Speech not found.<br/>';
			$row_has_error = true;
		} else {
			$part_speech = $mld_dictionary->parts_speech['name_index'][$part_speech_lc];
			$import_html.= '<td class="center">'.$mld_dictionary->parts_speech['id_index'][$part_speech].'</td>';
		}
	} elseif ( $part_speech && is_numeric($part_speech) ) {
		if ( !array_key_exists($part_speech, $mld_dictionary->parts_speech['id_index']) ) {
			$import_html.= '<td class="center cell-error">'.$part_speech.' (Not Found)</td>';
			$error_text.= '- Part of Speech error - Part of Speech with ID "'.$part_speech.'" not found.<br/>';
			$row_has_error = true;
		} else {
			$import_html.= '<td class="center">'.$mld_dictionary->parts_speech['id_index'][$part_speech].'</td>';
		}
	}

	$field = (array_key_exists(5, $post) && $post[5] != ""  ?  $post[5] : false);
	if ( $field ) {
		$field = trim($field);
	}
	if ( $field && !is_numeric($field) ) {
		$field_lc = strtolower($field);
		if ( !array_key_exists($field_lc, $mld_dictionary->fields['name_index']) ) {
			$import_html.= '<td class="center cell-error">'.$field.' <br/>(Not Found)</td>';
			$error_text.= '- Field error - "'.$field.'" Field not found.<br/>';
			$row_has_error = true;
		} else {
			$field = $mld_dictionary->fields['name_index'][$field_lc];
			$import_html.= '<td class="center">'.$mld_dictionary->fields['id_index'][$field].'</td>';
		}
	} elseif ( $field && is_numeric($field) ) {
		if ( !array_key_exists($field, $mld_dictionary->fields['id_index']) ) {
			$import_html.= '<td class="center cell-error">'.$field.' <br/>(Not Found)</td>';
			$error_text.= '- Field error - Field with ID "'.$field.'" not found.<br/>';
			$row_has_error = true;
		} else {
			$import_html.= '<td class="center">'.$mld_dictionary->fields['id_index'][$field].'</td>';
		}
	}

	$definition = (array_key_exists(6, $post) && $post[6] != ""  ?  $post[6] : false);
	if ( $definition ) {
		$definition = trim($definition);
		$import_html.= '<td>'.$definition.'</td>';
	} else {
		$import_html.= '<td class="center cell-error">Error (Required)</td>';
		$error_text.= '- English Definition (required) not specified.<br/>';
		$row_has_error = true;
	}

	$source_language_definition = (array_key_exists(7, $post) && $post[7] != ""  ?  $post[7] : false);
	if ( $source_language_definition ) {
		$source_language_definition = trim($source_language_definition);
		$import_html.= '<td>'.$source_language_definition.'</td>';
	} else {
		$import_html.= '<td class="center">-</td>';
	}	

	$target_language_definition = (array_key_exists(8, $post) && $post[8] != ""  ?  $post[8] : false);
	if ( $target_language_definition ) {
		$target_language_definition = trim($target_language_definition);
		$import_html.= '<td>'.$target_language_definition.'</td>';
	} else {
		$import_html.= '<td class="center">-</td>';
	}
	
	$sources_array = (array_key_exists(9, $post) && $post[9] != ""  ?  $post[9] : false);
	
	if ( $sources_array && strpos($sources_array, '||') !== false ) {
	
		$test_cell_html = '<ol>';
		$sources_error = false;
	
		$sources_array = trim($sources_array);
		$sources_array = rtrim($sources_array , '||');
		$sources_array = trim($sources_array);
		$sources_array = explode('||',$sources_array);
		
		foreach ($sources_array as $source) {
			$single_source = explode('|', $source);
			if ( !is_array($single_source) || count($single_source) != 2 ) {
				$error_text.= '- Source Type error - Source or Source Type not specified or delimited with | (single-bar).<br/>';
				$row_has_error = true;
				$test_cell_html.= '<li>Source or Source Type not specified or delimited with | (single-bar).</li>';
				$sources_error = true;
			} else {
				$single_source[1] = trim($single_source[1]);
	
				if ( is_numeric($single_source[1]) ) {
					if ( !array_key_exists($single_source[1], $mld_dictionary->source_types['id_index']) ) {
						$error_text.= '- Source Type error - Source Type with ID "'.$single_source[1].'" not found.<br/>';
						$row_has_error = true;
						$test_cell_html.= '<li>Source Type with ID "'.$single_source[1].'" not found.</li>';	
						$sources_error = true;	
					} else {
						$test_cell_html.= '<li>'.$single_source[0].' <strong class="green">('.$mld_dictionary->source_types['id_index'][$single_source[1]].')</strong></li>';
						$source_type = $single_source[1];
					}
				} else {
					$single_source_lc = strtolower($single_source[1]);
					if ( !array_key_exists($single_source_lc, $mld_dictionary->source_types['name_index']) ) {
						$error_text.= '- Source Type error - Source Type "'.$single_source[1].'" not found.<br/>';
						$row_has_error = true;
						$test_cell_html.= '<li>Source Type "'.$single_source[1].'" not found.</li>';
						$sources_error = true;
					} else {
						$source_type = $mld_dictionary->source_types['name_index'][$single_source_lc];
						$test_cell_html.= '<li>'.$single_source[0].' <strong class="green">('.$mld_dictionary->source_types['id_index'][$source_type].')</strong></li>';
					}
				}
				$sources[] = trim($single_source[0]);
				$source_types[] = $source_type;
			}
		}
		
		$test_cell_html.= '</ol>';
		
		if ( $sources_error ) {
			$import_html.= '<td class="cell-error">'.$test_cell_html.'</td>';
		} else {
			$import_html.= '<td>'.$test_cell_html.'</td>';
		}
		
	} elseif ( $sources_array && strpos($sources_array, '|') !== false ) {
	
		$single_source = explode('|', $sources_array);
		if ( !is_array($single_source) || count($single_source) != 2 ) {
			$error_text.= '- Source Type error - Source or Source Type not specified or delimited with | (single-bar).<br/>';
			$row_has_error = true;
			$import_html.= '<td class="cell-error">Source or Source Type not specified or delimited with | (single-bar).</td>';
			$sources_error = true;
		} else {
			$single_source[1] = trim($single_source[1]);

			if ( is_numeric($single_source[1]) ) {
				if ( !array_key_exists($single_source[1], $mld_dictionary->source_types['id_index']) ) {
					$error_text.= '- Source Type error - Source Type with ID "'.$single_source[1].'" not found.<br/>';
					$row_has_error = true;
					$import_html.= '<td class="cell-error">Source Type with ID "'.$single_source[1].'" not found.</td>';	
					$sources_error = true;	
				} else {
					$import_html.= '<td><ol><li>'.$single_source[0].' <strong class="green">('.$mld_dictionary->source_types['id_index'][$single_source[1]].')</strong></li></ol></td>';
					$source_type = $single_source[1];
				}
			} else {
				$single_source_lc = strtolower($single_source[1]);
				if ( !array_key_exists($single_source_lc, $mld_dictionary->source_types['name_index']) ) {
					$error_text.= '- Source Type error - Source Type "'.$single_source[1].'" not found.<br/>';
					$row_has_error = true;
					$import_html.= '<td class="cell-error">Source Type "'.$single_source[1].'" not found.</td>';
					$sources_error = true;
				} else {
					$source_type = $mld_dictionary->source_types['name_index'][$single_source_lc];
					$import_html.= '<td><ol><li>'.$single_source[0].' <strong class="green">('.$mld_dictionary->source_types['id_index'][$source_type].')</strong></li></ol></td>';
				}
			}
			$sources[] = trim($single_source[0]);
			$source_types[] = $source_type;
		}
		
	} elseif ( strlen($sources_array) > 5 ) {
	
		$error_text.= '- Sources error - Delimiters not found.<br/>';
		$row_has_error = true;
		$sources = false;
		$source_types = false;
		$import_html.= '<td class="center cell-error">Delimiters not found in the following (raw entry): <br/>'.$sources_array.'</td>';
	
	} else {
		
		$sources = false;
		$source_types = false;
		$import_html.= '<td class="center">-</td>';

	}
	
	$usage_examples_array = (array_key_exists(10, $post) && $post[10] != ""  ?  $post[10] : false);
	
	if ( $usage_examples_array && strpos($usage_examples_array, '||') !== false ) {
		$import_html.= '<td><ol>';
		$usage_examples = explode('||', $usage_examples_array);
		foreach ( $usage_examples as $usage_example) {
			$usage_example_trimmed[] = trim($usage_example);
			$import_html.= '<li>'.trim($usage_example).'</li>';
		}
		$usage_examples = $usage_example_trimmed;
		$import_html.= '</ol></td>';
	} elseif ( $usage_examples_array ) {
		$usage_examples = trim($usage_examples_array);
		$import_html.= '<td><ol><li>'.$usage_examples.'</li></ol></td>';
	} else {
		$usage_examples = false;
		$import_html.= '<td class="center">-</td>';
	}
	
	$notes = (array_key_exists(11, $post) && $post[11] != ""  ?  $post[11] : false);
	if ( $notes ) {
		$notes = trim($notes);
		$import_html.= '<td class="center">'.$notes.'</td>';
	} else {
		$import_html.= '<td class="center">-</td>';
	}
	
	$term_author = (array_key_exists(12, $post) && $post[12] != ""  ?  $post[12] : false);
	
	$term_author_id = false;
	if ( $term_author ) {
		$term_author = trim($term_author);
		if ( is_numeric($term_author) ) {
			$term_author_id = get_user_by( 'id', $term_author );
		} else {
			$term_author_id = get_user_by( 'login', $term_author );
			if ( !$term_author_id ) {
				$term_author_id = get_user_by( 'slug', $term_author );
			}
			
		}
		if ($term_author_id) {
			$term_author_id = $term_author_id->ID;
			$term_author_name = get_user_by('id', $term_author_id);
			$import_html.= '<td class="center">'.$term_author_name->user_nicename.'</td>';
		} else {
			$error_text.= '- Term Author error - Author with name, login or ID of "'.$term_author.'" not found.<br/>';
			$row_has_error = true;
			$import_html.= '<td class="center cell-error">'.$term_author.' <br/>(Not found)</td>';
		}
	} else {
		$import_html.= '<td class="center"> - </td>';
	}
	
	$display_author = (array_key_exists(13, $post) && $post[13] != ""  ?  $post[13] : 0);
	if ( $display_author != 1 ) {
		$display_author = 0;
		$import_html.= '<td class="center">No</td>';
	} else {
		$import_html.= '<td class="center">Yes</td>';
	}
	
	// Create the translation
	$translation = array(
		'post_title' => $term,
		'post_content' => $definition,
		'post_type' => 'mld_translation',
		'post_status' => 'publish',
	);

	if ( $row_has_error ) {
		$term_and_row = $term.' (Row #'.$GLOBALS['row_count'].') ';
		$_SESSION['error_rows'][$term_and_row] = $error_text;
		$GLOBALS['errored']++;
	} elseif ( !$test_import ) {		
		
		if ($term_author_id) {
			$translation['post_author'] = $term_author_id;
		}
		$translation_id = wp_insert_post( $translation );	
		$GLOBALS['import_batch_ids'][] = $translation_id;
	}
		
	$data = array(
		'_mld_translation' => trim($mld_translation),
		'_mld_source_language' => trim($source_language),
		'_mld_translation_language' => trim($translation_language),
		'_mld_source_language_definition' => trim($source_language_definition),
		'_mld_target_language_definition' => trim($target_language_definition),
		'_mld_notes' => trim($notes),
		'_mld_part_speech' => trim($part_speech),
		'_mld_field' => trim($field),
		'_mld_source' => $sources,
		'_mld_source_type' => $source_types,
		'_mld_usage_example' => $usage_examples,
		'_mld_approved' => '1',
		'_mld_display_author' => $display_author,
	);
	
	foreach ($data as $meta_key => $value) {
		if (is_null($value)) {
			continue;
		}

		if ( !is_array($value) ) {
			
			if (strlen($value) > 0) {
				$value = sanitize_text_field($value);
				if ( !$row_has_error && !$test_import ) {
					update_post_meta( $translation_id, $meta_key, $value );
				}
			}
			
		} else {
			if (!empty($value) && is_array($value)) {
				//delete_post_meta($translation_id, $meta_key);
				foreach ($value as $value) {
				
					// Convert source types to their corresponding IDs
					if ( $meta_key == '_mld_source_type' ) {
						if ( !is_numeric($value) ) {
							$value = $mld_dictionary->source_types['name_index'][$value];
						}
					}
				
					if (strlen($value) > 0) {
						if ( !$row_has_error && !$test_import ) {
							add_post_meta($translation_id, $meta_key, $value);
						}
					}
				}
			}
		}
		
	}
	
	// echo "Importing: <strong>" . $term  . "</strong> <i> (" . $count ." items remaining)...</i><br />";
	if ( $test_import ) {
		$import_html.= '</tr></tbody></table></div>';
		echo $import_html;
	}
	
	return true;
	
}