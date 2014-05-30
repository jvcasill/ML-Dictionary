<?php

/**
 * Front-end Search Form
 * 
 */ 

?>
    
<form id="mld_search" name="mld_search" action="" method="post" enctype="multipart/form-data">
    <input type="hidden" id="mld_search_performed" name="mld_search_performed" />
    <p>
        <label for="mld_term" class="mld_term">Term:</label>
        <input type="text" id="mld_term" name="mld_term" class="mld_term" value="<?php echo $GLOBALS['mld_dictionary']->term['name']; ?>" />
        
        <select id="mld_source_language" name="mld_source_language" class="mld_select">
            <option value="false">Source Language</option>
            <?php $GLOBALS['mld_dictionary']->display_languages_select_options_front( 'source_language' ); ?>                    
        </select>
        
        <select id="mld_translation_language" name="mld_translation_language" class="mld_select">
            <option value="false">Target Language</option>
            <?php $GLOBALS['mld_dictionary']->display_languages_select_options_front( 'translation_language' ); ?>                    
        </select>
                      
        <input class="button mld_submit" type="submit" value="Search" />
    </p>                     

</form>