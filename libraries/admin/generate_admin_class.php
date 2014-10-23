<?php

load_libraries(array('utilities/menu_barr_hierarchy', 'generate_admin_ng'));

class GenerateAdminClass {

	public $class, $arr_fields, $arr_fields_edit, $url_options, $options_func, $where_sql, $arr_fields_form, $type_list, $url_back, $no_search, $txt_list_new, $txt_add_new_item, $txt_edit_item, $simple_redirect, $class_add, $separator_element_opt, $extra_menu_create, $listmodel, $number_id, $arr_fields_no_showed;

	public $search_asc;
	public $search_desc;
	public $arr_categories;
	
	function __construct($model_name)
	{
	
		global $model, $lang, $base_url;
	
		$this->model_name=$model_name;
		$this->arr_fields=array(); 
		$this->arr_fields_edit=array();
		$this->url_options=make_fancy_url($base_url, PHANGO_SCRIPT_BASE_CONTROLLER, PHANGO_SCRIPT_FUNC_NAME, TEXT_FUNCTION_CONTROLLER, array());
		$this->url_back=$this->url_options;
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
		$this->class_add='add_class_item_link';
		$this->goback_class='add_class_item_link';
		$this->separator_element_opt='<br />';
		$this->extra_menu_create='';
		$this->search_asc=$lang['common']['ascent'];
		$this->search_desc=$lang['common']['descent'];
		$this->show_goback=1;
		$this->arr_fields_order=array();
		$this->arr_fields_search=array();
		$this->number_id=0;
		$this->arr_categories=array('default' => array());
		$this->arr_fields_no_showed=array();
	
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
				
				//$this->url_back=$this->url_options;
				
				//$this->url_options=add_extra_fancy_url($this->url_options, array('op_edit' => $_GET['op_edit']));
				
				$listmodel=new ListModelClass($this->model_name, $this->arr_fields, $this->url_options, $this->options_func, $this->where_sql, $this->arr_fields_edit, $this->type_list, $this->no_search, $this->show_id, $this->yes_options, $this->extra_fields, $this->separator_element_opt);
				
				$listmodel->url_back=$this->url_back;
				
				if($listmodel->url_back=='')
				{
					
					$listmodel->url_back=$listmodel->url_options;
					
				}
				
				//echo $listmodel->url_back; die;
				$listmodel->simple_redirect=$this->simple_redirect;
				
				$listmodel->admin_class=&$this;
				
				if(count($this->arr_fields_order)>0)
				{
				
					$listmodel->arr_fields_order=$this->arr_fields_order;
				
				}
				
				if(count($this->arr_fields_search)>0)
				{
				
					$listmodel->arr_fields_search=$this->arr_fields_search;
				
				}
				
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

				//InsertModelForm($this->model_name, $url_admin, $this->url_options, $this->arr_fields_edit, $id=0, $this->show_goback, $this->simple_redirect);
				
				$this->url_back=$this->url_options;
				
				$this->url_options=add_extra_fancy_url($this->url_options, array('op_action' => 1));
				
				$this->insert_model_form();
				
			break;

		}
	
	}
	
	function show_config_mode($post=array())
	{
	
		global $base_url, $model;
		
		$model[$this->model_name]->func_update='Config';

		if(count($model[$this->model_name]->forms)==0)
		{
		
			$model[$this->model_name]->create_form();
		
		}
		//Obtain data
		
		if(count($post)==0)
		{
		
			$post=$model[$this->model_name]->select_a_row_where($this->where_sql, $this->arr_fields_edit, 1);
		
		}
		
		SetValuesForm($post, $model[$this->model_name]->forms, 0);
		
		//nsertModelForm($model_name, $url_admin, $url_back, $arr_fields=array(), $id=0, $goback=1)
		
		//InsertModelForm($this->model_name, $this->url_options, $this->url_back, $this->arr_fields_edit, $id=0, $this->show_goback, $this->simple_redirect, $this->where_sql);
		
		if($this->url_back=='')
		{
			$this->url_back=$this->url_options;
		}
		
		$this->url_options=add_extra_fancy_url($this->url_options, array('op_action' => 1));
	
		$this->insert_model_form();
		
	}
	

	function insert_model_form()
	{
	
		global $model, $lang, $std_error, $arr_block, $base_url;
		//Setting op variable to integer for use in switch
		
		//function InsertModelForm($model_name, $url_admin, $url_back, $arr_fields=array(), $id=0, $goback=1, $simple_redirect=0, $where_sql='')
		
		$model_name=$this->model_name;
		$url_admin=$this->url_options; 
		
		$url_back=$this->url_back;
		$arr_fields=$this->arr_fields_edit;
		$id=$this->number_id; 
		$goback=$this->show_goback; 
		$simple_redirect=$this->simple_redirect;
		$where_sql=$this->where_sql;
		
		if(isset($model[$model_name]))
		{

			settype($_GET['op_update'], 'integer');
			settype($_GET['success'], 'integer');

			$url_post=add_extra_fancy_url($url_admin, array('op_update' =>1));
			
			if( count($model[$model_name]->forms)==0)
			{	
				$model[$model_name]->create_form();
			}
			
			//UpdateModelFormView($model_form, $arr_fields=array(), $url_post)

			if(count($arr_fields)==0)
			{

				$arr_fields=array_keys($model[$model_name]->forms);

			}
			
			switch($_GET['op_update'])
			{

				default:

					ob_start();
					
					echo load_view(array($model[$model_name]->forms, $arr_fields, $url_post, $model[$model_name]->enctype, '_generate_admin_'.$model_name, $this->arr_categories), 'common/forms/updatemodelform');

					$cont_index=ob_get_contents();

					ob_end_clean();

					echo load_view(array($lang['common']['edit'], $cont_index), 'content');
					
				break;

				case 1:
			
					$arr_update[$id]=$model[$model_name]->func_update.'UpdateModel';
					$arr_update[0]=$model[$model_name]->func_update.'InsertModel';

					$func_update=$arr_update[$id];
					
					if(!$func_update($model_name, $arr_fields, $_POST, $id, $where_sql))
					{

						ob_start();
						
						echo '<p class="error">'.$lang['common']['cannot_update_insert_in_model'].' '.$model_name.': '.$model[$model_name]->std_error.'</p>';

						$post=filter_fields_array($arr_fields, $_POST);
						
						SetValuesForm($post, $model[$model_name]->forms);

						echo load_view(array($model[$model_name]->forms, $arr_fields, $url_post, $model[$model_name]->enctype, '_generate_admin_'.$model_name, $this->arr_categories), 'common/forms/updatemodelform');

						$cont_index=ob_get_contents();

						ob_end_clean();

						echo load_view(array($lang['common']['edit'], $cont_index), 'content');

					}
					else
					{

						//die(header('Location: '.$url_admin.'/success/1'));
						
						
						$text_output=$lang['common']['success'];
						
						ob_end_clean();
						
						if($simple_redirect==0)
						{
							
							load_libraries(array('redirect'));
							die( redirect_webtsys( $url_back, $lang['common']['redirect'], $text_output, $lang['common']['press_here_redirecting'] , $arr_block) );
						}
						else
						{
						
							load_libraries(array('redirect'));
							simple_redirect( $url_back, $lang['common']['redirect'], $lang['common']['success'], $lang['common']['press_here_redirecting']);

						}
						
					}

				break;

			}

			if($goback==1)
			{
		
				?>
				<p><a href="<?php echo $url_back; ?>" class="<?php echo $this->goback_class; ?>"><?php echo $lang['common']['go_back']; ?></a></p>
				<?php

			}

		}
		else
		{

			

		}
	
	}
	
	public function set_url_post($url_post)
	{
	
		$this->url_options=$url_post;
		$this->url_back=$url_post;
	
	}
	
	public function set_url_back($url_back)
	{
		
		$this->url_back=$url_back;
		
	}
	
}

