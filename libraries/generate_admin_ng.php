<?php

load_libraries(array('generate_forms', 'table_config', 'pages'));

function InsertModelForm($model_name, $url_admin, $url_back, $arr_fields=array(), $id=0, $goback=1)
{
	global $model, $lang, $std_error, $arr_block;
	//Setting op variable to integer for use in switch

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

			$arr_fields=array_keys($model[$model_name]->components);

		}
		
		switch($_GET['op_update'])
		{

			default:

				ob_start();
				
				echo load_view(array($model[$model_name]->forms, $arr_fields, $url_post, $model[$model_name]->enctype), 'common/forms/updatemodelform');

				$cont_index=ob_get_contents();

				ob_end_clean();

				echo load_view(array($lang['common']['edit'], $cont_index), 'content');
				
			break;

			case 1:
		
				$arr_update[$id]=$model[$model_name]->func_update.'UpdateModel';
				$arr_update[0]=$model[$model_name]->func_update.'InsertModel';

				$func_update=$arr_update[$id];
				
				if(!$func_update($model_name, $arr_fields, $_POST, $id))
				{

					ob_start();
					
					echo '<p class="error">'.$lang['common']['cannot_update_insert_in_model'].' '.$model_name.': '.$model[$model_name]->std_error.'</p>';

					$post=filter_fields_array($arr_fields, $_POST);
					
					SetValuesForm($post, $model[$model_name]->forms);

					echo load_view(array($model[$model_name]->forms, $arr_fields, $url_post, $model[$model_name]->enctype), 'common/forms/updatemodelform');

					$cont_index=ob_get_contents();

					ob_end_clean();

					echo load_view(array($lang['common']['edit'], $cont_index), 'content');

				}
				else
				{

					//die(header('Location: '.$url_admin.'/success/1'));
					
					ob_end_clean();
					load_libraries(array('redirect'));
					die( redirect_webtsys( $url_back, $lang['common']['redirect'], $lang['common']['success'], $lang['common']['press_here_redirecting'] , $arr_block) );

				}

			break;

		}

		if($goback==1)
		{
	
			?>
			<p><a href="<?php echo $url_back; ?>"><?php echo $lang['common']['go_back']; ?></a></p>
			<?php

		}

	}
	else
	{

		

	}

}

function BasicInsertModel($model_name, $arr_fields, $post)
{
	global $model;

	//Check $std_error if fail

	$post=filter_fields_array($arr_fields, $post);

	if( $model[$model_name]->insert($post) )
	{

		return 1;

	}

	return 0;

}

function BasicUpdateModel($model_name, $arr_fields, $post, $id)
{

	global $model;

	if( $model[$model_name]->update($post, 'where '.$model[$model_name]->idmodel.'='.$id) )
	{
		
		return 1;

	}

	return 0;

}

function BasicDeleteModel($model_name, $id)
{

	global $model;

	settype($id, 'integer');
	
	if( $model[$model_name]->delete('where '.$model[$model_name]->idmodel.'='.$id) )
	{
		
		return 1;

	}

	return 0;

}

function ConfigInsertModel($model_name, $arr_fields, $post)
{

	global $model;

	$num_insert=$model[$model_name]->select_count($model_name, $model[$model_name]->idmodel);

	$func_update='insert';

	if($num_insert>0)
	{

		$func_update='update';

	}

	if($model[$model_name]->$func_update($post, 'limit 1'))
	{

		return 1;

	}
	
	return 0;

}

function ConfigUpdateModel($model_name, $arr_fields, $post, $id)
{

	global $model;

	return 0;

}

function ConfigDeleteModel($model_name, $id)
{

	return 0;

}

