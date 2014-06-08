<?php

function ContactAdmin()
{

	global $base_url, $base_path, $model, $lang, $header;

	$header='<script language="Javascript" src="'.make_fancy_url($base_url, 'jscript', 'load_jscript', 'script', array('input_script' => 'jquery.min.js')).'"></script>';

	load_libraries(array('generate_admin_ng', 'forms/textareabb'));
	load_model('contact');

	settype($_GET['op'], 'integer');
	settype($_GET['IdContact'], 'integer');

	load_lang('contact');

	$url_options_default=set_admin_link( 'contact', array('IdModule' => $_GET['IdModule']));

	switch($_GET['op'])
	{

		default:

		$model['contact']->create_form();

		$model['contact']->forms['name']->label=$lang['common']['name'];
		$model['contact']->forms['email']->label=$lang['common']['email'];
		$model['contact']->forms['description']->label=$lang['common']['description'];
		
		$model['contact']->forms['description']->parameters=array('description', '', '', 'TextAreaBBForm');

		$arr_fields=array('name');
		$arr_fields_edit=array('name', 'email', 'description', 'template');

		generate_admin_model_ng('contact', $arr_fields, $arr_fields_edit, $url_options_default, $options_func='ContactOptionsListModel', $where_sql='', $arr_fields_form=array(), $type_list='Basic');

		break;

		case 1:

		echo '<h3>'.$lang['contact']['add_contact_fields'].'</h3>';

		$arr_fields=array('name', 'order');
		$arr_fields_edit=array('name', 'type', 'idcontact', 'required');

		$model['contact_field']->create_form();

		$model['contact_field']->forms['idcontact']->SetForm($_GET['IdContact']);

		$model['contact_field']->forms['name']->label=$lang['common']['name'];
		$model['contact_field']->forms['type']->label=$lang['contact']['type'];
		$model['contact_field']->forms['required']->label=$lang['common']['required'];
		$model['contact_field']->forms['order']->label=$lang['common']['order'];

		$url_options=set_admin_link( 'contact', array('IdModule' => $_GET['IdModule'], 'op' => 1, 'IdContact' => $_GET['IdContact']));

		generate_admin_model_ng('contact_field', $arr_fields, $arr_fields_edit, $url_options, $options_func='BasicOptionsListModel', $where_sql='where idcontact='.$_GET['IdContact'], $arr_fields_form=array(), $type_list='Basic');

		$url_options_order=set_admin_link( 'contact', array('IdModule' => $_GET['IdModule'], 'op' => 2, 'IdContact' => $_GET['IdContact']));

		echo '<p><a href="'.$url_options_order.'">'.$lang['contact']['order_fields'].'</a>';

		echo '<p><a href="'.$url_options_default.'">'.$lang['contact']['go_back_index_contact'].'</a>';

		break;

		case 2:

		echo '<h3>'.$lang['contact']['order_fields'].'</h3>';

		$url=set_admin_link( 'contact', array('IdModule' => $_GET['IdModule'], 'op' => 2, 'IdContact' => $_GET['IdContact']));
		$url_options_fields=set_admin_link( 'contact', array('IdModule' => $_GET['IdModule'], 'op' => 1, 'IdContact' => $_GET['IdContact']));

		GeneratePositionModel('contact_field', 'name', 'order', $url, $where='where idcontact='.$_GET['IdContact']);

		echo '<p><a href="'.$url_options_fields.'">'.$lang['common']['go_back'].'</a>';

		break;

	}

}

function ContactOptionsListModel($url_options, $model_name, $id, $row)
{

	global $lang, $base_url, $model;

	$arr_options=BasicOptionsListModel($url_options, $model_name, $id);

	$arr_options[]='<a href="'.set_admin_link( 'contact', array('IdModule' => $_GET['IdModule'], 'op' => '1', 'IdContact' => $id) ).'">'.$lang['contact']['add_contact_fields'].'</a>';

	$arr_options[]='<a target="_blank" href="'.make_fancy_url($base_url, 'contact', 'index', $model['contact']->components['name']->show_formatted($row['name']), array('IdContact' => $id) ).'">'.$lang['contact']['preview_contact'].'</a>';

	return $arr_options;

}

?>