<?php

function Posting()
{

	global $user_data, $model, $ip, $lang, $config_data, $base_path, $base_url, $cookie_path, $arr_block, $prefix_key, $block_title, $block_content, $block_urls, $block_type, $block_id, $config_data, $webtsys_id;

	$cont_index='';

	$arr_block='';

	$arr_block=select_view(array('blog'));

	//Load page...

	load_model('blog', 'captcha');

	load_libraries(array('form_date', 'form_time', 'check_admin', 'pages', 'generate_forms', 'forms/textareabb', 'forms/textbbpost'));

	load_libraries(array('blog_functions'), $base_path.'modules/blog/libraries/');

	load_lang('blog');

	settype($_GET['IdPage_blog'], 'integer');

	$last_write=TODAY-$config_data['wait_message'];

	$query=webtsys_query('select page_blog.*, user.private_nick from page_blog, user where user.Iduser=page_blog.author and page_blog.IdPage_blog='.$_GET['IdPage_blog']);

	$result=webtsys_fetch_array($query);

	settype($result['idblog'], 'integer');

	$query=$model['blog']->select('where idblog='.$result['idblog'], array('num_words'));

	list($num_words)=webtsys_fetch_row($query);

	settype($result['IdPage_blog'], 'integer');
	settype($_GET['preview'], 'integer');

	if($result['IdPage_blog']>0 && $result['accept_comment']==1)
	{

		$check_captcha=1;

		$num_email=0;

		if($user_data['IdUser']>0)
		{

			$post['idauthor']=$user_data['IdUser'];
			$_POST['author']=$user_data['private_nick'];
			$_POST['email']=$user_data['email'];
			$_POST['website']=$user_data['website'];

		}
		else
		{

			//Check that email in not in use...

			//I cannot check email why if bot check that email exists in the system, can capture and spam in it.

			//Check captcha..

			if($config_data['captcha_type']!='')
			{

				load_libraries(array('captchas/'.$config_data['captcha_type']));

				$result_captcha=CaptchaCheck($_POST);
				
				if($result_captcha[0]=='false')
				{

					$check_captcha=0;

				}

			}

		}

		$arr_fields_form=array('author', 'email', 'website', 'text');

		foreach($arr_fields_form as $field)
		{

			settype($_POST[$field], 'string');

			$post[$field]=$_POST[$field];

		}

		//check_error_field_required ip , check_error_field_required idpage_blog , check_error_field_required date_comment cant_insert
		
		$post['ip']=$ip;
		$post['idpage_blog']=$result['IdPage_blog'];
		$post['date_comment']=TODAY;

		$yes_last_write=1;
		$txt_last_write='';

		if($user_data['write_message']>$last_write)
		{

			$yes_last_write=0;
			$txt_last_write=$lang['blog']['please_wait_a_seconds_for_send_a_new_message'];

		}

		if($check_captcha==1 && $num_email==0 && $yes_last_write==1)
		{

			if($model['comment_blog']->insert($post))
			{

				//Operations

				$insert_id=webtsys_insert_id();
		
				//Update user	
		
				if($user_data['IdUser']>0)
				{
		
					$query=webtsys_query('update user set num_messages=num_messages+1, write_message='.time().' where IdUser='.$user_data['IdUser']);
		
				}
				else
				{
		
					$query=webtsys_query('update anonymous set write_message='.time().' where key_connection="'.$webtsys_id.'"');

					//Save data for anonymous user...

					settype($_POST['save_data'], 'integer');
	
	
					if( !isset($_COOKIE['webtsys_savedata']) && $_POST['save_data']==1)
					{
		
						$save_data['author']=$_POST['author'];
						$save_data['email']=$_POST['email'];
						$save_data['website']=$_POST['website'];
						$save_data['time']=time()+2592000;
						$token= uniqid(md5(rand()), true);
						$save_data['token']=md5($token);
		
						if($model['save_data']->insert($save_data))
						{
							setcookie  ( 'webtsys_savedata', $token, $save_data['time'],$cookie_path);
		
						}
		
					}
					else 
					{
		
						if($model['save_data']->select_count('where token="'.md5($_COOKIE['webtsys_savedata']).'"', 'IdSave_data')==1 && $_POST['save_data']==0)
						{
			
							$save_data['author']=$_POST['author'];
							$save_data['email']=$_POST['email'];
							$save_data['website']=$_POST['website'];
							$save_data['time']=time()+2592000;
							$model['save_data']->components['token']->required=0;
							
			
							echo $model['save_data']->update($save_data, 'where token="'.md5($_COOKIE['webtsys_savedata']).'"');
							
							setcookie  ( 'webtsys_savedata', $_COOKIE['webtsys_savedata'], $save_data['time'],$cookie_path);
			
						}
						else 
						{
		
							setcookie ( "webtsys_savedata", FALSE, 0, $cookie_path);
		
						}
					}
		
				}

				//susbcription
			
				settype($_POST['subscription'], 'integer');

				if($_POST['subscription']==1)
				{
					
					if($model['subscription']->select_count('where email="'.$_POST['email'].'" and idarticle="'.$_GET['idarticle'].'"', 'IdSubscription')==0)
					{
		
						$post['token']=md5(uniqid(rand(), true));
						$post['idpage_blog']=$_GET['IdPage_blog'];
						$post['email']=mysql_real_escape_string ($_POST['email']);
			
						$model['subscription']->insert($post);
			
					}
		
				}
		
				//Update post...
		
				$query=webtsys_query('update page_blog set num_comments=num_comments+1 where IdPage_blog='.$_GET['IdPage_blog']);

				//Calculate page...
	
				$count_c=$model['comment_blog']->select_count('where idpage_blog='. $_GET['IdPage_blog'], 'IdComment_blog');
		
				//$total=$count_c/20;
		
				$total_page=0;
		
				while($total_page<$count_c)
				{
					
					
					$total_page+=20;
					
		
				}
		
				$total_page-=20;

				//If all is fine, redirect...

				//Send email for suscriptors...

				$url=make_fancy_url($base_url, 'blog', 'post', $result['title'], array('IdPage_blog' => $_GET['IdPage_blog'], 'begin_page' => $total_page.'#comment'.$insert_id) );

				send_email_suscriptors($_GET['IdPage_blog'], $url, form_text($_POST['email']), form_text($_POST['author']), form_text($_POST['text']));

				//$base_url.'/blog/'.$url_redirect.'?IdPage_blog='.$_GET['IdPage_blog'].'&amp;begin_page='.$total_page.'#comment'.($insert_id)

				ob_end_clean();

				load_libraries(array('redirect'));

				die( redirect_webtsys( $url, $lang['common']['redirect'], $lang['common']['success'], $lang['common']['press_here_redirecting'] , $arr_block) );


			}
			else
			{
				
				$cont_form=form_comment($_POST, $check_captcha);

				ob_clean();

				echo load_view(array($lang['blog']['send_post'], $cont_form), 'content');

			}

		}
		else
		{

			//echo 'fail captcha';

			//form_comment($_POST);

			$cont_form='<span class="error">'.$txt_last_write.'</span>'.form_comment($_POST, $check_captcha);

			ob_clean();

			echo load_view(array($lang['blog']['send_post'], $cont_form), 'content');

		}

	}
	else
	{

		echo load_view(array($lang['blog']['no_exist_post'], $lang['blog']['no_exist_post']), 'content');
		$result['title']=$lang['blog']['no_exist_post'];

	}

	$cont_index.=ob_get_contents();

	ob_end_clean();

	echo load_view(array($result['title'], $cont_index, $block_title, $block_content, $block_urls, $block_type, $block_id, $config_data, ''), $arr_block);

}

