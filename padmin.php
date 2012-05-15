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

	//Update modules...

	$arr_padmin_mod[$argv[1]]=str_replace('.php', '', $argv[2]);
	
}

//Connect to database

if(! ( $connection=webtsys_connect($host_db, $login_db, $pass_db) && webtsys_select_db($db) ) )
{

	die("Error: ".webtsys_error()." - I can't connect to database\n");

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
