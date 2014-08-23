<?php

/**
 * Import Instructions (for Include)
 * 
 */
 
?>

<h2>Import Translations from CSV/TSV</h2>

<ul>
    <li>
        <a class="mld-show-details" href="">Click here for spreadsheet &amp; import instructions.</a>
        <div class="mld-translation-details">

            <h3 style="text-align: center;">How to Import Translations into the ML Dictionary</h3>
            
            <h4 style="margin-bottom: 10px;">Step 1. Creating Your Spreadsheet</h4>
            
            <p>Using Microsoft Excel, Open Office, or a similar spreadsheet tool, create a new spreadsheet.  Each row in your document will contain a single term and its translation details.  You may optionally include column headings in the first row of cells.</p>
            
            <p>To get started, you can also download one of the example spreadsheets with the necessary column structure at the end of this document.</p>
            
            <h4 style="margin-bottom: 10px;">Step 2a. Entering Your Terms</h4>
            
            <p>The column structure of your document <strong>must match</strong> the "Spreadsheet Column Structure" outlined below for a successful import.  Fields marked with an asterisk are <em>required</em>.  All other fields are optional.  If you choose to omit an optional field for a term, <em>leave the cell blank</em>, and proceed to the next column.</p>
            
            <p>The outline below specifies the format in which you may specify values for individual fields.  In most cases, you can use the <strong>name</strong> or the <strong>ID</strong> of the field (language, field, part of speech, etc).</p>
            
            <h4 style="margin-bottom: 10px;">Step 2b. Entering Fields with Multiple Values</h4>
            
            <p>Most fields will require only a single value.  <strong>Usage Examples</strong> and <strong>Sources</strong> are fields that can have <em>multiple</em> values for a single term (ie spreadsheet data cell).  These entries are delimited by a <strong>||</strong> (double-bar).</p>
            
            <p style="padding-top: 10px;"><strong style="display: inline-block; width: 130px; padding-left: 25px;">Usage Examples: </strong> Usage Example One || The sky is blue. || The ocean is blue.</p>
            
            <p style="padding-top: 15px;">Each <strong>Source</strong> entry also requires a Source Type (book, website, etc) &mdash; the Source and Source Type are separated by a <strong>|</strong> (single-bar).</p>
            
            <p style="padding-top: 10px;"><strong style="display: inline-block; width: 130px; padding-left: 25px;">Sources: </strong> Source One | Source Type || http://www.google.com | Website || Green Eggs and Ham by Dr. Seuss | Book</p>
            
            <h4 style="margin-bottom: 10px;">Step 3. Saving Your Document</h4>
            
            <p>Save your document as a CSV (comma-separated values) or TSV (tab-separated values) with UTF-8 encoding and a " (double quote) text delimiter to ensure successful import.</p>
            
            <h4 style="margin-bottom: 10px;">Step 4. Running a Test Import</h4>
            
            <p>Using the import form below, select the "Test" import type.  The import will process your document and display its contents in a table reflecting the structure in which it will be imported.  All fields, source types, languages and other special values will display as they will upon import so that you may check for accuracy.</p>
            
            <p>Any highlighted or missing cells should be checked in your document for errors.  If there are other errors, please check your file's column structure, encoding and delimiter settings when saving and try again.</p>
            
            <h4 style="margin-bottom: 10px;">Step 5. Running a Live Import</h4>
            
            <p>If the output generated during your "Test" import appears correctly, complete the same process selecting "Live" for import type.  Your terms will now be added to the live Dictionary.</p>
            
            <h4 style="margin-bottom: 10px;">Step 6. Viewing, Editing &amp; Removing Imported Terms</h4>
            
            <p>Each import is automatically assigned a <strong>Batch ID</strong> so that you can view, edit and remove terms creating during past imports.</p>
            
            <hr />
            
            <h3>Spreadsheet: Delimiters &amp; Encoding Settings</h3>

            <div style="width: 45%; padding: 0; margin: -10px 0 20px 7.5%; display: inline-block; vertical-align: top;">
                <h3>CSV</h3>
                <strong style="display: inline-block; width: 130px;">Column Delimiter:</strong> <span style="margin: 0 15px;">,</span> (comma)<br/>
                <strong style="display: inline-block; width: 130px;">Text Delimiter:</strong> <span style="margin: 0 15px;">"</span> (double quote)<br/>
                <strong style="display: inline-block; width: 130px;">Encoding:</strong> <span style="margin: 0 15px;">UTF-8</span><br/>
            </div>
            
            <div style="width: 45%; padding: 0; margin: -10px 0 20px 0; display: inline-block; vertical-align: top;">
                <h3>TSV</h3>
                <strong style="display: inline-block; width: 130px;">Column Delimiter:</strong> <span style="margin: 0 15px;">{Tab}</span> <br/>
                <strong style="display: inline-block; width: 130px;">Text Delimiter:</strong> <span style="margin: 0 15px;">"</span> (double quote)<br/>
                <strong style="display: inline-block; width: 130px;">Encoding:</strong> <span style="margin: 0 15px;">UTF-8</span><br/>
            </div>
            
            <hr />
            
            <h3>Spreadsheet: Column Structure</h3>

            <ol style="line-height: 25px;">
                <li><strong>Term*</strong> (String, English)</li>
                <li><strong>Translation*</strong> (String)</li>
                <li><strong>Source Language*</strong> (Language ID <strong>or</strong> Language Name in English)</li>
                <li><strong>Translation Language*</strong> (Language ID <strong>or</strong> Language Name in English)</li>
                <li><strong>Part of Speech</strong> (Part of Speech ID <strong>or</strong> Part of Speech Name)</li>
                <li><strong>Field</strong> (Field ID <strong>or</strong> Feild Name)</li>
                <li><strong>English Definition*</strong> (String)</li>
                <li><strong>Source Language Definition</strong> (String)</li>
                <li><strong>Target Language Definition</strong> (String)</li>
                <li><strong>Sources</strong> (String(s): English, <strong style="color: #009900;">Source | Source Type ID <strong>or</strong> Name</strong>, || Delimited)<br/>
                    <span style="display: block; margin-top: 0px; font-size: .9em;">
                        <strong>Note:</strong> The "Source" and the "Source Type" are separated by bar character "|", and each set of these is separated by a double bar "||"<br/>
                        <strong>Example:</strong> Source Number One | Book || http://www.google.com | Website || Some Publication Name | Journal
                    </span>
                </li>
                <li><strong>Usage Examples</strong> (String(s): English, || Delimited)<br/>
                    <span style="display: block; margin-top: 0px; font-size: .9em;">
                        <strong>Note:</strong> Each usage example is separated by a double bar "||"<br/>
                        <strong>Example:</strong> Usage Example 1 || Usage Example 2 || Usage Example 3
                    </span>
                <li><strong>Notes</strong> (String: English)</li>
                <li><strong>User</strong> (String: User ID <strong>or</strong> "Display Name" <strong>or</strong> Login)</li>
                <li><strong>Display Author</strong> (Int: <strong>1</strong> to show author in search results, <strong>0</strong> to hide author's name)</li>
            </ol>

            <hr />
            
            <h3>Spreadsheet Examples</h3>

            <h4 style="margin-bottom: 10px;">Download Spreadsheets with Headings &amp; Example Terms</h4>
                                
            <p><a download href="<?php echo plugins_url(); ?>/multi-dictionary/admin/misc/example-translation-import.csv">Click here</a> to download an <a download href="<?php echo plugins_url(); ?>/multi-dictionary/admin/misc/example-translation-import.csv">example CSV file</a>.</p>
            <p><a download href="<?php echo plugins_url(); ?>/multi-dictionary/admin/misc/example-translation-import.tsv">Click here</a> to download an <a download href="<?php echo plugins_url(); ?>/multi-dictionary/admin/misc/example-translation-import.tsv">example TSV file</a>.</p>

            <h4 style="margin-bottom: 10px;">Download Spreadsheet with Column Structure &amp; Headings Only</h4>
            
            <p><a download href="<?php echo plugins_url(); ?>/multi-dictionary/admin/misc/example-translation-import-headings.csv">Click here</a> to download an <a download href="<?php echo plugins_url(); ?>/multi-dictionary/admin/misc/example-translation-import-headings.csv">example CSV file with table headings only</a>.</p>

        </div>
    </li>
</ul>