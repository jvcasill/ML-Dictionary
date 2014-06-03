jQuery(document).ready(function() {
								
	update_form_fields_for_mobile();
								
	jQuery('.mld_submit').click(function(event) {
	
		event.preventDefault();
		var error = false;
		
		var source_language = jQuery('#mld_source_language').val();
		var translation_language = jQuery('#mld_translation_language').val();
		var term = jQuery('#mld_term').val().toLowerCase();
		
		// Check values
		jQuery('input#mld_term').removeClass('error-alert');
		jQuery('select#mld_source_language').removeClass('error-alert');
		jQuery('select#mld_translation_language').removeClass('error-alert');
		
		if ( term.length < 1 ) {
			error = true;
			jQuery('input#mld_term').addClass('error-alert');
		}
		if ( source_language == 'false' ) {
			error = true;
			jQuery('select#mld_source_language').addClass('error-alert');
		}
		if ( translation_language == 'false' ) {
			error = true;
			jQuery('select#mld_translation_language').addClass('error-alert');
		}
		if ( source_language == translation_language ) {
			error = true;
			jQuery('select#mld_source_language').addClass('error-alert');
			jQuery('select#mld_translation_language').addClass('error-alert');
		}
		
		if ( !error ) {
			var url = mld_variables.blog_url+'/dictionary/'+source_language+'/'+translation_language+'/'+term+mld_variables.trailing_slash;		
			jQuery('form#mld_search').attr('action',url);
			jQuery('form#mld_search').submit();
		}
	
	});
									
});

jQuery( window ).resize(function() {

	update_form_fields_for_mobile();

});

function update_form_fields_for_mobile() {

	var viewport_width = jQuery(window).width();
	var mld_source_lang_select_text = jQuery('.mld_source_lang_change').html();
	var mld_translation_lang_select_text = jQuery('.mld_translation_lang_change').html();
	
	if ( viewport_width < 500 ) {
		
		if ( mld_source_lang_select_text == 'Source Language' ) {
			jQuery('.mld_source_lang_change').html('SL');
			jQuery('.mld_translation_lang_change').html('TL');
		}
		
	} else {

		if ( mld_source_lang_select_text == 'SL' ) {
			jQuery('.mld_source_lang_change').html('Source Language');
			jQuery('.mld_translation_lang_change').html('Target Language');
		}

	}

}