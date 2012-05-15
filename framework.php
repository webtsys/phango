<?php
//Basic framework
ob_start();

if(!@include("config.php")) 
{

	//If no config error message
	//This site is no configured...
	
	$base_url='.';
	
	include('../views/default/common/common.php');

	CommonView('Phango Framework is installed', '<p>Phango Framework is installed, but you need create config.php</p><p>Copy config_sample.php  to config.php and edit the file</p>');
	die();

}

session_name(COOKIE_NAME);

session_set_cookie_params(0, $cookie_path);

session_start();

$_SESSION['default_language']=$language;

//Set Timezone

date_default_timezone_set (MY_TIMEZONE);

include("classes/webmodel.php");
include("database/".TYPE_DB.".php");

load_lang('common', 'error_model');

//Variables very used

$_SERVER['HTTP_REFERER']=@form_text($_SERVER['HTTP_REFERER']);

$arr_block=array();

//Initialize csrf_token 

$user_data['key_csrf']=$prefix_key;

//Begin execution of script...

//Controller: directory Function: file name, function name with ucfirst
//Format fancy url: $base_url/index.php/(controller|dir_controller/controller)/show/function/text_slug/variable1/value1/variable2/value2
//Fancy url accept without problems normal format.

//Add index.php if not exists...

if(strpos($_SERVER['REQUEST_URI'],'/index.php/')!==false)
{

	$path_server=str_replace($cookie_path.'index.php/', '', $_SERVER['REQUEST_URI']);
	
	$arr_get_url=explode('/show/', $path_server);
	$arr_get_url[0]=preg_replace('/^(.*)\/$/', '$1', $arr_get_url[0]);

}
else
{

	$arr_get_url=array('');

}

$script_controller=str_replace('/show', '', $arr_get_url[0]); 

$script_base_controller=dirname($script_controller);

if($script_base_controller=='.')
{

	$script_base_controller=$script_controller;

}

$arr_url=@explode('/', $arr_get_url[1]);

$script_file=@form_text(slugify($arr_url[0]));

$arr_variables=array_slice($arr_url, 2);

if($script_controller=='') 
{

	$script_controller=$app_index;
	$script_base_controller=$app_index;
}

if($script_file=='') 
{

	$script_file='index';

}

$script_function=ucfirst($script_file);

$script_file='controller_'.$script_file;

//Converse fancy urls in get parameters...


//clean_url_getvar();
	
//$arr_variables=array_slice($arr_url, 4);
$cget=count($arr_variables);

if($cget % 2 !=0 ) 
{

	$arr_variables[]='';
	$cget++;
}

if($cget % 2 ==0 )
{
	//Get variables

	for($x=0;$x<$cget;$x+=2)
	{
		
		//Cut big variables...

		$_GET[$arr_variables[$x]]=slugify(substr($arr_variables[$x+1], 0, 255));

		//$arr_func_encode_get[DEBUG]($arr_variables[$x]);

	}

}


//Get variables very used

settype($_GET['begin_page'], 'integer');

//Connection to sqldb

$connection=@webtsys_connect( $host_db, $login_db, $pass_db );

$select_db=@webtsys_select_db( $db );

//Load all tables for check if exists al models...



//If no connect error message...

if($connection==false  || $select_db!=1) {
    
	$arr_error_sql[0]='Please wait. The site is down.';    
	$arr_error_sql[1]='Error: database don\'t work -> '.webtsys_error();
	
	echo load_view(array('title' => 'Phango site is down', 'content' => '<p>'.$arr_error_sql[DEBUG].'</p>'), 'common/common');
	die();

}

//Variables

//set_magic_quotes is deprecated but many versions of php use them and we need disable it...

@set_magic_quotes_runtime(0);

//Preparing models for checking in load_model...

$table='';

$query=webtsys_query(SQL_SHOW_TABLES);

while(list($table)=webtsys_fetch_row($query))
{

	$arr_check_table[$table]=1;

}

//Now loading things how sessions or another modules.

