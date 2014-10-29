<?php
//Basic framework
ob_start();

//Set basic arrays...

$arr_extension_model=array();
$arr_module_list_js=array();

/**
* An array used for check if the module media is in document root.
*
* Use the name of the module how key for an new array with the keys 'image' and 'css' that define the m
*
*/

$arr_media_modules_set=array();

//Adding config...

if(!include("config.php")) 
{

	//If no config error message
	//This site is no configured...
	
	$error=ob_get_contents();
	
	ob_clean();
	
	$base_url='.';
	
	include('../views/default/common/common.php');

	CommonView('Phango Framework is installed', '<p>Phango Framework is installed, but you need create config.php</p><p>Copy config_sample.php  to config.php and edit the file</p>');
	die();

}

//Check if path is correct...

if(!file_exists($base_path))
{

	$base_path=str_replace('application/index.php', '', $_SERVER['SCRIPT_FILENAME']);
	
	//Need port, if 80 $port=''
	
	$port=':'.$_SERVER['SERVER_PORT'];
	
	if($port==':80')
	{
	
		$port='';
	
	}
	
	$http='http://';
	
	if(isset($_SERVER['HTTPS']))
	{
	
		$http='https://';
	
	}
	
	$cookie_path=str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
	
	$base_url=$http.$_SERVER['SERVER_NAME'].$port.''.str_replace('/index.php', '', $cookie_path);
	
	$cookie_path=$cookie_path.'/';

}

//Check session_id, if exists $_COOKIE[COOKIE_NAME], change id to COOKIE_NAME id...

if(isset($_COOKIE[COOKIE_NAME]))
{

	//echo session_id();
	session_id($_COOKIE[COOKIE_NAME]);

}

session_name(COOKIE_NAME.'_session');

session_set_cookie_params(0, $cookie_path);

session_start();

settype($_SERVER['HTTP_ACCEPT_LANGUAGE'], 'string');

$arr_default_browser_lang=explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);

$default_browser_lang=trim($arr_default_browser_lang[0]);

if(in_array($default_browser_lang, $arr_i18n))
{

  $language=$default_browser_lang;

}

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

//I have to modify REQUEST_URI if defined NO_INDEX_PHP

if(defined('NO_INDEX_PHP'))
{

	$_SERVER['REQUEST_URI']=str_replace($cookie_path, $cookie_path.'index.php/', $_SERVER['REQUEST_URI']);

}

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

$arr_url[1]=@form_text($arr_url[1]);

define('TEXT_FUNCTION_CONTROLLER', $arr_url[1]);

$arr_variables=array_slice($arr_url, 2);

if($script_controller=='') 
{

	$script_controller=$app_index;
	$script_base_controller=$app_index;
}

define('PHANGO_SCRIPT_BASE_CONTROLLER', $script_controller);

if($script_file=='') 
{

	$script_file='index';

}

define('PHANGO_SCRIPT_FUNC_NAME', $script_file);

$script_function=ucfirst($script_file);

$script_file='controller_'.$script_file;

//Converse fancy urls in get parameters...

$text_url='';

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

		$_GET[$arr_variables[$x]]=urldecode(slugify(substr($arr_variables[$x+1], 0, 255), 1));

		//$arr_func_encode_get[DEBUG]($arr_variables[$x]);

	}

}

/*if($cget>0)
{*/

$text_url=slugify($arr_url[1]);

//}

//Get variables very used

settype($_GET['begin_page'], 'integer');

//Connection to sqldb

$set_query=0;

//Connect to all dbs...

$connection=array();

$select_db=array();

foreach($host_db as $key_server => $item_server)
{

	$connection=@webtsys_connect( $host_db[$key_server], $login_db[$key_server], $pass_db[$key_server] , $key_server);

	$select_db=@webtsys_select_db( $db[$key_server] , $key_server);
	
}

//Variables

//set_magic_quotes is deprecated but many versions of php use them and we need disable it...

@set_magic_quotes_runtime(0);

//Preparing models for checking in load_model...

