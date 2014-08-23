jQuery('document').ready(function(){
								  
	// Submit check
	jQuery('input#add_translation').click(function(e){
		
		var mld_captcha_options = new Array();
		mld_captcha_options[1] = 'X6J';
		mld_captcha_options[2] = '2FQ';
		mld_captcha_options[3] = 'TFE';
		mld_captcha_options[4] = 'Y4U';
		mld_captcha_options[5] = 'PWU';
		mld_captcha_options[6] = 'DW5';
		mld_captcha_options[7] = 'L28';
		mld_captcha_options[8] = 'S2M';
		mld_captcha_options[9] = 'A2X';
		mld_captcha_options[10] = 'AR6';
		mld_captcha_options[11] = '7FK';
		mld_captcha_options[12] = 'GHK';
		mld_captcha_options[13] = 'GY9';
		mld_captcha_options[14] = '3YM';
		mld_captcha_options[15] = 'UR5';
		
		var error = false;
		jQuery('.alert').hide();
					
		var term = jQuery('form#mld_add_translation input[name="mld_term"]').val();
		var translation = jQuery('form#mld_add_translation input[name="mld_translation"]').val();
		var definition = jQuery('form#mld_add_translation textarea[name="mld_definition"]').val();
		var source_language = jQuery('form#mld_add_translation select[name="mld_source_language"]').val();
		var translation_language = jQuery('form#mld_add_translation select[name="mld_translation_language"]').val();
		
		jQuery('form#mld_add_translation .mld_term').removeClass('error-alert');
		jQuery('form#mld_add_translation .mld_translation').removeClass('error-alert');
		jQuery('form#mld_add_translation .mld_definition').removeClass('error-alert');
		jQuery('form#mld_add_translation .mld_source_language').removeClass('error-alert');
		jQuery('form#mld_add_translation .mld_translation_language').removeClass('error-alert');
		jQuery('form#mld_add_translation .mld_captcha').removeClass('error-alert');

		if ( !mld_variables.logged_in ) {
		// Check captcha if not logged in 
			var mld_captcha_number = jQuery('input[name="mld_captcha_num"]').val();
			var mld_captcha_input = jQuery('input[name="mld_captcha_input"]').val();
			mld_captcha_input_upper = mld_captcha_input.toUpperCase();
			if ( mld_captcha_input_upper != mld_captcha_options[mld_captcha_number] ) {
				jQuery('form#mld_add_translation div.mld_captcha').addClass('error-alert');
				error = true;
			}
		}

		if (term.length == 0) {
			jQuery('form#mld_add_translation .mld_term').addClass('error-alert');
			error = true;
		}
		
		if (translation.length == 0) {
			jQuery('form#mld_add_translation .mld_translation').addClass('error-alert');
			error = true;			
		}
		
		if (definition.length == 0) {
			jQuery('form#mld_add_translation .mld_definition').addClass('error-alert');
			error = true;			
		}
		
		if (source_language == 'false') {
			jQuery('form#mld_add_translation .mld_source_language').addClass('error-alert');
			error = true;
		}
		
		if (translation_language == 'false') {
			jQuery('form#mld_add_translation .mld_translation_language').addClass('error-alert');
			error = true;			
		}
		
		if (translation_language == source_language) {
			jQuery('form#mld_add_translation .mld_source_language').addClass('error-alert');
			jQuery('form#mld_add_translation .mld_translation_language').addClass('error-alert');
			error = true;			
		}
		
		if (error) {
			jQuery('form#mld_add_translation .alert').show();
			e.preventDefault();
		}
		
	});

	// Add additional sources
	var source_count = 0;
	var source_field = '';
	var source_select_html = jQuery('select#mld_source_type').html();

	jQuery('a.mld_add-source').click(function(e){
		e.preventDefault();
		source_count++;
		if (source_count < 5) {
			source_select_html_display = '<select class="form-control" id="mld_source_type['+source_count+']" name="mld_source_type['+source_count+']">'+source_select_html+'</select>';
			source_field = '<input type="text" class="form-control" name="mld_source['+source_count+']" value="" placeholder="Enter another source" /> '+source_select_html_display;
			jQuery('div.mld_source').append(source_field);
		}
		
		if (source_count == 4) {
			jQuery('a.mld_add-source').hide();
		}
		
	});		
	
	// Add additional usage examples
	var usage_example_count = 0;
	var usage_example_field = '';
	jQuery('a.mld_add-usage-example').click(function(e){
		e.preventDefault();
		usage_example_count++;
		if (usage_example_count < 5) {
			usage_example_field = '<input class="wide form-control" type="text" name="mld_usage_example['+usage_example_count+']" value="" placeholder="Enter another usage example" />';
			jQuery('div.mld_usage_example').append(usage_example_field);
		}
		
		if (usage_example_count == 4) {
			jQuery('a.mld_add-usage-example').hide();
		}
	});
	
});