if(isset($arr_check_table['module']))
{
	
	load_model("modules");

	$module_names=array();

	$query=$model['module']->select('WHERE load_module!="" order by order_module ASC', array('IdModule', 'name', 'load_module') );

	while(list($idmodule, $module, $load_module)=webtsys_fetch_row($query)) 
	{
		
		$module_names[$idmodule]=basename($module);
		$general_modules[]=basename($load_module);
		
	}

	$c_modules=0;

	foreach($module_names as $module) 
	{
		
		if(!include($base_path.'modules/'.$module.'/loaders/'.$general_modules[$c_modules]))
		{

			$arr_error_sql[0]='<p>Error: Cannot load a loader.</p>';    
			$arr_error_sql[1]='<p>Error: Cannot load '.$general_modules[$c_modules].' loader.</p>';
			
			$output=ob_get_contents();

			$arr_error_sql[1].='<p>Output: '.$output.'</p>';

			ob_clean();
		
			echo load_view(array('Phango site is down', $arr_error_sql[DEBUG]), 'common/common');

			die();

		}
		
		$c_modules++;
	}

}

//Check for csrf attacks

if(count($_POST)>0)
{

	//Check csrf_token

	settype($_POST['csrf_token'], 'string');
	settype($user_data['IdUser'], 'integer');

	//If no isset $_POST['csrf_token'] check $_GET['csrf_token']

	if(isset($_GET['csrf_token']))
	{

		$_POST['csrf_token']=$_GET['csrf_token'];

	}

	if($_POST['csrf_token']!=$user_data['key_csrf'])
	{

		//Check if csrf_token in variable basic_csrf for anonymous connect, necessary for gateways payment for example...

		if($user_data['IdUser']>0 && $_POST['csrf_token']!=$prefix_key)
		{

			header('HTTP/1.1 404 Not Found');

			$arr_error_sql[0]='Post denied';
			$arr_error_sql[1]='Post denied. Error in csrf_token';

			echo load_view(array('title' => 'Phango site is down', 'content' => '<p>'.$arr_error_sql[DEBUG].'</p>'), 'common/common');

			die;

		}
	}
	
	unset($_POST['csrf_token']);

}

//Load controller and file.

if(in_array($script_base_controller, $activated_controllers)) 
{
	$path_script_controller=$base_path.'modules/'.$script_controller.'/controllers/'.$script_file.'.php';
	
	if(include($path_script_controller)) 
	{
		
		$script_function=ucfirst($script_function);

		if(function_exists($script_function)) 
		{

			//script function obtain external data from get or post
			//If you need access to variables, use global keyword.

			$script_function();

		}
		else 
		{

			$output=ob_get_contents();

			ob_clean();

			$arr_no_controller[0]='<p>Don\'t exist controller function</p>';
			$arr_no_controller[1]='<p>Don\'t exist '.$script_function.' on <strong>'.$script_file.'.php</strong> on <strong>'.$script_controller.'</strong> controller folder</p><p>Output: '.$output.'</p>';

			echo load_view(array('title' => 'Phango site is down', 'content' => $arr_no_controller[DEBUG]), 'common/common');

		}

	}
	else 
	{

		$output=ob_get_contents();

		ob_clean();

		$arr_no_controller[0]='<p>Don\'t exist controller</p>';
		$arr_no_controller[1]='<p>Don\'t exist <strong>'.$script_file.'.php</strong> on <strong>'.$script_controller.'</strong> controller folder</p><p>Output: '.$output.'</p>';

		echo load_view(array('title' => 'Phango site is down', 'content' => $arr_no_controller[DEBUG]), 'common/common');

	}
}
else 
{

	$arr_no_controller[0]='<p>Permission denied.</p>';
	$arr_no_controller[1]='<p>Permission denied add <strong>'.$script_controller.'</strong> to config.php.</p>';

	echo load_view(array('title' => 'Phango site is down', 'content' => $arr_no_controller[DEBUG]), 'common/common');
	
}

ob_end_flush();

?>