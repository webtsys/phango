<?php

function select_a_row_method_class($class, $idrow, $arr_select=array(), $raw_query=0, $assoc=0)
{

	settype($idrow, 'integer');
	
	$query=$class->select('where '.$class->name.'.`'.$class->idmodel.'`=\''.$idrow.'\'', $arr_select, $raw_query);
	
	return webtsys_fetch_array($query, $assoc);

}

?>