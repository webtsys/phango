<?php

class GenerateAdminClass {

	public $class, $arr_fields, $arr_fields_edit, $url_options, $options_func, $where_sql, $arr_fields_form, $type_list;

	function __construct($model_name)
	{
	
		$this->class=$model_name;
		$this->arr_fields=array(); 
		$this->arr_fields_edit=array();
		$this->url_options;
		$this->options_func='BasicOptionsListModel';
		$where_sql='';
		$arr_fields_form=array();
		$type_list='Basic';
		
	}
	
	function show()
	{
	
		global $model;
	
		$model[$model_name]->generate_admin($this->arr_fields, $this->arr_fields_edit, $this->url_options, $this->options_func, $this->where_sql, $this->arr_fields_form, $this->type_list);
		
	
	}

	
}

?>