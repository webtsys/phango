<?php

function Profile()
{

	ob_start();

	global $user_data, $model, $ip, $lang, $config_data, $base_path, $base_url, $cookie_path, $arr_block, $prefix_key, $block_title, $block_content, $block_urls, $block_type, $block_id, $config_data;

	$arr_block=select_view(array('users'));
	
	load_libraries(array('form_date'));
	load_lang('user');

	settype($_GET['IdUser'], 'integer');

	$query=webtsys_query('select user.IdUser, user.private_nick, user.email, user.website, user.interests, user.signature, user.avatar, user.rank, user.show_email, user.hidden_status, user.num_messages, user.date_register, user.last_connection, rank.name from user, rank where rank.IdRank=user.rank and IdUser='.$_GET['IdUser']);

	$result=webtsys_fetch_array($query);

	settype($result['IdUser'], 'integer');

	if($result['IdUser']>0)
	{
		
		if($result['show_email']==0)
		{
		
			$result['email']=$lang['common']['hidden'];

		}
		else
		{

			$result['email']=str_replace('@', '.at.', $result['email']).' '.$lang['common']['email_symbol'];

		}

		if($result['avatar']!='')
		{
		
			$result['avatar']='<img src="'.$result['avatar'].'" />';

		}

		$result['date_register']=form_date( $result['date_register'], $user_data['format_date'] , $user_data['format_time']);

		if($config_data['accept_bbcode_signature']==0)
		{

			$result['signature']=$result['signature'];

		}

		$arr_status[0]=$lang['common']['offline'];
		$arr_status[1]=$lang['common']['hidden'];
		$time_check=time()-350;

		if($result['last_connection']>$time_check)
		{
			
			$arr_status[0]=$lang['common']['connected'];

		}
		
		$result['hidden_status']=$arr_status[$result['hidden_status']];
		
		//profile($result, $lang_user);
		echo load_view(array($result), 'user/profile');

		$title_content=$lang['common']['profile'].' - '.$result['private_nick'];

	}
	else
	{

		echo '<p>'.$lang['user']['no_user_profile'].'</p>';

		$title_content=$lang['user']['no_user_profile'];

	}

	$cont_user=ob_get_contents();
	
	ob_clean();

	echo load_view(array($title_content, $cont_user), 'content');

	$cont_index=ob_get_contents();

	ob_end_clean();

	echo load_view( array($lang['user']['user_zone'], $cont_index, $block_title, $block_content, $block_urls, $block_type, $block_id, $config_data, ''), $arr_block);

}

?>