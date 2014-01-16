<?php

//Loading libraries with includes, don't need more sofisticated methods...

ob_start();

include('config.php');

date_default_timezone_set (MY_TIMEZONE);

include('database/'.TYPE_DB.'.php');
include('classes/webmodel.php');

$utility_cli=1;

//load_lang('common', 'user');

$model=array();

//Check arguments

define('OPTS', 'm:c:');

$longopts=array();

$options = getopt(OPTS, $longopts);

if(!isset($options['m']) && !isset($options['c']))
{

	die("Use: php cli.php -m=module -c=cli_controller [more arguments for daemon]\n");

}

$module=@form_text(basename($options['m']));

$cli_controller=@form_text(basename($options['c']));

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

function get_opts_cli($my_opts, $arr_opts=array())
{

	return getopt(OPTS.$my_opts, $arr_opts);

}

?>