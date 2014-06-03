<?php

/**
 * Front-end Search Form: Header
 * 
 */ 

?>
    
<form id="mld_search" class="mld_search_header" name="mld_search" action="" method="post" enctype="multipart/form-data">
    <input type="hidden" id="mld_search_performed" name="mld_search_performed" />

        <input type="text" id="mld_term" name="mld_term" class="form-control" value="<?php echo $GLOBALS['mld_dictionary']->term['name']; ?>" placeholder="enter search text" />
        
        <div class="mld_no_wrap">
        
            <select id="mld_source_language" name="mld_source_language" class="mld_select form-control mld_source_language">
                <option class="mld_source_lang_change" value="false">Source Language</option>
                <?php $GLOBALS['mld_dictionary']->display_languages_select_options_front( 'source_language' ); ?>                    
            </select>
            
            <select id="mld_translation_language" name="mld_translation_language" class="mld_select form-control mld_translation_language">
                <option class="mld_translation_lang_change" value="false">Target Language</option>
                <?php $GLOBALS['mld_dictionary']->display_languages_select_options_front( 'translation_language' ); ?>                    
            </select>
            
            <div class="btn-group">
                <span class="mld_desktop">
                	<input class="button mld_submit btn btn-info" type="submit" value="Search" />
                </span>
                <span class="mld_mobile">
                	<input class="button mld_submit btn btn-info" type="submit" value="Go" />
                </span>
                
            </div>              
            
        </div>

</form>