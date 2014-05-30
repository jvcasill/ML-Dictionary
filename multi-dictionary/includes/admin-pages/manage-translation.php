<?php

/**
 * Translations Admin Page
 * 
 */ 

$page_slug = '?page=mld-manage-translation';
$edit_slug = '';
$submit_text = 'Add Translation';
$default_selected = ' selected value="false" ';

// Get the dictionary moderator
$mld_moderators = new mld_Dictionary_Users();
$moderator = wp_get_current_user();
$mld_moderators->set_moderator($moderator);

/**
 * Add & Edit Translation
 */

if ( isset($_POST['mld_add_translation___nonce']) || isset($_POST['mld_edit_translation___nonce']) ) {

	$mld_term = sanitize_text_field( $_POST['mld_term'] );
	$mld_definition = sanitize_text_field( $_POST['mld_definition'] );
		
	if ( isset($_POST['mld_add_translation___nonce']) ) {
	
		$mld_author = get_current_user_id();

		// Create the translation
		$translation = array(
			'post_title' => $mld_term,
			'post_content' => $mld_definition,
			'post_type' => 'mld_translation',
			'post_status' => 'publish',
			'post_author' => $mld_author,
		);
		$translation_id = wp_insert_post( $translation );	
		
	} elseif ( isset($_POST['mld_edit_translation___nonce']) ) {
		$translation_id = (int) $_GET['edit'];
		$translation = array(
			'ID' => $translation_id,
			'post_title' => $mld_term,
			'post_content' => $mld_definition,
			'post_status' => 'publish',
		);
		$translation_id = wp_update_post( $translation );			
	}
	
	$data = array(
		'_mld_translation' => $_POST['mld_translation'],
		'_mld_source_language' => $_POST['mld_source_language'],
		'_mld_translation_language' => $_POST['mld_translation_language'],
		'_mld_notes' => $_POST['mld_notes'],
		'_mld_field' => $_POST['mld_field'],
		'_mld_source' => $_POST['mld_source'],
		'_mld_source_type' => $_POST['mld_source_type'],
		'_mld_usage_example' => $_POST['mld_usage_example'],
	);
	
	foreach ($data as $meta_key => $value) {
		if (is_null($value)) {
			continue;
		}
		if ( !is_array($value) ) {
			$value = sanitize_text_field($value);
			update_post_meta( $translation_id, $meta_key, $value );
		} else {
			if (!empty($value) && is_array($value)) {
				delete_post_meta($translation_id, $meta_key);
				foreach ($value as $value) {
					if (strlen($value) > 0) {
						add_post_meta($translation_id, $meta_key, $value);
					}
				}
			}
		}
	}
	
	if ( isset($_POST['mld_approval']) ) {
		$approval_status = (int) $_POST['mld_approval'];
		delete_post_meta($translation_id, '_mld_approved');
		add_post_meta($translation_id, '_mld_approved', $approval_status);	
	} else {
		delete_post_meta($translation_id, '_mld_approved');
		add_post_meta($translation_id, '_mld_approved', 1);	
	}
}

?>

<div id="mld-admin" class="width-85">
        
    <h1>
    	<img src="<?php echo plugins_url(); ?>/multi-dictionary/images/dictionary-icon-large.png" alt="ML Dictionary Icon" />
        <span>Multilingual Dictionary:</span> <?php if (isset($_GET['edit'])) { ?>Edit Translation<?php } else {  ?>Add Translation<?php } ?>
    </h1>
    
    <div class="box">
        <h2><?php if (isset($_GET['edit'])) { ?>Edit Translation<?php } else {  ?>Add Translation<?php } ?></h2>
        
