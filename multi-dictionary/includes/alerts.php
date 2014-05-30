<?php

/**
 * Wordpress Alert Messages
 * 
 */ 

/**
 * Translation Added
 */

function translation_added_notice() {
    ?>
    <div class="updated mld">
        <p><?php _e( 'Translation has been added', 'multilingual-dictionary' ); ?></p>
    </div>
    <?php
}
 
if ( isset($_POST['mld_add_translation___nonce']) ) {
	add_action( 'admin_notices', 'translation_added_notice' );
}
 
/**
 * Translation Deleted
 */
 
function translation_deleted_notice() {
	?>
	<div class="updated mld">
		<p><?php _e( 'Translation has been updated', 'multilingual-dictionary' ); ?></p>
	</div>
	<?php
}
 
if ( isset($_POST['mld_edit_translation___nonce']) ) {
	add_action( 'admin_notices', 'translation_deleted_notice' );
}

/**
 * Translation Approved
 */

function translation_approve_notice() {
    ?>
    <div class="updated mld">
        <p><?php _e( 'Translation has been approved', 'multilingual-dictionary' ); ?></p>
    </div>
    <?php
}
 
if ( isset($_GET['approve-translation']) ) {
	add_action( 'admin_notices', 'translation_approve_notice' );
}

/**
 * Translation Deleted
 */

function translation_delete_notice() {
    ?>
    <div class="updated mld">
        <p><?php _e( 'Translation has been deleted', 'multilingual-dictionary' ); ?></p>
    </div>
    <?php
}
 
if ( isset($_GET['delete-translation']) ) {
	add_action( 'admin_notices', 'translation_delete_notice' );
}

/**
 * Translation Imported
 */

function translation_imported_notice() {

	$count = $_GET['mld_imported'];
    ?>
    <div class="updated mld">
        <p><?php echo $count; ?> Translations have been imported.</p>
    </div>
    <?php
}
 
if ( isset($_GET['mld_imported']) ) {
	add_action( 'admin_notices', 'translation_imported_notice' );
}
