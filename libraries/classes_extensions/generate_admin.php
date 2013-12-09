<?php

function generate_admin_method_class($class, $arr_fields, $arr_fields_edit, $url_options, $options_func='BasicOptionsListModel', $where_sql='', $arr_fields_form=array(), $type_list='Basic', $no_search=false)
{

	load_libraries(array('generate_admin_ng'));
	
	generate_admin_model_ng($class->name, $arr_fields, $arr_fields_edit, $url_options, $options_func, $where_sql, $arr_fields_form, $type_list, $no_search);

}

?>