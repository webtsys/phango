<?php

function ShowMedia()
{

	global $base_path, $config_data, $script_base_controller;

	settype($_GET['images'], 'string');
	settype($_GET['css'], 'string');
	settype($_GET['module'], 'string');
	
	$module_theme_loaded=''; //$config_data['module_theme'];
	
	$theme=$config_data['dir_theme'];
	
	$container_theme=$config_data['module_theme'];
	
	if($_GET['module']!='')
	{
	
		$module_theme_loaded=slugify(basename($_GET['module']), 1).'/';
	
	}
	
	/*settype($_GET['decoded'], 'integer');
	
	if($_GET['encoded']==1)
	{*/
	
	format_media_type('images');

	format_media_type('css');
	
	format_media_type('font');
	
	format_media_type('jscript');
	
	$cont_error=ob_get_contents();
	
	ob_clean();
	
	//Accept .gif, .png o .jpg
	
	if($_GET['images']!='')
	{
		
		$check_file=0;
		
		$ext_info=pathinfo($_GET['images']);
		
		settype($ext_info['extension'], 'string');
		
		$_GET['images']=check_path($_GET['images']);
		
		//theme path, can be a module theme. If module_theme_loaded exists, rewrite.
		
		$file_path=$base_path.$container_theme.'views/'.$theme.'/media/'.$module_theme_loaded.'images/'.$_GET['images'];
		
		if($ext_info['extension']=='gif' || $ext_info['extension']=='jpg' || $ext_info['extension']=='png')
		{
		
			$check_file=0;
			
			//First on normal theme or module theme.
			
			if(!file_exists($file_path))
			{
			
				//Second on module directly.
			
				$file_path=$base_path.'modules/'.$module_theme_loaded.'media/images/'.$_GET['images'];
			
				if(file_exists($file_path))
				{
				
					$check_file=1;
				
				}
				
				
			
			}
			else
			{
			
				$check_file=1;
			
			}
			
			if($check_file==1)
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
		
		settype($ext_info['extension'], 'string');
		
		if($ext_info['extension']=='css')
		{
			$check_file=0;
		
			$_GET['css']=check_path($_GET['css']);
		
			//First, theme or module theme
		
			$file_path=$base_path.$container_theme.'views/'.$theme.'/media/'.$module_theme_loaded.'css/'.$_GET['css'];
			
			if(!file_exists($file_path))
			{
			
				//Second on module.
			
				//$file_path=$base_path.$config_data['module_theme'].'views/'.$config_data['dir_theme'].'/media/css/'.$_GET['css'];
			
				$file_path=$base_path.'modules/'.$module_theme_loaded.'media/css/'.$_GET['css'];
				
				if(file_exists($file_path))
				{
				
					$check_file=1;
				
				}
				
			
			}
			else
			{
			
				$check_file=1;
			
			}
			
			if($check_file==1)
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
		
		settype($ext_info['extension'], 'string');
		
		if($ext_info['extension']=='ttf')
		{
			$check_file=0;
			
			//normal theme or module theme
			
			$file_path=$base_path.$container_theme.'views/'.$theme.'/media/'.$module_theme_loaded.'fonts/'.$_GET['font'];
			
			if(!file_exists($file_path))
			{
			
				//Second on module.
			
				//$file_path=$base_path.$config_data['module_theme'].'views/'.$config_data['dir_theme'].'/media/fonts/'.$_GET['font'];
			
				$file_path=$base_path.'modules/'.$module_theme_loaded.'media/fonts/'.$_GET['font'];
			
				if(file_exists($file_path))
				{
				
					$check_file=1;
				
				}
				
			
			}
			else
			{
			
				$check_file=1;
			
			}
			
			if($check_file==1)
			{
				
				header('Content-Type: application/x-font-woff');
			
				readfile($file_path);
			
			}
			
		}
		
		ob_end_flush();
		
		die;
	
	}
	
	if($_GET['jscript']!='')
	{
		
		$ext_info=pathinfo($_GET['jscript']);
		
		settype($ext_info['extension'], 'string');
		
		if($ext_info['extension']=='js')
		{
			$check_file=0;
			
			//normal theme or module theme
			
			$file_path=$base_path.$container_theme.'views/'.$theme.'/media/'.$module_theme_loaded.'jscript/'.$_GET['jscript'];
			
			if(!file_exists($file_path))
			{
			
				//Second on module.
			
				//$file_path=$base_path.$config_data['module_theme'].'views/'.$config_data['dir_theme'].'/media/fonts/'.$_GET['font'];
			
				$file_path=$base_path.'modules/'.$module_theme_loaded.'media/jscript/'.$_GET['jscript'];
			
				if(file_exists($file_path))
				{
				
					$check_file=1;
				
				}
				
			
			}
			else
			{
			
				$check_file=1;
			
			}
			
			if($check_file==1)
			{
				
				header('Content-Type: application/javascript');
			
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

function format_media_type($type)
{
	
	$final=urldecode_redirect($_GET[$type]);
	
	if(!$final)
	{
	
		$_GET[$type]=slugify($_GET[$type], 1);
		
	}
	else
	{
	
		$_GET[$type]=$final;
	
	}
	
	$_GET[$type]=str_replace('./', '', form_text($_GET[$type]));

}

?>