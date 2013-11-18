<?php

//Functions for create and update a user...

function UserInsertModel($model_name, $arr_fields, $post)
{

	//Check $std_error if fail
	global $model, $lang, $language, $config_data;

	$set_error=0;

	if($model[$model_name]->select_count('where email="'.$post['email'].'"', 'IdUser'))
	{

		//ModelFormSetParameter('user', 'email', 'std_error', $lang['user']['error_email_exists']);
		$model['user']->forms['email']->std_error=$lang['user']['error_email_exists'];
		
		$set_error++;

	}

	if($model[$model_name]->select_count('where private_nick="'.$post['private_nick'].'"', 'IdUser'))
	{	
		//ModelFormSetParameter('user', 'private_nick', 'std_error', $lang['user']['error_username_exists']);

		$model['user']->forms['private_nick']->std_error=$lang['user']['error_username_exists'];
		
		$set_error++;

	}

	//Set time defaults

	$post['timezone']=MY_TIMEZONE;
	$post['format_time']=obtain_timestamp_zone(MY_TIMEZONE);
	$post['ampm']=$config_data['ampm'];
	$post['date_register']=TODAY;
	$post['language']=$language;

	$arr_active_user[0]=1;
	$arr_active_user[1]=0;

	$post['activated_user']=$arr_active_user[$config_data['active_users']];
	
	$arr_fields[]='format_time';
	$arr_fields[]='ampm';
	$arr_fields[]='date_register';
	$arr_fields[]='language';
	$arr_fields[]='timezone';
	$arr_fields[]='activated_user';
	
	if($post['password']=='')
	{
		
		return 0;

	}
	else if($post['password']!=$post['repeat_password'])
	{

		$model['user']->forms['password']->std_error=$lang['user']['error_password_neq_repeat_password'];

		return 0;
	}
	else
	if($post['password']==$post['repeat_password'] && $set_error==0)
	{
		
		$post['password']=sha1($post['password']);
		
		return BasicInsertModel($model_name, $arr_fields, $post);

	}
	

}

function UserUpdateModel($model_name, $arr_fields, $post, $id)
{

	global $model, $lang, $user_data;
	
	$set_error=0;

	$email=@form_text($post['email']);
	settype($post['password'], 'string');
	settype($post['repeat_password'], 'string');

	if( $model[$model_name]->select_count('where email="'.$email.'" and '.$model[$model_name]->idmodel.'!='.$id, $model[$model_name]->idmodel) )
	{

		$model['user']->forms['email']->std_error=$lang['user']['error_email_exists'];
		
		$set_error++;

	}

	if($user_data['privileges_user']==2 && isset($post['private_nick']) )
	{

		if($model[$model_name]->select_count( 'where private_nick="'.$post['private_nick'].'" and '.$model[$model_name]->idmodel.'!='.$id, $model[$model_name]->idmodel ))
		{	

			$model['user']->forms['private_nick']->std_error=$lang['user']['error_username_exists'];
			
			$set_error++;

		}

	}
	
	/*if($post['password']=='')
	{

		unset($arr_fields['password']);
		unset($arr_fields['repeat_password']);
		unset($post['password']);

	}*/
	
	if($post['password']==$post['repeat_password'] && $set_error==0)
	{

		if($post['password']!='')
		{

			$post['password']=sha1($post['password']);

		}
		else
		{

			unset($post['password']);
			$model[$model_name]->components['password']->required=0;

		}

		unset($post['repeat_password']);

		$arr_fields[]='timezone';
		$post['format_time']=obtain_timestamp_zone($post['timezone']);
		
		return BasicUpdateModel($model_name, $arr_fields, $post, $id);

	}
	else
	{
	
		//ModelFormSetParameter('user', 'password', 'std_error', $lang['user']['error_password_neq_repeat_password']);
		$model['user']->forms['password']->std_error=$lang['user']['error_password_neq_repeat_password'];

		return 0;
	}

}

