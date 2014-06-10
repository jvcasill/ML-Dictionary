<?php

/**
 * Settings Admin Page
 * 
 */
 
$page_slug = '?page=mld-settings';
 
$mld_users = new mld_Dictionary_Users();
 
/**
 * Add Language
 */ 
 
if ( isset( $_POST['mld_add_lang__nonce'] ) ) {

	if ( isset( $_POST['native_spelling'] ) && isset( $_POST['language_name'] ) ) {
		$language_name = sanitize_text_field( $_POST['language_name'] );
		$native_spelling = sanitize_text_field( $_POST['native_spelling'] );
		
		$language = array(
  			'post_title' => $language_name,
			'post_type' => 'mld_language',
			'post_status' => 'publish',
		);
		
		if ( strlen($language_name) > 0) {
			$language_id = wp_insert_post( $language );
			update_post_meta( $language_id, '_mld_native_spelling', $native_spelling );
		}
		
	}

}

/**
 * Delete Language
 */ 
 
if ( isset( $_GET['delete-language'] ) ) {
	
	$delete = (int) $_GET['delete-language'];
	$language_type = get_post_type( $delete );
	
	if ( $language_type == 'mld_language') {
		wp_delete_post( $delete, true );
	}
	
} 

/**
 * Add Field
 */ 
 
if ( isset( $_POST['mld_add_field__nonce'] ) ) {

	if ( isset( $_POST['field_name'] ) ) {
		$field_name = sanitize_text_field( $_POST['field_name'] );
		
		$field = array(
  			'post_title' => $field_name,
			'post_type' => 'mld_field',
			'post_status' => 'publish',
		);

		if ( strlen($field_name) > 0) {
			$field_id = wp_insert_post( $field );
		}
	}

}

/**
 * Delete Field
 */ 
 
if ( isset( $_GET['delete-field'] ) ) {
	
	$delete = (int) $_GET['delete-field'];
	$field_type = get_post_type( $delete );
	
	if ( $field_type == 'mld_field') {
		wp_delete_post( $delete, true );
	}
	
} 

/**
 * Add Source Type
 */ 
 
if ( isset( $_POST['mld_add_source_type__nonce'] ) ) {

	if ( isset( $_POST['source_type_name'] ) ) {
		$source_type_name = sanitize_text_field( $_POST['source_type_name'] );
		
		$source_type = array(
  			'post_title' => $source_type_name,
			'post_type' => 'mld_source_type',
			'post_status' => 'publish',
		);
		
		if ( strlen($source_type_name) > 0) {
			$source_type_id = wp_insert_post( $source_type );
		}
	}

}

/**
 * Delete Source Type
 */ 
 
if ( isset( $_GET['delete-source-type'] ) ) {
	
	$delete = (int) $_GET['delete-source-type'];
	$source_type_type = get_post_type( $delete );
	
	if ( $source_type_type == 'mld_source_type') {
		wp_delete_post( $delete, true );
	}
	
}

/**
 * Remove Moderator
 */ 
 
if ( isset( $_GET['remove-moderator'] ) ) {

	$moderator_id = (int) $_GET['remove-moderator'];
	$moderator = wp_update_user( array( 'ID' => $moderator_id, 'role' => 'subscriber' ) );
	$mld_users->get_moderators();

}

/**
 * Update Moderator
 */ 
 
if ( isset( $_POST['mld_update_moderators__nonce'] ) ) {

	$mld_users->update_assigned_languages($_POST['lang']);
	$mld_users->get_moderators();
	
}

/**
 * Import Translations
 */ 
 
if ( isset( $_POST['mld_mld_import_csv__nonce'] ) ) {

	if ( isset($_POST['mld_csv_heading']) && $_POST['mld_csv_heading'] == 'true') {
		mld_upload_csv($_FILES['mld_csv'], true);
	} else {
		mld_upload_csv($_FILES['mld_csv'], false);
	}
	
}

?>

