<?php

function generate_paginator_method_class($class, $where, $arr_fields, $arr_extra_fields, $url_paginament, $total_elements, $num_elements, $initial_num_pages=20, $begin_page_var='begin_page', $raw_query=0)
{
	
	load_libraries(array('table_config', 'pages'));
	
	up_table_config($arr_fields['heads'], $arr_fields['widths']);
	
	$query=$class->select($where, array(), $raw_query);
	
	while($arr_content=webtsys_fetch_array($query)
	{
	
		$arr_new_list=array();
	
		foreach($arr_fields['fields'] as $field)
		{
		
			$arr_new_list[$field]=$arr_content[$field];
		
		}
	
		//Add callbacks
		
		foreach($arr_extra_fields as $extra_field_func)
		{
		
			$arr_content[$extra_field_func]=$extra_field_func($arr_content);
		
		}
		
	
		middle_table_config($arr_content);
		
	}
	
	down_table_config();
	
	echo pages( $_GET['begin_page'], $total_elements, $num_elements, $url_paginament ,$initial_num_pages, $begin_page_var);

}

?>