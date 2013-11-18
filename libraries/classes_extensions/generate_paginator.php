<?php

function generate_paginator_method_class($class, $where, $arr_fields, $arr_extra_fields, $url_paginament,$num_elements, $initial_num_pages=20, $begin_page_var='begin_page', $raw_query=0)
{

	global $lang;
	
	load_libraries(array('table_config', 'pages', 'generate_admin_ng'));
	
	if(count($class->forms)==0)
	{
	
		$class->create_form();
	
	}
	
	if(!in_array($class->idmodel, $arr_fields['fields']))
	{
	
		array_unshift($arr_fields['fields'], $class->idmodel);
	
	}
	
	$arr_heads=array();
	
	foreach($arr_fields['fields'] as $field)
	{
		
		$arr_heads[]=$class->forms[$field]->label;
	
	}
	
	foreach($arr_extra_fields['fields'] as $field)
	{
		
		$arr_heads[]=$field;
	
	}
	
	up_table_config($arr_heads, $arr_fields['widths']);
	
	$total_elements=$class->select_count($where, $class->idmodel);
	
	$query=$class->select($where, $arr_fields['fields'], $raw_query);
	
	while($arr_content=webtsys_fetch_array($query))
	{
	
		$arr_new_list=array();
	
		foreach($arr_fields['fields'] as $field)
		{
			
			$arr_new_list[$field]=$class->components[$field]->show_formatted($arr_content[$field]);
		
		}
	
		//Add callbacks
		
		foreach($arr_extra_fields['func'] as $extra_field_func)
		{
		
			$arr_new_list[$extra_field_func]=implode('<br />', $extra_field_func($url_paginament, $class->name, $arr_content[$class->idmodel], $arr_content));
		
		}
		
	
		middle_table_config($arr_new_list);
		
	}
	
	down_table_config();
	
	echo '<p class="paginator">'.$lang['common']['pages'].': '.pages( $_GET['begin_page'], $total_elements, $num_elements, $url_paginament ,$initial_num_pages, $begin_page_var).'</p>';

}

?>