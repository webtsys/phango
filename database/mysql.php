<?php

global $connection, $set_query;

if(!function_exists('mysql_query'))
{

	show_error('Error: database don\'t supported by php', 'Error: Mysql database don\'t supported by php', $output_external='');

}

if(DEBUG==1)
{

	function print_sql_fail($sql_fail, $server_data='default')
	{
		global $connection;

		$error=mysqli_error($connection[$server_data]);

		if($error!='')
		{
			echo '<p>Error: '.$sql_fail.' -> '.$error.'</p>';
		}

	}

}
else
{

	function print_sql_fail($sql_fail)
	{

		return '';

	}

}


function webtsys_query( $sql_string , $server_data='default')
{

	global $connection, $set_query;
	
	$query = mysqli_query($connection[$server_data], $sql_string );
	
	print_sql_fail($sql_string, $server_data);

	$set_query++;
	
	return $query;
} 

function webtsys_affected_rows( $idconnection , $server_data='default')
{

	global $connection;

	$num_rows = mysqli_affected_rows($connection[$server_data], $idconnection );

	return $num_rows;
} 

function webtsys_close( $idconnection )
{

	mysqli_close( $idconnection );

	return 1;
} 

function webtsys_fetch_array( $query ,$assoc_type=0)
{
	global $connection;
	
	$arr_assoc[0]=MYSQL_ASSOC;
	$arr_assoc[1]=MYSQL_NUM;
	;
	$arr_final = mysqli_fetch_array( $query ,$arr_assoc[$assoc_type]);

	return $arr_final;
} 

function webtsys_fetch_row( $query )
{	
	$arr_final = mysqli_fetch_row( $query );

	return $arr_final;
} 

function webtsys_get_client_info($server_data='default')
{
	global $connection;

	$version = mysqli_get_client_info($connection[$server_data]);

	return $version;
} 

function webtsys_get_server_info($server_data='default')
{
	global $connection;

	$version = mysqli_get_server_info($connection[$server_data]);

	return $version;
} 

function webtsys_insert_id($server_data='default')
{

	global $connection;

	$idinsert = mysqli_insert_id($connection[$server_data]);

	return $idinsert;
} 

function webtsys_num_rows( $query )
{
    $num_rows = mysqli_num_rows( $query );

    return $num_rows;
} 

/*function connection_database( $host_db, $login_db, $contra_db, $db )
{
    global $con_persistente;

    $connection = $con_persistente( $host_db, $login_db, $contra_db );

    webtsys_select_db( $db );

    return $connection;
}*/

function webtsys_connect( $host_db, $login_db, $contra_db , $server_data='default')
{

	$connection[$server_data]=mysqli_init();
	
	if ( !( mysqli_real_connect($connection[$server_data], $host_db, $login_db, $contra_db ) ) )
	{
		
		return false;
		
	} 

	return $connection;
} 

function webtsys_pconnect( $host_db, $login_db, $contra_db, $server_data='default' )
{

    if ( !( $connection[$server_data] = @mysql_pconnect( $host_db, $login_db, $contra_db ) ) )
    {
	return false;
    } 

    return $connection;
} 

function webtsys_select_db( $db , $server_data='default')
{

	global $set_query, $connection;

	$result_db=mysqli_select_db($connection[$server_data], $db);
	
	if($result_db==false)
	{

		return 0;

	}
	
	return 1;
} 

function webtsys_escape_string($sql_string, $server_data='default')
{
	global $connection;

	return mysqli_real_escape_string($connection[$server_data], $sql_string);

}

function webtsys_error($server_data='default')
{

	global $connection;

	return mysqli_error($connection, $server_data='default');

}

//Specific sql querys for this db...

define('SQL_SHOW_TABLES', 'show tables');

?>
