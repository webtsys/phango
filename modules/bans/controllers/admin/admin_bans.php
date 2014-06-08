<?php

function BansAdmin()
{
	global $base_url, $base_path, $model, $lang;

	load_libraries(array('generate_admin_ng', 'admin/generate_admin_class', 'forms/selectmodelform'));

	load_model('bans');

	$arr_fields=array('description', 'iduser', 'ip');
	$arr_fields_edit=array('iduser', 'description', 'ip', 'message', 'time_ban', 'modules_ban');
	$url_options=set_admin_link('bans', array());

	$model['ban']->components['iduser']->fields_related_model=array();
	$model['ban']->components['iduser']->name_field_to_field='private_nick';

	$model['ban']->create_form();

	$model['ban']->forms['iduser']->form='SelectModelForm';
	$model['ban']->forms['iduser']->parameters=array('iduser', '', 0, 'user', 'private_nick', 'where IdUser>0');

	$model['ban']->forms['iduser']->label=$lang['common']['user'];
	$model['ban']->forms['description']->label=$lang['common']['description'];
	$model['ban']->forms['ip']->label=$lang['common']['ip'];
	$model['ban']->forms['message']->label=$lang['common']['message'];
	$model['ban']->forms['time_ban']->label=$lang['bans']['time_ban'];
	$model['ban']->forms['time_ban']->SetForm(0);
	$model['ban']->forms['modules_ban']->label=$lang['bans']['modules_ban'];

	$arr_modules=array(array(0), $lang['bans']['all_modules'], 0);

	$query=$model['module']->select('order by order_module ASC', array('IdModule', 'name') );

	while(list($idmodule, $module_name)=webtsys_fetch_row($query))
	{

		if(file_exists($base_path.'modules/'.$module_name.'/controllers/controller_index.php'))
		{

			$arr_modules[]=$module_name;
			$arr_modules[]=$idmodule;

		}
		
	}

	$model['ban']->forms['modules_ban']->SetParameters($arr_modules);

	echo '<h3>'.$lang['bans']['edit_bans'].'</h3>';

	generate_admin_model_ng('ban', $arr_fields, $arr_fields_edit, $url_options, $options_func='BasicOptionsListModel', $where_sql='', $arr_fields_form=array(), $type_list='Basic');
	/*
	$admin=new GenerateAdminClass('ban');
	
	$admin->arr_fields=$arr_fields;
	$admin->arr_fields_edit=$arr_fields_edit;
	$admin->url_options=$url_options;
	
	$admin->show();*/
	
}

?>