<?php

//Loading libraries with includes, don't need more sofisticated methods...
//In the future can update files independently
ob_start();

include('config.php');
include('database/'.TYPE_DB.'.php');
include('classes/webmodel.php');
include('libraries/update_table.php');

$utility_cli=1;

$config_data['dir_theme']='default';

//load_lang('common', 'user');

$model=array();

//Check arguments

if($argc<2)
{


	die("Use: php padmin.php model [file_model]\n");

}

//Connect to database

$connection='';

$connection=webtsys_connect($host_db['default'], $login_db['default'], $pass_db['default']);

if(! (  $connection && webtsys_select_db($db['default']) ) )
{

	die("Error: ".webtsys_error()." - I can't connect to database\n");

}

//Load cache for can use load_model

$query=webtsys_query(SQL_SHOW_TABLES);

while(list($table)=webtsys_fetch_row($query))
{

	$arr_check_table[$table]=1;

}

//Obtain name file...

$argv[1]=basename($argv[1]);

if(!isset($argv[2]))
{

	$argv[2]=$argv[1];

}

$dir_models='modules/'.$argv[1].'/models/';

$arr_padmin_mod=array();

if(file_exists('modules/'.$argv[1].'/models/models_'.$argv[2].'.php'))
{

	//If is a file, update this file...

	if(!include('modules/'.$argv[1].'/models/models_'.$argv[2].'.php'))
	{
	
		die("Don't exist ".$argv[2]." in modules/".$argv[1]."/models\n");
	
	}
	
	//Now load extensions for this model...
	if(file_exists('modules/'.$argv[1].'/models/extension_'.$argv[2].'.php'))
	{
	
		load_extension($argv[2]);
		
	}

	//Update modules...

	$arr_padmin_mod[$argv[1]]=str_replace('.php', '', $argv[2]);
	
}

//Update/Insert table with primitive function update_table

update_table($model);

if(!isset($model['module']))
{

	include('modules/modules/models/models_modules.php');
}

//Add modules if this model is a base module and don't have module row

add_module($arr_padmin_mod);

ob_end_flush();

?>
