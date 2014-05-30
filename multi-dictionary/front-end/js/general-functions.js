jQuery(document).ready(function() {
								
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