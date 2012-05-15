<?php

function Change_lang()
{

	ob_start();

	global $user_data, $model, $ip, $lang, $config_data, $base_path, $base_url, $cookie_path, $arr_block, $prefix_key, $block_title, $block_content, $block_urls, $block_type, $block_id, $config_data, $arr_i18n, $webtsys_id;

	$arr_block=select_view(array('users'));
	
	load_libraries(array('form_date'));
	load_lang('user');

	$arr_slug_lang=array();

	foreach($arr_i18n as $lang_item)
	{

		$arr_slug_lang[slugify($lang_item)]=$lang_item;

	}

	if($user_data['IdUser']>0)
	{

		$model['user']->reset_require();

		$model['user']->components['language']->required=1;
		
		if($model['user']->update(array('language' => $arr_slug_lang[$_GET['language']]), 'where IdUser='.$user_data['IdUser']))
		{
			if($_SERVER['HTTP_REFERER']=='')
			{

				$_SERVER['HTTP_REFERER']=$base_url;

			}

			$_SESSION['language']=$arr_slug_lang[$_GET['language']];

			header('Location: '.$_SERVER['HTTP_REFERER']);
			die;

		}
		else
		{
		
			echo $lang['user']['cannot_change_language'];

		}

	}
	else
	{

		$model['anonymous']->reset_require();

		$model['anonymous']->components['language']->required=1;

		if($model['anonymous']->update(array('language' => $arr_slug_lang[$_GET['language']]), 'where key_connection="'.$webtsys_id.'"'))
		{
			if($_SERVER['HTTP_REFERER']=='')
			{

				$_SERVER['HTTP_REFERER']=$base_url;

			}

			$_SESSION['language']=$arr_slug_lang[$_GET['language']];

			header('Location: '.$_SERVER['HTTP_REFERER']);
			die;

		}
		else
		{
		
			echo $lang['user']['cannot_change_language'];

		}

	}

	$cont_user=ob_get_contents();
	
	ob_clean();

	echo load_view(array($lang['user']['change_language'], $cont_user), 'content');

	$cont_index=ob_get_contents();

	ob_end_clean();

	echo load_view( array($lang['user']['user_zone'], $cont_index, $block_title, $block_content, $block_urls, $block_type, $block_id, $config_data, ''), $arr_block);

}

?>