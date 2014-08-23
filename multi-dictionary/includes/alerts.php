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

	$count = (int) $_GET['mld_imported'];
	$errors = (int) $_GET['mld_errored'];
	
	$count = $count - $errors;
    ?>
    <div class="updated mld">
        <p><?php echo $count; ?> Translations have been imported.</p>
    </div>
    <?php
	if ($errors > 0) {
	?>
    <div class="error mld">
        <p style="color: #ff0000;"><?php echo $errors; ?> Translations failed to import:</p>
        <?php
        if ( isset($_SESSION) && is_array($_SESSION['error_rows']) ) {
            echo '<ol>';
            foreach ( $_SESSION['error_rows'] as $term_and_row => $error_text ) {
                echo '<li><strong>'.$term_and_row. ':</strong> <br/>' . $error_text . '  </li>';
            }
            echo '</ol>';
        }
        ?>            
    </div>
    <?php
	}
	?>
    <?php
}
 
if ( isset($_GET['mld_imported']) ) {
	add_action( 'admin_notices', 'translation_imported_notice' );
}
