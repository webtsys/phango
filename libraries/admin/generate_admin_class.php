<?php

class GenerateAdminClass {

	public $class, $arr_fields, $arr_fields_edit, $url_options, $options_func, $where_sql, $arr_fields_form, $type_list;

	function __construct($model_name)
	{
	
		$this->model_name=$model_name;
		$this->arr_fields=array(); 
		$this->arr_fields_edit=array();
		$this->url_options;
		$this->options_func='BasicOptionsListModel';
		$this->where_sql='';
		$this->arr_fields_form=array();
		$this->type_list='Basic';
		
	}
	
	function initial_order()
	{
	
		
	  
	}
	
	function show()
	{
	
		global $model;
	
		$model[$this->model_name]->generate_admin($this->arr_fields, $this->arr_fields_edit, $this->url_options, $this->options_func, $this->where_sql, $this->arr_fields_form, $this->type_list);
		
	
	}

	
}

?>