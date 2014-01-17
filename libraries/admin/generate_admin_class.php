<?php

class GenerateAdminClass {

	public $class, $arr_fields, $arr_fields_edit, $url_options, $options_func, $where_sql, $arr_fields_form, $type_list, $url_back, $no_search, $txt_list_new, $txt_add_new_item, $txt_edit_item, $simple_redirect, $class_add, $separator_element_opt, $extra_menu_create;

	public $search_asc;
	public $search_desc;
	
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
		$this->simple_redirect=0;
		$this->class_add='';
		$this->separator_element_opt='<br />';
		$this->extra_menu_create='';
		$this->search_asc=$lang['common']['ascent'];
		$this->search_desc=$lang['common']['descent'];
		$this->show_goback=1;
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
				
					echo '<p class="add_new_item"><a class="'.$this->class_add.'" href="'.add_extra_fancy_url($this->url_options, array('op_action' => 1)).'">'.$this->txt_add_new_item.'</a> '.$this->extra_menu_create.'</p>';
					
				}
				else
				{
				
					echo '<p>'.menu_barr_hierarchy($arr_menu_edit, 'op_edit', $_GET['op_edit']).'</p>';
				
				}
				
				//ListModel($this->model_name, $this->arr_fields, $this->url_options, $this->options_func, $this->where_sql, $this->arr_fields_edit, $this->type_list, $this->no_search, $this->show_id, $this->yes_options, $this->extra_fields, $this->separator_element_opt);
				
				$listmodel=new ListModelClass($this->model_name, $this->arr_fields, $this->url_options, $this->options_func, $this->where_sql, $this->arr_fields_edit, $this->type_list, $this->no_search, $this->show_id, $this->yes_options, $this->extra_fields, $this->separator_element_opt);
				
				$listmodel->simple_redirect=$this->simple_redirect;
				
				$listmodel->search_asc=$this->search_asc;
				$listmodel->search_desc=$this->search_desc;
				$listmodel->show_goback=$this->show_goback;
				
				$listmodel->show();

			break;

			case 1:
			
				$arr_block='none';
			
				if($_GET['op_edit']==0)
				{

					echo '<p>'.menu_barr_hierarchy($arr_menu, 'op_action', $_GET['op_action']).'</p>';
					
				}
				
				echo '<h3>'.$this->txt_add_new_item.'</h3>';

				InsertModelForm($this->model_name, $url_admin, $this->url_options, $this->arr_fields_edit, $id=0, $this->show_goback, $this->simple_redirect);

			break;

		}
	
	}
	
	function show_config_mode()
	{
	
		global $base_url, $model;
	
		$model[$this->model_name]->func_update='Config';

		//nsertModelForm($model_name, $url_admin, $url_back, $arr_fields=array(), $id=0, $goback=1)
		
		InsertModelForm($this->model_name, $this->url_options, $this->url_back, $this->arr_fields_edit, $id=0, $this->show_goback, $this->simple_redirect);
	
	}

	
}

class ListModelClass {

	public $model_name, $arr_fields, $url_options, $options_func='BasicOptionsListModel', $where_sql='', $arr_fields_form=array(), $type_list='Basic', $no_search=false, $yes_id=1, $yes_options=1, $extra_fields=array(), $separator_element='<br />', $simple_redirect=0;
	
	public $search_asc;
	public $search_desc;

	function __construct($model_name, $arr_fields, $url_options, $options_func='BasicOptionsListModel', $where_sql='', $arr_fields_form=array(), $type_list='Basic', $no_search=false, $yes_id=1, $yes_options=1, $extra_fields=array(), $separator_element='<br />', $simple_redirect=0)
	{
	
		global $lang;
	
		$this->model_name=$model_name;
		$this->arr_fields=$arr_fields;
		$this->url_options=$url_options;
		$this->options_func=$options_func; 
		$this->where_sql=$where_sql; 
		$this->arr_fields_form=$arr_fields_form; 
		$this->type_list=$type_list; 
		$this->no_search=$no_search; 
		$this->yes_id=$yes_id; 
		$this->yes_options=$yes_options; 
		$this->extra_fields=$extra_fields; 
		$this->separator_element=$separator_element; 
		$this->simple_redirect=$simple_redirect;
		$this->search_asc=$lang['common']['ascent'];
		$this->search_desc=$lang['common']['descent'];
		$this->show_goback=1;
	
	}
	