if($connection!==false  && $select_db==1) 
{
	
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

		$query=$model['module']->select('WHERE load_module!="" OR yes_config=1 order by order_module ASC', array('IdModule', 'name', 'load_module', 'yes_config') );
		$arr_yes_config=array();

		while(list($idmodule, $module, $load_module, $yes_config)=webtsys_fetch_row($query)) 
		{
			
			/*if($yes_config==0)
			{*/
			
				$module_names[$yes_config][$idmodule]=basename($module);
				$general_modules[$idmodule]=basename($load_module);
			
			/*}
			else
			{
			
				$module_names_config[$idmodule]=basename($module);
			
			}*/
		}
		
		foreach($module_names[1] as $idmodule => $module)
		{
		
			if(!include($base_path.'modules/'.$module.'/config/config_module.php'))
			{
			
				$arr_error_sql[0]='<p>Error: Cannot load config for this module.</p>';    
				$arr_error_sql[1]='<p>Error: Cannot load '.$base_path.'modules/'.$module.'/config/config_module.php'.' config for this module.</p>';
				
				$output=ob_get_contents();

				$arr_error_sql[1].='<p>Output: '.$output.'</p>';

				ob_clean();
			
				echo load_view(array('Phango site is down', $arr_error_sql[DEBUG]), 'common/common');

				die();
			
			}
			
		
		}

		foreach($module_names[0] as $idmodule => $module) 
		{
			
			if(!include($base_path.'modules/'.$module.'/loaders/'.$general_modules[$idmodule]))
			{

				$arr_error_sql[0]='<p>Error: Cannot load a loader.</p>';    
				$arr_error_sql[1]='<p>Error: Cannot load '.$general_modules[$idmodule].' loader.</p>';
				
				$output=ob_get_contents();

				$arr_error_sql[1].='<p>Output: '.$output.'</p>';

				ob_clean();
			
				echo load_view(array('Phango site is down', $arr_error_sql[DEBUG]), 'common/common');

				die();

			}

		}

	}
	
}
else if(USE_DB==1 && $connection===false)
{

	$output=ob_get_contents();

	$text_error.='<p>Output: '.$output.'</p>';

	$arr_error_sql[0]='<p>Error: Cannot connect to MySQL db.</p>';    
	$arr_error_sql[1]='<p>Error: Cannot connect to MySQL db.'.$text_error.'</p>';
	
	ob_clean();

	echo load_view(array('Phango site is down', $arr_error_sql[DEBUG]), 'common/common');
	
	die;

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
		
		$script_class_name=ucfirst($script_function).'SwitchClass';
		
		if(function_exists($script_function)) 
		{

			//script function obtain external data from get or post
			//If you need access to variables, use global keyword.
			
			if($connection===false && $set_query>0)
			{
				

				show_error('Error in system', 'Error: system tried access to db but the system cannot connect any db.', $output_external='');


			}
			
			$script_function();
			
			//If we tried use queries, see $set_query variable and connection.
			

		}
		
		//The new behaviour for controllers, with classes.
		
		else if(class_exists($script_class_name))
		{
		
			$script_class=new $script_class_name();
		
			$script_method='index';
		
			if(isset($_GET[$script_class->op_var]))
			{
			
				$script_method=$_GET[$script_class->op_var];
			
			}
			else
			{
			
				$_GET[$script_class->op_var]='index';
			
			}
		
			if(method_exists($script_class, $script_method))
			{
			
				$script_class->$script_method();
			
			}
			else
			{
			
				$output=ob_get_contents();

				ob_clean();

				$arr_no_controller[0]='<p>Don\'t exist controller method</p>';
				$arr_no_controller[1]='<p>Don\'t exist '.$script_method.' on <strong>'.$script_file.'.php</strong> on <strong>'.$script_controller.'</strong> controller folder</p><p>Output: '.$output.'</p>';

				echo load_view(array('title' => 'Phango site is down', 'content' => $arr_no_controller[DEBUG]), 'common/common');
			
			}
		
		
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
