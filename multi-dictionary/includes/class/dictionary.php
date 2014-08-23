<?php

/**
 * Dictionary Class
 * 
 */ 

class mld_Dictionary {
	
	// Customized "Settings" Set in Admin
	public $languages = false;
	public $fields = false;
	public $parts_speech = false;
	public $source_types = false;
	
	// Imported Terms
	public $batch_id = false;
	
	// Term/Search Criteria
	public $term = false;
	public $source_language = false;
	public $translation_language = false;
	public $approval_status = false;
	public $sort_order = false;
	
	// Whether or not a search has been made
	public $search = false;
	public $exact_match = false;
	
	// The WP_Query Object
	private $query;
	private $reverse_query;
	
	private $args;
	private $reverse_args;
	
	// The Retrieved Translations
	public $translations = false;
	
	public function __construct( $args = false ) {

		// Load languages, fields and source types
		$this->loadSettings();
	
		if ( $args['term'] && $args['source_language'] && $args['translation_language'] ) {
			
			$this->set_term($args['term']);
			$this->set_source_language($args['source_language']);
			$this->set_translation_language($args['translation_language']);			
			
		}
	
	}
	
/**
 * Load Languages, Fields and Source Types
 */

	public function loadSettings() {
	
	// Load Languages
	
		$this->args = array( 
			'post_type' => 'mld_language', 
			'orderby' => 'title', 
			'order' => 'ASC',
			'post_status' => 'any'
		);
		
		$this->query = new WP_Query($this->args);
		
		if ( $this->query->have_posts() ) {

			$this->languages = array( 'name_index' => array(), 'id_index' => array() );

			while ( $this->query->have_posts() ) {
				
				$this->query->the_post();
				
				$language_name = get_the_title();
				$language_name_lowercase = strtolower($language_name);
				$language_native_spelling = get_post_meta( $this->query->post->ID, '_mld_native_spelling', true );
				
				$this->languages['id_index'][$this->query->post->ID]['name'] = $language_name;
				$this->languages['id_index'][$this->query->post->ID]['native_spelling'] = $language_native_spelling;
				
				$this->languages['name_index'][$language_name_lowercase]['id'] = $this->query->post->ID;
				$this->languages['name_index'][$language_name_lowercase]['native_spelling'] = $language_native_spelling;
				$this->languages['name_index'][$language_name_lowercase]['name'] = $language_name;

			}
			
		} else {
			$this->languages = false;
		}
		
		// Reset post
		wp_reset_postdata();
		
	// Load Fields
	
		$this->args = array( 
			'post_type' => 'mld_field', 
			'orderby' => 'title', 
			'order' => 'ASC',
			'post_status' => 'any'
		);
		
		$this->query = new WP_Query($this->args);
		
		if ( $this->query->have_posts() ) {

			$this->fields = array( 'name_index' => array(), 'id_index' => array() );

			while ( $this->query->have_posts() ) {
				
				$this->query->the_post();
				
				$field_name = get_the_title();
				$field_name_lowercase = strtolower($field_name);
				
				$this->fields['id_index'][$this->query->post->ID] = $field_name;
				$this->fields['name_index'][$field_name_lowercase] = $this->query->post->ID;
				
			}
			
		} else {
			$this->fields = false;
		}
		
		// Reset post
		wp_reset_postdata();	

	// Load Parts of Speech
	
		$this->args = array( 
			'post_type' => 'mld_part_speech', 
			'orderby' => 'title', 
			'order' => 'ASC',
			'post_status' => 'any'
		);
		
		$this->query = new WP_Query($this->args);
		
		if ( $this->query->have_posts() ) {

			$this->parts_speech = array( 'name_index' => array(), 'id_index' => array() );

			while ( $this->query->have_posts() ) {
				
				$this->query->the_post();
				
				$part_speech_name = get_the_title();
				$part_speech_name_lowercase = strtolower($part_speech_name);
				
				$this->parts_speech['id_index'][$this->query->post->ID] = $part_speech_name;
				$this->parts_speech['name_index'][$part_speech_name_lowercase] = $this->query->post->ID;
				
			}
			
		} else {
			$this->parts_speech = false;
		}
		
		// Reset post
		wp_reset_postdata();	
		
	// Load Source Types
	
		$this->args = array( 
			'post_type' => 'mld_source_type', 
			'orderby' => 'title', 
			'order' => 'ASC',
			'post_status' => 'any'
		);
		
		$this->query = new WP_Query($this->args);
		
		if ( $this->query->have_posts() ) {
		
			$this->source_types = array( 'name_index' => array(), 'id_index' => array() );
		
			while ( $this->query->have_posts() ) {
				
				$this->query->the_post();
	
				$source_type = get_the_title();
				$source_type_lowercase = strtolower($source_type);
				
				$this->source_types['id_index'][$this->query->post->ID] = $source_type;
				$this->source_types['name_index'][$source_type_lowercase] = $this->query->post->ID;
				
			}
			
		} else {
			$this->source_types = false;
		}
				
		// Reset post
		wp_reset_postdata();	
		
	}

/**
 * Set Source Language
 */
 
