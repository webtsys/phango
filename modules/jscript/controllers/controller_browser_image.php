<?php

function Browser_image()
{

	global $model, $base_path, $base_url, $config_data, $user_data, $lang, $arr_block;
	
	settype($_GET['op'], 'integer');
	
	load_lang('jscript');
	
	load_model('jscript');
	
	load_libraries(array('check_admin', 'send_email', 'generate_admin_ng'));
	
	$original_theme=$config_data['dir_theme'];

	//$config_data['dir_theme']=$original_theme.'/admin';
	

	$arr_block='admin/admin_none';
	
	if(check_admin($user_data['IdUser']))
	{
	
		ob_start();
		
		//Generate form
		
		$arr_form=array();
			
		$x=6;
		
		//$model['jscript_image']->components['image']->name_file='image_form1';
		$arr_field['image_form1']=new ImageField('image_form1', $model['jscript_image']->components['image']->path, $model['jscript_image']->components['image']->url_path, '');
		
		$arr_form['image_form1']=new ModelForm('create_image', 'image_form1', 'ImageForm', $lang['common']['image'].' 1', $arr_field['image_form1'], $required=1, $parameters='');
		
		for($x=2;$x<6;$x++)
		{
		
			$arr_field['image_form'.$x]=new ImageField('image_form'.$x, $model['jscript_image']->components['image']->path, $model['jscript_image']->components['image']->url_path, '');
		
			$arr_form['image_form'.$x]=new ModelForm('create_image', 'image_form'.$x.'', 'ImageForm', $lang['common']['image'].' '.$x, $arr_field['image_form'.$x], $required=0, $parameters='' );
		
		}
			
		//Columns in principal view
	
		$arr_block='admin/admin_none';
		
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
	
		switch($_GET['op'])
		
		{
		
		default:
		
			
			$arr_fields=array('image');
			
			$url_options=make_fancy_url($base_url, 'jscript', 'browser_image', 'script', array('CKEditorFuncNum' => $_GET['CKEditorFuncNum']));
			
			$url_add_images=make_fancy_url($base_url, 'jscript', 'browser_image', 'script', array('op' => 1, 'CKEditorFuncNum' => $_GET['CKEditorFuncNum']));
			
			?>
			<p><a href="<?php echo $url_add_images; ?>"><?php echo $lang['jscript']['add_new_images']; ?></a></p>
			<?php
			
			//?order_field=image&order_desc=1&search_word=&search_field=IdJscript_image
			
			if(!isset($_GET['order_field']))
			{
			
				$_GET['order_field']='IdJscript_image';
				$_GET['order_desc']=1;
			
			}
			
			$model['jscript_image']->create_form();
			
			$model['jscript_image']-> set_enctype_binary();
			
			//ImageForm($name="", $class='', $value='', $delete_inline=0, $path_image='')
			$model['jscript_image']->forms['image']->label=$lang['common']['image'];
			$model['jscript_image']->forms['image']->parameters=array('image', '', '', $delete_inline=0, $path_image=$model['jscript_image']->components['image']->url_path);
			
			ListModel('jscript_image', $arr_fields, $url_options, $options_func='ImageOptionsListModel', $where_sql='', $arr_fields_form=array(), $type_list='Basic');
			
			$content=ob_get_contents();
			
			ob_end_clean();
			
		
		break;
		
		case 1:
		
			ob_start();
			
			
			
			//UpdateModelFormView($model_form, $arr_fields=array(), $url_post, $enctype='')
			
			$url_post=make_fancy_url($base_url, 'jscript', 'browser_image', 'script', array('op' => 2, 'CKEditorFuncNum' => $_GET['CKEditorFuncNum']));
			
			echo load_view(array($arr_form, array(), $url_post, 'enctype="multipart/form-data"'), 'common/forms/updatemodelform');
			
			$cont_text=ob_get_contents();
			
			ob_end_clean();
		
			ob_start();
			
			echo load_view(array($lang['jscript']['add_new_images'], $cont_text), 'content');
			
			$url_go_back=make_fancy_url($base_url, 'jscript', 'browser_image', 'script', array('op' => 0, 'CKEditorFuncNum' => $_GET['CKEditorFuncNum']));
			
			echo '<p><a href="'.$url_go_back.'">'.$lang['common']['go_back'].'</a></p>';
			
			$content=ob_get_contents();
			
			ob_end_clean();
		
		break;
		
		case 2:
		
			ob_start();
			
			$arr_post=ModelForm::check_form($arr_form, $_POST);
			
			if($arr_post!=0)
			{
			
				//Insert images..
				
				$error=0;
				
				$c_arr_form=count($arr_form);
				
				foreach($arr_form as $img_form)
				{
					
					if($img_form->error_flag==0)
					{
					
						$file_name=$img_form->type->name_file;
					
						if(!$model['jscript_image']->insert(array('image' => $arr_post[$file_name])))
						{
							
							$error++;
						
						}
					
					}
				
				}
				echo $error.'!='.$c_arr_form;
				if($error!=$c_arr_form)
				{
				
					ob_end_clean();
					load_libraries(array('redirect'));
					die( redirect_webtsys( make_fancy_url($base_url, 'jscript', 'browser_image', 'script', array('CKEditorFuncNum' => $_GET['CKEditorFuncNum'])), $lang['common']['redirect'], $lang['common']['success'], $lang['common']['press_here_redirecting'] , $arr_block) );
					
					$content=ob_get_contents();
					
					ob_end_clean();
					
				}
				else
				{
				
					$content=$lang['common']['error_cannot_upload_this_image_to_the_server'].'<br />'.ob_get_contents();
					
					ob_end_clean();
				
				}
			
			}
			else
			{
			
				//die(header('Location:'. make_fancy_url($base_url, 'jscript', 'browser_image', 'script', array('CKEditorFuncNum' => $_GET['CKEditorFuncNum'])) ));
				
				$url_go_back=make_fancy_url($base_url, 'jscript', 'browser_image', 'script', array('op' => 1, 'CKEditorFuncNum' => $_GET['CKEditorFuncNum']));
			
				echo '<p><a href="'.$url_go_back.'">'.$lang['common']['go_back'].'</a></p>';
				
				$content=$lang['common']['error_cannot_upload_this_image_to_the_server'].'<br />'.ob_get_contents();
					
				ob_end_clean();
			
			}
			
		
		break;
		
		}
		
		
		$title=$lang['jscript']['search_images'];
			
		echo load_view(array($title, $content, $block_title=array(), $block_content=array(), $block_urls=array(), $block_type=array(), $block_id=array(), $config_data, $headers), 'admin/admin_none');
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
