<?php

/**
 * Front-end Search Form: Homepage
 * 
 */ 

?>
    
<form id="mld_search" class="mld_search_home" name="mld_search" action="" method="post" enctype="multipart/form-data">
    <input type="hidden" id="mld_search_performed" name="mld_search_performed" />

        <input type="text" id="mld_term" name="mld_term" class="form-control" value="<?php echo $GLOBALS['mld_dictionary']->term['name']; ?>" placeholder="search linguistic term" />
        
        <select id="mld_source_language" name="mld_source_language" class="mld_select form-control mld_source_language">
            <option value="false">Source Language</option>
            <?php $GLOBALS['mld_dictionary']->display_languages_select_options_front( 'source_language' ); ?>                    
        </select>
        
        <select id="mld_translation_language" name="mld_translation_language" class="mld_select form-control mld_translation_language">
            <option value="false">Target Language</option>
            <?php $GLOBALS['mld_dictionary']->display_languages_select_options_front( 'translation_language' ); ?>                    
        </select>
        
        <div class="btn-group">
		    <input class="button mld_submit btn btn-info" type="submit" value="Search" />
        </div>              

</form>