<div id="mld-admin" class="width-85">
        
    <h1><img src="<?php echo plugins_url(); ?>/multi-dictionary/images/dictionary-icon-large.png" alt="ML Dictionary Icon" /><span>Multilingual Dictionary:</span> Settings</h1>
    
    <div class="box">
        <h2>Languages</h2>
        
        <ul>
<?php

		// Show the languages
		$args = array( 'post_type' => 'mld_language', 'orderby' => 'title', 'order' => 'ASC' );
		$languages_query = new WP_Query( $args );
	
		if ( $languages_query->have_posts() ) {
			while ( $languages_query->have_posts() ) {
				$languages_query->the_post();
				$native_spelling = get_post_meta( $languages_query->post->ID, '_mld_native_spelling', true );
				echo '<li><strong>'.get_the_title() . '</strong> ('.$native_spelling.') ';
				echo '<a class="mld-delete" data-type="language" href="'.$page_slug.'&amp;delete-language='.$languages_query->post->ID.'"></a></li>';
			}
		}		
?>    
		</ul>
<?php
		// Add a new language
?>
        <form id="mld_add_language" name="mld_add_language" action="<?php echo $page_slug; ?>" method="post" enctype="multipart/form-data">
        
            <h3>Add a Language</h3>
            <input type="hidden" name="mld_add_lang__nonce" value="add" />
            <label for="language_name">Language:</label> <input type="text" name="language_name" /><br/>
            <label for="native_spelling">Native Spelling:</label> <input type="text" name="native_spelling" /><br/>
            <p class="center"><input class="button" type="submit" value="Add Language" /></p>
        
        </form>
    
    </div>

    <div class="box">
        <h2>Fields</h2>
    
    	<ul>
<?php

		// Show the fields
		$args = array( 'post_type' => 'mld_field', 'orderby' => 'title', 'order' => 'ASC' );
		$fields_query = new WP_Query( $args );
	
		if ( $fields_query->have_posts() ) {
			while ( $fields_query->have_posts() ) {
				$fields_query->the_post();
				echo '<li>'.get_the_title();
				echo '<a class="mld-delete" data-type="field" href="'.$page_slug.'&amp;delete-field='.$fields_query->post->ID.'"></a></li>';
			}
		}
?>
		</ul>
<?php		
		// Add a new field
?>    
        <form id="mld_add_field" name="mld_add_field" action="<?php echo $page_slug; ?>" method="post" enctype="multipart/form-data">
        
            <h3>Add a Field</h3>
            <input type="hidden" name="mld_add_field__nonce" value="add" />
            <label for="field_name">Field:</label> <input type="text" name="field_name" /><br/>
            <p class="center"><input class="button" type="submit" value="Add Field" /></p>
        
        </form>
        
    </div>
    
    <div class="box">
        <h2>Source Types</h2>
    
    	<ul>
<?php

		// Show the source types
		$args = array( 'post_type' => 'mld_source_type', 'orderby' => 'title', 'order' => 'ASC' );
		$source_types_query = new WP_Query( $args );
	
		if ( $source_types_query->have_posts() ) {
			while ( $source_types_query->have_posts() ) {
				$source_types_query->the_post();
				echo '<li>'.get_the_title();
				echo '<a class="mld-delete" data-type="source type" href="'.$page_slug.'&amp;delete-source-type='.$source_types_query->post->ID.'"></a></li>';
			}
		}
?>
		</ul>
<?php		
		// Add a new field
?>    
        <form id="mld_add_source_type" name="mld_add_source_type" action="<?php echo $page_slug; ?>" method="post" enctype="multipart/form-data">
        
            <h3>Add a Source Type</h3>
            <input type="hidden" name="mld_add_source_type__nonce" value="add" />
            <label for="source_type_name">Source Type:</label> <input type="text" name="source_type_name" /><br/>
            <p class="center"><input class="button" type="submit" value="Add Source Type" /></p>
        
        </form>
    
    </div>
    
    <div class="box">
        <h2>Moderators</h2>
           
        <p>Check the languages you would like each user to moderate.</p>
      
        <form id="mld_update_moderators" name="mld_update_moderators" action="<?php echo $page_slug; ?>" method="post" enctype="multipart/form-data">
       
        	<input type="hidden" name="mld_update_moderators__nonce" value="update" />
        
            <ul>
