jQuery(document).ready(function($) {
								
	// Delete confirmation
	$('a.mld-delete').click(function(e){
		var type = $(this).attr('data-type');
		if( !window.confirm("Are you sure you want to delete this "+type+"?")) {
			e.preventDefault();
		}
	});
	
	// Show translation details
	$('a.mld-show-details').click(function(e){
		e.preventDefault();
		if ( $(this).hasClass('up') ) {
			$(this).removeClass('up');
		} else {
			$(this).addClass('up');
		}
		$(this).parent('li').find('.mld-translation-details').toggle('fast');
	});

});