class ListModelClass {

	public $model_name, $arr_fields, $url_options, $options_func='BasicOptionsListModel', $where_sql='', $arr_fields_form=array(), $type_list='Basic', $no_search=false, $yes_id=1, $yes_options=1, $extra_fields=array(), $separator_element='<br />', $simple_redirect=0, $admin_class;
	
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
		$this->separator_element_opt='<br />';
		$this->arr_fields_order=$this->arr_fields;
		$this->arr_fields_search=$this->arr_fields;
		$this->admin_class=new GenerateAdminClass($this->model_name);
	
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
				$search=new SearchInFieldClass($this->model_name, $this->arr_fields_order, $this->arr_fields_search, $this->where_sql, $this->url_options, $this->yes_id, $show_form);
				
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
			
			//$this->admin_class->url_back=$this->url_options;
			//echo $this->admin_class->url_back; die;
			$this->admin_class->url_options=$url_options_edit;
			
			if($this->url_back!='')
			{
			
				$this->admin_class->url_back=$this->url_back;
			
			}
			
			//InsertModelForm($this->model_name, $url_options_edit, $this->url_options, $this->arr_fields_form, $_GET[$model[$this->model_name]->idmodel], $this->show_goback, $this->simple_redirect);
			
			$this->admin_class->number_id=$_GET[$model[$this->model_name]->idmodel];
			
			$this->admin_class->insert_model_form();
			
			/*ob_start();
			
			echo load_view(array($model[$this->model_name]->forms, $this->arr_fields_form, $this->url_options, $model[$this->model_name]->enctype, '_generate_admin_'.$this->model_name), 'common/forms/updatemodelform');

			$cont_index=ob_get_contents();

			ob_end_clean();

			echo load_view(array($lang['common']['edit'], $cont_index), 'content');*/
			
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

class SimpleList
{

