<?php

function load_jscript_editor($name_editor, $value, $profiles='all')
{

	global $base_url, $arr_i18n_ckeditor, $language, $arr_cache_jscript, $arr_cache_header;
	
	load_libraries(array('emoticons'));

	list($smiley_text, $smiley_img)=set_emoticons();
	
	$arr_cache_jscript[]='jquery.min.js';
	$arr_cache_jscript[]='textbb/ckeditor/ckeditor.js';

	ob_start();
	
	?>
	
	<script type="text/javascript">
	//<![CDATA[

		// This call can be placed at any point after the
		// <textarea>, or inside a <head><script> in a
		// window.onload event handler.

		// Replace the <textarea id="editor"> with an CKEditor
		// instance, using default configurations.
	
	$(document).ready( function () {
	
		CKEDITOR.replace( '<?php echo $name_editor; ?>' , 
		{
			//Here, function, load_profile
			//extraPlugins : 'bbcodeweb,devtools',
			//removePlugins: 'flash,div,filebrowser,flash,format,forms,horizontalrule,iframe',
			filebrowserImageBrowseUrl : '<?php echo make_fancy_url($base_url, 'jscript', 'browser_image', 'browser_image', array()); ?>',
			//filebrowserImageUploadUrl : '<?php echo make_fancy_url($base_url, 'jscript', 'upload_image', 'upload_image', array()); ?>',
			filebrowserBrowseUrl: '<?php echo make_fancy_url($base_url, 'jscript', 'browser_image', 'browser_image', array()); ?>',
			filebrowserWindowWidth : '800',
			filebrowserWindowHeight : '600',
			
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

});
	//]]>
	</script>

	<?php

$arr_cache_header[]=ob_get_contents();

ob_end_clean();
	
}

?>