<?php
if ( isset($_GET['edit']) ) {

	$translation_id = (int) $_GET['edit'];
	
	$default_selected = '';
	$edit_slug = '&amp;edit='.$translation_id;
	$submit_text = 'Update Translation';
	
	$translation_post = get_post($translation_id); 
	$term = $translation_post->post_title;
	$term_definition = $translation_post->post_content;
	$term_approval_status = get_post_meta( $translation_id, '_mld_approved', true );
	
	// Reset post
	wp_reset_postdata();

	$translation['translation'] = get_post_meta( $translation_id, '_mld_translation', true );
	$translation['source_language'] = get_post_meta( $translation_id, '_mld_source_language', true );
	$translation['translation_language'] = get_post_meta( $translation_id, '_mld_translation_language', true );
	$translation['notes'] = get_post_meta( $translation_id, '_mld_notes', true );
	$translation['field'] = get_post_meta( $translation_id, '_mld_field', true );
	$translation['source'] = get_post_meta( $translation_id, '_mld_source', false ); // Multiple entries possible
	$translation['source_type'] = get_post_meta( $translation_id, '_mld_source_type', false ); // Multiple entries possible
	$translation['usage_example'] = get_post_meta( $translation_id, '_mld_usage_example', false ); // Multiple entries possible

}
?>        
		<form id="mld_add_translation" name="mld_add_translation" action="<?php echo $page_slug . $edit_slug; ?>" method="post" enctype="multipart/form-data">

<?php
if ( isset($_GET['edit']) ) {
	
	echo '<input type="hidden" id="mld_edit_translation___nonce" name="mld_edit_translation___nonce" value="" />';
	
} else {
	$translation = array (
		'source_language',
		'translation_language',
		'translation',
		'notes',
		'field',
		'source' => array(),
		'source_type' => array(),
		'usage_example' => array(),
	);
	
	echo '<input type="hidden" id="mld_add_translation___nonce" name="mld_add_translation___nonce" value="" />';
}

// Approval Status

if ( isset($term_approval_status) ){

	if ( $term_approval_status == 0 ) {
		$term_approved = '';
		$term_unapproved = 'selected';
	} else {
		$term_approved = 'selected';	
		$term_unapproved = '';	
	}
?>
	<div class="mld_approval">
        <label for="mld_approval"><?php _e( 'Approval Status: ', 'multilingual-dictionary' ); ?></label>
        <select id="mld_approval" name="mld_approval">
        	<option <?php echo $term_unapproved; ?> value="0">Unapproved</option>
            <option <?php echo $term_approved; ?> value="1">Approved</option>
        </select>
	</div>

<?php
}

// Term
?>

	<div class="mld_term">
        <label for="mld_term"><?php _e( 'Term: ', 'multilingual-dictionary' ); ?></label>
        <input type="text" id="mld_term" name="mld_term" value="<?php echo $term; ?>" size="25" /><br/>
	</div>
    
<?php        
// Translation
?>
	<div class="mld_translation">
        <label for="mld_translation"><?php _e( 'Translation: ', 'multilingual-dictionary' ); ?></label>
        <input class="error" type="text" id="mld_translation" name="mld_translation" value="<?php echo esc_attr( $translation['translation'] ); ?>" size="25" /><br/>
	</div>
	<hr/>

<?php
// Source Language			
	
	// Retrive the languages that have been set
	$args = array( 'post_type' => 'mld_language', 'orderby' => 'title', 'order' => 'ASC' );
	$languages_query = new WP_Query( $args );
				
?>
	<div class="mld_source_language">
        <label for="mld_source_language"><?php _e( 'Source Language: ', 'multilingual-dictionary' ); ?></label>
        
        <select id="mld_source_language" name="mld_source_language">
        <option <?php echo $default_selected; ?>>Select Source Language</option>
    
<?php
		if ( $languages_query->have_posts() ) {
			while ( $languages_query->have_posts() ) {
				$languages_query->the_post();
				$selected = false;
				
				if ( in_array($languages_query->post->ID, $mld_moderators->moderator_assigned_langs ) || is_super_admin() ) {
					if ($languages_query->post->ID == esc_attr( $translation['source_language'] ) ) { 
						$selected = ' selected '; 
					}
					echo '<option '.$selected.' value="'.$languages_query->post->ID.'">'.get_the_title().'</option>';
				}
			}
		}
?>
        </select><br/>
    </div>
<?php	
		// Rewind posts
		$languages_query->rewind_posts();

// Translation Language
?>	
	<div class="mld_translation_language">

        <label for="mld_translation_language"><?php _e( 'Translation Language: ', 'multilingual-dictionary' ); ?></label>
        
        <select id="mld_translation_language" name="mld_translation_language">
        <option <?php echo $default_selected; ?>>Select Translation Language</option>

