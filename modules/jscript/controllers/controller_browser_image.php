<?php

function Browser_image()
{

	global $model, $base_path, $base_url, $config_data, $user_data, $lang, $arr_block;
	
	load_lang('jscript');
	
	load_model('jscript');
	
	load_libraries(array('check_admin', 'send_email', 'generate_admin_ng'));
	
	$original_theme=$config_data['dir_theme'];

	$config_data['dir_theme']=$original_theme.'/admin';

	$arr_block='admin_none';
	
	if(check_admin($user_data['IdUser']))
	{
		
		ob_start();
		
		$arr_block='admin_none';
		
		//Obtain headers..
		//http://localhost/phangodev/index.php/jscript/show/browser_image/browser_image/?CKEditor=text[es-ES]&CKEditorFuncNum=2&langCode=es
		
		settype($_GET['CKEditorFuncNum'], 'integer');
		
		?>
		<script type="text/javascript" src="<?php echo make_fancy_url($base_url, 'jscript', 'load_jscript', 'script', array('no_compression' => 1, 'input_script' => 'ckeditor_path.js.php')); ?>"></script>
		<script type="text/javascript" src="<?php echo make_fancy_url($base_url, 'jscript', 'load_jscript', 'script', array('no_compression' => 0, 'input_script' => 'textbb--ckeditor--ckeditor.js') ); ?>"></script>
		<script language="Javascript">
		
		function put_url(num_editor, url)
		{
		
			window.opener.CKEDITOR.tools.callFunction( num_editor, url); 
			window.close();
		
		}
		
		</script>
		<?php
		
		$headers=ob_get_contents();
		
		ob_clean();
		
		$arr_fields=array('image');
		
		$url_options=make_fancy_url($base_url, 'jscript', 'browser_image', 'script', array());
		
		ListModel('jscript_image', $arr_fields, $url_options, $options_func='ImageOptionsListModel', $where_sql='', $arr_fields_form=array(), $type_list='Basic');
		
		$content=ob_get_contents();
		
		ob_end_clean();
		
		$title=$lang['jscript']['search_images'];
		
		echo load_view(array($title, $content, $block_title=array(), $block_content=array(), $block_urls=array(), $block_type=array(), $block_id=array(), $config_data, $headers), 'admin_none');
		
	}

}

function ImageOptionsListModel($url_options, $model_name, $id, $arr_row)
{

	global $lang, $model;

	$arr_options=BasicOptionsListModel($url_options, $model_name, $id);
	
	$url_image=$model['jscript_image']->components['image']->show_image_url($arr_row['image']);
	
	$func_js='put_url(\''.$_GET['CKEditorFuncNum'].'\', \''.$url_image.'\');';
	
	$arr_options[]='<a href="javascript:'.$func_js.'">'.$lang['jscript']['add_image_to_form'].'</a>';
	
	return $arr_options;

}


?>