	public function set_source_language ($language = false) {
	
		if ($language) {
		
			$language_lc = strtolower($language);
			
			if ( array_key_exists($language_lc, $this->languages['name_index']) ) {
				$this->source_language['name'] = $language;
				$this->source_language['id'] = $this->languages['name_index'][$language_lc]['id'];
			} elseif ( array_key_exists($language, $this->languages['id_index']) ) {
				$this->source_language['name'] = $this->languages['id_index'][$language]['name'];
				$this->source_language['id'] = $language;
			} else {
				$this->source_language = false;
				return false;
			}
			
		} else {
			$this->source_language = false;
		}
	
	}

/**
 * Set Translation Language
 */

	public function set_translation_language ($language = false) {
	
		if ($language) {
		
			$language_lc = strtolower($language);
			
			if ( array_key_exists($language_lc, $this->languages['name_index']) ) {
				$this->translation_language['name'] = $language;
				$this->translation_language['id'] = $this->languages['name_index'][$language_lc]['id'];
			} elseif ( array_key_exists($language, $this->languages['id_index']) ) {
				$this->translation_language['name'] = $this->languages['id_index'][$language]['name'];
				$this->translation_language['id'] = $language;
			} else {
				$this->translation_language = false;
				return false;
			}
			
		} else {
			$this->translation_language = false;
		}
	
	}

/**
 * Set Term
 */
	
	public function set_term ($term = false) {
	
		if ($term) {
			$this->term = array();
			$this->term['name'] = trim($term);
			$this->term['lower'] = trim(strtolower($term));
		} else {
			$this->term = false;
		}
	
	}

/**
 * Retrieve Translations Pending Approval
 */

	public function get_pending() {
	
		if ($this->get_translations('0')) {
			return true;
		} else {
			return false;
		}
	
	}

/**
 * Retrieve Approved Translations
 */

	public function get_approved() {
	
		if ($this->get_translations('1')) {
			return true;
		} else {
			return false;
		}
	
	}

/**
 * Retrieve Translations
 */

