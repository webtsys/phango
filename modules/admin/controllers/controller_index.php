<?php

function Index()
{
	ob_start();

	global $model, $lang, $base_url, $base_path, $user_data, $arr_module_admin, $config_data, $arr_block, $original_theme, $module_admin, $header;
	
	$header='';
	$content='';
	
	load_lang('admin');
	load_libraries(array('check_admin'));

	settype($_GET['IdModule'], 'integer');

	$original_theme=$config_data['dir_theme'];

	$config_data['dir_theme']=$original_theme.'/admin';

	$arr_block='admin_none';

	//Make menu...
	//Admin was internationalized
	
	if(check_admin($user_data['IdUser']))
	{

		//variables for define titles for admin page

		$title_admin=$lang['admin']['admin'];
		$title_module=$lang['admin']['home'];
		
		$content='';

		$name_modules=array();

		$urls=array();

		$module_admin=array();

		$arr_admin_script[0]=array('admin', 'admin');
		
		//Define $module_admin[$_GET['IdModule']] for check if exists in database the module

		$module_admin[$_GET['IdModule']]='AdminIndex';

		$lang[$module_admin[$_GET['IdModule']].'_admin']['AdminIndex_admin_name']=ucfirst($lang['admin']['admin']);

		$query=$model['module']->select('where admin=\'1\'', array('IdModule', 'name', 'admin_script'));

		while( list($idmodule, $name_module, $ser_admin_script)=webtsys_fetch_row($query) )
		{
	
			$arr_admin_script[$idmodule]=unserialize($ser_admin_script);

			//load little file lang with the name for admin. With this you don't need bloated with biggest files of langs...

			$dir_lang_admin='';

			if($arr_admin_script[$idmodule][0]!=$arr_admin_script[$idmodule][1])
			{

				$dir_lang_admin=$arr_admin_script[$idmodule][0].'_';

			}

			load_lang($dir_lang_admin.$name_module.'_admin');
			
			if(!isset($lang[$name_module.'_admin'][$name_module.'_admin_name']))
			{

				$name_modules[$name_module]=$name_module;
				$lang[$name_module.'_admin'][$name_module.'_admin_name']=ucfirst($name_modules[$name_module]);
			
			}
			else
			{
				
				$name_modules[$name_module]=ucfirst($lang[$name_module.'_admin'][$name_module.'_admin_name']);

			}

			$urls[$name_module]=make_fancy_url($base_url, 'admin', 'index', $name_module, array('IdModule' => $idmodule));

			$module_admin[$idmodule]=$name_module;

		}

		$file_include=$base_path.'modules/'.$arr_admin_script[ $_GET['IdModule'] ][0].'/controllers/admin/admin_'.$arr_admin_script[ $_GET['IdModule'] ][1].'.php';
		
		if(file_exists($file_include) && $module_admin[$_GET['IdModule']]!='')
		{
			
			include($file_include);

			$func_admin=$module_admin[$_GET['IdModule']].'Admin';
			
			if(function_exists($func_admin))
			{	

				echo '<h1>'.$lang[$module_admin[$_GET['IdModule']].'_admin'][$module_admin[$_GET['IdModule']].'_admin_name'].'</h1>';

				$func_admin();

			}
			else
			{

				$arr_error[0]='Error: no exists function for admin application';
				$arr_error[1]='Error: no exists function '.ucfirst($func_admin).' for admin application';
				ob_clean();
				echo load_view(array('title' => 'Phango site is down', 'content' => '<p>'.$arr_error[DEBUG].'</p>'), 'common/common');
				die();

			}

		}
		else if($module_admin[$_GET['IdModule']]!='')
		{

			$arr_error[0]='Error: no exists file for admin application';
			$arr_error[1]='Error: no exists file '.$file_include.' for admin application';
			ob_clean();
			echo load_view(array('title' => 'Phango site is down', 'content' => '<p>'.$arr_error[DEBUG].'</p>'), 'common/common');
			die();


		}

		$content=ob_get_contents();
	
		ob_end_clean();
		
		echo load_view(array('header' => $header, 'title' => $lang['admin']['admin_zone'], 'content' => $content, 'name_modules' => $name_modules, 'urls' => $urls ), 'admin');

	}
	else
	{

		die(header('Location: '.make_fancy_url($base_url, 'user', 'index', 'login', array('register_page' => 'admin') ) ));

	}
    
}

?>