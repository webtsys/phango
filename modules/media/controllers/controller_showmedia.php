<?php

function ShowMedia()
{

	global $base_path, $config_data;

	settype($_GET['images'], 'string');
	settype($_GET['css'], 'string');
	
	
	settype($_GET['decoded'], 'integer');
	
	if($_GET['decoded']==1)
	{
	
		$_GET['images']=urldecode_redirect($_GET['images']);
	
	}
	
	$_GET['images']=str_replace('./', '', form_text($_GET['images']));
	$_GET['css']==str_replace('./', '', form_text($_GET['css']));
	
	$cont_error=ob_get_contents();
	
	ob_clean();
	
	//Accept .gif, .png o .jpg
	
	if($_GET['images']!='')
	{
		
		$ext_info=pathinfo($_GET['images']);
		
		if($ext_info['extension']=='gif' || $ext_info['extension']=='jpg' || $ext_info['extension']=='png')
		{
		
			$file_path=$base_path.$config_data['module_theme'].'/media/images/'.$_GET['images'];
			
			if(file_exists($file_path))
			{
			
				header('Content-Type: image/'.$ext_info['extension']);
			
				readfile($file_path);
			
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
		
			$file_path=$base_path.$config_data['module_theme'].'/media/css/'.$_GET['css'];
			
			if(file_exists($file_path))
			{
			
				header('Content-Type: text/css');
			
				readfile($file_path);
			
			}
			
		}
		
		ob_end_flush();
		
		die;
	
	}
	
	

}

?>