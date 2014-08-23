<?php

/**
 * Front-end Add Translation (Styled for Lingreference.org)
 * 
 */ 

global $wp_query;
$mld_trailing_slash = mld_check_trailing_slash();

$mld_term = '';
$mld_source_language = false;
$mld_translation_language = false;

$mld_translation_added = false;

/**
 * Get the term and languages if submitted from another page to get started
 */
 
if ( isset($_POST['mld_add_translation']) ) {
	$mld_term = sanitize_text_field($_POST['mld_term']);
	$mld_source_language = (int) $_POST['mld_source_language'];
	$mld_translation_language = (int) $_POST['mld_translation_language'];
}

/**
 * Add Translation
 */


if ( isset($_POST['mld_add_translation___nonce']) ) {

	if ( !is_user_logged_in() ) {
	// Check the captcha

		$mld_captcha_options = array (
			1 => 'X6J',
			2 => '2FQ',
			3 => 'TFE',
			4 => 'Y4U',
			5 => 'PWU',
			6 => 'DW5',
			7 => 'L28',
			8 => 'S2M',
			9 => 'A2X',
			10 => 'AR6',
			11 => '7FK',
			12 => 'GHK',
			13 => 'GY9',
			14 => '3YM',
			15 => 'UR5'
		);	
		
		if ( !isset($_POST['mld_captcha_input']) || !isset($_POST['mld_captcha_num']) ) {
			exit();
		}

		if ( strtoupper($_POST['mld_captcha_input']) != $mld_captcha_options[$_POST['mld_captcha_num']] ) {
			exit();
			die('Captcha is wrong or javascript is disbaled.');
		}

	}
	
	$mld_term_add = sanitize_text_field( $_POST['mld_term'] );
	$mld_definition = sanitize_text_field( $_POST['mld_definition'] );
	$mld_target_language_definition = sanitize_text_field( $_POST['mld_target_language_definition'] );
	
	if ( is_user_logged_in() ) {
		$mld_author = get_current_user_id();
	} else {
		$mld_author = 'unknown';
	}
		
	// Create the translation
	$translation = array(
		'post_title' => $mld_term_add,
		'post_content' => $mld_definition,
		'post_type' => 'mld_translation',
		'post_status' => 'publish',
		'post_author' => $mld_author,
	);
	$translation_id = wp_insert_post( $translation );	
	
	$data = array(
		'_mld_translation' => $_POST['mld_translation'],
		'_mld_source_language_definition' => $_POST['mld_source_language_definition'],
		'_mld_target_language_definition' => $_POST['mld_target_language_definition'],
		'_mld_source_language' => $_POST['mld_source_language'],
		'_mld_translation_language' => $_POST['mld_translation_language'],
		'_mld_notes' => $_POST['mld_notes'],
		'_mld_part_speech' => $_POST['mld_part_speech'],
		'_mld_display_author' => $_POST['mld_display_author'],
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

	// All front-end translations are unapproved
	add_post_meta($translation_id, '_mld_approved', 0);
	
	$mld_translation_added = true;
	
	do_action('mld_submit_translation');

}

?>

<form id="mld_add_translation" class="mld_lingreference" name="mld_add_translation" action="<?php echo get_bloginfo('url').'/dictionary/add-translation'.$mld_trailing_slash; ?>" method="post" enctype="multipart/form-data">

<?php if ( $mld_translation_added ) { ?>
	<div id="mld_translation_added_success">
    	Thank you.  <strong>Your translation has been added</strong> and is pending approval.<br/>
    	Would you like to add another translation?
    </div>
<?php } ?>

    <input type="hidden" id="mld_add_translation___nonce" name="mld_add_translation___nonce" value="" />

<?php
// Term
?>

	<div class="mld_term">
        <label for="mld_term"><?php _e( 'Term:<span class="req">*</span> ', 'multilingual-dictionary' ); ?></label>
        <input class="form-control" type="text" id="mld_term" name="mld_term" value="<?php echo $mld_term; ?>" size="25" placeholder="Enter term" /><br/>
	</div>
    
<?php        
// Translation
?>
	<div class="mld_translation">
        <label for="mld_translation"><?php _e( 'Translation:<span class="req">*</span> ', 'multilingual-dictionary' ); ?></label>
        <input class="form-control" type="text" id="mld_translation" name="mld_translation" value="" size="25" placeholder="Enter term translation" /><br/>
	</div>

<?php
// Source Language			
	
	// Retrive the languages that have been set
	$args = array( 'post_type' => 'mld_language', 'orderby' => 'title', 'order' => 'ASC' );
	$languages_query = new WP_Query( $args );
				
?>
	<div class="mld_source_language">
        <label for="mld_source_language"><?php _e( 'Source Language:<span class="req">*</span> ', 'multilingual-dictionary' ); ?></label>
        
        <select class="form-control add-trans-field" id="mld_source_language" name="mld_source_language">
        <option value="false">Select Source Language</option>
    
<?php
		$GLOBALS['mld_dictionary']->display_languages_select_options($mld_source_language);
?>
        </select><br/>
    </div>
<?php	
		// Rewind posts
		$languages_query->rewind_posts();

// Translation Language
?>	
	<div class="mld_translation_language">

        <label for="mld_translation_language"><?php _e( 'Target Language:<span class="req">*</span> ', 'multilingual-dictionary' ); ?></label>
        
        <select class="form-control add-trans-field" id="mld_translation_language" name="mld_translation_language">
        <option value="false">Select Target Language</option>

<?php
		$GLOBALS['mld_dictionary']->display_languages_select_options($mld_translation_language);
?>
        </select><br/>
    </div>
    
<?php	
// Part of Speech	
?>	
	<div class="mld_part_speech">
    
        <label for="mld_part_speech"><?php _e( 'Part of Speech: ', 'multilingual-dictionary' ); ?></label>
    
        <select class="form-control" id="mld_part_speech" name="mld_part_speech">
        <option>Select Part of Speech</option>
    
<?php
		$GLOBALS['mld_dictionary']->display_parts_speech_select_options();
?>	
        </select><br/>
    </div>
    
<?php	
// Field	
?>	
	<div class="mld_field">
    
        <label for="mld_field"><?php _e( 'Field: ', 'multilingual-dictionary' ); ?></label>
    
        <select class="form-control" id="mld_field" name="mld_field">
        <option>Select Field</option>
    
<?php
		$GLOBALS['mld_dictionary']->display_fields_select_options();
?>	
        </select><br/>
    </div>

<?php
// Definition
?>
	<div class="mld_definition">

        <label for="mld_definition"><?php _e( 'English Definition:<span class="req">*</span> ', 'multilingual-dictionary' ); ?></label>
        <textarea class="form-control" id="mld_definition" name="mld_definition" placeholder="Enter term description"></textarea><br/>

	</div>

<?php
// Source Language Definition
?>
	<div class="mld_source_language_definition">

        <label for="mld_source_language_definition"><?php _e( '<span>Source Language</span> Definition: ', 'multilingual-dictionary' ); ?></label>
        <textarea class="form-control" id="mld_source_language_definition" name="mld_source_language_definition" placeholder="Enter term description (in source language)"></textarea><br/>

	</div>

<?php
// Target Language Definition
?>

	<div class="mld_target_language_definition">

        <label for="mld_target_language_definition"><?php _e( '<span>Target Language</span> Definition: ', 'multilingual-dictionary' ); ?></label>
        <textarea class="form-control" id="mld_target_language_definition" name="mld_target_language_definition" placeholder="Enter term description (in target language)"></textarea><br/>

	</div>

<?php	
// Source(s) (and Source Type(s))	
?>
	
	<div class="mld_source">
	
		<label for="mld_source"><?php _e( 'Source(s): ', 'multilingual-dictionary' ); ?></label><br/>
        <input class="form-control" type="text" name="mld_source[0]" value="" placeholder="Enter source" />

        <select class="form-control" id="mld_source_type" name="mld_source_type[0]">
			<option value="false">Select Type</option>
<?php
		$GLOBALS['mld_dictionary']->display_source_types_select_options();
?>
        </select>

	</div>
		
	<div><a class="mld_add-source" href="" data-key="0">+ Add another source.</a><br/></div>

<?php		
// Usage Example(s)	
?>
		
        <div class="mld_usage_example">
        
            <label for="mld_usage_example"><?php _e( 'Usage Example(s): ', 'multilingual-dictionary' ); ?></label><br/>
			<input class="form-control wide" type="text" name="mld_usage_example[0]" value="" placeholder="Enter usage example" /><br/>	

    </div>

	<div><a class="mld_add-usage-example" href="" data-key="0">+ Add another usage example.</a><br/></div>

<?php	
// Notes
?>

	<label for="mld_notes"><?php _e( 'Notes: ', 'multilingual-dictionary' ); ?></label>
    	
    <textarea class="form-control" id="mld_notes" name="mld_notes" placeholder="Please share any comments or relevant notes regarding your translation here"></textarea>

<?php
// Display Author Name Check

if ( is_user_logged_in() ):
?>
	<div class="mld_display_author">

        <input type="hidden" name="mld_display_author" value="0" />
        <label for="mld_display_author" style="position: relative; top: -10px; display: block; max-width: none; width: auto; max-width: none;"><?php _e( 'Would you like your name to be shown publicly as the author of this translation? ', 'multilingual-dictionary' ); ?>
        <br/>
        <input style="display: inline; width: auto; position: relative; top: 12px; margin: 0 5px 0 10px;" type="checkbox" value="1" class="form-control" id="mld_display_author" name="mld_display_author" /> <em style="font-weight: normal;">Yes, please display my name.</em><br/>
		</label>
	</div>
<?php
endif;
?>
         
<?php
// Captcha
if ( !is_user_logged_in() ) {
?>    
    <?php $mld_captcha_number = rand(1,15); ?>

    	<label class="mld_captcha" for="mld_captcha"><?php _e( 'Please enter the following letters/numbers (spam filter): ', 'multilingual-dictionary' ); ?></label>

    	<div class="mld_captcha">
        	<img class="mld_captcha_img" src="<?php echo plugins_url(); ?>/multi-dictionary/images/captcha/<?php echo $mld_captcha_number; ?>.jpg" alt="captcha" />
	    	<input class="form-control" type="text" class="mld_captcha_input" id="mld_captcha_input" name="mld_captcha_input" />
        	<input type="hidden" name="mld_captcha_num" value="<?php echo $mld_captcha_number; ?>" />
        </div>
<?php
	}

	// Reset post
	wp_reset_postdata();
?>  
    <p class="alert">
        Some required fields are missing.<br/>Please check your entries above and re-submit.
    </p>

    <p class="mld_center"><input id="add_translation" class="button btn btn-info" type="submit" value="Submit Translation" /></p>

</form>