<?php

/**
 * Front-end Search Results
 * 
 */ 

global $wp_query;
$mld_trailing_slash = mld_check_trailing_slash();

/**
 * Display the search results
 */ 

if ( $GLOBALS['mld_dictionary']->search ) {
	
	if ( $GLOBALS['mld_dictionary']->translations ) {
	
		$GLOBALS['mld_dictionary']->order_by_votes();
	
		if ( $GLOBALS['mld_dictionary']->exact_match ) {
		
			$GLOBALS['mld_dictionary']->list_translations_front_ul();

?>			

		<div class="mld_results">

            <p>Would you like to submit a new <?php echo ucfirst($GLOBALS['mld_dictionary']->source_language['name']); ?> &raquo; <?php echo ucfirst($GLOBALS['mld_dictionary']->translation_language['name']); ?> translation for <strong><?php echo $GLOBALS['mld_dictionary']->term['name']; ?></strong>?</p>
            
            <form id="mld_add_translation" action="<?php echo get_bloginfo('url').'/dictionary/add-translation'.$mld_trailing_slash; ?>" method="post" enctype="multipart/form-data">
            	<input type="hidden" name="mld_add_translation" value="submit" />
            	<input type="hidden" name="mld_term" value="<?php echo $GLOBALS['mld_dictionary']->term['name'];?>" />
                <input type="hidden" name="mld_source_language" value="<?php echo $GLOBALS['mld_dictionary']->source_language['id'];?>" />
                <input type="hidden" name="mld_translation_language" value="<?php echo $GLOBALS['mld_dictionary']->translation_language['id'];?>" />
                
                <p style="text-align: center;"><input class="button btn btn-info" type="submit" value="Submit a Translation" /></p>
            
            </form>
            
        </div>

<?php			
		} else {
?>
        <div class="mld_partial_results">
        
            <p>No <strong><?php echo ucfirst($GLOBALS['mld_dictionary']->source_language['name']); ?> &raquo; <?php echo ucfirst($GLOBALS['mld_dictionary']->translation_language['name']); ?></strong> exact matches were found for <strong><?php echo $GLOBALS['mld_dictionary']->term['name']; ?></strong>, but similar terms were found.</p>
            
            <p>Would you like to submit a new <?php echo ucfirst($GLOBALS['mld_dictionary']->source_language['name']); ?> &raquo; <?php echo ucfirst($GLOBALS['mld_dictionary']->translation_language['name']); ?> translation for <strong><?php echo $GLOBALS['mld_dictionary']->term['name']; ?></strong>?</p>
            
            <form id="mld_add_translation" action="<?php echo get_bloginfo('url').'/dictionary/add-translation'.$mld_trailing_slash; ?>" method="post" enctype="multipart/form-data">
            	<input type="hidden" name="mld_add_translation" value="submit" />
            	<input type="hidden" name="mld_term" value="<?php echo $GLOBALS['mld_dictionary']->term['name'];?>" />
                <input type="hidden" name="mld_source_language" value="<?php echo $GLOBALS['mld_dictionary']->source_language['id'];?>" />
                <input type="hidden" name="mld_translation_language" value="<?php echo $GLOBALS['mld_dictionary']->translation_language['id'];?>" />
                
                <p style="text-align: center;"><input class="button btn btn-info" type="submit" value="Submit a Translation" /></p>
            
            </form>
            
        </div>

<?php
			$GLOBALS['mld_dictionary']->list_translations_front_ul();

		}
	
	
	} else {
?>
        <div class="mld_no_results">
        
            <p>No <strong><?php echo ucfirst($GLOBALS['mld_dictionary']->source_language['name']); ?> &raquo; <?php echo ucfirst($GLOBALS['mld_dictionary']->translation_language['name']); ?></strong> translations were found for <strong><?php echo $GLOBALS['mld_dictionary']->term['name']; ?></strong>.</p>
            
            <p>Would you like to submit a new <?php echo ucfirst($GLOBALS['mld_dictionary']->source_language['name']); ?> &raquo; <?php echo ucfirst($GLOBALS['mld_dictionary']->translation_language['name']); ?> translation for <strong><?php echo $GLOBALS['mld_dictionary']->term['name']; ?></strong>?</p>
            
            <form id="mld_add_translation" action="<?php echo get_bloginfo('url').'/dictionary/add-translation'.$mld_trailing_slash; ?>" method="post" enctype="multipart/form-data">
            	<input type="hidden" name="mld_add_translation" value="submit" />
            	<input type="hidden" name="mld_term" value="<?php echo $GLOBALS['mld_dictionary']->term['name'];?>" />
                <input type="hidden" name="mld_source_language" value="<?php echo $GLOBALS['mld_dictionary']->source_language['id'];?>" />
                <input type="hidden" name="mld_translation_language" value="<?php echo $GLOBALS['mld_dictionary']->translation_language['id'];?>" />
                
                <p style="text-align: center;"><input class="button btn btn-info" type="submit" value="Submit a Translation" /></p>
            
            </form>
            
        </div>
    
<?php

	}

}

?>

<div id="mld_login_to_vote_notice">
	<div class="mld_login_to_vote">
    	<p>Please log in or register to vote. <a class="mld_close_vote_notice" href=""></a></p>
    </div>
</div>