	public function get_translations( $approval_status = false, $moderator_languages = false ) {
		
		global $wpdb;
		
		$has_reverse_results = false;
		$meta_query = array();
		$meta_query_reverse_lookup = array();
		
		if ( is_numeric($approval_status) ) {
			$this->approval_status = $approval_status;
		}

		// Prepare the meta query
		
		// Moderator search (show all terms for assigned languages) 
		if ( $moderator_languages ) {
		
			$source_language_meta_query = array(
				'key' => '_mld_source_language',
				'value' => $moderator_languages,
				'compare' => 'IN',
			);
			$meta_query[] = $source_language_meta_query;

			$translation_language_meta_query = array(
				'key' => '_mld_translation_language',
				'value' => $moderator_languages,
				'compare' => 'IN',
			);
			$meta_query[] = $translation_language_meta_query;

		} else {
		// General search with Source Language and Translation Language set
		
			if ( is_numeric($this->source_language['id']) ) {
			
				$source_language_meta_query = array(
					'key' => '_mld_source_language',
					'value' => $this->source_language['id'],
					'compare' => '=',
				);
				$meta_query[] = $source_language_meta_query;
				
			}
			
			if ( is_numeric($this->translation_language['id']) ) {
			
				$translation_language_meta_query = array(
					'key' => '_mld_translation_language',
					'value' => $this->translation_language['id'],
					'compare' => '=',
				);
				$meta_query[] = $translation_language_meta_query;
				
			}
			
			// Reverse language lookup
			if ( is_numeric($this->translation_language['id']) && is_numeric($this->source_language['id']) ) {
			
				$reverse_translation_language_meta_query = array(
					'key' => '_mld_translation_language',
					'value' => $this->source_language['id'],
					'compare' => '=',
				);
				$meta_query_reverse_lookup[] = $reverse_translation_language_meta_query;
			
				$reverse_source_language_meta_query = array(
					'key' => '_mld_source_language',
					'value' => $this->translation_language['id'],
					'compare' => '=',
				);
				$meta_query_reverse_lookup[] = $reverse_source_language_meta_query;
				
				$reverse_approval_meta_query = array(
					'key' => '_mld_approved',
					'value' => '1',
					'compare' => '=',
				);
				$meta_query_reverse_lookup[] = $reverse_approval_meta_query;
				
				$reverse_term_meta_query = array(
					'key' => '_mld_translation',
					'value' => $this->term['name'],
					'compare' => '=',
				);
				$meta_query_reverse_lookup[] = $reverse_term_meta_query;
				
			}
			
		}
							
		if ( is_numeric($this->approval_status) ) {

			$approval_meta_query = array(
				'key' => '_mld_approved',
				'value' => $this->approval_status,
				'compare' => '=',
			);
			$meta_query[] = $approval_meta_query;

		}
	
		$this->args = array( 
			'post_type' => 'mld_translation', 
			'meta_query' => $meta_query,
			'posts_per_page' => -1
		);
		
		// Set the Sort Order
		if ( $this->sort_order ) {
			switch ($this->sort_order) {
				case (2):
					$this->args['orderby'] = 'title';
					$this->args['order'] = 'DESC';
					break;
				case (3):
					$this->args['orderby'] = 'date';
					$this->args['order'] = 'ASC';
					break;
				case (4):
					$this->args['orderby'] = 'date';
					$this->args['order'] = 'DESC';
					break;
				default:
					$this->args['orderby'] = 'title';
					$this->args['order'] = 'ASC';
					break;
			}
		} else {
			$this->args['orderby'] = 'title';
			$this->args['order'] = 'ASC';
		}

		$this->reverse_args = array( 
			'post_type' => 'mld_translation', 
			'meta_query' => $meta_query_reverse_lookup,
			'orderby' => 'title', 
			'order' => 'ASC',
			'posts_per_page' => -1
		);
		
		// Batch Import
		if ($this->batch_id) {
			$the_import = get_post( $this->batch_id );
			if ( $the_import ) {
				$imported_ids = $the_import->post_content;
				$imported_ids = explode(',',$imported_ids);
			} else {
				$this->batch_id = false;
			}
		}
		
		if ( $this->term && !$this->batch_id ) {
			
			$post_ids = $wpdb->get_col("select ID from $wpdb->posts where post_title = '".$this->term['name']."' AND post_type = 'mld_translation' ");
			
			// For front-end search, when there is at least one exact match, include reverse language translations as well
			if ( !$moderator_languages && !is_admin() ) {

				$this->reverse_query = new WP_Query($this->reverse_args);
				
				if ( $this->reverse_query->have_posts() ) {
					
					$has_reverse_results = true;
					
					while ( $this->reverse_query->have_posts() ) {
		
						$this->reverse_query->the_post();
						$post_ids[] = "".$this->reverse_query->post->ID."";
						
					}
										
					// Reset post
					wp_reset_postdata();	
					
				}

			}

			if ( $post_ids ) {
			
				$this->exact_match = true;
													
			} else {
				$post_ids = $wpdb->get_col("select ID from $wpdb->posts where post_title LIKE '".$this->term['name']."%' AND post_type = 'mld_translation' ");
			} 
			
			if ( $post_ids ) {
				// Send a 404 header when no matches found.
				if ( !is_admin() && !$this->exact_match ) {
					status_header(404);
				}
				$this->args['post__in'] = $post_ids;
			} else {
				return false;
			}
			
		} elseif ( $this->batch_id ) {
			
			$this->args['post__in'] = $imported_ids;
			
		}
		
		$this->query = new WP_Query($this->args);
		
		if ( $this->query->have_posts() ) {

			$this->translations = $this->query;

			if ( $has_reverse_results ) {
				$this->translations->posts = array_merge ( $this->translations->posts, $this->reverse_query->posts );
				$this->translations->post_count = $this->translations->post_count + $this->reverse_query->post_count;
			}

			// Get Votes
			$this->calculate_votes();
			return true;

		} elseif ( $has_reverse_results ) {

			$this->translations = $this->reverse_query;

			// Get Votes
			$this->calculate_votes();
			return true;

		} else {
			$this->translations = false;
			return false;
		}
				
		// Reset post
		wp_reset_postdata();	
	
	}
	

/**
 * Calculate Translation Votes
 */

