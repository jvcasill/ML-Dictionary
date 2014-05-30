<?php

/**
 * Dictionary Class
 * 
 */ 

class mld_Dictionary {
	
	// Customized "Settings" Set in Admin
	public $languages = false;
	public $fields = false;
	public $source_types = false;
	
	// Term/Search Criteria
	public $term = false;
	public $source_language = false;
	public $translation_language = false;
	public $approval_status = false;
	
	// Whether or not a search has been made
	public $search = false;
	public $exact_match = false;
	
	// The WP_Query Object
	private $query;
	
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
		$meta_query = array();
		
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
			'orderby' => 'title', 
			'order' => 'ASC',
			'posts_per_page' => -1
		);
		
		if ( $this->term ) {
			
			$post_ids = $wpdb->get_col("select ID from $wpdb->posts where post_title = '".$this->term['name']."' AND post_type = 'mld_translation' ");
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
			
		}
		
		$this->query = new WP_Query($this->args);
		
		if ( $this->query->have_posts() ) {
			$this->translations = $this->query;
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
						  	  <label>Field:</label> '.$this->fields['id_index'][$post_meta['_mld_field'][0]].'
						  </p>
						  <p>
						  	  <label>Definition:</label> '.get_the_content().'
						  </p>
						  <p>
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
						  
				echo'       </p>
				
	
							<p class="mld_vote_block">
							    <strong>Votes:</strong> <span>'.$this->translations->post->vote_percentage.'%</span> ('.$this->translations->post->vote_count_up.' / '.$this->translations->post->vote_count_total.')				
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
									
			echo '<p class="mld-languages">'.$this->languages['id_index'][$post_meta['_mld_source_language'][0]]['name'].' &raquo;
							  '.$this->languages['id_index'][$post_meta['_mld_translation_language'][0]]['name'].'</p>';
							  
			echo '<span class="mld-term-title">'.$post_meta['_mld_translation'][0].'</span> ('.$this->languages['id_index'][$post_meta['_mld_source_language'][0]]['name'].': '.get_the_title().') <hr/>';
    
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
				echo '<p><label>Definition:</label> '.$term_definition.'</p>';
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


}