function ListModel($model_name, $arr_fields, $url_options, $options_func='BasicOptionsListModel', $where_sql='', $arr_fields_form=array(), $type_list='Basic')
{

	global $model, $lang, $std_error, $arr_block;

	settype($_GET['op_edit'], 'integer');

	if( count($model[$model_name]->forms)==0)
	{	
		$model[$model_name]->create_form();
	}

	switch($_GET['op_edit'])
	{

	default:

		$arr_label_fields=array();
		$cell_sizes=array();

		list($where_sql, $arr_where_sql, $location, $arr_order)=SearchInField($model_name, $arr_fields, $where_sql, $url_options);
		
		//Num elements in page
		
		if(!function_exists($model[$model_name]->func_update.'List'))
		{
	
			BasicList($model_name, $where_sql, $arr_where_sql, $location, $arr_order, $arr_fields, $cell_sizes, $options_func, $url_options);

		}
		else
		{

			$func_list=$model[$model_name]->func_update.'List';

			$func_list($model_name, $where_sql, $arr_where_sql, $location, $arr_order, $arr_fields, $cell_sizes, $options_func, $url_options);

		}

	break;

	case 1:
		
		settype($_GET[$model[$model_name]->idmodel], 'integer');
		
		$query=$model[$model_name]->select('where '.$model[$model_name]->idmodel.'='.$_GET[$model[$model_name]->idmodel], $arr_fields_form, true);
		
		$post=webtsys_fetch_array($query);
		
		//model_set_form($model_name, $post, NO_SHOW_ERROR);
		
		SetValuesForm($post, $model[$model_name]->forms, 0);
		
		$url_options_edit=add_extra_fancy_url($url_options, array('op_edit' =>1, $model[$model_name]->idmodel => $_GET[$model[$model_name]->idmodel]) );
		
		InsertModelForm($model_name, $url_options_edit, $url_options, $arr_fields_form, $_GET[$model[$model_name]->idmodel]);
		
	break;

	case 2:

		settype($_GET[$model[$model_name]->idmodel], 'integer');

		$func_delete=$model[$model_name]->func_update.'DeleteModel';
		
		$url_options_delete=add_extra_fancy_url($url_options, array('success_delete' => 1) );

		if($func_delete($model_name, $_GET[ $model[$model_name]->idmodel ]))
		{	
			//die(header('Location: '.$url_options_delete));
			ob_end_clean();
			load_libraries(array('redirect'));
			die( redirect_webtsys( $url_options_delete, $lang['common']['redirect'], $lang['common']['success'], $lang['common']['press_here_redirecting'] , $arr_block) );

		}
		else
		{

			echo 'Error: '.$std_error;

		}
	
	break;

	}

}

function generate_admin_model_ng($model_name, $arr_fields, $arr_fields_edit, $url_options, $options_func='BasicOptionsListModel', $where_sql='', $arr_fields_form=array(), $type_list='Basic')
{
	global $model;

	settype($_GET['op_edit'], 'integer');
	settype($_GET['op_action'], 'integer');
	settype($_GET[$model[$model_name]->idmodel], 'integer');

	$url_admin=add_extra_fancy_url($url_options, array('op_action' => 1));
	
	switch($_GET['op_action'])
	{

		default:

			
			if($_GET['op_edit']==0)
			{

				InsertModelForm($model_name, $url_admin, $url_options, $arr_fields_edit, $id=0, 0);

			}

			ListModel($model_name, $arr_fields, $url_options, $options_func, $where_sql, $arr_fields_edit, $type_list);

		break;

		case 1:
			

			InsertModelForm($model_name, $url_admin, $url_options, $arr_fields_edit, $id=0);

		break;


	}

}

