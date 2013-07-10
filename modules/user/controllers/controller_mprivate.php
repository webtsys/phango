<?php

function MPrivate()
{

	ob_start();

	global $user_data, $model, $ip, $lang, $config_data, $base_path, $base_url, $cookie_path, $arr_block, $prefix_key, $block_title, $block_content, $block_urls, $block_type, $block_id, $config_data, $arr_i18n, $webtsys_id, $language;

	$arr_block=select_view(array('users'));

	$arr_block='/none';
	
	load_model('user/mprivate');
	load_libraries(array('table_config', 'form_date', 'form_time', 'pages'));
	
	//load_lang('user');

	settype($_GET['op'], 'integer');

	$total_ab_kb=50000;

	if($user_data['IdUser']>0)
	{

		switch($_GET['op'])
		{
		
			default:

				$query=webtsys_query('select SUM(LENGTH(text)) from mprivate where iduser='.$user_data['IdUser']);
				
				list($total_kb)=webtsys_fetch_row($query);
				
				$total_box_size=($total_kb/$total_ab_kb)*100;

				$total_box_size='<strong>'.$lang['user']['no_message'].'</strong>: '.number_format( $total_box_size, 2 ).'%';

				$lang_pages=$lang['user']['pages'];

				$total_elements=$model['mprivate']->select_count('where mprivate.iduser='.$user_data['IdUser'].' order by read_message DESC, date DESC', 'Id'.ucfirst($model['mprivate']->name));
				
				$num_elements=20;

				$pages=pages( $_GET['begin_page'], $total_elements, $num_elements, make_fancy_url($base_url, 'user', 'mprivate', 'private_messages', array('dummy' => 1)) );

				$fields=array('IdMprivate' => '', 'iduser_sender' => 'IdUser', 'read_message' => $lang['common']['status'], 'author' => $lang['common']['author'],  'subject' => $lang['common']['subject'], 'date' => $lang['common']['date']);
				$cells=array('read_message' => ' style="width:15px;" ', 'select' => ' style="width:15px;" ' );

				$arr_read[0]='<span class="new_message"></span>';
				$arr_read[1]='<span class="message"></span>';

				$query=$model['mprivate']->select('where mprivate.iduser='.$user_data['IdUser'].' order by read_message ASC, date DESC', array_keys($fields));

				unset($fields['iduser_sender']);
				unset($fields['IdMprivate']);

				$fields['select']=$lang['user']['select_message'];

				echo '<form method="post" action="'.make_fancy_url($base_url, 'user', 'mprivate', 'private_messages', array('op' => 2)).'" name="delete_mprivate">';

				set_csrf_key();

				echo '<p><input type="submit" value="'.$lang['user']['delete_message'].'"/></p>';

				$arr_javascript=array();
				
				pages_table($pages, $total_box_size);

				up_table_config($fields, $cells);

				while($result=webtsys_fetch_array($query))
				{

					$result['date']=form_date( $result['date'], $user_data['format_date'] , $user_data['format_time']);

					$result['read_message']=$arr_read[$result['read_message']];

					$result['author']='<a href="'.make_fancy_url($base_url, 'user', 'profile', 'viewprofile', array('IdUser' => $result['iduser_sender'])).'">'.$result['author'].'</a>';

					$result['subject']='<a href="'.make_fancy_url($base_url, 'user', 'mprivate', 'private_messages', array('op' => 1, 'IdMprivate' => $result['IdMprivate'])).'">'.$result['subject'].'</a>';

					$result['select']='<input type="checkbox" name="'.$model['mprivate']->idmodel.'['.$result['IdMprivate'].']" value="'.$result['IdMprivate'].'"/>';

					$arr_javascript[]='document.forms[\'delete_mprivate\'].elements[\'IdMprivate['.$result['IdMprivate'].']\'].checked=1;';

					$arr_javascript_uncheck[]='document.forms[\'delete_mprivate\'].elements[\'IdMprivate['.$result['IdMprivate'].']\'].checked=0;';

					unset($result['iduser_sender']);
					unset($result['IdMprivate']);

					middle_table_config($result);

				}

				down_table_config();

				?>
				<script language="javascript">

				var check=0;

				function check_all_messages()
				{

					if(check==0)
					{

					<?php

						echo implode("\n", $arr_javascript);

					?>

						check=1;

					}
					else
					{

						<?php

						echo implode("\n", $arr_javascript_uncheck);

					?>

						check=0;

					}

				}


				</script>
				<?php

				$check_all=$lang['user']['select_all'].' <input type="checkbox" onclick="javascript:check_all_messages();" />';

				pages_table($pages, $check_all);

				echo '<p>&nbsp</p><p><input type="submit" value="'.$lang['user']['delete_message'].'"/></p>';

				echo '<form/>';

				echo load_view(array($lang['common']['more_options'], '<a href="'.make_fancy_url($base_url, 'user', 'profiles', 'profiles_list', array()).'">'.$lang['user']['search_users_message'].'</a>'), 'content');

			break;

			case 1:

				settype($_GET['IdMprivate'], 'integer');

				$query=webtsys_query('select mprivate.*, user.avatar, user.website, user.date_register, user.hidden_status, user.num_messages, user.last_connection, user.signature, rank.name as rank_name, rank.image as rank_image from mprivate, user, rank where mprivate.IdMprivate='.$_GET['IdMprivate'].' and mprivate.iduser_sender=user.IdUser and rank.IdRank=user.rank and mprivate.iduser='.$user_data['IdUser']);
				
				$result_c=webtsys_fetch_array($query);

				settype($result_c['IdMprivate'], 'integer');

				if($result_c['IdMprivate']>0)
				{
					$result_c['author']='<a href="'.make_fancy_url($base_url, 'user', 'profile', 'profile', array('IdUser' => $result_c['iduser_sender']) ).'">'.$result_c['author'].'</a>';
					$model['mprivate']->components['iduser_sender']->required=0;
					$model['mprivate']->components['iduser']->required=0;
					$model['mprivate']->components['author']->required=0;
					$model['mprivate']->components['subject']->required=0;
					$model['mprivate']->components['text']->required=0;
					$model['mprivate']->components['date']->required=0;
					
					$query=$model['mprivate']->update(array('read_message' => 1), 'where IdMprivate='.$result_c['IdMprivate']);

					if($result_c['avatar']!='')
					{
		
						$result_c['avatar']='<img src="'.$result_c['avatar'].'" />';
		
					}
		
					$result_c['date_register']=$lang['common']['date_register'].': '.form_date( $result_c['date_register'], $user_data['format_date'] , $user_data['format_time']);
		
					$result_c['date']=form_date( $result_c['date'], $user_data['format_date'] , $user_data['format_time']).' '.' '.form_time( $result_c['date'], $user_data['format_time'], $user_data['ampm'] );;
					
		
					$result_c['num_messages']=$lang['common']['num_messages'].': '.$result_c['num_messages'];
					/*
					if($config_data['accept_bbcode_signature']==0)
					{
		
						$result_c['signature']=$result_c['signature'];
				
					}
					*/
					$arr_status[0]=$lang['common']['offline'];
					$arr_status[1]=$lang['common']['hidden'];
					$time_check=time()-350;
		
					if($result_c['last_connection']>$time_check)
						{
							
							$arr_status[0]=$lang['common']['connected'];
				
						}
				
					$result_c['hidden_status']=$arr_status[$result_c['hidden_status']];
			
					$n_comment='';

					if($result_c['rank_image']!='')
					{
		
						$result_c['rank_name'].='<br /><img src="'.$result_c['rank_image'].'" />';
		
					}
		
					//post_comment($author, $avatar, $date_register, $num_messages, $idpost, $x, $subject, $text, $signature, $posted, $iduser, $status, $options)
			
					echo load_view(array($result_c['author'], '', $result_c['avatar'], $result_c['date_register'], $result_c['website'], $result_c['num_messages'], $n_comment, $result_c['subject'], $result_c['text'], $result_c['signature'], $result_c['date'], $result_c['iduser_sender'], $result_c['hidden_status'], '<a href="'.make_fancy_url($base_url, 'user', 'sendprivate', 'send_message', array('IdUser' => $result_c['iduser_sender'], 'IdMprivate' => $_GET['IdMprivate'] ) ).'">'.$lang['common']['quote'].'</a>', '', '', '', $result_c['rank_name']), 'post/comment');

					//CommentView($author, $email, $avatar, $date_register, $website, $num_messages, $x, $subject, $text, $signature, $posted, $iduser, $status, $options, $url, $num_comment, $ip, $rank)

				}
				else
				{

					echo load_view(array($lang['common']['error'], $lang['user']['no_message']), 'content');

				}

				echo load_view(array($lang['common']['more_options'], '<a href="'.make_fancy_url($base_url, 'user', 'mprivate', 'private_messages', array()).'">'.$lang['common']['go_back'].'</a>'), 'content');

			break;

			case 2:

				settype($_POST['IdMprivate'], 'array');

				$arr_id=array(0);
		
				foreach($_POST['IdMprivate'] as $idmprivate)
				{

					settype($idmprivate, 'integer');

					$arr_id[]=$idmprivate;

				}

				$model['mprivate']->delete('where IdMprivate IN ('.implode(', ', $arr_id).')');
				
				ob_end_clean();

				load_libraries(array('redirect'));

				die( redirect_webtsys( make_fancy_url($base_url, 'user', 'mprivate', 'private_messages', array()), $lang['common']['redirect'], $lang['common']['success'], $lang['common']['press_here_redirecting'] , $arr_block) );

			break;
		
		}
	}
	else
	{

		//content($lang['user']['forbbiden_access'], $lang['user']['no_message']);

		die(header('Location: '.make_fancy_url($base_url, 'user', 'index', 'login', array('register_page' => 'user')) ));

	}

	$cont_index=ob_get_contents();

	ob_end_clean();

	echo load_view( array($lang['user']['user_zone'], $cont_index, $block_title, $block_content, $block_urls, $block_type, $block_id, $config_data, ''), $arr_block);

}

?>