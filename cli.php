<?php

//Loading libraries with includes, don't need more sofisticated methods...

ob_start();

include('config.php');
include('database/'.TYPE_DB.'.php');
include('classes/webmodel.php');

$utility_cli=1;

//load_lang('common', 'user');

$model=array();

//Check arguments

if($argc<3)
{


	die("Use: php cli.php module cli_controller\n");

}

$module=@form_text($argv[1]);

$cli_controller=@form_text($argv[2]);

//Connect to database

$connection='';

$connection=webtsys_connect($host_db, $login_db, $pass_db);

if(! (  $connection && webtsys_select_db($db) ) )
{

	die("Error: ".webtsys_error()." - I can't connect to database\n");

}

//Load cache for can use load_model

$query=webtsys_query(SQL_SHOW_TABLES);

while(list($table)=webtsys_fetch_row($query))
{

	$arr_check_table[$table]=1;

}

//Include cli_controller

if(include($base_path.'modules/'.$module.'/cli/controller_'.$cli_controller.'.php'))
{

	$function_cli=$cli_controller.'Cli';

	if( function_exists($function_cli) )
	{
	
		$function_cli();
	
	}

}
else
{

	die("Error: Don't exists the controller for cli statement...\n");

}

?>