function BasicOptionsListModel($url_options, $model_name, $id)
{

	global $lang, $model;

	?>
	<script language="javascript">
		function warning()
		{
			if(confirm('<?php echo $lang['common']['delete_model']; ?>'))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	</script>
	<?php

	$url_options_edit=add_extra_fancy_url($url_options, array('op_edit' =>1, $model[$model_name]->idmodel => $id));
	$url_options_delete=add_extra_fancy_url($url_options, array('op_edit' =>2, $model[$model_name]->idmodel => $id));

	$arr_options=array('<a href="'.$url_options_edit.'">'.$lang['common']['edit'].'</a>', '<a href="'.$url_options_delete.'" onclick="javascript: if(warning()==false) { return false; }">'.$lang['common']['delete'].'</a>');

	return $arr_options;

}

function BasicList($model_name, $where_sql, $arr_where_sql, $location, $arr_order, $arr_fields, $cell_sizes, $options_func, $url_options, $yes_id=1, $yes_options=1, $extra_fields=array())
{

	global $model, $lang;

	if(!in_array($model[$model_name]->idmodel, $arr_fields))
	{

		array_unshift($arr_fields, $model[$model_name]->idmodel);

	}

	settype($extra_fields[0], 'array');
	settype($extra_fields[1], 'array');

	foreach($arr_fields as $field_label)
	{

		$arr_label_fields[$field_label]=$model[$model_name]->forms[$field_label]->label;
		$cell_sizes[]='';

	}

	foreach($extra_fields[0] as $field_label => $new_field)
	{

		$arr_label_fields[$field_label]=$new_field;

	}

	//Num elements in page

	$num_elements=20;

	$where_sql.=$arr_where_sql.' order by `'.$location.$_GET['order_field'].'` '.$arr_order[$_GET['order_desc']].' limit '.$_GET['begin_page'].', '.$num_elements;

	//Quit id if don't need

	$arr_strip_fields=$arr_fields;

	if($yes_id==1)
	{

		$model[$model_name]->forms[ $model[$model_name]->idmodel ]->label='#Id.';
		$cell_sizes[$model[$model_name]->idmodel]=' width="18" align="center"';

		function remove_idrow($arr_row, $model_idmodel)
		{

			return $arr_row;

		}

	}
	else
	{

		unset($arr_label_fields[$model[$model_name]->idmodel]);

		function remove_idrow($arr_row, $model_idmodel)
		{

			unset($arr_row[$model_idmodel]);	

			return $arr_row;

		}

	}

	if($yes_options==1)
	{

		$arr_label_fields[]=$lang['common']['options'];

		function add_options($arr_row, $arr_row_raw, $options_func, $url_options, $model_name, $model_idmodel)
		{

			$arr_row[]=implode('<br />', $options_func($url_options, $model_name, $model_idmodel, $arr_row_raw) );

			return $arr_row;

		}

	}
	else
	{


		function add_options($arr_row, $url_options, $model_name, $model_idmodel)
		{

			return $arr_row;

		}

	}
	
	//View table...
	
	$total_elements=$model[$model_name]->select_count($arr_where_sql, $model[$model_name]->idmodel);
	
	up_table_config($arr_label_fields, $cell_sizes);

	$query=$model[$model_name]->select($where_sql, $arr_fields);

	while($arr_row=webtsys_fetch_array($query))
	{

		//Process function...
		
		$arr_row_raw=$arr_row;

		foreach($arr_row as $key_row => $value_row)
		{
			
			/*if(isset($model[$model_name]->components[$key_row]))
			{*/
			
			$arr_row[$key_row]=$model[$model_name]->components[$key_row]->show_formatted($value_row);

		}
		
		foreach($extra_fields[1] as $new_field)
		{
			
			$arr_row[]=$new_field($url_options, $model_name, $arr_row[$model[$model_name]->idmodel]);

		}
		
		$arr_row=add_options($arr_row, $arr_row_raw, $options_func, $url_options, $model_name, $arr_row[$model[$model_name]->idmodel]);

		$arr_row=remove_idrow($arr_row, $model[$model_name]->idmodel);

		middle_table_config($arr_row, $cell_sizes);

	}

	down_table_config();

	//Pagination...
	//$total_elements=50;
	
	//Add to url_options url_data for options

	$url_options_link=add_extra_fancy_url($url_options, array('order_field' => $_GET['order_field'], 'order_desc' => $_GET['order_desc'], 'search_word' => $_GET['search_word'], 'search_field' => $_GET['search_field']));

	$pages=pages( $_GET['begin_page'], $total_elements, $num_elements, $url_options_link);

	pages_table($pages);

}

function SearchInField($model_name, $arr_fields, $where_sql, $url_options, $yes_id=1)
{

	global $lang, $model;

	load_libraries(array('search_in_field'));

	if(count($model[$model_name]->forms)==0)
	{

		$arr_error_sql[0]='Do you need create a form for this model';    
		$arr_error_sql[1]='Do you need create a form for this model '.$model_name.' for use SearchInField function';
		ob_end_clean();
		echo load_view(array('title' => 'Phango site is down', 'content' => '<p>'.$arr_error_sql[DEBUG].'</p>'), 'common/common');
		die();

	}

	if(!in_array($model[$model_name]->idmodel, $arr_fields) && $yes_id==1)
	{

		array_unshift($arr_fields, $model[$model_name]->idmodel);
		
		$model[$model_name]->forms[ $model[$model_name]->idmodel ]->label='#Id.';

	}

	//Set order

	$_GET['order_field']=@form_text($_GET['order_field']);
	$_GET['search_word']=@form_text($_GET['search_word']);
	$_GET['search_field']=@form_text($_GET['search_field']);
	
	$arr_order_select=array();

	if( !in_array($_GET['order_field'], $arr_fields) )
	{

		$_GET['order_field']=$arr_fields[0]; //$model[$model_name]->idmodel;

	}

	if( !in_array($_GET['search_field'], $arr_fields) )
	{

		$_GET['search_field']=$arr_fields[0]; //$model[$model_name]->idmodel;

	}
	
	//0=DESC
	//1=ASC

	settype($_GET['order_desc'], 'integer');
		
	$arr_order[$_GET['order_desc']]='ASC';

	$arr_order[0]='ASC';
	$arr_order[1]='DESC';

	$arr_order_select[]=$_GET['order_desc'];
	$arr_order_select[]=$lang['common']['ascent'];
	$arr_order_select[]=0;
	$arr_order_select[]=$lang['common']['descent'];
	$arr_order_select[]=1;

	$arr_order_field=array($_GET['order_field']);

	foreach($arr_fields as $field_label)
	{

		$arr_order_field[]=$model[$model_name]->forms[$field_label]->label;
		$arr_order_field[]=$field_label;

	}

	$form_search='<form method="get" action="'.add_extra_fancy_url( $url_options, array() ).'">';
	$form_search.=set_csrf_key();
	$form_search.=$lang['common']['order_by'].': '.SelectForm('order_field', '', $arr_order_field).' '.$lang['common']['in_order'].': '.SelectForm('order_desc', '', $arr_order_select);

	$arr_order_field[0]=$_GET['search_field'];

	$form_search.='<p>'.$lang['common']['search'].': '.TextForm('search_word', '', $_GET['search_word']).' '.$lang['common']['search_by'].': '.SelectForm('search_field', '', $arr_order_field).'</p><p><input type="submit" value="'.$lang['common']['send'].'"/> <input type="button" value="'.$lang['common']['reset'].'" onclick="javascript:location.href=\''.$url_options.'\'"/>';

	$form_search.='</form></p>';
	
	echo load_view(array($lang['common']['order_and_search'], $form_search), 'content');

	//Query for order

	//Query for search_by
	
	list($location, $arr_where_sql)=search_in_field($model_name, array($_GET['search_field']), $_GET['search_word']);
	
	if($location!='')
	{

		$location=$location.' DESC ,';

	}

	if($where_sql=='' && $arr_where_sql!='')
	{
		
		$where_sql='where ';

	}
	else if($where_sql!='' && $arr_where_sql!='')
	{

		$where_sql.=' AND ';

	}
	
	return array($where_sql, $arr_where_sql, $location, $arr_order);

}

function GeneratePositionModel($model_name, $field_name, $field_position, $url, $where='')
{
	global $base_path, $arr_block, $lang, $model;

	settype($_GET['action_field'], 'integer');

	$num_order=$model[$model_name]->select_count($where, $model[$model_name]->idmodel );

	if($num_order>0)
	{
	
		switch($_GET['action_field'])
		{
		default:

			ob_start();

			$url_post=add_extra_fancy_url($url, array('action_field' => 1));

			echo '<form method="post" action="'.$url_post.'">';
			set_csrf_key();
			echo '<div class="form">';

			$query=$model[$model_name]->select($where.' order by `'.$field_position.'` ASC', array($model[$model_name]->idmodel, $field_name, $field_position));

			while(list($id, $name, $position)=webtsys_fetch_row($query))
			{
				$name=$model[$model_name]->components[$field_name]->show_formatted($name);

				echo '<p><label for="'.$field_position.'">'.$name.'</label><input type="text" name="position['.$id.']" value="'.$position.'" size="3"/></p>';

			}
			echo '<input type="submit" value="'.$lang['common']['send'].'"/>';
			echo '</div>';
			echo '</form>';
			
			$cont_order=ob_get_contents();

			ob_end_clean();

			echo load_view(array($lang['common']['order'], $cont_order), 'content');

		break;

		case 1:

			$arr_position=$_POST['position'];

			foreach($arr_position as $key => $value)
			{
				
				settype($key, 'integer');
				settype($value, 'integer');
				
				$where='where '.$model[$model_name]->idmodel.'='.$key;

				//Clean required...

				$model[$model_name]->reset_require();
				
				$query=$model[$model_name]->update(array($field_position => $value), $where);
				
			}
			
			ob_end_clean();

			load_libraries(array('redirect'));

			die( redirect_webtsys( $url, $lang['common']['redirect'], $lang['common']['success'], $lang['common']['press_here_redirecting'] , $arr_block) );

		break;

		}

	}
	else
	{

		echo '<p>'.$lang['common']['no_exists_elements_to_order'].'</p>';

	}

}

?>