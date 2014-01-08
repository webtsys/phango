<?php

class CreateModelForm {

	//Array with ModelForms
	public $arr_forms=array();
	//Array with the name of fields that i want show.
	public $arr_fields=array();
	public $url_post='';
	public $enctype='';
	public $html_id='';
	
	public function __construct($arr_forms, $arr_fields=array())
	{
	
		$this->arr_forms=$arr_forms;
		
		if(count($arr_fields)==0)
		{
		
			$this->arr_fields=array_keys($this->arr_forms);
	
		}
	}
	
	public function show($arr_values=array())
	{
	
		if(count($arr_values)>0)
		{
		
			SetValuesForm($arr_values, $this->arr_forms, $show_error=1);
		
		}
	
		echo load_view(array($this->arr_forms, $this->arr_fields, $this->url_post, $this->enctype, $this->html_id), 'common/forms/updatemodelform');
	
	}
	
	public function check($arr_values)
	{
	
		
	
	}

}

?>