	public function show()
	{
	
		global $model, $lang, $std_error, $arr_block;

		settype($_GET['op_edit'], 'integer');

		if( count($model[$this->model_name]->forms)==0)
		{	
			$model[$this->model_name]->create_form();
		}
		
		switch($_GET['op_edit'])
		{

		default:

			$arr_label_fields=array();
			$cell_sizes=array();
			/*$where_sql='';*/
			$arr_where_sql='';
			$location='';
			$arr_order=array();
			$show_form=1;
			
			if($this->no_search==true)
			{
			
				$show_form=0;
			
			}
			
			/*if($no_search==false)
			{*/
				$search=new SearchInFieldClass($this->model_name, $this->arr_fields, $this->arr_fields, $this->where_sql, $this->url_options, $this->yes_id, $show_form);
				
				$search->lang_asc=$this->search_asc;
				$search->lang_desc=$this->search_desc;
			
				list($where_sql, $arr_where_sql, $location, $arr_order)=$search->search();
			
				//list($where_sql, $arr_where_sql, $location, $arr_order)=SearchInField($this->model_name, $arr_fields, $arr_fields, $where_sql, $url_options, $yes_id, $show_form);
			//}
			//Num elements in page
			
			if(!function_exists($model[$this->model_name]->func_update.'List'))
			{
				
				BasicList($this->model_name, $this->where_sql, $arr_where_sql, $location, $arr_order, $this->arr_fields, $cell_sizes, $this->options_func, $this->url_options, $this->yes_id, $this->yes_options, $this->extra_fields, $this->separator_element);

			}
			else
			{
				
				$func_list=$model[$this->model_name]->func_update.'List';

				$func_list($this->model_name, $this->where_sql, $arr_where_sql, $location, $arr_order, $this->arr_fields, $cell_sizes, $this->options_func, $this->url_options, $this->yes_id, $this->yes_options, $this->extra_fields, $this->separator_element);

			}

		break;

		case 1:
			
			settype($_GET[$model[$this->model_name]->idmodel], 'integer');
			
			$query=$model[$this->model_name]->select('where '.$model[$this->model_name]->idmodel.'='.$_GET[$model[$this->model_name]->idmodel], $this->arr_fields_form, true);
			
			$post=webtsys_fetch_array($query);
			
			//model_set_form($this->model_name, $post, NO_SHOW_ERROR);
			
			SetValuesForm($post, $model[$this->model_name]->forms, 0);
			
			$url_options_edit=add_extra_fancy_url($this->url_options, array('op_edit' =>1, $model[$this->model_name]->idmodel => $_GET[$model[$this->model_name]->idmodel]) );
			
			InsertModelForm($this->model_name, $url_options_edit, $this->url_options, $this->arr_fields_form, $_GET[$model[$this->model_name]->idmodel], $go_back=1, $this->simple_redirect);
			
		break;

		case 2:

			settype($_GET[$model[$this->model_name]->idmodel], 'integer');

			$func_delete=$model[$this->model_name]->func_update.'DeleteModel';
			
			$url_options_delete=add_extra_fancy_url($this->url_options, array('success_delete' => 1) );

			if($func_delete($this->model_name, $_GET[ $model[$this->model_name]->idmodel ]))
			{	
				//die(header('Location: '.$url_options_delete));
				/*ob_end_clean();
				load_libraries(array('redirect'));
				die( redirect_webtsys( $url_options_delete, $lang['common']['redirect'], $lang['common']['success'], $lang['common']['press_here_redirecting'] , $arr_block) );*/
				
				load_libraries(array('redirect'));
				simple_redirect( $url_options_delete, $lang['common']['redirect'], $lang['common']['success'], $lang['common']['press_here_redirecting']);

			}
			else
			{

				echo 'Error: '.$std_error;

			}
		
		break;

		}
	
	}

}

//Class for search, the function is deprecated when need many arguments, is more easy use classes for its.

class SearchInFieldClass {

	public $model_name, $arr_fields_order, $arr_fields_search, $where_sql, $url_options, $yes_id=1, $show_form=1, $lang_asc;

	function __construct($model_name, $arr_fields_order, $arr_fields_search, $where_sql, $url_options, $yes_id=1, $show_form=1)
	{
	
		global $lang;
	
		$this->model_name=$model_name;
		$this->arr_fields_order=$arr_fields_order;
		$this->arr_fields_search=$arr_fields_search;
		$this->where_sql=$where_sql;
		$this->url_options=$url_options;
		$this->yes_id=$yes_id;
		$this->show_form=$show_form;
		$this->lang_asc=$lang['common']['ascent'];
		$this->lang_desc=$lang['common']['descent'];
	
	
	}
	
