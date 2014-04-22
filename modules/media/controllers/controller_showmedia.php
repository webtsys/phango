<?php

function ShowMedia()
{

	global $base_path, $config_data;

	settype($_GET['images'], 'string');
	settype($_GET['css'], 'string');
	settype($_GET['module'], 'string');
	
	$module_theme_loaded=$config_data['module_theme'];
	
	if($_GET['module']!='')
	{
	
		$_GET['module']=slugify(basename($_GET['module']), 1);
		
		$module_theme_loaded='modules/'.$_GET['module'].'/';
	
	}
	
	/*settype($_GET['decoded'], 'integer');
	
	if($_GET['encoded']==1)
	{*/
	
	$img_final=urldecode_redirect($_GET['images']);
	
	if(!$img_final)
	{
	
		$_GET['images']=slugify($_GET['images'], 1);
		
	}
	else
	{
	
		$_GET['images']=$img_final;
	
	}
	
	
	$css_final=urldecode_redirect($_GET['css']);
	
	if(!$css_final)
	{
	
		$_GET['css']=slugify($_GET['css'], 1);
		
	}
	else
	{
	
		$_GET['css']=$css_final;
	
	}
	
	$font_final=urldecode_redirect($_GET['font']);
	
	if(!$font_final)
	{
	
		$_GET['font']=slugify($_GET['font'], 1);
		
	}
	else
	{
	
		$_GET['font']=$font_final;
	
	}
	
	
	/*}*/
	
	$_GET['images']=str_replace('./', '', form_text($_GET['images']));
	$_GET['css']=str_replace('./', '', form_text($_GET['css']));
	$_GET['font']=str_replace('./', '', form_text($_GET['font']));
	
	$cont_error=ob_get_contents();
	
	ob_clean();
	
	//Accept .gif, .png o .jpg
	
	if($_GET['images']!='')
	{
		
		$ext_info=pathinfo($_GET['images']);
		
		$_GET['images']=check_path($_GET['images']);
		
		$file_path=$base_path.$module_theme_loaded.'/media/images/'.$_GET['images'];
		
		if($ext_info['extension']=='gif' || $ext_info['extension']=='jpg' || $ext_info['extension']=='png')
		{
			
			if(file_exists($file_path))
			{
			
				header('Content-Type: image/'.$ext_info['extension']);
			
				readfile($file_path);
			
			}
			else
			{
			
				show_error('Don\'t exists the image', 'Don\'t exists the image with path: '.$file_path, $output_external='');
			
			}
			
		}
		
		ob_end_flush();
		
		die;
	
	}
	
	if($_GET['css']!='')
	{
		
		$ext_info=pathinfo($_GET['css']);
		
		if($ext_info['extension']=='css')
		{
		
			$_GET['css']=check_path($_GET['css']);
		
			$file_path=$base_path.$module_theme_loaded.'/media/css/'.$_GET['css'];
			
			if(file_exists($file_path))
			{
			
				header('Content-Type: text/css');
			
				readfile($file_path);
			
			}
			else
			{
			
				show_error('Don\'t exists the css file', 'Don\'t exists the css file with path: '.$file_path, $output_external='');
			
			}
			
		}
		
		ob_end_flush();
		
		die;
	
	}
	
	if($_GET['font']!='')
	{
		
		$ext_info=pathinfo($_GET['font']);
		
		if($ext_info['extension']=='ttf')
		{
			
			$file_path=$base_path.$module_theme_loaded.'/media/fonts/'.$_GET['font'];
			
			if(file_exists($file_path))
			{
				
				header('Content-Type: application/x-font-woff');
			
				readfile($file_path);
			
			}
			
		}
		
		ob_end_flush();
		
		die;
	
	}
	

}

function check_path($file)
{

	$arr_file=explode('/', $file);
	$arr_file_final=array();
	
	foreach($arr_file as $file_part)
	{
	
		$arr_file_final[]=slugify(basename($file_part), 1);
	
	}
	
	return implode('/', $arr_file_final);

}

?>