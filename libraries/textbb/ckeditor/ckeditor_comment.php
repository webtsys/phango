<?php

function load_jscript_editor($name_editor, $value, $profiles='all')
{

	global $base_url, $arr_i18n_ckeditor, $language, $arr_cache_jscript, $arr_cache_jscript_gzipped;
	
	load_libraries(array('emoticons'));

	list($smiley_text, $smiley_img)=set_emoticons();

	$arr_cache_jscript_gzipped['no_gzipped_path_ckeditor']=1;
	$arr_cache_jscript['no_gzipped_path_ckeditor']='ckeditor_path.js.php';
	$arr_cache_jscript[]='textbb--ckeditor--ckeditor.js';

	$edit_image='';

	/*if(ini_get ( "allow_url_fopen" )==1)
	{

		$edit_image=', \'Image\'';

	}*/
	
	?>
	
	<script type="text/javascript">
	//<![CDATA[

		// This call can be placed at any point after the
		// <textarea>, or inside a <head><script> in a
		// window.onload event handler.

		// Replace the <textarea id="editor"> with an CKEditor
		// instance, using default configurations.

		CKEDITOR.config.entities = true;
		
		CKEDITOR.replace( '<?php echo $name_editor; ?>' , 
		{
			//Here, function, load_profile
			//extraPlugins : 'devtools',
			//removePlugins: 'flash,div,filebrowser,flash,format,forms,horizontalrule,iframe',
			language: '<?php echo $arr_i18n_ckeditor[$language]; ?>',
			enterMode : CKEDITOR.ENTER_BR,
			toolbar :[

				['Source'],'-', ['Bold', 'Italic','Underline'], '-', ['Blockquote'] , '-', ['Link', 'Unlink'], '-', ['SpecialChar'<?php echo $edit_image; ?>],  '-', ['Undo','Redo'], '-', ['Smiley']

				],
			smiley_columns: 10,
			smiley_path: [''], //['<?php echo $base_url; ?>/media/smileys/'],
			smiley_images :
			[
				
				<?php
				echo '\''.implode('\',\'', $smiley_img).'\'';

				?>
				
			],
			
			smiley_descriptions :
			[
				
				<?php

				$arr_smiley=array();

				$c=count($smiley_img);

				for($x=0;$x<$c;$x++)
				{

					$arr_smiley[]=basename( preg_replace('/icon_(.*?).gif/', '$1', $smiley_img[$x]) );

				}

				echo '\''.implode('\',\'', $arr_smiley).'\'';

				?>
				

			]

			,on :
			{
				instanceReady : function( ev )
				{
					// Output paragraphs as <p>Text</p>.
					this.dataProcessor.writer.setRules( 'p',
					{
						indent : false,
						breakBeforeOpen : true,
						breakAfterOpen : false,
						breakBeforeClose : false,
						breakAfterClose : true
					});
				}
			}

		}


		);

// When opening a dialog, its "definition" is created for it, for
// each editor instance. The "dialogDefinition" event is then
// fired. We should use this event to make customizations to the
// definition of existing dialogs.
	//CKEDITOR.config.protectedSource.push( /<p>/g );
CKEDITOR.on( 'dialogDefinition', function( ev )
	{
		// Take the dialog name and its definition from the event
		// data.
		var dialogName = ev.data.name;
		var dialogDefinition = ev.data.definition;

		// Check if the definition is from the dialog we're
		// interested on (the "Link" dialog).
		if ( dialogName == 'link' )
		{
			// Get a reference to the "Link Info" tab.
			var infoTab = dialogDefinition.getContents( 'info' );
			/*
			// Add a text field to the "info" tab.
			infoTab.add( {
					type : 'text',
					label : 'My Custom Field',
					id : 'customField',
					'default' : 'Sample!',
					validate : function()
					{
						if ( /\d/.test( this.getValue() ) )
							return 'My Custom Field must not contain digits';
					}
				});*/

			// Remove the "Link Type" combo and the "Browser
			// Server" button from the "info" tab.
			infoTab.remove( 'linkType' );
			infoTab.remove( 'browse' );
			dialogDefinition.removeContents( 'target' );
			dialogDefinition.removeContents( 'advanced' );
		}

		if ( dialogName == 'image' )
		{

			var infoTab = dialogDefinition.getContents( 'info' );
			infoTab.remove( 'txtAlt' );
			infoTab.remove( 'txtWidth' );
			infoTab.remove( 'txtHeight' );
			infoTab.remove( 'ratioLock' );
			infoTab.remove( 'txtHSpace' );
			infoTab.remove( 'txtVSpace' );
			infoTab.remove( 'txtBorder' );
			infoTab.remove( 'cmbAlign' );
			infoTab.remove( 'htmlPreview' );
			
			dialogDefinition.removeContents( 'target' );
			dialogDefinition.removeContents( 'advanced' );
			dialogDefinition.removeContents( 'Link' );

		}
	});

	</script>

	<?php
	
}

?>