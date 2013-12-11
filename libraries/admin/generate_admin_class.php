<?php

class GenerateAdminClass {

	public $class, $arr_fields, $arr_fields_edit, $url_options, $options_func, $where_sql, $arr_fields_form, $type_list, $url_back, $no_search, $txt_list_new, $txt_add_new_item, $txt_edit_item;

	function __construct($model_name)
	{
	
		global $model, $lang;
	
		$this->model_name=$model_name;
		$this->arr_fields=array(); 
		$this->arr_fields_edit=array();
		$this->url_options;
		$this->no_search=false;
		$this->options_func='BasicOptionsListModel';
		$this->where_sql='';
		$this->arr_fields_form=array();
		$this->type_list='Basic';
		$this->show_id=1;
		$this->yes_options=1;
		$this->extra_fields=array();
		$this->txt_list_new=$lang['common']['listing_new'].': '.$model[$this->model_name]->label;
		$this->txt_add_new_item=$lang['common']['add_new_item'].': '.$model[$this->model_name]->label;
		$this->txt_edit_item=$lang['common']['edit'];
		
	}
	
	function initial_order()
	{
	
		
	  
	}
	
	function show()
	{
	
		//$model[$this->model_name]->generate_admin($this->arr_fields, $this->arr_fields_edit, $this->url_options, $this->options_func, $this->where_sql, $this->arr_fields_form, $this->type_list, $this->no_search);
		
		global $model, $arr_cache_header, $arr_cache_jscript, $lang;
		
		settype($_GET['op_edit'], 'integer');
		settype($_GET['op_action'], 'integer');
		settype($_GET[$model[$this->model_name]->idmodel], 'integer');
		
		load_libraries(array('generate_admin_ng', 'utilities/menu_barr_hierarchy'));

		$url_admin=add_extra_fancy_url($this->url_options, array('op_action' => 1));
		
		$arr_menu=array( 0 => array($this->txt_list_new, $this->url_options), 1 => array($this->txt_add_new_item, $url_admin) );
		
		$arr_menu_edit=array( 0 => array($this->txt_list_new, $this->url_options), 1 => array($this->txt_edit_item, '') );
		
		switch($_GET['op_action'])
		{

			default:
				
				if($_GET['op_edit']==0)
				{

					echo '<p>'.menu_barr_hierarchy($arr_menu, 'op_action', $_GET['op_action']).'</p>';
				
					echo '<p class="add_new_item"><a href="'.add_extra_fancy_url($this->url_options, array('op_action' => 1)).'">'.$this->txt_add_new_item.'</a></p>';
					
				}
				else
				{
				
					echo '<p>'.menu_barr_hierarchy($arr_menu_edit, 'op_edit', $_GET['op_edit']).'</p>';
				
				}
				
				ListModel($this->model_name, $this->arr_fields, $this->url_options, $this->options_func, $this->where_sql, $this->arr_fields_edit, $this->type_list, $this->no_search, $this->show_id, $this->yes_options, $this->extra_fields);

			break;

			case 1:
			
				if($_GET['op_edit']==0)
				{

					echo '<p>'.menu_barr_hierarchy($arr_menu, 'op_action', $_GET['op_action']).'</p>';
					
				}
				
				echo '<h3>'.$this->txt_add_new_item.'</h3>';

				InsertModelForm($this->model_name, $url_admin, $this->url_options, $this->arr_fields_edit, $id=0);

			break;

		}
	
	}
	
	function show_config_mode()
	{
	
		global $base_url, $model;
	
		$model[$this->model_name]->func_update='Config';

		//nsertModelForm($model_name, $url_admin, $url_back, $arr_fields=array(), $id=0, $goback=1)
		
		InsertModelForm($this->model_name, $this->url_options, $this->url_back, $this->arr_fields_edit, $id=0, $goback=1);
	
	}

	
}

?>