function UserDeleteModel($model_name, $id)
{
	
	$return=BasicDeleteModel($model_name, $id);
	
	return $return;

}

function AvatarForm($name="", $class='', $value='', $real_value='', $form_choose_avatar='')
{
	global $lang, $config_data;

	$arr_size_x[$config_data['x_avatar']]=$config_data['x_avatar'];
	$arr_size_x[0]=$lang['common']['unlimited'];

	$arr_size_y[$config_data['y_avatar']]=$config_data['y_avatar'];
	$arr_size_y[0]=$lang['common']['unlimited'];

	$size_info=$lang['common']['width'].' -> '.$arr_size_x[$config_data['x_avatar']]. ', '.$lang['common']['height'].' -> '.$arr_size_x[$config_data['x_avatar']];

	ob_start();

	if($real_value!='')
	{
	?>
		<img src="<?php echo $real_value; ?>">
		<!--<input type="button" value="<?php echo $lang['user']['delete_avatar']; ?>" onclick="javascript:location.href='<?php echo $form_avatar; ?>';" />-->

	<?php
	}
	else
	{

		echo $lang['user']['no_avatar_selected'];

	}

	if(ini_get ( "allow_url_fopen" )==1)
	{
		?>
		<p>
		<label for="avatar_web"><?php echo $lang['user']['choose_avatar_out']; ?> (<?php echo $lang['user']['max_size']; ?>: <?php echo $size_info; ?>):</label>
		<input type="text" name="avatar" value="<?php echo $real_value; ?>" />
		</p>
		<?php
	}
	
	?>
	<p>
		<label for="choose_avatar"><?php echo $lang['user']['choose_avatar_web']; ?>:</label>
		<input type="button" value="<?php echo $lang['user']['choose_avatar']; ?>" onclick="javascript:location.href='<?php echo $form_choose_avatar; ?>'">
	</p>
	<?php
	$form=ob_get_contents();

	ob_end_clean();

	return $form;

}

function AvatarFormSet($post, $value)
{

	return $value;

}

