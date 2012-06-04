<?php

function TemplatesAdmin()
{

	global $base_url, $base_path, $model, $lang, $header;

	$header='<script language="Javascript" src="'.make_fancy_url($base_url, 'jscript', 'load_jscript', 'script', array('input_script' => 'jquery.min.js')).'"></script>';

	settype($_GET['op'], 'integer');

	settype($_GET['IdTemplate'], 'integer');

	load_libraries(array('generate_admin_ng', 'forms/textareabb'));
	load_model('templates');
	load_lang('templates');

	switch($_GET['op'])
	{

		default:

			$arr_fields=array('name');
			$arr_fields_edit=array();

			$url_options=make_fancy_url($base_url, 'admin', 'index', 'admin_templates', array('IdModule' => $_GET['IdModule']) );

			$arr_prop=array('', $lang['templates']['no_template'], '');
			$arr_choice_prop=array();
	
			if ($dh = opendir($base_path.'modules/templates/templates/')) 
			{
				while ($file = readdir($dh))
				{
				
					if($file!='.' && $file!='..')
					{
			
						$arr_prop[]=ucfirst( str_replace('.php', '', $file) );
						$arr_prop[]=$file;
						$arr_choice_prop[]=$file;
				
					}
			
				}
			
				closedir($dh);
			}

			$model['template']->create_form();

			$model['template']->components['name_template']->arr_values=$arr_choice_prop;
			$model['template']->forms['name_template']->SetParameters($arr_prop);
			
			$model['template']->forms['name']->label=$lang['common']['name'];
			$model['template']->forms['name_template']->label=$lang['templates']['name_template'];
			
			generate_admin_model_ng('template', $arr_fields, $arr_fields_edit, $url_options, $options_func='TemplateOptionsListModel', $where_sql='', $arr_fields_form=array(), $type_list='Basic');

		break;

		case 1:

			$arr_fields=array('name', 'position');
			$arr_fields_edit=array('name', 'subtitle', 'text', 'idtemplate');

			$url_options=make_fancy_url($base_url, 'admin', 'index', 'admin_templates', array('IdModule' => $_GET['IdModule'], 'op' => '1', 'IdTemplate' => $_GET['IdTemplate']) );

			$model['template_content']->create_form();

			$model['template_content']->forms['idtemplate']->SetForm($_GET['IdTemplate']);

			$model['template_content']->forms['name']->label=$lang['common']['name'];
			$model['template_content']->forms['subtitle']->label=$lang['templates']['subtitle'];
			$model['template_content']->forms['text']->label=$lang['common']['text'];
			
			$model['template_content']->forms['text']->parameters=array('text', '', '', 'TextAreaBBForm');

			generate_admin_model_ng('template_content', $arr_fields, $arr_fields_edit, $url_options, $options_func='BasicOptionsListModel', $where_sql='where idtemplate='.$_GET['IdTemplate'], $arr_fields_form=array(), $type_list='Basic');

			echo '<p><a href="'.make_fancy_url($base_url, 'admin', 'index', 'admin_templates', array('IdModule' => $_GET['IdModule'], 'op' => '2', 'IdTemplate' => $_GET['IdTemplate']) ).'">'.$lang['templates']['go_to_order_template_content'].'</a></p>';

			echo '<p><a href="'.make_fancy_url($base_url, 'admin', 'index', 'admin_templates', array('IdModule' => $_GET['IdModule']) ).'">'.$lang['common']['go_back'].'</a></p>';

		break;

		case 2:

			$query=$model['template']->select('where IdTemplate='.$_GET['IdTemplate'], array('name'));

			list($name_template)=webtsys_fetch_row($query);

			$url_options=make_fancy_url($base_url, 'admin', 'index', 'admin_templates', array('IdModule' => $_GET['IdModule'], 'op' => '2', 'IdTemplate' => $_GET['IdTemplate']) );

			echo '<h3>'.$lang['templates']['order_content_template_for'].' '.I18nField::show_formatted($name_template).'</h3>';

			GeneratePositionModel('template_content', 'name', 'position', $url_options, $where_sql='where idtemplate='.$_GET['IdTemplate']);

			echo '<p><a href="'.make_fancy_url($base_url, 'admin', 'index', 'admin_templates', array('IdModule' => $_GET['IdModule'], 'op' => '1', 'IdTemplate' => $_GET['IdTemplate']) ).'">'.$lang['common']['go_back'].'</a></p>';

		break;

	}

}

function TemplateOptionsListModel($url_options, $model_name, $id, $row_template)
{

	global $lang, $base_url;

	$arr_options=BasicOptionsListModel($url_options, $model_name, $id);

	$arr_options[]='<a href="'.make_fancy_url($base_url, 'admin', 'index', 'admin_templates', array('IdModule' => $_GET['IdModule'], 'op' => '1', 'IdTemplate' => $id) ).'">'.$lang['templates']['add_content'].'</a>';

	$arr_options[]='<a target="_blank" href="'.make_fancy_url($base_url, 'templates', 'index', I18nField::show_formatted($row_template['name']), array('IdTemplate' => $id) ).'">'.$lang['templates']['view_template'].'</a>';

	return $arr_options;

}

?>
