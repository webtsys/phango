<?php

function BannersAdmin()
{

	global $base_url, $base_path, $model, $lang;
	
	load_libraries(array('generate_admin_ng'));
	load_model('banners');
	load_lang('banners');
	
	$arr_fields=array('title');
	$arr_fields_edit=array('title', 'content', 'position_banner');
	$url_options=make_fancy_url($base_url, 'admin', 'index', 'admin_banners', array('IdModule' => $_GET['IdModule']));
	
	$model['banners']->create_form();
	
	$model['banners']->forms['title']->label=$lang['common']['title'];
	$model['banners']->forms['content']->label=$lang['common']['text'];
	$model['banners']->forms['position_banner']->label=$lang['banners']['position_banner'];
	
	$arr_post=array(0);
	$arr_val_banner=array();

	$arr_post[]=$lang['banners']['banner_position_top_all_pages'];
	$arr_post[]=0;
	$arr_val_banner[]=0;

	$arr_post[]=$lang['banners']['banner_blocks'];
	$arr_post[]='optgroup';

	$query=$model['blocks']->select('where url_block like "%banners.php%"', array('IdBlocks', 'title_block'));

	while(list($idblock, $title_block)=webtsys_fetch_row($query))
	{

	$arr_post[]=I18nField::show_formatted($title_block);
	$arr_post[]='blocks'.$idblock;
	$arr_val_banner[]='blocks'.$idblock;

	}

	$arr_post[]='';
	$arr_post[]='end_optgroup';

	/*$dir=opendir($base_path.'modules/banners/addons');

	while($file=readdir($dir))
	{
		if($file!='.' && $file!='..' && $file !='.svn')
		{
		
			include($base_path.'modules/banners/addons/'.$file);
			
		}
		

	}*/
	
	$model['banners']->forms['position_banner']->SetParameters($arr_post);
	
	$model['banners']->components['position_banner']->arr_values=$arr_val_banner;

	generate_admin_model_ng('banners', $arr_fields, $arr_fields_edit, $url_options, $options_func='BasicOptionsListModel', $where_sql='', $arr_fields_form=array(), $type_list='Basic');

}

?>