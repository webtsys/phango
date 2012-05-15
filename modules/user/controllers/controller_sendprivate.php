<?php

function SendPrivate()
{

	ob_start();

	global $user_data, $model, $ip, $lang, $config_data, $base_path, $base_url, $cookie_path, $arr_block, $prefix_key, $block_title, $block_content, $block_urls, $block_type, $block_id, $config_data, $arr_i18n, $webtsys_id, $language;

	$arr_block=select_view(array('users'));
	
	load_model('user/mprivate');
	load_libraries(array('forms/textareabb', 'forms/textbbpost'));

	load_lang('user');

	settype($_GET['IdUser'], 'integer');
	settype($_GET['op'], 'integer');
	settype($_GET['preview'], 'integer');

	settype($_POST['subject'], 'string');
	settype($_POST['text'], 'string');

	$post['subject']=$_POST['subject'];
	$post['text']=$_POST['text'];
	$post['iduser']=$_GET['IdUser'];
	$post['iduser_sender']=$user_data['IdUser'];

	$query=$model['user']->select('where IdUser='.$_GET['IdUser'], array('IdUser', 'private_nick', 'email', 'notify_private_messages'));

	list($iduser, $private_nick, $email, $notify_private_messages)=webtsys_fetch_row($query);

	settype($iduser, 'integer');

	if($user_data['IdUser']>0 && $iduser>0)
	{

		switch($_GET['op'])
		{
		
			default:
		
				settype($_GET['IdMprivate'], 'integer');
		
				ob_clean();
		
				if(get_magic_quotes_gpc())
				{
		
					$post['text']=stripslashes($post['text']);
					$post['subject']=stripslashes($post['subject']);
		
				}
		
				$post['subject']=form_text($post['subject']);
				$post['text']=$post['text'];

				if($_GET['IdMprivate']>0 && $post['subject']=='' && $post['text']=='')
				{

					$query=$model['mprivate']->select('where IdMprivate='.$_GET['IdMprivate'], array('subject', 'text') );

					list($post['subject'], $post['text'])=webtsys_fetch_row($query);

					if($post['subject']!='')
					{

						$post['subject']=$lang['user']['re_mprivate'].':'.$post['subject'];

						$post['text']='<blockquote>'.$post['text'].'</blockquote>';

					}

				}
		
				?>
				<form method="post" action="<?php echo make_fancy_url($base_url, 'user', 'sendprivate', 'send_private_message', array('op' => 1, 'IdUser' => $_GET['IdUser']) ); ?>" name="posting">
				<?php set_csrf_key(); ?>
				<div class="form">
						<p><label for="subject"><?php echo $lang['common']['subject']; ?>:</label><?php echo TextForm('subject', '', unform_text( $post['subject']) ); ?></p>
					<p><label for="subject"><?php echo $lang['common']['text']; ?>:</label><?php echo TextAreaBBPostForm('text', '', unform_text($post['text']) ); ?></p>
				</div>
				<p><input type="submit" value="<?php echo $lang['common']['send']; ?>" name="send"/> <input type="submit" value="<?php echo $lang['common']['preview']; ?>" name="preview" onclick="javascript:create_preview();"/></p>
				</form>
				<script language="Javascript">
		
				function create_preview()
				{
				
					document.forms.posting.action="<?php echo make_fancy_url($base_url, 'user', 'sendprivate', 'send_private_message', array('IdUser' => $_GET['IdUser'], 'preview' => '1#preview') ); ?>";
				
				}
				</script>
				<?php
		
				$cont_form=ob_get_contents();
		
				ob_clean();
		
				echo load_view(array($lang['user']['send_message'].' '.$private_nick, $cont_form), 'content');
		
				?>
				<a name="preview"></a>
				<?php
		
				if($_GET['preview']==1)
				{
		
					echo load_view(array($post['subject'], $model['mprivate']->components['text']->check($post['text'])), 'content');
		
				}

				echo load_view(array($lang['common']['options'], '<a href="'.make_fancy_url($base_url, 'user', 'mprivate', 'private_messages', array() ).'">'.$lang['common']['go_back'].'</a>'), 'content');
		
			break;
		
			case 1:

				include($base_path.'libraries/send_email.php');
				
				$post['date']=time();
				$post['author']=$user_data['private_nick'];
		
				$total_ab_kb=50000;

				//Check size box

				$query=webtsys_query('select SUM(LENGTH(text)) from mprivate where iduser='.$post['iduser']);
				
				list($total_kb)=webtsys_fetch_row($query);

				$total_kb=$total_kb+strlen($_POST['text']);

				if($total_kb<=$total_ab_kb)
				{

					if($model['mprivate']->insert($post))
					{
						
						if($notify_private_messages==1)
						{
							
							$subject=$config_data['portal_name'];
		
							//$message=$lang['user']['message_send_user']."\n\n".$lang['user']['see_message_user'].': '.make_fancy_url($base_url, 'user', 'mprivate', 'go_back_private_messages', array() );

							$message=load_view(array(), 'common/user/mailviews/mailprivatemessage');
			
							send_mail($email, $subject, $message, 'html');
							

						}
			
						ob_end_clean();

						load_libraries(array('redirect'));

						die( redirect_webtsys( make_fancy_url($base_url, 'user', 'mprivate', 'go_back_private_messages', array() ), $lang['common']['redirect'], $lang['common']['success'], $lang['common']['press_here_redirecting'] , $arr_block) );
			
					}
					else
					{
			
						echo load_view(array($lang['user']['need_data'], '<a href="javascript:history.back();">'.$lang['common']['go_back'].'</a>'), 'content');
			
					}
					

				}
				else
				{

					//Send a email to user...

					$portal_name=html_entity_decode($config_data['portal_name']);

					$header = "From:" .$portal_name."<".$config_data['portal_email'].">\r\nReply-To:".$config_data['portal_email']."\r\nX-Mailer: PHP5";

					$subject=$portal_name.' - '.$lang['user']['data_overloaded_why'];

					$message=$lang['user']['data_overloaded_user'].': '.make_fancy_url($base_url, 'user', 'mprivate', 'go_back_private_messages', array() );

					load_libraries(array('send_email'));

					//mail($email  , $subject  , $message  , $header);
					send_mail($email, $subject, $message);

					echo load_view(array($lang['common']['error'], $lang['user']['data_overloaded'].'<br /><a href="'.make_fancy_url($base_url, 'user', 'mprivate', 'go_back_private_messages', array() ).'">'.$lang['user']['goback'].'</a>'), 'content');
					

				}
		
			break;
		
		}
	}
	else
	{

		//content($lang['user']['forbbiden_access'], $lang['user']['no_message']);
		die(header('Location: '.make_fancy_url($base_url, 'user', 'index', 'login', array('register_page' => 'user')) ) );

	}

	$cont_index=ob_get_contents();

	ob_end_clean();

	echo load_view( array($lang['user']['user_zone'], $cont_index, $block_title, $block_content, $block_urls, $block_type, $block_id, $config_data, ''), $arr_block);

}

?>