<?php
		if ( $languages_query->have_posts() ) {
			while ( $languages_query->have_posts() ) {
				$languages_query->the_post();
				$selected = false;
				
				if ( in_array($languages_query->post->ID, $mld_moderators->moderator_assigned_langs ) || is_super_admin() )	{			
					if ($languages_query->post->ID == esc_attr( $translation['translation_language'] ) ) { 
						$selected = ' selected '; 
					}
					echo '<option '.$selected.' value="'.$languages_query->post->ID.'">'.get_the_title().'</option>';
				}

			}
		}
?>
        </select><br/>
    </div>
<?php	
// Field	
	
	// Retrive the fields that have been set
	$args = array( 'post_type' => 'mld_field', 'orderby' => 'title', 'order' => 'ASC' );
	$fields_query = new WP_Query( $args );
?>	
	<div class="mld_field">
    
        <label for="mld_field"><?php _e( 'Field: ', 'multilingual-dictionary' ); ?></label>
    
        <select id="mld_field" name="mld_field">
        <option <?php echo $default_selected; ?>>Select Field</option>
    
<?php
		if ( $fields_query->have_posts() ) {
			while ( $fields_query->have_posts() ) {
				$fields_query->the_post();
				$selected = false;
				if ($fields_query->post->ID == esc_attr( $translation['field'] ) ) { 
					$selected = ' selected '; 
				}
				echo '<option '.$selected.' value="'.$fields_query->post->ID.'">'.get_the_title().'</option>';
			}
		}
?>	
        </select><br/>
    </div>
    <hr/>

<?php
// Definition
?>
	<div class="mld_definition">

        <label for="mld_definition"><?php _e( 'English Definition: ', 'multilingual-dictionary' ); ?></label>
        <textarea id="mld_definition" name="mld_definition"><?php echo $term_definition; ?></textarea><br/>

	</div>
	<hr/>

<?php	
// Source(s) (and Source Type(s))	
	
	// Retrive the source types that have been set
	$args = array( 'post_type' => 'mld_source_type', 'orderby' => 'title', 'order' => 'ASC' );
	$source_types_query = new WP_Query( $args );
?>
	
	<div class="mld_source">
	
		<label for="mld_source"><?php _e( 'Source(s): ', 'multilingual-dictionary' ); ?></label><br/>
<?php        
		for ($source_count=0; $source_count<count($translation["source"]);$source_count++) {
			echo "<input type=\"text\" name=\"mld_source[".$source_count."]\" value=\"".$translation["source"][$source_count]."\" />";
			
				// Drop down menu of source types
				echo '<select id="mld_source_type" name="mld_source_type['.$source_count.']">';
				echo '<option '.$default_selected.'>Select Type</option>';

				if ( $source_types_query->have_posts() ) {
					while ( $source_types_query->have_posts() ) {
						$source_types_query->the_post();
						$selected = false;
						if ($source_types_query->post->ID == esc_attr( $translation['source_type'][$source_count] ) ) { 
							$selected = ' selected '; 
						}
						echo '<option '.$selected.' value="'.$source_types_query->post->ID.'">'.get_the_title().'</option>';
					}
				}
				echo '</select><br/>';
				
				$source_types_query->rewind_posts();
				
		}
		$source_count--;
		
		if ( count($translation["source"]) == 0) {

			$source_count = 0;
?>
			<input type="text" name="mld_source[0]" value="" />

			<select id="mld_source_type" name="mld_source_type[0]">
			<option value="false">Select Type</option>
<?php
			if ( $source_types_query->have_posts() ) {
				while ( $source_types_query->have_posts() ) {
					$source_types_query->the_post();
					echo '<option value="'.$source_types_query->post->ID.'">'.get_the_title().'</option>';
				}
			}
?>
			</select>
<?php
		}
?>

	</div>
		
	<div><a class="mld_add-source" href="" data-key="<?php echo $source_count; ?>">+ Add another source.</a><br/></div>

<?php		
// Usage Example(s)		
?>
		
	<div class="mld_usage_example">
	
		<label for="mld_usage_example"><?php _e( 'Usage Example(s): ', 'multilingual-dictionary' ); ?></label><br/>

