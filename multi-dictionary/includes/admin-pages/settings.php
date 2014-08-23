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
 * Add Part of Speech
 */ 
 
if ( isset( $_POST['mld_add_part_speech__nonce'] ) ) {

	if ( isset( $_POST['part_speech_name'] ) ) {
		$part_speech_name = sanitize_text_field( $_POST['part_speech_name'] );
		
		$part_speech = array(
  			'post_title' => $part_speech_name,
			'post_type' => 'mld_part_speech',
			'post_status' => 'publish',
		);

		if ( strlen($part_speech_name) > 0) {
			$part_speech_id = wp_insert_post( $part_speech );
		}
	}

}

/**
 * Delete Part of Speech
 */ 
 
if ( isset( $_GET['delete-part-speech'] ) ) {
	
	$delete = (int) $_GET['delete-part-speech'];
	$part_speech_type = get_post_type( $delete );
	
	if ( $part_speech_type == 'mld_part_speech') {
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
        <h2>Part of Speech</h2>
    
    	<ul>
<?php

		// Show the part of speech
		$args = array( 'post_type' => 'mld_part_speech', 'orderby' => 'title', 'order' => 'ASC' );
		$fields_query = new WP_Query( $args );
	
		if ( $fields_query->have_posts() ) {
			while ( $fields_query->have_posts() ) {
				$fields_query->the_post();
				echo '<li>'.get_the_title();
				echo '<a class="mld-delete" data-type="part of speech" href="'.$page_slug.'&amp;delete-part-speech='.$fields_query->post->ID.'"></a></li>';
			}
		}
?>
		</ul>
<?php		
		// Add a new part of speech
?>    
        <form id="mld_add_part_speech" name="mld_add_part_speech" action="<?php echo $page_slug; ?>" method="post" enctype="multipart/form-data">
        
            <h3>Add a Part of Speech</h3>
            <input type="hidden" name="mld_add_part_speech__nonce" value="add" />
            <label for="field_name">Part of Speech:</label> <input type="text" name="part_speech_name" /><br/>
            <p class="center"><input class="button" type="submit" value="Add Part of Speech" /></p>
        
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
      
</div>