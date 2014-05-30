<?php

/**
 * Translations Admin Page
 * 
 */ 

$page_slug = '?page=mld-admin';

/**
 * Approve Translation
 */

if ( isset( $_GET['approve-translation'] ) ) {
	$approve = (int) $_GET['approve-translation'];
	delete_post_meta($approve, '_mld_approved');
	add_post_meta($approve, '_mld_approved', 1);
}

/**
 * Delete Translation
 */

if ( isset( $_GET['delete-translation'] ) ) {
	
	$delete = (int) $_GET['delete-translation'];
	$translation_type = get_post_type( $delete );
	
	if ( $translation_type == 'mld_translation') {
		wp_delete_post( $delete, true );
	}
	
}

// Initialize the dictionary
$mld_dictionary = new mld_Dictionary();	

// Get the dictionary moderator
$mld_moderators = new mld_Dictionary_Users();
$moderator = wp_get_current_user();
$mld_moderators->set_moderator($moderator);

/**
 * Search Translations
 */

if ( isset($_POST['mld_search__nonce']) ) {

	$mld_dictionary->search = true;
	$page_slug.= '&amp;search';
	
	// Set the search term
	if ( strlen($_POST['mld_term']) > 0 ) {
		$mld_term = sanitize_text_field($_POST['mld_term']);
		$mld_dictionary->set_term($mld_term);
		$page_slug.= '&amp;mld_t='.$mld_term;
	}
	// Set the languages
	if ( $_POST['mld_source_language'] != 'false' ) {
		$source_language = (int) $_POST['mld_source_language'];
		$mld_dictionary->set_source_language($source_language);
		$page_slug.= '&amp;mld_sl='.$source_language;
	}
	if ( $_POST['mld_translation_language'] != 'false' ) {
		$translation_language = (int) $_POST['mld_translation_language'];
		$mld_dictionary->set_translation_language($translation_language);
		$page_slug.= '&amp;mld_tl='.$translation_language;
	}
	if ( $_POST['mld_approval_status'] != 'false' ) {
		$approval_status = (int) $_POST['mld_approval_status'];
		$mld_dictionary->approval_status = $approval_status;
		$page_slug.= '&amp;mld_a='.$approval_status;
	}
	
} elseif ( isset($_GET['search']) ){
	
	$mld_dictionary->search = true;
	$page_slug.= '&amp;search';
	
	// Set the search term
	if ( isset($_GET['mld_t']) && strlen($_GET['mld_t']) > 0 ) {
		$mld_term = sanitize_text_field($_GET['mld_t']);
		$mld_dictionary->set_term($mld_term);
		$page_slug.= '&amp;mld_t='.$mld_term;
	}
	// Set the languages
	if ( isset($_GET['mld_sl']) && $_GET['mld_sl'] != 'false' ) {
		$source_language = (int) $_GET['mld_sl'];
		$mld_dictionary->set_source_language($source_language);
		$page_slug.= '&amp;mld_sl='.$source_language;
	}
	if ( isset($_GET['mld_tl']) && $_GET['mld_tl'] != 'false' ) {
		$translation_language = (int) $_GET['mld_tl'];
		$mld_dictionary->set_translation_language($translation_language);
		$page_slug.= '&amp;mld_tl='.$translation_language;
	}
	if ( isset($_GET['mld_a']) && $_GET['mld_a'] != 'false' ) {
		$approval_status = (int) $_GET['mld_a'];
		$mld_dictionary->approval_status = $approval_status;
		$page_slug.= '&amp;mld_a='.$approval_status;
	}	
}

?>

<div id="mld-admin" class="width-85">
        
    <h1><img src="<?php echo plugins_url(); ?>/multi-dictionary/images/dictionary-icon-large.png" alt="ML Dictionary Icon" /><span>Multilingual Dictionary</span></h1>
    
    <div class="box">
        <h2>Search Translations</h2>
        
        <form id="mld_search" name="mld_search" action="<?php echo $page_slug; ?>" method="post" enctype="multipart/form-data">
        	<input type="hidden" id="mld_search__nonce" name="mld_search__nonce" />
        	<p>
            	<label for="mld_term" class="mld_term">Term:</label>
                <input type="text" id="mld_term" name="mld_term" class="mld_term" value="<?php if ($mld_dictionary->term) { echo $mld_dictionary->term['name']; } ?>" />
                
                <select id="mld_source_language" name="mld_source_language" class="mld_select">
                	<option value="false">Source Language</option>
                    <option value="false" <?php if ($mld_dictionary->search && !$mld_dictionary->source_language) { echo 'selected'; } ?>>&mdash; Any Language &mdash;</option>
					<?php $mld_dictionary->display_languages_select_options($mld_dictionary->source_language['id']); ?>                    
                </select>
                
                <select id="mld_translation_language" name="mld_translation_language" class="mld_select">
                	<option value="false">Translation Language</option>
                    <option value="false" <?php if ($mld_dictionary->search && !$mld_dictionary->translation_language) { echo 'selected'; } ?>>&mdash; Any Language &mdash;</option>
					<?php $mld_dictionary->display_languages_select_options($mld_dictionary->translation_language['id']); ?>                    
                </select>
              
                <select id="mld_approval_status" name="mld_approval_status" class="mld_select">
                	<option value="false">Approval Status</option>
                    <option value="false" <?php if ($mld_dictionary->search && !$mld_dictionary->approval_status ) { echo 'selected'; } ?>>&mdash; Any Status &mdash;</option>
                    <option <?php mld_selected_if($mld_dictionary->approval_status, '0'); ?> value="0">Pending Approval</option>
                    <option <?php mld_selected_if($mld_dictionary->approval_status, '1'); ?> value="1">Approved</option>
                </select>
                
                <input class="button mld_submit" type="submit" value="Search" />
            </p>                     

        </form>
        
<?php
	// Show search results
	if ( $mld_dictionary->search ) {
		if ( $mld_dictionary->get_translations() ) {
			$mld_dictionary->order_by_votes();
			$mld_dictionary->list_translations_admin_ul(true, $page_slug, $mld_moderators->moderator_assigned_langs );
		} else {
			echo '<p class="center mld_no_results">No search results found</p>';
		}
	}
?>
	</div>
        
</div>