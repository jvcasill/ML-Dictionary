<?php

/**
 * Main Admin Page
 * 
 */ 

$page_slug = '?page=mld-admin';


?>

<div id="mld-admin" class="width-75">
        
    <h1><img src="<?php echo plugins_url(); ?>/multi-dictionary/images/dictionary-icon-large.png" alt="ML Dictionary Icon" /><span>Multilingual Dictionary</span></h1>
    
    <div class="box">
        <h2>Heading</h2>
	
        <?php
		$dictionary = new mld_Dictionary();
		
		$dictionary->set_source_language('english');
		$dictionary->set_translation_language('spanish');
		
		if ( $dictionary->get_approved() ) {
			$dictionary->list_translations_admin_ul();
		} else {
			echo 'No translations found.';
		}
		?>

    
    </div>
        
</h1>