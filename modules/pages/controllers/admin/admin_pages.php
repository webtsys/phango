<?php

function PagesAdmin()
{

	global $base_url, $base_path, $model, $lang, $header, $module_admin, $arr_cache_jscript;

	settype($_GET['op'], 'integer');
	settype($_GET['IdPage'], 'integer');

	load_libraries(array('generate_admin_ng', 'forms/textareabb', 'admin/generate_admin_class'));
	load_model('pages');
	load_lang('pages');
	
	//$header='<script language="Javascript" src="'.make_fancy_url($base_url, 'jscript', 'load_jscript', 'script', array('input_script' => 'jquery.min.js')).'"></script>';
	
	$arr_cache_jscript[]='jquery.min.js';

	switch($_GET['op'])
	{

		default:

			$model['page']->create_form();

			$model['page']->label=$lang['pages']['pages'];
			
			$model['page']->forms['name']->label=$lang['common']['title'];
			$model['page']->forms['text']->label=$lang['common']['text'];
		
			
			$model['page']->forms['text']->parameters=array('text', $class='', $arr_values=array(), $type_form='TextAreaBBForm');
			$arr_fields=array('name');
			$arr_fields_edit=array('name', 'text');
			$url_options=set_admin_link( 'admin_pages', array('IdModule' => $_GET['IdModule']));

			//generate_admin_model_ng('page', $arr_fields, $arr_fields_edit, $url_options, $options_func='PagesOptions', $where_sql='', $arr_fields_form=array(), $type_list='Basic');

			$admin=new GenerateAdminClass('page');
			
			$admin->url_options=$url_options;
			$admin->arr_fields=$arr_fields;
			$admin->arr_fields_edit=$arr_fields_edit;
			$admin->options_func='PagesOptions';
			
			$admin->show();
			
		break;

		case 1:

			$model['property_page']->create_form();

			$model['property_page']->forms['name']->label=$lang['common']['name'];
			$model['property_page']->forms['property']->label=$lang['pages']['property'];
			$model['property_page']->forms['order_page']->label=$lang['pages']['order_page'];

			$arr_prop=array('', $lang['pages']['no_property'], '');
			$arr_check_prop=array();
		
			if ($dh = opendir($base_path.'modules/pages/property/php/')) 
			{
				while ($file = readdir($dh))
				{
				
					if($file!='.' && $file!='..')
					{

						$arr_prop[]=ucfirst( str_replace('.php', '', $file) );
						$arr_prop[]='pages|'.$file;
						$arr_check_prop[]='pages|'.$file;
				
					}
			
				}
			
				closedir($dh);
			}
			
			foreach($module_admin as $path_module)
			{
				
				$handle=@opendir($base_path.'modules/'.$path_module.'/pages/property/php/');
				
				if($handle!==false)
				{	
					
					while ($file = readdir($handle))
					{
						if($file!="." && $file!="..")
						{

							//$file_base="modules/".$path_module."/blocks/html/".$file;
							$arr_prop[]=ucfirst( str_replace('.php', '', $file) );
							$arr_prop[]=$path_module.'/pages|'.$file;
							$arr_check_prop[]=$path_module.'/pages|'.$file;
				
						}
					}

					closedir($handle);

				}

				
			}

			$arr_fields=array('name', 'order_page');
			$arr_fields_edit=array('name', 'property', 'idpage');
			$url_options=set_admin_link( 'admin_pages', array('IdModule' => $_GET['IdModule'], 'op' => 1, 'IdPage' => $_GET['IdPage']));

			$model['property_page']->forms['property']->SetParameters($arr_prop);

			$model['property_page']->forms['idpage']->form='HiddenForm';

			$model['property_page']->forms['idpage']->SetForm($_GET['IdPage']);
			
			$model['property_page']->components['property']->arr_values=$arr_check_prop;
			
			generate_admin_model_ng('property_page', $arr_fields, $arr_fields_edit, $url_options, $options_func='PagesOptionsProp', $where_sql='', $arr_fields_form=array(), $type_list='Basic');

			$url_change_order_prop=set_admin_link( 'admin_pages', array('IdModule' => $_GET['IdModule'], 'op' => 3, 'IdPage' => $_GET['IdPage']));

			echo '<p><a href="'.$url_change_order_prop.'">'.$lang['pages']['change_order_prop'].'</a>';

			echo '<p><a href="'.set_admin_link( 'admin_pages', array('IdModule' => $_GET['IdModule']) ).'">'.$lang['pages']['go_back_index_pages'].'</a>';

		break;

		case 2:

			$model['property_page']->components['name']->required=0;

			$model['property_page']->components['idpage']->required=0;

			$model['property_page']->components['property']->required=0;

			settype($_GET['IdProperty_page'], 'integer');
			
			$query=$model['property_page']->select('where IdProperty_page='.$_GET['IdProperty_page'], array('idpage', 'property'));
			
			list($idpage, $property)=webtsys_fetch_row($query);

			$arr_property=explode('|', $property);

			$property_path=$arr_property[0];
			$property=$arr_property[1];

			if(!include($base_path.'modules/'.$property_path.'/property/admin/'.$property))
			{

				$output_error=ob_get_contents();

				ob_end_clean();

				echo load_view(array($lang['pages_admin']['pages'],  '<p>'.$lang['pages']['property_no_editable'].'</p><p><a href="javascript:history.back();">'.$lang['common']['go_back'].'</a>'), 'content');

			}
			else
			{

				$url_back=set_admin_link( 'admin_pages', array('IdModule' => $_GET['IdModule'], 'op' => 1, 'IdPage' => $_GET['IdPage']));

				echo '<p><a href="'.$url_back.'">'.$lang['common']['go_back'].'</a>';

			}

		break;

		case 3:

			//Order property

			$url_back=set_admin_link( 'admin_pages', array('IdModule' => $_GET['IdModule'], 'op' => 1, 'IdPage' => $_GET['IdPage']));
			
			$url_post=set_admin_link( 'admin_pages', array('IdModule' => $_GET['IdModule'], 'op' => 3, 'IdPage' => $_GET['IdPage']));

			GeneratePositionModel('property_page', 'name', 'order_page', $url_post, $where='where idpage='.$_GET['IdPage']);

			echo '<p><a href="'.$url_back.'">'.$lang['common']['go_back'].'</a>';

		break;

	}

}

function PagesOptions($url_options, $model_name, $id, $arr_row)
{

	global $lang, $base_url, $model;
	
	//, 'IdPage' => $arr_row['idpage']

	$url_options_final=add_extra_fancy_url($url_options, array('op' => 1, 'IdPage' => $id));

	$arr_options=BasicOptionsListModel($url_options, $model_name, $id);

	$title=$model['page']->components['name']->show_formatted($arr_row['name']);
	
	$arr_options[]='<a href="'.$url_options_final.'">'.$lang['pages']['page_properties'].'</a>';
	$arr_options[]='<a target="_blank" href="'.make_fancy_url($base_url, 'pages', 'index', $title,  array('IdPage' => $id) ).'">'.$lang['pages']['preview'].'</a>';

	return $arr_options;

}

function PagesOptionsProp($url_options, $model_name, $id, $arr_row)
{

	global $lang;
	
	//, 'IdPage' => $arr_row['idpage']

	$url_options_final=add_extra_fancy_url($url_options, array('op' => 2, 'IdProperty_page' => $id));

	$arr_options=BasicOptionsListModel($url_options, $model_name, $id);
	
	$arr_options[]='<a href="'.$url_options_final.'">'.$lang['pages']['page_properties_admin'].'</a>';

	return $arr_options;

}

?>
