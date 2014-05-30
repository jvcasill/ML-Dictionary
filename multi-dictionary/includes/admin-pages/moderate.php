<?php

/**
 * Translations Admin Page
 * 
 */ 

$page_slug = '?page=mld-moderate';

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

?>

<div id="mld-admin" class="width-85">
        
    <h1><img src="<?php echo plugins_url(); ?>/multi-dictionary/images/dictionary-icon-large.png" alt="ML Dictionary Icon" /><span>Multilingual Dictionary:</span> Moderate</h1>
    
    <div class="box">
        <h2>Translations Pending Approval</h2>
        
<?php
	if ($mld_moderators->moderator_assigned_langs) {

		if ( $mld_dictionary->get_translations( '0', $mld_moderators->moderator_assigned_langs ) ) {
		
			$mld_dictionary->list_translations_admin_ul(true, $page_slug, $mld_moderators->moderator_assigned_langs );
			
		} else {
		
			echo '<p class="center">There are currently no translations pending approval.  Check back soon!</p>';
	
		}

	} else {
	
		echo '<p class="center">It appears you haven\'t been assigned any languages to moderate yet.</p>';
	
	}
?>
	</div>
    
</div>