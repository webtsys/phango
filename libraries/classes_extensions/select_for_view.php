<?php

function select_for_view_method_class($class, $param_templates, $conditions="", $arr_select=array(), $raw_query=0)
{

	//Load view...
	
	load_libraries_views($param_templates['view_library'], $param_templates['func_views']);
	
	//Load header...
	
	echo load_view($param_templates['func_views']['header']['arr_template'], $param_templates['func_views']['header']['params_template']);
	
	//Load query

	$query=$class->select($conditions, $arr_select, $raw_query);
	
	//Load interval...
	
	while($arr_element=webtsys_fetch_row($query))
	{
	
		echo load_view($arr_element, $param_templates['func_views'][1]['params_template']);
	
	}
	
	//Load footer
	
	echo load_view($param_templates['func_views']['header']['footer'], $param_templates['func_views']['footer']['params_template']);

}

?>