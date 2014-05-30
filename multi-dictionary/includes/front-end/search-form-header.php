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
                <option class="mld_desktop" value="false">Source Language</option>
                <option class="mld_mobile" value="false">SL</option>
                <?php $GLOBALS['mld_dictionary']->display_languages_select_options_front( 'source_language' ); ?>                    
            </select>
            
            <select id="mld_translation_language" name="mld_translation_language" class="mld_select form-control mld_translation_language">
                <option class="mld_desktop" value="false">Target Language</option>
                <option class="mld_mobile" value="false">TL</option>
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