function setlogin($email, $password, $register_page, $automatic_login, $redirecting=1)
{

	global $model, $lang, $prefix_key, $cookie_path, $base_url, $arr_block, $ip, $user_data;

	settype($automatic_login, 'integer');
	settype($password, 'string');
	settype($register_page, 'string');
	$sha1_password=sha1($password);
	$md5_password=md5($password);

	$post_values=array('email' => $email, 'password' => $password, 'register_page' => $register_page, 'automatic_login' => $automatic_login);

	$update_pass_sha1='';
	
	$email=@form_text($email);
	
	$result['IdUser']=0;
	
	$query=$model['user']->select('where email="'.$email.'" and password="'.$sha1_password.'" and activated_user=1', array(), 1);
	
	$result=webtsys_fetch_array($query);
	
	settype($result['IdUser'], 'integer');
	
	if($result['IdUser']==0)
	{

		//Check again with md5
		
		$query=$model['user']->select('where email="'.$email.'" and password="'.$md5_password.'" and activated_user=1');
	
		$result=webtsys_fetch_array($query);
		
		settype($result['IdUser'], 'integer');

		$update_pass_sha1=sha1($password);

	}

	$arr_pconnect[$automatic_login]=0;
	$arr_pconnect[0]=0;
	$arr_pconnect[1]=TODAY+31536000;
	
	if($result['IdUser']>0)
	{

		
		$id=sha1(uniqid(rand(), true));

		$post=array('key_connection'=>sha1($id));

		$post['before_last_connection']=$result['last_connection'];

		//Csrf token

		$csrf_token=$prefix_key.'_'.sha1(uniqid(rand(), true));

		$post['key_csrf']=$csrf_token;

		$user_data['key_csrf']=$csrf_token;

		if($result['privileges_user']==2)
		{

			$idadmin=sha1(uniqid(rand(), true));
			$post['key_privileges']=sha1($idadmin);
			setcookie  ( 'webtsys_admin', $idadmin, $arr_pconnect[$automatic_login],$cookie_path);

		}

		$model['user']->components['email']->required=0;
		$model['user']->components['private_nick']->required=0;

		if($update_pass_sha1!='')
		{

			$post['password']=$update_pass_sha1;

		}

		$model['user']->components['password']->required=0;
		
		$model['user']->update($post, 'where IdUser='.$result['IdUser']);
		
		if($model['user']->update($post, 'where IdUser='.$result['IdUser']))
		{
			
			
			setcookie(COOKIE_NAME, $id, $arr_pconnect[$automatic_login], $cookie_path);
			
			//$_SESSION['webtsys_id']=$id;

			if($redirecting==1)
			{

				$redirect=make_fancy_url($base_url, 'user', 'index', 'user_zone', $arr_data=array('op' => 0));

				//settype($register_page, 'string');

				if($register_page!="")
				{
				
					//Check if base_64 url...
				
					$register_page_decoded = urldecode_redirect($register_page);
					
					if($register_page_decoded===false)
					{
					
						$register_page=form_text($register_page);

						$redirect=make_fancy_url($base_url, $register_page, 'index', $register_page, array());
						
					}
					else if(preg_match('/^'.str_replace('/', '\/', $base_url).'/', $register_page_decoded))
					{
					
						$register_page_decoded=form_text($register_page_decoded);

						$redirect=$register_page_decoded;
					
					}

				}

				die( redirect_webtsys( $redirect, $lang['user']['redirect'], $lang['user']['login_success'], $lang['user']['no_redir'] , $arr_block) );

			}
			else
			{

				return 1;

			}

		}
		else
		{

			if($redirecting==1)
			{

				echo '<p><span class="error">'.$lang['user']['error_login'].'</span><p>';
				echo '<p>'.$lang['user']['try_login_again'].'<p>';
				
				SetValuesForm($post_values, $model['user']->forms);
				
				if($model['user']->components['email']->check($email)=='')
				{

					$model['user']->forms['email']->std_error=$lang['error_model']['email_format_error'];

				}
				
				echo load_view(array($model['user']->forms, $register_page), 'common/user/login');

			}
			else
			{

				return 0;

			}

		}

	}
	else
	{


		$register_page=form_text($register_page);

		$query=$model['login_tried']->select('where ip="'.$ip.'"', array('num_tried'));

		list($num_tried)=webtsys_fetch_row($query);
		
		if($num_tried==0)
		{

			$model['login_tried']->insert(array('ip' => $ip, 'num_tried' => 1, 'time' => time()+600));
			
		}
		else
		{
			
			if($num_tried>5)
			{

				load_model('bans');

				$timeban=time()+300;
				
				$model['ban']->insert( array('ip' => $ip, 'message' => html_entity_decode($lang['user']['error_login_many_times']), 'description' => $lang['user']['error_login_many_times'], 'time_ban' => $timeban, 'dynamic' => 1) );
				
				$model['login_tried']->delete('where ip="'.$ip.'"');
				
				die(header('Location: '.$base_url.'/index.php'));

			}
			else
			{

				$model['login_tried']->components['num_tried']->quot_open='num_tried+';
				$model['login_tried']->components['num_tried']->quot_close='';

				$model['login_tried']->update(array('num_tried' => 1), 'where ip="'.$ip.'"');

			}


		}

		if($redirecting==1)
		{

			echo '<p><span class="error">'.$lang['user']['error_login'].'</span><p>';

			SetValuesForm($post_values, $model['user']->forms);
			
			if($model['user']->components['email']->check($email)=='')
			{

				$model['user']->forms['email']->std_error=$lang['error_model']['email_format_error'];

			}
			
			echo load_view(array($model['user']->forms, $register_page), 'common/user/login');

		}
		else
		{

			return 0;

		}

	}

}

function get_token()
{

	$rand_prefix=generate_random_password();
	
	return sha1( uniqid($rand_prefix, true) );

}

?>