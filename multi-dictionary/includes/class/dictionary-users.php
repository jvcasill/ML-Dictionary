<?php

/**
 * Dictionary Users Class
 * 
 * For use in managing dictionary moderators
 *
 */ 

class mld_Dictionary_Users {
	
	public $languages = false;
	public $moderators = false;
	public $moderator_languages = false;
	public $moderator = false;
	public $moderator_assigned_langs = false;
	
	public function __construct( $args = false ) {

		// Load languages and users
		$this->loadSettings();
		
	}
	
/**
 * Load Languages
 */

	public function loadSettings() {
	
	// Load Languages
	
		$this->args = array( 
			'post_type' => 'mld_language', 
			'orderby' => 'title', 
			'order' => 'ASC' 
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

			}
			
		} else {
			$this->languages = false;
		}
		
		// Reset post
		wp_reset_postdata();
		
		// Get moderators
		$this->get_moderators();
		
	}

/**
 * Load Moderators
 */

	public function get_moderators() {
	
		$args = array(
			'blog_id'      => $GLOBALS['blog_id'],
			'role'         => 'mld_moderator',
			'meta_key'     => '',
			'meta_value'   => '',
			'meta_compare' => '',
			'meta_query'   => array(),
			'include'      => array(),
			'exclude'      => array(),
			'orderby'      => 'login',
			'order'        => 'ASC',
			'offset'       => '',
			'search'       => '',
			'number'       => '',
			'count_total'  => false,
			'fields'       => 'all',
			'who'          => ''
		);
		
		$this->moderators = get_users( $args );
		
		if ( $this->moderators ) {
			
			$this->moderator_languages = array();
			foreach ( $this->moderators as $moderator ) {
				$moderator_langs = get_user_meta( $moderator->ID, '_mld_assigned_languages');
				if ( $moderator_langs ){
					$this->moderator_languages[$moderator->ID] = $moderator_langs;
				}
			}
				
			return true;
		} else {
			return false;
		}
	
	}

/**
 * Display Users
 */

	public function list_moderators_ul($page_slug = '?page=mld-settings') {
	
		if ($this->moderators) {
			foreach ($this->moderators as $moderator) {			
				echo '<li><span class="mld_moderator_name">'.$moderator->display_name.'</span>
				          <a class="mld-delete" data-type="moderator" href="'.$page_slug.'&amp;remove-moderator='.$moderator->ID.'"></a>';
				
				$this->display_language_checkboxes($moderator->ID);
				
				echo  '</li>';
			}
		}
		
	}
	
/**
 * List Languages Checkboxes for Assigning Languages to Moderators
 */

	public function display_language_checkboxes ( $moderator_id ) {
		
		if ( !$this->languages || !is_numeric($moderator_id) ) {
			return false;
		}
							
		foreach ( $this->languages['id_index'] as $key => $language ) {

			$checked = '';

			if ( is_array($this->moderator_languages[$moderator_id]) && in_array($key, $this->moderator_languages[$moderator_id]) ) {
				$checked = ' checked ';
			}
			echo '<span class="mld_lang_checkboxes">
					  <input '.$checked.' class="mld_lang_checkbox" type="checkbox" id="lang['.$moderator_id.']['.$key.']" name="lang['.$moderator_id.']['.$key.']" value="1">
					  <label for="lang['.$moderator_id.']['.$key.']">'.$language['name'].'</label>
				  </span>';
		}
		
	}	

/**
 * Update Moderator Languages
 */

	public function update_assigned_languages ( $form_input_array ) {
		
		if ( !is_array($form_input_array) ) {
			return false;
		} else {
			// Update each moderator
			foreach ($this->moderators as $moderator) {
	
				delete_user_meta( $moderator->ID, '_mld_assigned_languages' );				
				
				if ( !is_array($form_input_array[$moderator->ID]) ) {
					continue;
				}
				
				foreach ( $form_input_array[$moderator->ID] as $language_id => $value ) {
					add_user_meta( $moderator->ID, '_mld_assigned_languages', $language_id, false );
				}
				
			}
		}
		
	}
	
/**
 * Set Current Moderator
 */

	public function set_moderator ( $moderator ) {
		
		$this->moderator = $moderator;
		
		$moderator_langs = get_user_meta( $this->moderator->ID, '_mld_assigned_languages');
		if ( $moderator_langs ){
			$this->moderator_assigned_langs = $moderator_langs;
		} else {
			$this->moderator_assigned_langs = false;
		}

	}	
	
	
}