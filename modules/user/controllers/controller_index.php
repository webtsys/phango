<?php

function Index()
{

	ob_start();

	global $user_data, $model, $ip, $lang, $config_data, $base_path, $base_url, $cookie_path, $arr_block, $prefix_key, $block_title, $block_content, $block_urls, $block_type, $block_id, $config_data, $arr_i18n;

	load_libraries(array('table_config', 'pages', 'timestamp_zone', 'generate_admin_ng', 'redirect', 'forms/userforms', 'forms/textbbpost', 'send_email'));

	load_libraries(array('func_users'), $base_path.'modules/user/libraries/');

	$arr_block=select_view(array('users'));
	
	settype($_GET['op'], 'integer');

	$model['user']->forms['password']->required=1;
	$model['user']->forms['repeat_password']->required=1;
	$model['user']->components['password']->required=1;
	$model['user']->components['timezone']->arr_values=timezones_array();

	if($user_data['IdUser']==0)
	{

		//If user is not identified, show login and register options...

		switch($_GET['op'])
		{

			default:

				if(isset($_GET['register_page']))
				{

					$_GET['register_page']=form_text($_GET['register_page']);

				}
				else
				{

					$_GET['register_page']="";

				}

				echo load_view(array($model['user']->forms, $_GET['register_page']), 'common/user/login');

			break;

			case 1:

				/*

				settype($_POST['automatic_login'], 'integer');
				settype($_POST['password'], 'string');
				settype($_POST['register_page'], 'string');
				$sha1_password=sha1($_POST['password']);
				$md5_password=md5($_POST['password']);

				$update_pass_sha1='';
				
				$_POST['email']=@form_text($_POST['email']);
				
				$result['IdUser']=0;
				
				$query=$model['user']->select('where email="'.$_POST['email'].'" and password="'.$sha1_password.'" and activated_user=1');
				
				$result=webtsys_fetch_array($query);
				
				settype($result['IdUser'], 'integer');
				
				if($result['IdUser']==0)
				{

					//Check again with md5
					
					$query=$model['user']->select('where email="'.$_POST['email'].'" and password="'.$md5_password.'" and activated_user=1');
				
					$result=webtsys_fetch_array($query);
					
					settype($result['IdUser'], 'integer');

					$update_pass_sha1=sha1($_POST['password']);

				}

				$arr_pconnect[$_POST['automatic_login']]=0;
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

					if($result['privileges_user']==2)
					{

						$idadmin=sha1(uniqid(rand(), true));
						$post['key_privileges']=sha1($idadmin);
						setcookie  ( 'webtsys_admin', $idadmin, $arr_pconnect[$_POST['automatic_login']],$cookie_path);

					}

					$model['user']->components['email']->required=0;
					$model['user']->components['private_nick']->required=0;

					if($update_pass_sha1!='')
					{

						$post['password']=$update_pass_sha1;
			
					}

					$model['user']->components['password']->required=0;
					
					if($model['user']->update($post, 'where IdUser='.$result['IdUser']))
					{

						setcookie(COOKIE_NAME, session_id(), $arr_pconnect[$_POST['automatic_login']], $cookie_path);
						
						$_SESSION['webtsys_id']=$id;

						$redirect=make_fancy_url($base_url, 'user', 'index', 'user_zone', $arr_data=array('op' => 0));

						settype($_POST['register_page'], 'string');

						if($_POST['register_page']!="")
						{

							$_POST['register_page']=form_text($_POST['register_page']);

							$redirect=make_fancy_url($base_url, $_POST['register_page'], 'index', $_POST['register_page'], array());//$base_url.$_POST['register_page'];

						}

						die( redirect_webtsys( $redirect, $lang['user']['redirect'], $lang['user']['login_success'], $lang['user']['no_redir'] , $arr_block) );

					}
					else
					{

						echo '<p><strong>'.$lang['user']['error_login_update'].'</strong><p>';
						echo '<p>'.$lang['user']['try_login_again'].'<p>';
						
						SetValuesForm($_POST, $model['user']->forms);
						
						if($model['user']->components['email']->check($_POST['email'])=='')
						{

							$model['user']->forms['email']->std_error=$lang['error_model']['email_format_error'];

						}
						
						echo load_view(array($model['user']->forms, $_POST['register_page']), 'common/user/login');

					}

				}
				else
				{


					$_POST['register_page']=form_text($_POST['register_page']);

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

					echo '<p>'.$lang['user']['error_login'].'<p>';

					SetValuesForm($_POST, $model['user']->forms);
					
					if($model['user']->components['email']->check($_POST['email'])=='')
					{

						$model['user']->forms['email']->std_error=$lang['error_model']['email_format_error'];

					}
					
					echo load_view(array($model['user']->forms, $_POST['register_page']), 'common/user/login');

				}

				*/

				settype($_POST['automatic_login'], 'integer');
	
				settype($_POST['email'], 'string');
	
				settype($_POST['password'], 'string');

				settype($_POST['register_page'], 'string');

				setlogin($_POST['email'], $_POST['password'], $_POST['register_page'], $_POST['automatic_login']);

			break;

			case 2:

				//load_libraries(array('captcha'));

				if($config_data['create_user']==0)
				{

					//load_model('key_code');

					settype($_GET['action'], 'integer');

					$arr_fields_form=array('private_nick', 'email', 'password', 'repeat_password', 'name', 'last_name', 'address', 'zip_code', 'city' ,'country');

					if($config_data['captcha_type']!='')
					{

						load_libraries(array('captchas/'.$config_data['captcha_type']));

						$model['user']->forms['captcha']=new ModelForm('captcha', 'captcha', 'CaptchaForm', $lang['common']['captcha'], new CharField(255), $required=0, $parameters='');

						$model['user']->forms['captcha']->required=1;

						$arr_fields_form[]='captcha';

					}

					switch($_GET['action'])
					{

						default:

							echo load_view(array($model['user']->forms, $arr_fields_form), 'common/user/register');

						break;

						case 1:

							settype($_POST['private_nick'], 'string');
							settype($_POST['email'], 'string');
							settype($_POST['password'], 'string');

							$check_captcha=1;

							if($config_data['captcha_type']!='')
							{

								$result_captcha=CaptchaCheck($_POST);
								
								if($result_captcha[0]=='false')
								{

									$check_captcha=0;

								}

							}

							$result_insert=0;

							if($check_captcha==1)
							{

								$result_insert=UserInsertModel('user', $arr_fields_form, $_POST);

							}
	
				
							if($result_insert==1)
							{
								
								$email=$model['user']->components['email']->check($_POST['email']);

								$portal_name=html_entity_decode($config_data['portal_name']);

								$topic_email=$lang['user']['text_confirm'];
							
								$body_email=load_view(array($_POST['private_nick'], $_POST['email'], form_text($_POST['password']) ), 'common/user/mailviews/mailregister');
					
								/*$body_email.=$lang['user']['text_welcome']." " .$_POST['private_nick']."\n";	
								$body_email.=$lang['user']['text_answer']."\n\n";
								$body_email.=$lang['user']['text_password'].": ".form_text($_POST['password'])."\n\n";*/
					
								//$body_email.=$lang['user']['thanks']."\n\n";
								
								if( !send_mail($email, $topic_email, $body_email, 'html') )
								{
					
									echo "<p align=\"center\">".$lang['user']['error_email']."</p>";

								}

								$active_users_explain='';

								if($config_data['active_users']==1)
								{

									send_mail($config_data['portal_email'], $lang['user']['new_user_is_registered'], $lang['user']['new_user_is_registered_explain']."\n\n".make_fancy_url($base_url, 'admin', 'index', 'admin', array()) );

									$active_users_explain='<p><strong>'.$lang['user']['your_user_need_activated_explain'].'</strong></p>';


								}
								echo $active_users_explain;
								?>
								<p>
									<?php echo $lang['user']['user_created']; ?><br />
									<?php echo $lang['user']['user_instructions']; ?><br />
									<a href="<?php echo make_fancy_url($base_url, 'user', 'index', 'login_user', $arr_data=array('op' => 0)); ?>"><?php echo $lang['user']['goback']; ?></a><br />
								</p>
								<?php
								
							}
							else
							{

								echo '<p>'.$lang['user']['error_login'].'<p>';

								if($check_captcha==0)
								{

									$model['user']->forms['captcha']->std_error=$lang['user']['error_captcha'];

								}

								SetValuesForm($_POST, $model['user']->forms);

								if(@$model['user']->components['email']->check($_POST['email'])=='')
								{

									$model['user']->forms['email']->std_error=$lang['error_model']['email_format_error'];

								}

								echo load_view(array($model['user']->forms, $arr_fields_form), 'common/user/register');

							}

						break;

					}
					

				}
				else
				{

					echo $lang['user']['cannot_register_users'];

				}

			break;

			case 3:

				settype($_GET['action'],"integer");

				switch ( $_GET['action'] )
				{

					default:

						echo load_view(array(), 'common/user/recoverypass');

						echo  "<p><a href=\"".make_fancy_url($base_url, 'user', 'index', 'login_user', $arr_data=array('op' => 0))."\"><b>" . $lang['common']['go_back'] . "</b></a></div>";

						break;

					case 1:
					
						$email = form_text( $_POST['email'] );

						$password=generate_random_password(); 
						
						$query=$model['user']->select( 'where email="'.$email.'" and iduser>0', array('private_nick', 'email') );

						list( $nick, $email ) = webtsys_fetch_row( $query );

						$topic_email = $lang['user']['lost_name'];
						$body_email = $lang['user']['hello'] . "\n\n".$lang['user']['user']." : $nick"."\n\n". $lang['common']['email']." : $email"."\n\n"  . $lang['user']['new_pass'] . " : $password" . "\n\n" . $lang['user']['use_data'] . "\n\n" . $lang['common']['thanks'];
						
						$password = sha1( $password );
							
						if ( $email != "" )
						{
							
							$portal_name=html_entity_decode($config_data['portal_name']);	

							echo "<div align=\"center\">";

							if ( send_mail($email, $topic_email, $body_email) )
							{
								$query = webtsys_query( "update user set password=\"$password\" where email=\"$email\"" );
								echo  "<p>" . $lang['user']['success_change_password'];

							} 
							else
							{

								echo  "<p>" . $lang['user']['success_change_password'];

							} 
						} 

						else
						{

							echo  "<div align=\"center\"><p>" . $lang['user']['error_db_pass'];

						} 

						echo  "<p><a href=\"".make_fancy_url($base_url, 'user', 'index', 'login_user', $arr_data=array('op' => 0))."\"><b>" . $lang['common']['go_back'] . "</b></a></div>";

					break;

				} 

			break;

		}

	}
	else
	{

		switch($_GET['op'])
		{

			default:
				
				echo load_view(array(), 'common/user/paneluser');

			break;

			case 1:

				settype($_GET['action'], 'integer');

				if($config_data['user_extra']==0)
				{

					$arr_fields_form=array('email', 'password', 'repeat_password', 'name', 'last_name', 'address', 'zip_code', 'city' ,'country' ,'phone' ,'show_email' ,'hidden_status' ,'notify_private_messages', 'format_date' ,'timezone' ,'ampm', 'language', 'yes_list');

				}
				else
				{

					$arr_fields_form=array('email', 'password', 'repeat_password', 'name', 'last_name', 'address', 'zip_code', 'city' ,'country' ,'phone' ,'show_email' ,'hidden_status' ,'notify_private_messages', 'format_date' ,'timezone' ,'ampm', 'language', 'yes_list', 'avatar', 'signature');

				}

				$model['user']->forms['password']->required=0;
				$model['user']->forms['repeat_password']->required=0;
				$model['user']->components['private_nick']->required=0;

				$return_zone_user='<p><a href="'.make_fancy_url($base_url, 'user', 'index', 'change_data', $arr_data=array() ).'">'.$lang['common']['go_back'].'</a></p>';

				switch($_GET['action'])
				{

					default:
						
						SetValuesForm($user_data, $model['user']->forms, 0);

						$model['user']->forms['avatar']->parameters=array('avatar', '', $user_data['avatar'], $user_data['avatar'], make_fancy_url($base_url, 'user', 'index', 'change_data', $arr_data=array('op' => 1, 'action' => 2) ));

						echo load_view(array($model['user']->forms, $arr_fields_form), 'common/user/modifyuser');

						echo $return_zone_user;
			
					break;

					case 1:

						if(UserUpdateModel('user', $arr_fields_form, $_POST, $user_data['IdUser']))
						{

							if(isset($_POST['language']))
							{

								if(in_array($_POST['language'], $arr_i18n))
								{

									$_SESSION['language']=$_POST['language'];

								}

							}

							$redirect=make_fancy_url($base_url, 'user', 'index', 'change_data', $arr_data=array() );

							die( redirect_webtsys( $redirect, $lang['user']['redirect'], $lang['user']['change_data_success'], $lang['user']['no_redir'] , $arr_block) );

						}
						else
						{

							SetValuesForm($_POST, $model['user']->forms, 1);

							echo load_view(array($model['user']->forms, $arr_fields_form), 'common/user/modifyuser');

							echo $return_zone_user;

						}

					break;

					case 2:
		
						//Here change avatar...

						ob_start();

						load_libraries(array('make_avatar'), $base_path.'modules/user/libraries/');

						make_avatar( $user_data['IdUser'], make_fancy_url($base_url, 'user', 'index', 'change_data', $arr_data=array('op' => 1) ), make_fancy_url( $base_url, 'user', 'index', 'change_data', $arr_data=array('op' => 1, 'action' => 2) ),  $base_path.'application/media/');

						$cont_avatar=ob_get_contents();

						ob_end_clean();

						//echo load_view(array('title' => $lang['user']['choose_avatar'], 'content' => $cont_avatar), 'content');

						echo $cont_avatar;

					break;

				}

			break;

			case 2:

				$model['user']->update(array('key_connection' => ''), 'where IdUser='.$user_data['IdUser']);

				$_SESSION['webtsys_id']='';
				setcookie ( "webtsys_id", FALSE, 0, $cookie_path);
				die( redirect_webtsys( "index.php", $lang['user']['redirect'], $lang['user']['exit_login'], $lang['user']['no_redir'] , $arr_block) );

			break;

		}

	}

	$cont_user=ob_get_contents();
	
	ob_clean();

	echo load_view(array($lang['user']['user_zone'], $cont_user), 'content');

	$cont_index=ob_get_contents();

	ob_end_clean();

	echo load_view( array($lang['user']['user_zone'], $cont_index, $block_title, $block_content, $block_urls, $block_type, $block_id, $config_data, ''), $arr_block);

}

?>