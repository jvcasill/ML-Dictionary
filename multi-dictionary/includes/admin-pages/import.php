<?php

/**
 * Import Admin Page
 * 
 */
 
$page_slug = '?page=mld-import';

/**
 * Deleted Imported Translations Batch
 */ 

if ( isset($_GET['batch_id']) && isset($_GET['delete']) ) {

	$batch_id = (int) $_GET['batch_id'];
	$the_import = get_post( $batch_id );
	if ( $the_import && $the_import->post_type == 'mld_import' ) {
		$imported_ids = $the_import->post_content;
		$imported_ids = explode(',',$imported_ids);
		
		foreach ( $imported_ids as $key => $term_id ) {
			// Delete the terms
			wp_delete_post( $term_id, true );
		}
		$deleted_count = count($imported_ids);
		
		// Delete the import record
		wp_delete_post( $the_import->ID, true );
	?>
		<div class="updated mld">
			<p><?php echo $deleted_count; ?> Imported translations have been deleted.</p>
		</div>
	<?php
    }

} 

/**
 * Import Translations
 */ 
 
if ( isset( $_POST['mld_mld_import_csv__nonce'] ) ) {
	
	$file_type = 'csv';
	if ( $_POST['mld_file_type'] == 'tsv' ) {
		$file_type = 'tsv';
	}

	if ( isset($_POST['mld_csv_heading']) && $_POST['mld_csv_heading'] == 'true') {
		
		if ( isset($_POST['mld_import_type']) && $_POST['mld_import_type'] == 'test') {
			mld_upload_csv($_FILES['mld_csv'], true, $file_type, 'test');
		} else {
			mld_upload_csv($_FILES['mld_csv'], true, $file_type);
		}
		
	} else {

		if ( isset($_POST['mld_import_type']) && $_POST['mld_import_type'] == 'test') {
			mld_upload_csv($_FILES['mld_csv'], false, $file_type, 'test');
		} else {
			mld_upload_csv($_FILES['mld_csv'], false, $file_type);
		}
	
	}
	
}

?>

<div id="mld-admin" class="width-85">
        
    <h1><img src="<?php echo plugins_url(); ?>/multi-dictionary/images/dictionary-icon-large.png" alt="ML Dictionary Icon" /><span>Multilingual Dictionary:</span> Import</h1>
     
    <div class="box">
        
        <?php include ('import-instructions.php'); ?>

    </div>
    
    <div class="box">
                         
        <form style="padding-top: 0; margin-top: 0;" id="mld_import_csv" name="mld_import_csv" action="<?php echo $page_slug; ?>" method="post" enctype="multipart/form-data">
             
            <h3 style="text-align: center;">Import Terms</h3>
             
        	<input type="hidden" name="mld_mld_import_csv__nonce" value="update" />

			<p style="margin: 0; padding: 15px 0 5px 0;">Does your spreadsheet have a row of column headings?</p>                        
            
            	<select name="mld_csv_heading">
                	<option value="true">Yes, don't import the first row.</option>
                    <option value="false">No, import the first row.</option>
                </select>
            <br/>
         
            <label for="mld_csv_heading">File Type:</label> 
            
            <select id="mld_file_type" name="mld_file_type">
            	<option value="csv">CSV &mdash; UTF-8</option>
                <option value="tsv">TSV &mdash; UTF-8</option>
            </select>
            <br/>
            
            <label for="mld_csv_heading">Import Type:</label> 
            
            <select id="mld_import_type" name="mld_import_type">
            	<option value="live" selected>Live &mdash; Import to Database</option>
                <option value="test">Test &mdash; Check Spreadsheet for Errors</option>
            </select>
            <br/>
        
        	<label for="mld_csv">Select your file:</label> <input type="file" name="mld_csv" id="mld_csv" />
        
            <p class="center"><input class="button" type="submit" value="Import Translations" /></p>

        </form> 
        
    </div>
    
    <div class="box">
            
        <h3 style="text-align: center;">Import History</h3>
        
        <?php
		$args = array( 
			'post_type' => 'mld_import', 
			'orderby' => 'date', 
			'order' => 'ASC',
			'post_status' => 'any'
		);
		
		$imports = new WP_Query($args);
		
		if ( $imports->have_posts() ) {
			echo '<table cellspacing="0" cellpadding="0" class="import-history"><tbody>
					  <tr class="history-import-heading-row">
						  <td>Date</td>
						  <td class="center">Batch ID</td>
						  <td class="center">Term Count</td>
						  <td class="center">View Terms</td>
						  <td class="center">Delete Imported Terms</td>
					  </tr>
			     ';
		
			while ( $imports->have_posts() ) {
				
				$imports->the_post();
				
				$imported_ids = $imports->post->post_content;
				$imported_ids = explode(',',$imported_ids);
				$import_count = count($imported_ids);
				
			    echo '<tr>
						  <td>'.date('m/d/Y -- g:ia',strtotime($imports->post->post_date)).'</td>
						  <td class="center">'.$imports->post->ID.'</td>
						  <td class="center">'.$import_count.'</td>
						  <td class="center"><a href="admin.php?page=mld-admin&amp;search&amp;batch_id='.$imports->post->ID.'">[View]</a></td>
						  <td class="center"><a style="float: none; left: 40%;" class="mld-delete" data-type="import" href="admin.php?page=mld-import&amp;batch_id='.$imports->post->ID.'&amp;delete"></a></td>
					  </tr>';
				
			}
			
			echo '</tbody></table>';
		
		} else {
			echo '<p>There is no import history to report.</p>';
		}
		
		?>
        
	</div>    
    
</div>