	public function calculate_votes() {

		if ( !$this->translations ) {
			return false;
		}
		
		$vote_count = array();
		$this->translations_order = array();
		
		while ( $this->translations->have_posts() ) {
		
			$this->translations->the_post();	

			$vote_count['total_count'] = 0;
			$vote_count['up_votes'] = 0;
			$vote_count['down_votes'] = 0;
			$vote_count['percentage'] = 100;
			
			$up_votes = get_post_meta($this->translations->post->ID, '_mld_upvote');
			$down_votes = get_post_meta($this->translations->post->ID, '_mld_downvote');
			
			if ($up_votes) {
				$count = count($up_votes);
				$vote_count['up_votes'] = $count;
				$vote_count['total_count'] = $count;
			}
			
			if ($down_votes) {
				$count = count($down_votes);
				$vote_count['down_votes'] = $count;
				$vote_count['total_count'] = $vote_count['total_count'] + $count;
			}
			
			if ( $vote_count['total_count'] > 0 ) {
				$vote_count['percentage'] = 
					number_format( ($vote_count['up_votes'] / $vote_count['total_count']) * 100, 0, '', ' ');
			}
			
			// Add to array containing percentage PLUS total count and post IDs 
			$percentage_plus_votes = $vote_count['percentage']+($vote_count['total_count']/100);
			foreach ($this->translations->posts as $key => $post) {
				if ( $post->ID == $this->translations->post->ID ) {
					$this->translations->posts[$key]->vote_rank = $percentage_plus_votes;
					$this->translations->posts[$key]->vote_count_total = $vote_count['total_count'];
					$this->translations->posts[$key]->vote_count_up = $vote_count['up_votes'];
					$this->translations->posts[$key]->vote_count_down = $vote_count['down_votes'];
					$this->translations->posts[$key]->vote_percentage = $vote_count['percentage'];
				}
			}
			
		}
		
		$this->translations_votes = $vote_count;
		
		// Reset post
		wp_reset_postdata();			

	}

/**
 * Compare Translation Order by Votes
 */
	
	private static function compare_translation_order($a,$b) {

		if ($a->vote_rank == $b->vote_rank) {
			return 0;
		}
		return ($a->vote_rank < $b->vote_rank) ? 1 : -1;

	}
		
/**
 * Order by Translation Votes
 */
	
	public function order_by_votes() {

		if ( !$this->translations || !$this->translations_votes ) {
			return false;
		}
		
		usort($this->translations->posts, array('mld_Dictionary','compare_translation_order'));
						
	}	
		
/**
 * List Translation Names
 */

	public function list_translations() {
	
		if ($this->translations) {
			while ( $this->translations->have_posts() ) {
				$this->translations->the_post();				
				echo '<br/>';
				echo get_the_title();
				echo '<br/>';
			}
		} else {
			echo 'No translations Found';
		}
		
		// Reset post
		wp_reset_postdata();	
		
	}	

/**
 * Translations UL in Admin
 */