<?php        
		for ($usage_example_count=0; $usage_example_count<count($translation["usage_example"]);$usage_example_count++) {
			echo "<input class=\"wide\" type=\"text\" name=\"mld_usage_example[".$usage_example_count."]\" value=\"".$translation["usage_example"][$usage_example_count]."\" /><br/>";
		}
		$i--;
		
		if ( count($translation["usage_example"]) == 0) {

			$usage_example_count = 0;
			echo '<input class="wide" type="text" name="mld_usage_example[0]" value="" /><br/>';		

		}
?>
	
    </div>

	<div><a class="mld_add-usage-example" href="" data-key="<?php echo $usage_example_count; ?>">+ Add another usage example.</a><br/></div>
	<hr />

<?php	
// Notes
?>

	<label for="mld_notes"><?php _e( 'Notes: ', 'multilingual-dictionary' ); ?></label>
	
    <textarea id="mld_notes" name="mld_notes"><?php echo esc_attr( $translation['notes'] ); ?></textarea><br/>

<?php	
	// Reset post
	wp_reset_postdata();
    
?>  
			<p class="alert">
            	Some required fields are missing.  Please check your entries above and re-submit.
            </p>
    
			<p class="center"><input id="add_translation" class="button" type="submit" value="<?php echo $submit_text; ?>" /></p>
			  
		</form>

	</div>
        
</div>

<script>

jQuery('document').ready(function(){

	// Submit check
	jQuery('input#add_translation').click(function(e){
		
		var error = false;
		jQuery('.alert').hide();
					
		var term = jQuery('input[name="mld_term"]').val();
		var translation = jQuery('input[name="mld_translation"]').val();
		var definition = jQuery('textarea[name="mld_definition"]').val();
		var source_language = jQuery('select[name="mld_source_language"]').val();
		var translation_language = jQuery('select[name="mld_translation_language"]').val();
		
		jQuery('.mld_term').removeClass('error-alert');
		jQuery('.mld_translation').removeClass('error-alert');
		jQuery('.mld_definition').removeClass('error-alert');
		jQuery('.mld_source_language').removeClass('error-alert');
		jQuery('.mld_translation_language').removeClass('error-alert');

		if (term.length == 0) {
			jQuery('.mld_term').addClass('error-alert');
			error = true;
		}
		
		if (translation.length == 0) {
			jQuery('.mld_translation').addClass('error-alert');
			error = true;			
		}
		
		if (definition.length == 0) {
			jQuery('.mld_definition').addClass('error-alert');
			error = true;			
		}
		
		if (source_language == 'false') {
			jQuery('.mld_source_language').addClass('error-alert');
			error = true;
		}
		
		if (translation_language == 'false') {
			jQuery('.mld_translation_language').addClass('error-alert');
			error = true;			
		}
		
		if (translation_language == source_language) {
			jQuery('.mld_source_language').addClass('error-alert');
			jQuery('.mld_translation_language').addClass('error-alert');
			error = true;			
		}
		
		if (error) {
			jQuery('.alert').show();
			e.preventDefault();
		}
		
	});

	// Add additional sources
	var source_count = <?php echo $source_count; ?>;
	var source_field = '';
	jQuery('a.mld_add-source').click(function(e){
		e.preventDefault();
		source_count++;
		if (source_count < 5) {
			source_field = '<input type="text" name="mld_source['+source_count+']" value="" /><select id="mld_source_type" name="mld_source_type['+source_count+']"><option value="">Select Type</option><?php
			if ( $source_types_query->have_posts() ) {
				while ( $source_types_query->have_posts() ) {
					$source_types_query->the_post();
					echo '<option value="'.$source_types_query->post->ID.'">'.get_the_title().'</option>';
				}
			}
			echo '</select><br/>';
			?>';
			jQuery('div.mld_source').append(source_field);
		}
		
		if (source_count == 4) {
			jQuery('a.mld_add-source').hide();
		}
		
	});		
	
	// Add additional usage examples
	var usage_example_count = <?php echo $usage_example_count; ?>;
	var usage_example_field = '';
	jQuery('a.mld_add-usage-example').click(function(e){
		e.preventDefault();
		usage_example_count++;
		if (usage_example_count <= 5) {
			usage_example_field = '<input class="wide" type="text" name="mld_usage_example['+usage_example_count+']" value="" />';
			jQuery('div.mld_usage_example').append(usage_example_field);
		}
		
		if (usage_example_count == 5) {
			jQuery('a.mld_add-usage-example').hide();
		}
	});		

});

</script>