function send_email_suscriptors($idpage_blog, $url_post, $email_user_send, $nick, $text)
{

	global $config_data, $lang, $model, $base_url;

	load_libraries(array('send_email'));

	$bcc_mail=array();

	$query=$model['subscription']->select('where idpage_blog='.$idpage_blog.' and email!="'.$email_user_send.'"', array('email', 'token'));
	
        while ( list( $email ) = webtsys_fetch_row( $query ) )
        {

		$arr_email[]=$email;

	}

	$bcc_string=implode(',', $arr_email);

	$url_unsubscribe=make_fancy_url($base_url, 'blog', 'unsubscribe', 'unsubscribe_post', array('IdPage_blog' => $idpage_blog));

	/*$message_final="<html><head></head><body>";

	$message_final.="<p>".$lang['blog']['inform_comment']."</p>\n\n<p><a href=\"".$url_post."\">".$url_post."</a></p>\n\n"."<p>".$lang['blog']['comment_made']." $nick:\n\n</p><p>".
	$text."</p>\n\n<hr /><p>".$lang['blog']['down_article'].": </p>\n\n<p><a href=\"".$url_unsubscribe."\">".$url_unsubscribe."</a>\n\n</p></body></html>";*/

	$message_final=load_view(array($nick, $text, $url_post, $url_unsubscribe), 'common/user/mailviews/mailposting');

	if($bcc_string!='')
	{

		return send_mail($config_data['portal_email'], $config_data['portal_name'], $message_final, $content_type='html', $bcc=$bcc_string);

	}

}

?>