	public function list_translations_admin_ul( $show_details = true, $page_slug = '?page=mld-translations', $assigned_language_ids = false) {
	
		if ( !is_admin() ) {
			return false;
		}
		
		if ( !$this->translations ) {
			return false;
		}
		
		echo '<ul>';
		
		while ( $this->translations->have_posts() ) {
			$this->translations->the_post();

			$post_meta = get_post_meta($this->translations->post->ID);
			
			// Only show edit and delete options if assigned to both translation languages or is Super Admin
			$user_can_update = false;	
			if ( $assigned_language_ids && 
			     in_array($post_meta['_mld_source_language'][0], $assigned_language_ids) &&
				 in_array($post_meta['_mld_translation_language'][0], $assigned_language_ids) ||
				 is_super_admin() ) {
				$user_can_update = true;	 
			}
						
			echo '<li><a class="mld-show-details" href="">'.get_the_title().'</a>';
			
			if ($user_can_update) {			
				echo '<a class="mld-delete" data-type="translation" href="'.$page_slug.'&amp;delete-translation='.$this->translations->post->ID.'"></a>
					  <a class="mld-edit" href="?page=mld-manage-translation&amp;edit='.$this->translations->post->ID.'"></a>';			
			
				if ( $post_meta['_mld_approved'][0] != '1') {
					echo '<a class="mld-approve" href="'.$page_slug.'&amp;approve-translation='.$this->translations->post->ID.'"></a>';
				}			
			}
			
			if ($show_details) {
					  
				echo '<div class="mld-translation-details">
				      	  <p>
							  <label>Language:</label> '.$this->languages['id_index'][$post_meta['_mld_source_language'][0]]['name'].' &raquo;
								  '.$this->languages['id_index'][$post_meta['_mld_translation_language'][0]]['name'].'
						  </p>
						  <p>
						  	  <label>Translation:</label> '.$post_meta['_mld_translation'][0].'
						  </p>
						  <p>
						  	  <label>Part of Speech:</label> '.$this->parts_speech['id_index'][$post_meta['_mld_part_speech'][0]].'
						  </p>
						  <p>
						  	  <label>Field:</label> '.$this->fields['id_index'][$post_meta['_mld_field'][0]].'
						  </p>
						  <p>
						  	  <label>English Definition:</label> '.get_the_content().'
						  </p>';
						  
				if ( strtolower($this->languages['id_index'][$post_meta['_mld_source_language'][0]]['name']) != 'english' ) {		  
						  
					echo '<p>
						  	  <label>'.$this->languages['id_index'][$post_meta['_mld_source_language'][0]]['name'].' Definition:</label> '.$post_meta['_mld_source_language_definition'][0].'
						  </p>';
					
				}
				
				if ( strtolower($this->languages['id_index'][$post_meta['_mld_translation_language'][0]]['name']) != 'english' ) {		  
						  
					echo '<p>
						  	  <label>'.$this->languages['id_index'][$post_meta['_mld_translation_language'][0]]['name'].' Definition:</label> '.$post_meta['_mld_target_language_definition'][0].'
						  </p>';
					
				}		  
						  
					echo '<p>
						 	  <label>Notes:</label> '.$post_meta['_mld_notes'][0].'
						  </p>
						  <p>
						  	  <label>Usage Examples:</label><br/>';
					
				if ( is_array($post_meta['_mld_usage_example']) ) {
					echo '<ol>';
					foreach ( $post_meta['_mld_usage_example'] as $usage_example ) {
						echo '<li>'.$usage_example.'</li>';
					}
					echo '</ol>';
				}
				
				echo '	  	</p>
				            <p>  
								<label>Sources:</label><br/>';
				
				if ( is_array($post_meta['_mld_source']) ) {
					echo '<ol>';
					foreach ( $post_meta['_mld_source'] as $key => $source ) {
						echo '<li>'.$source . ' <em>(' . $this->source_types['id_index'][$post_meta['_mld_source_type'][$key]] . ')</em>' . '</li>';
					}	  
					echo '</ol>';
				}
						  
				echo '      </p>';
				
				$author_display = get_the_author();
				if ( $post_meta['_mld_display_author'][0] != '1' ) {
					$author_display.= ' <em>(Anonymous on website)</em>';
				}
				
				echo '		<p>
								<label>Submitted by:</label> '.$author_display.'
							</p>
				
	
							<p class="mld_vote_block">
							    <label>Votes:</label> <span>'.$this->translations->post->vote_percentage.'%</span> ('.$this->translations->post->vote_count_up.' / '.$this->translations->post->vote_count_total.')				
					        </p>';
			
			}
			
			echo '</li>';
		}
		
		echo '</ul>';

		// Reset post
		wp_reset_postdata();
	
	}

/**
 * Translations UL in Front-end
 */

