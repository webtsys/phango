<?php

function select_to_array_method_class($class, $conditions="", $arr_select=array(), $raw_query=0)
{

	$arr_return=array();
	
	$arr_select[]=$class->idmodel;
	
	if(count($arr_select)==1)
	{
	
		$arr_select=$class->all_fields();
	
	}
	
	$query=$class->select($conditions, $arr_select, $raw_query);
	
	while($arr_row=webtsys_fetch_array($query))
	{
	
		
		$arr_return[$arr_row[$class->idmodel]]=$arr_row;
			
		
	}
	
	return $arr_return;

}

?>