	public $arr_options=array();
	public $yes_options=1;
	public $arr_fields=array();
	public $arr_fields_no_showed=array();
	public $arr_extra_fields=array();
	public $arr_extra_fields_func=array();
	public $arr_cell_sizes=array();
	public $model_name;
	public $where_sql='';
	public $options_func='BasicOptionsListModel';
	public $url_options='';
	public $separator_element='<br />';
	public $limit_rows=10;
	public $raw_query=1;
	public $yes_pagination=1;
	public $num_by_page=20;
	public $begin_page=0;
	public $initial_num_pages=20;
	public $variable_page='begin_page';
	
	function __construct($model_name)
	{
	
		global $model;
	
		$this->model_name=$model_name;
		
		$this->begin_page=$_GET['begin_page'];
		
		if( count($model[$this->model_name]->forms)==0)
		{	
			$model[$this->model_name]->create_form();
		}
	
	}
	
	public function show()
	{
	
		global $model, $lang;
		
		load_libraries(array('table_config'));
		
		$arr_fields_show=array();
		
		if(count($this->arr_fields)==0)
		{
			
			$this->arr_fields=array_keys($model[$this->model_name]->components);
		
		}
		
		if(!in_array($model[$this->model_name]->idmodel, $this->arr_fields))
		{
		
			$this->arr_fields[]=$model[$this->model_name]->idmodel;
			$this->arr_fields_no_showed[]=$model[$this->model_name]->idmodel;
		
		}
		
		$arr_fields_showed=array_diff($this->arr_fields, $this->arr_fields_no_showed);
		
		foreach($arr_fields_showed as $field)
		{
		
			$arr_fields_show[$field]=$model[$this->model_name]->components[$field]->label;
		
		}
		
		//Extra fields name_field
		
		foreach($this->arr_extra_fields as $extra_key => $name_field)
		{
		
			$arr_fields_show[$extra_key]=$name_field;
		
		}
		
		$options_method='no_add_options';
		
		if($this->yes_options)
		{
		
			$arr_fields_show[]=$lang['common']['options'];
			$options_method='yes_add_options';
		
		}
		
		if($this->limit_rows>0)
		{
		
			$this->where_sql=$this->where_sql.' limit '.$this->limit_rows;
		
		}
		
		up_table_config($arr_fields_show, $this->arr_cell_sizes);
		
		$query=$model[$this->model_name]->select($this->where_sql, $this->arr_fields, $this->raw_query);
		
		while($arr_row=webtsys_fetch_array($query))
		{
		
			$arr_row_final=array();
		
			foreach($arr_fields_showed as $field)
			{
			
				$arr_row_final[$field]=$model[$this->model_name]->components[$field]->show_formatted($arr_row[$field]);
			
			}
			
			//Extra arr_extra_fields
			
			foreach($this->arr_extra_fields_func as $name_func)
			{
				
				$arr_row_final[]=$name_func($arr_row);
				
			}
			
			$arr_row_final=$this->$options_method($arr_row_final, $arr_row, $this->options_func, $this->url_options, $this->model_name, $model[$this->model_name]->idmodel, $this->separator_element);
		
			middle_table_config($arr_row_final, $cell_sizes=array());
		
		}
		
		down_table_config();
		
		if($this->yes_pagination==1)
		{
		
			load_libraries(array('pages'));
			
			$total_elements=$model[$this->model_name]->select_count($this->where_sql);
			
			echo '<p>'.$lang['common']['pages'].': '.pages( $this->begin_page, $total_elements, $this->num_by_page, $this->url_options ,$this->initial_num_pages, $this->variable_page, $label='', $func_jscript='').'</p>';
		
		}
	
	}
	
	private function yes_add_options($arr_row, $arr_row_raw, $options_func, $url_options, $model_name, $model_idmodel, $separator_element)
	{
		
		$arr_row[]=implode($separator_element, $options_func($url_options, $model_name, $arr_row_raw[$model_idmodel], $arr_row_raw) );
		
		return $arr_row;

	}



	private function no_add_options($arr_row, $arr_row_raw, $options_func, $url_options, $model_name, $model_idmodel, $separator_element)
	{

		return $arr_row;

	}


}

class ListModelAjaxClass {


	public $where_sql, $arr_fields, $arr_fields_showed, $extra_fields, $yes_pagination, $limit_num;

	function __construct($model_name, $arr_fields=array(), $arr_fields_showed=array(), $extra_fields=array(), $where_sql='')
	{
	
		$this->model_name=$model_name;
		$this->where_sql=$where_sql;
		$this->arr_fields=$arr_fields;
		$this->arr_fields_showed=$arr_fields_showed;
		$this->extra_fields=$extra_fields;
		$this->yes_pagination=1;
		$this->limit_num=20;
	}
	
	function show()
	{
	
		//The limit is defined in query...
	
		$total_elements=$model[$this->model_name]->select_count($this->where_sql);
		
		$query=$model[$this->model_name]->select($this->where_sql, $arr_fields);
	
		echo load_view(array($query, $this), 'utilities/list');
		
	
	}
	
	function obtain_data_ajax($page)
	{
	
		
	
	}

}

?>