	public function list_translations_front_ul( $show_details = true ) {

		if ( !$this->translations ) {
			return false;
		}
		
		echo '<div class="mld-front-results">';
		
		while ( $this->translations->have_posts() ) {

			$this->translations->the_post();

			$post_meta = get_post_meta($this->translations->post->ID);
			$term_definition = get_the_content();
						
			echo '<div class="mld-result">';
			
			// Check if there is a part of speech for this translation
			$part_speech = '';
			if ( strlen( $this->parts_speech['id_index'][$post_meta['_mld_part_speech'][0]] ) > 0 ) {
				$part_speech = '<em>&mdash; '.$this->parts_speech['id_index'][$post_meta['_mld_part_speech'][0]].'</em>';
			}						
			
			// If it's a reverse term, reverse the display of the name and languages
			if ( $post_meta['_mld_source_language'][0] != $this->source_language['id'] ) {
				echo '<p class="mld-languages">'.$this->languages['id_index'][$post_meta['_mld_translation_language'][0]]['name'].' &raquo;
							  '.$this->languages['id_index'][$post_meta['_mld_source_language'][0]]['name'].'</p>';
			} else {									
				echo '<p class="mld-languages">'.$this->languages['id_index'][$post_meta['_mld_source_language'][0]]['name'].' &raquo;
							  '.$this->languages['id_index'][$post_meta['_mld_translation_language'][0]]['name'].'</p>';
			}
			
			// If it's a reverse term, reverse the display of the name and languages
			if ( $post_meta['_mld_source_language'][0] != $this->source_language['id'] ) {
				echo '<span class="mld-term-title">'.get_the_title().'</span> 
				      ('.$this->languages['id_index'][$post_meta['_mld_translation_language'][0]]['name'].': '.$post_meta['_mld_translation'][0].') '.$part_speech.' <hr/>';
			} else {
				echo '<span class="mld-term-title">'.$post_meta['_mld_translation'][0].'</span> 
				      ('.$this->languages['id_index'][$post_meta['_mld_source_language'][0]]['name'].': '.get_the_title().') '.$part_speech.' <hr/>';
			}
    
			// Votes
	
			echo '    <div class="mld_vote_block">
					      <span>'.$this->translations->post->vote_percentage.'%</span>
						  '.$this->translations->post->vote_count_up.'<a title="Vote for this term." href="'.strtok($_SERVER['REQUEST_URI']).'?mld_upvote='.$this->translations->post->ID.'"><img class="mld_vote_thumb mld_thumb_up" src="'.plugins_url().'/multi-dictionary/images/thumb-up.png" alt="Vote Up" /></a>
						  '.$this->translations->post->vote_count_down.'<a title="Vote against this term." href="'.strtok($_SERVER['REQUEST_URI']).'?mld_downvote='.$this->translations->post->ID.'"><img class="mld_vote_thumb mld_thumb_down" src="'.plugins_url().'/multi-dictionary/images/thumb-down.png" alt="Vote Down" /></a>
					  </div>';				

			if ( strlen( $this->fields['id_index'][$post_meta['_mld_field'][0]] ) > 0 ) {
				echo '<p><label>Field:</label> '.$this->fields['id_index'][$post_meta['_mld_field'][0]].'</p>';
			}			
			
			if ( strlen($term_definition) > 1 ) {
				echo '<p><label>English Definition:</label> '.$term_definition.'</p>';
			}
			
			if ( strtolower($this->languages['id_index'][$post_meta['_mld_source_language'][0]]['name']) != 'english' &&
			     strlen($post_meta['_mld_source_language_definition'][0]) > 1 ) {		  
					  
				echo '<p>
						  <label>'.$this->languages['id_index'][$post_meta['_mld_source_language'][0]]['name'].' Definition:</label> '.$post_meta['_mld_source_language_definition'][0].'
					  </p>';
				
			}
			
			if ( strtolower($this->languages['id_index'][$post_meta['_mld_translation_language'][0]]['name']) != 'english' &&
			     strlen($post_meta['_mld_target_language_definition'][0]) > 1 ) {		  
					  
				echo '<p>
						  <label>'.$this->languages['id_index'][$post_meta['_mld_translation_language'][0]]['name'].' Definition:</label> '.$post_meta['_mld_target_language_definition'][0].'
					  </p>';
				
			}		  
		
			if ( is_array($post_meta['_mld_usage_example']) ) {
				echo '<p><label>Usage Examples:</label></p>';
				echo '<ol>';
				foreach ( $post_meta['_mld_usage_example'] as $usage_example ) {
					echo '<li>'.$usage_example.'</li>';
				}
				echo '</ol>';
			}
			
			if ( is_array($post_meta['_mld_source']) ) {
				echo '<p><label>Sources:</label></p>';
				echo '<ol>';
				foreach ( $post_meta['_mld_source'] as $key => $source ) {
					echo '<li>'.$source . ' <em>(' . $this->source_types['id_index'][$post_meta['_mld_source_type'][$key]] . ')</em>' . '</li>';
				}	  
				echo '</ol>';
			}
			
			if ( $post_meta['_mld_display_author'][0] == '1' ) {
				$author_display = get_the_author();
				echo '<p><label>Submitted by:</label> '.$author_display.'</p>';
			}

							
			echo '</div>';
		}
				
		echo '</div>';

		// Reset post
		wp_reset_postdata();
	
	}
	
/**
 * Languages Select Dropdown Options in Admin (Language ID value and show uppercase language name)
 */