	public function search()
	{
	
		global $lang, $model;
	
		load_libraries(array('search_in_field'));

		if(count($model[$this->model_name]->forms)==0)
		{

			$arr_error_sql[0]='Do you need create a form for this model';    
			$arr_error_sql[1]='Do you need create a form for this model '.$this->model_name.' for use SearchInField function';
			ob_end_clean();
			echo load_view(array('title' => 'Phango site is down', 'content' => '<p>'.$arr_error_sql[DEBUG].'</p>'), 'common/common');
			die();

		}

		if(!in_array($model[$this->model_name]->idmodel, $this->arr_fields_order) && $this->yes_id==1)
		{

			array_unshift($this->arr_fields_order, $model[$this->model_name]->idmodel);
			array_unshift($this->arr_fields_search, $model[$this->model_name]->idmodel);
			
			$model[$this->model_name]->forms[ $model[$this->model_name]->idmodel ]->label='#Id.';

		}

		//Set order

		$_GET['order_field']=@form_text($_GET['order_field']);
		$_GET['search_word']=@form_text($_GET['search_word']);
		$_GET['search_field']=@form_text($_GET['search_field']);
		
		$arr_order_select=array();

		if( !in_array($_GET['order_field'], $this->arr_fields_order) )
		{

			$_GET['order_field']=$this->arr_fields_order[0]; //$model[$model_name]->idmodel;

		}
		
		if( !in_array($_GET['search_field'], $this->arr_fields_search) )
		{

			$_GET['search_field']=$this->arr_fields_search[0]; //$model[$model_name]->idmodel;

		}
		
		//0=DESC
		//1=ASC

		settype($_GET['order_desc'], 'integer');
			
		$arr_order[$_GET['order_desc']]='ASC';

		$arr_order[0]='ASC';
		$arr_order[1]='DESC';

		$arr_order_select[]=$_GET['order_desc'];
		$arr_order_select[]=$this->lang_asc;
		$arr_order_select[]=0;
		$arr_order_select[]=$this->lang_desc;
		$arr_order_select[]=1;

		$arr_order_field=array($_GET['order_field']);
		$arr_search_field=array($_GET['search_field']);

		foreach($this->arr_fields_order as $field_label)
		{

			$arr_order_field[]=$model[$this->model_name]->forms[$field_label]->label;
			$arr_order_field[]=$field_label;

		}
		
		foreach($this->arr_fields_search as $field_label)
		{

			$arr_search_field[]=$model[$this->model_name]->forms[$field_label]->label;
			$arr_search_field[]=$field_label;

		}
		
		if($this->show_form==1)
		{
			echo load_view(array($arr_search_field, $arr_order_field, $arr_order_select, $this->url_options), 'common/forms/searchform');
		}
		//Query for order

		//Query for search_by
		
		/*list($location, $arr_where_sql)=search_in_field($model_name, array($_GET['search_field']), $_GET['search_word']);
		
		if($location!='')
		{

			$location=$location.' DESC ,';

		}*/
		
		$location='';
		
		$arr_where_sql='';
		
		if(isset($model[$this->model_name]->components[$_GET['search_field']]) && $_GET['search_word']!='')
		{
		
			$value_search=$model[$this->model_name]->components[$_GET['search_field']]->search_field($_GET['search_word']);
			
			if(get_class($model[$this->model_name]->components[$_GET['search_field']])!='ForeignKeyField')
			{
			
				$arr_where_sql='`'.$this->model_name.'`.`'.$_GET['search_field'].'` LIKE \'%'.$value_search.'%\'';
				
			}
			else
			{
			
				$model_related_name=$model[$this->model_name]->components[$_GET['search_field']]->related_model;
				
				if($model[$this->model_name]->components[$_GET['search_field']]->name_field_to_field!='')
				{
				
					$field_related_name=$model[$this->model_name]->components[$_GET['search_field']]->name_field_to_field;
					
					$arr_where_sql='`'.$model_related_name.'`.`'.$field_related_name.'` LIKE \'%'.$value_search.'%\'';
					
				}
			
			}
		
		}

		if($this->where_sql=='' && $arr_where_sql!='')
		{
			
			$this->where_sql='where ';

		}
		else if($this->where_sql!='' && $arr_where_sql!='')
		{

			$this->where_sql.=' AND ';

		}
		
		return array($this->where_sql, $arr_where_sql, $location, $arr_order);
	
	}

}

?>