<?php

if ( $mld_users->moderators ) {
	$mld_users->list_moderators_ul($page_slug);
} else {
	echo '<li>There are currently no Dictionary Moderators aside from the Administrators.</li>';
}

?>
            </ul>
<?php if ( $mld_users->moderators ) { ?>
            <p class="center"><br/><input class="button" type="submit" value="Update Moderator Settings" /></p>
<?php } ?>        
        </form>        

        <p><br/><strong>Note:</strong> To add a new dictionary moderator, <a href="<?php echo get_bloginfo('url'); ?>/wp-admin/user-new.php">add a new Wordpress user</a> and select "Dictionary Moderator" as the user's role.</p>

	</div>   
    
    <div class="box">
        <h2>Import Translations from CSV</h2>
        
        <p>To use this feature, store each translation as a <em>single row</em> in a CSV file with the column structure oulined below.<br/><br/>
        	<strong>Column Delimiter:</strong> <span style="margin: 0 15px;">,</span> (Comma)<br/>
            <strong>Text Delimiter:</strong> <span style="margin: 0 15px;">"</span> (Apostophe)<br/>
            <strong>Encoding:</strong> <span style="margin: 0 15px;">UTF-8</span><br/>
        </p>
        
        <hr />
        
        <h3>CSV Column Structure</h3>
        
        <ul>
        	<li><strong>Term*</strong> (String, English)</li>
            <li><strong>Translation*</strong> (String)</li>
            <li><strong>Source Language*</strong> (Language ID or Language Name in English)</li>
            <li><strong>Translation Language*</strong> (Language ID or Language Name in English)</li>
            <li><strong>Field</strong> (Field ID or Feild Name)</li>
            <li><strong>Definition*</strong> (String, English)</li>
            <li><strong>Sources</strong> (String(s), English, <strong style="color: #009900;">Source | Source Type ID or Name</strong>, || Delimited)<br/>
            	<span style="display: block; margin-top: 10px; font-size: .9em;">
               		<strong>Note:</strong> The "Source" and the "Source Type" are separated by bar character "|", and each set of these is separated by a double bar "||"
                </span>
            </li>
            <li><strong>Usage Examples</strong> (String(s), English, || Delimited)<br/>
                <span style="display: block; margin-top: 10px; font-size: .9em;">
                    <strong>Note:</strong> Each usage example is separated by a double bar "||"
                </span>
            <li><strong>Notes</strong> (String, English)</li>
        </ul>
        
        <p style="padding-top: 15px;"><a download href="<?php echo plugins_url(); ?>/multi-dictionary/admin/misc/example-translation-import.csv">Click here</a> to download an <a download href="<?php echo plugins_url(); ?>/multi-dictionary/admin/misc/example-translation-import.csv">example CSV file</a>.</p>
                         
        <form id="mld_import_csv" name="mld_import_csv" action="<?php echo $page_slug; ?>" method="post" enctype="multipart/form-data">
 
         	<hr />
             
        	<input type="hidden" name="mld_mld_import_csv__nonce" value="update" />
            
            <p style="margin: 0; padding: 15px 0 5px 0;">Check the following box if your CSV has a heading as the first row.  If the first row contains a translation, leave unchecked.</p>
            
            <label for="mld_csv_heading">CSV Has Heading Row:</label> <input type="checkbox" name="mld_csv_heading" value="true" /><br/>
        
        	<label for="mld_csv">Select your file:</label> <input type="file" name="mld_csv" id="mld_csv" />
        
            <p class="center"><input class="button" type="submit" value="Import Translations" /></p>

        </form>        

	</div>    
     
    
</div>