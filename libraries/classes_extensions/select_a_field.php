<?php

function select_a_field_method_class($class, $where, $field)
{
	
	$arr_field=array();
	
	$query=$class->select($where, array($field), $raw_query=1);
	
	while(list($field_choose)=webtsys_fetch_row($query))
	{
	
		$arr_field[]=$field_choose;
	
	}
	
	return $arr_field;

}

?>