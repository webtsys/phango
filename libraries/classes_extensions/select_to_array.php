<?php

function select_to_array($class, $param_templates, $conditions="", $arr_select=array(), $raw_query=0)
{

	$arr_rows=array();

	$query=$class->select($conditions, $arr_select, $raw_query);
	
	//Load interval...
	
	while($arr_element=webtsys_fetch_array($query))
	{
	
		$arr_rows[$arr_element[$class->idmodel]]=$arr_element;
	
	}
	
	return $arr_rows;

}

?>