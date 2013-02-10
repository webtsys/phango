<?php

function select_to_array($conditions="", $arr_select=array(), $raw_query=0)
{

	$arr_return=array();
	
	if(count($arr_select)>0 && !isset($arr_select[$this->idmodel]))
	{
	
		$arr_select[]=$this->model;
	
	}

	$query=$this->select($conditions, $arr_select, $raw_query);
	
	while($arr_row=webtsys_fetch_array($query))
	{
	
		$arr_return[$this->idmodel]=$arr_row;
		
	}
	
	return $arr_return;

}

?>