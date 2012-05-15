<?php

function Unsubscribe()
{

	global $user_data, $model, $ip, $lang, $config_data, $base_path, $base_url, $cookie_path, $arr_block, $prefix_key, $block_title, $block_content, $block_urls, $block_type, $block_id, $config_data, $webtsys_id;

	$cont_index='';

	$arr_block='';

	$arr_block=select_view(array('blog'));

	load_model('blog');
	load_lang('blog');

	settype($_GET['IdPage_blog'], 'integer');

	settype($_GET['action'], 'integer');

	switch($_GET['action'])
	{

		default:
		
			?>
			<form method="post" action="unsubscribe.php?action=1">
			<?php set_csrf_key(); ?>
			<div class="form">
			<p>
			<label for="email"><?php echo $lang['blog']['unsubscribe_email']; ?>: </label>
			<input type="text" name="email" size="20" />
			</p>
			<p>
			<label for="IdPage_blog"><?php echo $lang['blog']['where_unsubscribe_email']; ?>: </label>
			<?php

			echo SelectForm('IdPage_blog', '', array($_GET['IdPage_blog'], $lang['blog']['only_unsubscribe_email_for_post'], $_GET['IdPage_blog'], $lang['blog']['unsubscribe_email_for_all_post'], 0 ));

			?>
			</p>
			<p><input type="submit" value="<?php echo $lang['common']['send']; ?>"></p>
			</div>
			</form>
			<?php
			
			$content=ob_get_contents();

			ob_clean();

			echo load_view(array($lang['blog']['unsubscribe_email'], $content), 'content');
		
		break;

		case 1:

			settype($_POST['IdPage_blog'], 'integer');
			$email=substr(form_text($_POST['email']), 0, 255);

			$email_post=new EmailField();

			if(!$email_post->check($email))
			{

				echo load_view(array($lang['common']['error'], $lang['common']['error_email_format']), 'content');

			}
			else
			{

				//Insert write_message?

				if($user_data['IdUser']==0)
				{

					$query=$model['anonymous']->update(array('write_message' => time()), 'where key_connection="'.$webtsys_id.'"');

				}
				else
				{

					$query=$model['user']->update(array('write_message' => time()), 'where IdUser="'.$user_data['IdUser']);

				}
				
				$time_limit=$user_data['write_message']+$config_data['wait_message'];

				$time_now=time();
				
				if($time_limit<$time_now)
				{

					$where_sql='where email="'.$email.'"';
		
					if($_POST['IdPage_blog']>0)
					{
		
						$where_sql.=' and idpage_blog='.$_POST['IdPage_blog'];
		
					}
		
					$query=$model['subscription']->select($where_sql.' limit 1', array('token'));
					
					list($token)=webtsys_fetch_row($query);
		
					if($token!='')
					{
		
						$portal_name=html_entity_decode($config_data['portal_name']);
			
						$header = "From:" .$portal_name."<".$config_data['portal_email'].">\r\nReply-To:".$config_data['portal_email']."\r\nX-Mailer: PHP5";

						$url_message_final=make_fancy_url( $base_url, 'blog', 'unsubscribe', 'unsubscribe_final_step', array('action' => 2, 'token' => $token, 'IdPage_blog' => $_POST['IdPage_blog']) );
				
						$message_final=$lang['blog']['copy_paste_next_link'].":\n\n ".$url_message_final;
				
						if(mail( $email, $portal_name, $message_final, $header))
						{
				
							echo load_view(array($lang['blog']['instructions_for_unsubscribe_send'], $lang['blog']['instructions_for_unsubscribe_send_explain']), 'content');
				
						}
		
					}
					else
					{
		
						echo load_view(array($lang['blog']['no_exists_email'], '<a href="javascript:history.back();">'.$lang['blog']['please_go_back_for_repeat'].'</a>'), 'content');
		
					}
				}
				else
				{

					echo load_view(array($lang['blog']['wait_a_seconds'], '<p>'.$lang['blog']['wait_a_minute_for_send_a_new_email'].'</p><p><a href="javascript:history.back();">'.$lang['blog']['please_go_back_for_repeat'].'</a></p>'), 'content');

				}
				

			}

		break;

		case 2:

			$token=form_text($_GET['token']);

			$delete_sql_token='where token="'.$token.'"';

			$delete_sql_page='';

			if($_GET['IdPage_blog']>0)
			{

				$delete_sql_page.=' and idpage_blog='.$_GET['IdPage_blog'];

			}

			$query=$model['subscription']->select($delete_sql_token.$delete_sql_page.' limit 1', array('email'));

			list($email)=webtsys_fetch_row($query);

			$delete_sql='where email="'.$email.'" '.$delete_sql_page;

			$model['subscription']->delete($delete_sql);
			
			ob_end_clean();

			load_libraries(array('redirect'));

			die( redirect_webtsys(  make_fancy_url( $base_url, 'blog', 'unsubscribe', 'unsubscribe_final_step', array('action' => 4) ), $lang['common']['redirect'], $lang['common']['success'], $lang['common']['press_here_redirecting'] , $arr_block) );
			

		break;

		case 4:

			echo load_view(array($lang['blog']['you_unsubscribe_from_posts'], $lang['blog']['you_unsubscribe_from_posts_explain']), 'content');

		break;

	}

	$cont_index.=ob_get_contents();

	ob_end_clean();

	echo load_view(array($lang['blog']['down_email_in_article'], $cont_index, $block_title, $block_content, $block_urls, $block_type, $block_id, $config_data, ''), $arr_block);

}

?>
