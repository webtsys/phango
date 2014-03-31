<?php

function ModulesAdmin()
{

	global $model, $base_url, $base_path, $lang, $cache_model, $arr_module_insert, $arr_padmin_mod, $arr_module_remove;
	
	settype($_GET['op'], 'integer');

	load_lang('modules');

	load_libraries(array('generate_admin_ng'));

	switch($_GET['op'])
	{

		default:

		echo '<h3>'.$lang['modules']['modules_enabled'].'</h3>';

		$model['module']->create_form();
		$model['module']->forms['name']->label=$lang['common']['name'];
		$model['module']->forms['required']->label=$lang['common']['required'];

		$arr_fields=array('name', 'required');
		$arr_fields_edit=array();
		$url_options=make_fancy_url($base_url, 'admin', 'index', 'edit_modules', $arr_data=array());

		//generate_admin_model_ng('module', $arr_fields, $arr_fields_edit, $url_options, $options_func='OptionsModulesModel', $where_sql='', $arr_fields_form=array(), $type_list='Basic');

		ListModel('module', $arr_fields, $url_options, $options_func='OptionsModulesModel', $where_sql='', $arr_fields_edit, $type_list='Basic');

		echo '<h3>'.$lang['modules']['modules_disabled'].'</h3>';

		up_table_config(array($lang['modules']['module'], $lang['common']['options']));

		$path_modules=$base_path.'modules/';

		if ($dh = opendir($path_modules)) 
		{
			while ($file = readdir($dh))
			{

				if( is_dir($path_modules.$file) && !preg_match('/^\./', $file) )
				{
					
					$models_dir=$path_modules.$file.'/models/';

					//echo $models_dir.'<p>';

					if(is_dir($models_dir))
					{
						
						if ($dh_models = opendir($models_dir)) 
						{

							while ($file_model = readdir($dh_models))
							{

								if(is_file($models_dir.$file_model) && !preg_match('/^\./', $file_model) && preg_match('/\.php$/', $file_model))
								{
	
									$my_model=preg_replace( '/^models_([aA-zZ]+)\.php/' , '$1', $file_model);
									
									include_once($models_dir.$file_model);
									
								}

							}

						}

					}

				}

				
		
			}
		
			closedir($dh);
		}
		
		$arr_mask_module=array();
		$arr_final_module=array();
		$arr_final_module_options=array();

		$query=$model['module']->select( '', array('name') );

		while(list($module_mask)=webtsys_fetch_row($query))
		{

			$arr_mask_module[$module_mask]=1;

		}

		foreach($arr_module_insert as $module => $arr_values_module)
		{

			if(!isset($arr_mask_module[$module]))
			{

				$arr_final_module=array();

				$url_edit_mod=$url_options=make_fancy_url($base_url, 'admin', 'index', 'edit_modules', $arr_data=array('IdModule' => $_GET['IdModule'], 'op' => 1, 'module' => $module));

				$arr_final_module[]=$module;
				$arr_final_module[]='<a href="'.$url_edit_mod.'">'.$lang['modules']['enable_module'].'</a>';

				middle_table_config($arr_final_module, array(' width="75%"', ''));

			}

		}


		down_table_config();

		break;

		case 1:

			load_libraries(array('update_table'));

			echo '<h3>'.$lang['modules']['adding_module'].'</h3>';

			$arr_padmin_mod=array();

			$module=@form_text($_GET['module']);

			/*$path_modules=$base_path.'modules/'.$module.'/models/';

			if ($dh = opendir($path_modules)) 
			{
				while ($file = readdir($dh))
				{

					if( is_file($path_modules.$file) && !preg_match('/^\./', $file) && preg_match('/\.php$/', $file) )
					{
						$my_model=preg_replace( '/^models_([aA-zZ]+)\.php/' , '$1', $file);

						$arr_padmin_mod[$module]=str_replace('.php', '', $my_model);
	
						include($base_path.'modules/'.$module.'/models/models_'.$my_model.'.php');


					}

				}

			}

			update_table($model);

			add_module($arr_padmin_mod);*/

			update_models_from_module(array($module));

			echo '<p>'.$lang['modules']['installed_model_if_not_check'].'</p>';

			echo '<p><a href="'.make_fancy_url( $base_url, 'admin', 'index', 'edit_modules', $arr_data=array('IdModule' => $_GET['IdModule']) ).'">'.$lang['common']['go_back'].'</a></p>';

		break;

		case 2:

			echo '<h3>'.$lang['modules']['deleting_module'].'</h3>';

			$module=@form_text($_GET['module']);

			$path_modules=$base_path.'modules/'.$module.'/models/';

			if ($dh = opendir($path_modules)) 
			{
				while ($file = readdir($dh))
				{

					if( is_file($path_modules.$file) && !preg_match('/^\./', $file) && preg_match('/\.php$/', $file) )
					{
					
						//Obtain model
	
						$my_model=preg_replace( '/^models_([aA-zZ]+)\.php/' , '$1', $file);
	
						//Load model with an include.

						include($base_path.'modules/'.$module.'/models/models_'.$my_model.'.php');

						if(isset($arr_module_remove[$my_model]))
						{
;
							$query=webtsys_query( 'DROP TABLE '.implode(', ', $arr_module_remove[$my_model]) );

							$model['module']->delete('where name="'.$my_model.'"');

							echo '<p>'.$lang['modules']['module_deleted_if_error_check'].'</p>';

						}
						else
						{

							echo '<p>'.$lang['modules']['error_not_set_arr_module_remove'].'</p>';

						}

					}

				}

			}
			else
			{

				echo '<p>'.$lang['modules']['module_no_exists'].'</p>';

			}


			echo '<p><a href="'.make_fancy_url( $base_url, 'admin', 'index', 'edit_modules', $arr_data=array('IdModule' => $_GET['IdModule']) ).'">'.$lang['common']['go_back'].'</a></p>';

		break;
		
		case 3:			
		
			load_libraries(array('forms/selectmodelform'));
			
			settype($_GET['idmodule'], 'integer');
			
			$query=$model['module']->select('where IdModule='.$_GET['idmodule'], array('IdModule', 'name'));
			
			list($idmodule, $name_module)=webtsys_fetch_row($query);
			
			settype($idmodule, 'integer');
			
			if($idmodule>0)
			{
			
				$name_module=$lang[$name_module.'_admin'][$name_module.'_admin_name'];
				
				echo '<h3>'.$lang['modules']['add_moderator_to_module'].' -  '.$name_module.'</h3>';
				$arr_fields=array('moderator');
				$arr_fields_edit=array();
				
				$url_options=make_fancy_url( $base_url, 'admin', 'index', 'edit_modules', $arr_data=array('IdModule' => $_GET['IdModule'], 'op' => 3, 'idmodule' => $_GET['idmodule']) );
				
				$model['moderators_module']->create_form();
				
				$model['moderators_module']->forms['idmodule']->form='HiddenForm';
				$model['moderators_module']->forms['idmodule']->SetForm($_GET['idmodule']);
				
				$model['moderators_module']->forms['moderator']->form='SelectModelForm';
				$model['moderators_module']->forms['moderator']->label=$lang['common']['moderator'];
				
				$model['moderators_module']->forms['moderator']->parameters=array('moderator', '', 0, 'user', 'private_nick', $where='where privileges_user=1');
			
				generate_admin_model_ng('moderators_module', $arr_fields, $arr_fields_edit, $url_options, $options_func='BasicOptionsListModel', $where_sql='where idmodule='.$_GET['idmodule'], $arr_fields_form=array(), $type_list='Basic');
				
				echo '<p><a href="'.make_fancy_url( $base_url, 'admin', 'index', 'edit_modules', $arr_data=array('IdModule' => $_GET['IdModule']) ).'">'.$lang['modules']['go_back_home'].'</a></p>';
				
			}
		
		break;

	}

}

function OptionsModulesModel($url_options, $model_name, $id, $arr_row)
{

	global $lang, $base_url;

	$arr_options=array();

	if($arr_row['required']==0)
	{

		$arr_options[]='<a href="'.make_fancy_url($base_url, 'admin', 'index', 'edit_modules', $arr_data=array('IdModule' => $_GET['IdModule'], 'op' => 3, 'module' => $arr_row['name'], 'idmodule' => $id)).'">'.$lang['modules']['add_moderator_to_module'].'</a>';
		$arr_options[]='<a href="'.make_fancy_url($base_url, 'admin', 'index', 'edit_modules', $arr_data=array('IdModule' => $_GET['IdModule'], 'op' => 2, 'module' => $arr_row['name'])).'">'.$lang['modules']['disable_module'].'</a>';

	}
	else
	{

		//$arr_options[]=$lang['modules']['no_options_module'];
		$arr_options[]='<a href="'.make_fancy_url($base_url, 'admin', 'index', 'edit_modules', $arr_data=array('IdModule' => $_GET['IdModule'], 'op' => 3, 'module' => $arr_row['name'], 'idmodule' => $id)).'">'.$lang['modules']['add_moderator_to_module'].'</a>';

	}

	return $arr_options;

}

?>