<?php

if(DEBUG==1)
{

	function print_sql_fail($sql_fail)
	{

		$error=mysql_error();

		if($error!='')
		{
			echo '<p>Error: '.$sql_fail.' -> '.mysql_error().'</p>';
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


function webtsys_query( $sentencia )
{
    $query = mysql_query( $sentencia );

    print_sql_fail($sentencia);

    return $query;
} 

function webtsys_affected_rows( $idconnection )
{
    $num_rows = mysql_affected_rows( $idconnection );

    return $num_rows;
} 

function webtsys_close( $idconnection )
{
    mysql_close( $idconnection );

    return 1;
} 

function webtsys_fetch_array( $query ,$assoc_type=MYSQL_ASSOC)
{
	
    $arr_final = mysql_fetch_array( $query ,$assoc_type);

    return $arr_final;
} 

function webtsys_fetch_row( $query )
{
    $arra_final = mysql_fetch_row( $query );

    return $arra_final;
} 

function webtsys_get_client_info()
{
    $version = mysql_get_client_info();

    return $version;
} 

function webtsys_get_server_info()
{
    $version = mysql_get_server_info();

    return $version;
} 

function webtsys_insert_id()
{
    $idinsert = mysql_insert_id();

    return $idinsert;
} 

function webtsys_num_rows( $query )
{
    $num_rows = mysql_num_rows( $query );

    return $num_rows;
} 

function webtsys_result( $idresultado, $idfila, $columna = 0 )
{
    $result = mysql_result( $idresultado, $idfila );

    return $result;
} 

function connection_database( $host_db, $login_db, $contra_db, $db )
{
    global $con_persistente;

    $connection = $con_persistente( $host_db, $login_db, $contra_db );

    webtsys_select_db( $db );

    return $connection;
} 

function webtsys_connect( $host_db, $login_db, $contra_db )
{

    if ( !( $connection = @mysql_connect( $host_db, $login_db, $contra_db ) ) )
    {
	return false;
    } 

    return $connection;
} 

function webtsys_pconnect( $host_db, $login_db, $contra_db )
{

    if ( !( $connection = @mysql_pconnect( $host_db, $login_db, $contra_db ) ) )
    {
	return false;
    } 

    return $connection;
} 

function webtsys_select_db( $db )
{
   $query=mysql_query('use '.$db);

	if(mysql_error()=='')
	{
	
		return 1;
	
	}
	else
	{

		return 0;

	}
} 

function webtsys_error()
{

return mysql_error();

}

//Specific sql querys for this db...

define('SQL_SHOW_TABLES', 'show tables');

?>
