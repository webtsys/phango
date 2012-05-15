<?php

function Profiles()
{

	ob_start();

	//function ShowListModel($model_name, $arr_fields, $url_options, $where_sql='', $yes_id=0, $yes_options=0, $func_options='')

	global $user_data, $model, $ip, $lang, $config_data, $base_path, $base_url, $cookie_path, $arr_block, $prefix_key, $block_title, $block_content, $block_urls, $block_type, $block_id, $config_data, $script_base_controller;

	$arr_block=select_view(array('users'));
	
	load_libraries(array('form_date'));
	load_lang('user');

	load_lang('user');
	load_libraries(array('pages', 'form_date', 'showlistmodel'));

	echo load_view(array( $lang['user']['profiles_list'], $lang['user']['profiles_list_explain']), 'content');
	
	ShowListModel('user', array('private_nick', 'website', 'hidden_status', 'date_register'), make_fancy_url($base_url, 'user', 'profiles', 'profiles_list', array()), $where_sql='where IdUser>0', 0, 1, 'ShowUserOptions');

	$cont_index=ob_get_contents();

	ob_end_clean();

	echo load_view( array($lang['user']['profiles_list'], $cont_index, $block_title, $block_content, $block_urls, $block_type, $block_id, $config_data, ''), $arr_block);

}

function ShowUserOptions($url_options, $model_name, $id)
{
	global $base_url, $lang;

	$arr_options[]='<a href="'.make_fancy_url($base_url, 'user', 'profile', 'viewprofile', array('IdUser' => $id)).'">'.$lang['user']['see_profile'].'</a>';

	return $arr_options;

}

?>