// Voting logged in check
jQuery('.mld_vote_block a').click(function(event){
	if ( !mld_variables.logged_in ) {
		event.preventDefault();
		
		var login_to_vote_html = jQuery('#mld_login_to_vote_notice').html();
		jQuery(this).closest('div').append(login_to_vote_html);
		jQuery('.mld_login_to_vote').show('fast');
		jQuery('body').addClass('mld_login_to_vote_notice');
				
		jQuery('a.mld_close_vote_notice').click(function(event){
			event.preventDefault();
			jQuery('.mld_vote_block .mld_login_to_vote').hide().remove();
		});
		
	}
});


// Definition Fields Checking/Hiding based on language selections
jQuery(document).ready(function(){
								
	hide_source_language_definition();
	hide_target_language_definition();
		
	// Hide Source Language Definition Fields if English is selected for source language
	jQuery('#mld_source_language.add-trans-field').change(function(){
		hide_source_language_definition();
		alert_on_same_language();		
	});
	
	// Hide Target Language Definition Fields if English is selected for target language
	jQuery('#mld_translation_language.add-trans-field').change(function(){
		alert_on_same_language();
		hide_target_language_definition();
	});

	function hide_source_language_definition() {

		var selected_language_id = jQuery("#mld_source_language.add-trans-field").val();
		var selected_language = jQuery("#mld_source_language.add-trans-field option[value='"+selected_language_id+"']").text()
		
		// Update the label text
		if ( !isNaN(parseFloat(selected_language_id)) && isFinite(selected_language_id) && selected_language_id != 'false' ) {
			jQuery('div.mld_source_language_definition label span').html(selected_language);
		} else {
			jQuery('div.mld_source_language_definition label span').html('Source Language');
		}
		
		// Hide the definition if English	
		if ( selected_language == 'English' ) {
			jQuery('div.mld_source_language_definition').hide();
		} else {
			jQuery('div.mld_source_language_definition').show();
		}
		
	}

	function hide_target_language_definition() {
		
		var selected_language_id = jQuery("#mld_translation_language.add-trans-field").val();
		var selected_language = jQuery("#mld_translation_language.add-trans-field option[value='"+selected_language_id+"']").text()

		// Update the label text
		if ( !isNaN(parseFloat(selected_language_id)) && isFinite(selected_language_id) && selected_language_id != 'false' ) {
			jQuery('div.mld_target_language_definition label span').html(selected_language);
		} else {
			jQuery('div.mld_target_language_definition label span').html('Target Language');
		}
		
		// Hide the definition if English	
		if ( selected_language == 'English' ) {
			jQuery('div.mld_target_language_definition').hide();
		} else {
			jQuery('div.mld_target_language_definition').show();
		}
		
	}
	
	function alert_on_same_language() {
		
		var source_language = jQuery('select.add-trans-field[name="mld_source_language"]').val();
		var translation_language = jQuery('select.add-trans-field[name="mld_translation_language"]').val();

		if (translation_language == source_language) {
			jQuery('.mld_source_language').addClass('error-alert');
			jQuery('.mld_translation_language').addClass('error-alert');
		} else {
			jQuery('.mld_source_language').removeClass('error-alert');
			jQuery('.mld_translation_language').removeClass('error-alert');	
		}
	
	}
	
});