	public function display_languages_select_options( $selected_language = false ) {
		
		if ( !$this->languages ) {
			return false;
		}
		
		foreach ( $this->languages['id_index'] as $key => $language ) {
			if ( $selected_language == $key || strtolower($selected_language) == strtolower($language['name']) ) {
				echo '<option selected value="'.$key.'">'.$language['name'].'</option>'."\n";
			} else {
				echo '<option value="'.$key.'">'.$language['name'].'</option>'."\n";
			}
		}
		
	}	

/**
 * Languages Select Dropdown Options for Front-end (lowercase language value and show uppercase language name)
 */
	
	public function display_languages_select_options_front( $language_type ) {
		
		if ( !$this->languages ) {
			return false;
		}
		
		if ( $language_type == 'source_language' ) {
			$selected_language = $this->source_language['id'];
		} elseif ( $language_type == 'translation_language' ) {
			$selected_language = $this->translation_language['id'];
		}

		foreach ( $this->languages['name_index'] as $language_lowercase_name => $language ) {
			if ( $selected_language == $language['id'] ) {
				echo '<option selected value="'.$language_lowercase_name.'">'.$language['name'].'</option>'."\n";
			} else {
				echo '<option value="'.$language_lowercase_name.'">'.$language['name'].'</option>'."\n";
			}
		}
		
	}	

/**
 * Parts of Speech Select Options
 */

	public function display_parts_speech_select_options( $selected_part_speech = false ) {
		
		if ( !$this->parts_speech ) {
			return false;
		}
		
		foreach ( $this->parts_speech['id_index'] as $key => $part_speech ) {
			if ( $selected_part_speech == $key || strtolower($selected_part_speech) == $part_speech ) {
				echo '<option selected value="'.$key.'">'.$part_speech.'</option>'."\n";
			} else {
				echo '<option value="'.$key.'">'.$part_speech.'</option>'."\n";
			}
		}
		
	}	

/**
 * Fields Select Options
 */

	public function display_fields_select_options( $selected_field = false ) {
		
		if ( !$this->fields ) {
			return false;
		}
		
		foreach ( $this->fields['id_index'] as $key => $field ) {
			if ( $selected_field == $key || strtolower($selected_field) == $field ) {
				echo '<option selected value="'.$key.'">'.$field.'</option>'."\n";
			} else {
				echo '<option value="'.$key.'">'.$field.'</option>'."\n";
			}
		}
		
	}	

/**
 * Source Types Select Options
 */

	public function display_source_types_select_options( $selected_source = false ) {
		
		if ( !$this->source_types ) {
			return false;
		}
		
		foreach ( $this->source_types['id_index'] as $key => $source_type ) {
			if ( $selected_source == $key || strtolower($selected_source) == $source_type ) {
				echo '<option selected value="'.$key.'">'.$source_type.'</option>'."\n";
			} else {
				echo '<option value="'.$key.'">'.$source_type.'</option>'."\n";
			}
		}
		
	}	
	
/**
 * Return total count of approved terms
 */

	public function count_total_terms() {
	
		$this->get_approved();
		return number_format( count($this->translations->posts) );
	
	}

/**
 * Creates Leaderboard
 */

	public function create_leaderboard() {
	
		$output_html = '<table class="mld-leaderboard"><tbody><tr class="heading-row"><td>#</td><td>Name</td><td>Submissions</td></tr>';
	
		$this->get_approved();
		$authors = array();
		
		foreach ($this->translations->posts as $id => $post) {
		
			$author_id = $post->post_author;
			if ( array_key_exists($author_id, $authors) ) {
				$authors[$author_id]++;
			} else {
				$authors[$author_id] = 1;
			}
		
		}
		
		// Order Authors by number of contributions
		arsort($authors);
		$count = 1;
		
		foreach ( $authors as $author_id => $term_count ) {
			if ($count > 10) {
				continue;
			}
			$author_name = get_the_author_meta( 'user_nicename', $author_id );
			$output_html.= '<tr><td>'.$count.'</td><td>'.$author_name.'</td><td>'.$term_count.'</td></tr>';
			$count++;
		}
		$output_html.= '</tbody></table>';
				
		return $output_html;
	
	}


} // End Class