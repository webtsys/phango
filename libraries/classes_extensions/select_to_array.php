<?php

function select_to_array_method_class($class, $conditions="", $arr_select=array(), $raw_query=0, $index_id='')
{

	$arr_return=array();
	
	if($index_id=='')
	{
	
		$index_id=$class->idmodel;
	
	}
	
	$arr_select[]=$index_id;
	
	if(count($arr_select)==1)
	{
	
		$arr_select=$class->all_fields();
	
	}
	
	$query=$class->select($conditions, $arr_select, $raw_query);
	
	while($arr_row=webtsys_fetch_array($query))
	{
	
		
		$arr_return[$arr_row[$index_id]]=$arr_row;
			
		
	}
	
	return $arr_return;

}

?>