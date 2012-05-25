<?php

function load_jscript_editor($name_editor, $value, $profiles='all')
{

	global $base_url, $arr_i18n_ckeditor, $language, $arr_cache_jscript, $arr_cache_jscript_gzipped;
	
	load_libraries(array('emoticons'));

	list($smiley_text, $smiley_img)=set_emoticons();
	
	$arr_cache_jscript_gzipped['no_gzipped_path_ckeditor']=1;
	$arr_cache_jscript['no_gzipped_path_ckeditor']='ckeditor_path.js.php';
	$arr_cache_jscript[]='textbb--ckeditor--ckeditor.js';

	?>
	
	<script type="text/javascript">
	//<![CDATA[

		// This call can be placed at any point after the
		// <textarea>, or inside a <head><script> in a
		// window.onload event handler.

		// Replace the <textarea id="editor"> with an CKEditor
		// instance, using default configurations.
		
		CKEDITOR.replace( '<?php echo $name_editor; ?>' , 
		{
			//Here, function, load_profile
			//extraPlugins : 'bbcodeweb,devtools',
			//removePlugins: 'flash,div,filebrowser,flash,format,forms,horizontalrule,iframe',
			filebrowserImageBrowseUrl : '/browser/browse.php',
			filebrowserImageUploadUrl : '<?php echo make_fancy_url($base_url, 'jscript', 'upload_image', 'upload_image', array()); ?>',
			filebrowserWindowWidth : '640',
			filebrowserWindowHeight : '480',
			
			removePlugins: 'div,forms,iframe',
			enterMode : CKEDITOR.ENTER_BR,
			language: '<?php echo $arr_i18n_ckeditor[$language]; ?>',

			/*toolbar :[

				['Source'],'-', ['Bold', 'Italic','Underline'], '-', ['Blockquote'] , '-', ['Link', 'Unlink'], '-', ['TextColor', 'SpecialChar', 'FontSize'], '-', ['Image','Table'], '-', ['Undo','Redo'], '-', ['Smiley']

				],*/
			smiley_columns: 10,
			smiley_path: [''], //['<?php echo $base_url; ?>/media/smileys/'],
			smiley_images :
			[
				
				<?php
				echo '\''.implode('\',\'', $smiley_img).'\'';

				?>
				//'regular_smile.gif','sad_smile.gif','wink_smile.gif','teeth_smile.gif','tounge_smile.gif','embaressed_smile.gif','omg_smile.gif','whatchutalkingabout_smile.gif','angel_smile.gif','shades_smile.gif', 'cry_smile.gif','kiss.gif'
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
				//'smiley', 'sad', 'wink', 'laugh', 'cheeky', 'blush', 'surprise',
				//'indecision', 'angel', 'cool', 'crying'
			]

			/*,on :
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
			}*/

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
			/*
			// Set the default value for the URL field.
			var urlField = infoTab.get( 'url' );
			urlField['default'] = 'www.example.com';

			// Remove the "Target" tab from the "Link" dialog.
			dialogDefinition.removeContents( 'target' );

			// Add a new tab to the "Link" dialog.
			dialogDefinition.addContents({
				id : 'customTab',
				label : 'My Tab',
				accessKey : 'M',
				elements : [
					{
						id : 'myField1',
						type : 'text',
						label : 'My Text Field'
					},
					{
						id : 'myField2',
						type : 'text',
						label : 'Another Text Field'
					}
				]
			});

			// Rewrite the 'onFocus' handler to always focus 'url' field.
			dialogDefinition.onFocus = function()
			{
				var urlField = this.getContentElement( 'info', 'url' );
				urlField.select();
			};

			*/
		}
	});

	//]]>
	</script>

	<?php
	
	/*
	?>
		
		
		<script type="text/javascript" src="<?php echo $base_url; ?>/media/jscript/textbb/ckeditor/ckeditor.js"></script>
		<script type="text/javascript">
			//<![CDATA[

			// Replace the <textarea id="editor"> with an CKEditor
			// instance, using the "bbcode" plugin, shaping some of the
			// editor configuration to fit BBCode environment.

			CKEDITOR.replace( '<?php echo $name_editor; ?>',
				{
					extraPlugins : 'bbcode',
					// Remove unused plugins.
					removePlugins : 'bidi,button,dialogadvtab,div,filebrowser,flash,format,forms,horizontalrule,iframe,indent,justify,liststyle,pagebreak,showborders,stylescombo,table,tabletools,templates',
					// Width and height are not supported in the BBCode format, so object resizing is disabled.
					disableObjectResizing : true,
					// Define font sizes in percent values.
					fontSize_sizes : "30/30%;50/50%;100/100%;120/120%;150/150%;200/200%;300/300%",
					
					toolbar :
					[
						['Source'],'-', ['Bold', 'Italic','Underline'], '-', ['Blockquote'] , '-', ['Link', 'Unlink'], '-', ['TextColor', 'FontSize', 'SpecialChar'], '-', ['Image'], '-', ['Undo','Redo'], '-', ['Smiley'], 'NewPage','-','Undo','Redo'],
						['Find','Replace','-','SelectAll','RemoveFormat'],
						['Link', 'Unlink', 'Image', 'Smiley','SpecialChar'],
						'/',
						['Bold', 'Italic','Underline'],
						['FontSize'],
						['TextColor'],
						['NumberedList','BulletedList','-','Blockquote'],
						['Maximize']
					],
					// Strip CKEditor smileys to those commonly used in BBCode.
					
					smiley_path: ['<?php echo $base_url; ?>/media/smileys/'],
					smiley_images :
					[
						
						<?php
						echo '\''.implode('\',\'', $smiley_img).'\'';

						?>
						'regular_smile.gif','sad_smile.gif','wink_smile.gif','teeth_smile.gif','tounge_smile.gif','embaressed_smile.gif','omg_smile.gif','whatchutalkingabout_smile.gif','angel_smile.gif','shades_smile.gif', 'cry_smile.gif','kiss.gif'
					],
					
					smiley_descriptions :
					[
						
						<?php
						echo '\''.implode('\',\'', $smiley_text).'\'';

						?>
						
						'smiley', 'sad', 'wink', 'laugh', 'cheeky', 'blush', 'surprise',
						'indecision', 'angel', 'cool', 'crying'
					]
					
			} );

			//]]>
			</script>
			
	*/
	
	/*
	?>
	<script language="Javascript">
	
	textbb=document.getElementById('<?php echo $name_editor.'_iframe'; ?>');
	framebb=document.getElementById('<?php echo $name_editor; ?>').contentWindow.document;

	StartTextBB();

	function StartTextBB() 
	{

		if(document.designMode)
		{
			alert(document.getElementById('<?php echo $name_editor; ?>').contentWindow.document.body.textContent);
			for(pepe in document.getElementById('<?php echo $name_editor; ?>').contentWindow.document)
			{

				document.write('<p>'+pepe);

			}
			
			framebb.designMode = "on";
			framebb.write('');
			framebb.close();
			//framebb.execCommand('Bold', false, null);
			//framebb.execCommand('inserthtml', false, 'pepe'); 
			
			first_code='<?php echo do_bbcode($value); ?>';
			framebb.execCommand('inserthtml', false, first_code); 


		}
		else
		{

			textbb.innerHTML='<?php echo TextAreaForm($name_editor, '', $value); ?>';

		}
		
	//}

	function put_bold()
	{

		//alert(open_tag+' '+close_tag);
		framebb.execCommand('Bold', false, null);

	}

	function emoticon(smiley_text, smiley_image)
	{

		//framebb.write(' <img src="'+smiley_image+'" alt="'+smiley_text+'"/> ');
		imagesrc=' <img src="'+smiley_image+'" alt="'+smiley_text+'"/> ';
		framebb.execCommand('inserthtml', false, imagesrc); 

	}

	</script>
	<?php
	*/
}

?>
