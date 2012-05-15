<?php

function Post()
{

	global $user_data, $model, $ip, $lang, $config_data, $base_path, $base_url, $cookie_path, $arr_block, $prefix_key, $block_title, $block_content, $block_urls, $block_type, $block_id, $config_data;

	$cont_index='';

	$arr_block='';

	$arr_block=select_view(array('blog'));

	//Load page...

	load_model('blog', 'captcha');

	load_libraries(array('form_date', 'form_time', 'check_admin', 'pages', 'generate_forms', 'forms/textareabb', 'forms/textbbpost'));

	load_libraries(array('blog_functions'), $base_path.'modules/blog/libraries/');

	load_lang('blog');

	settype($_GET['IdPage_blog'], 'integer');

	$query=webtsys_query('select page_blog.*, user.private_nick from page_blog, user where user.Iduser=page_blog.author and page_blog.IdPage_blog='.$_GET['IdPage_blog']);

	$result=webtsys_fetch_array($query);

	settype($result['idblog'], 'integer');

	$query=$model['blog']->select('where idblog='.$result['idblog'], array('num_words'));

	list($num_words)=webtsys_fetch_row($query);

	settype($result['IdPage_blog'], 'integer');
	settype($_GET['preview'], 'integer');

	if($result['IdPage_blog']>0)
	{

		//Get moderators..
		
		$num_mod=$model['moderator_blog']->select_count('where iduser='.$user_data['IdUser'].' and idblog='.$result['idblog'], 'idModerator_blog');
		
		if($num_mod>0 || check_admin($user_data['IdUser'], $model['user'])==1)
		{
		
			function options_admin($idpage_blog, $idcomment)
			{

				global $lang, $result, $base_url;

				$url_moderate=make_fancy_url($base_url, 'blog', 'moderate', 'moderate_comment', array('IdComment_blog' => $idcomment, 'IdBlog' => $result['idblog'], 'op' => 2));

				$url_delete_post=make_fancy_url($base_url, 'blog', 'moderate', 'moderate_comment', array('IdComment_blog' => $idcomment, 'IdBlog' => $result['idblog'], 'op' => 3));
		
				return '<a href="'.$url_moderate.'">'.$lang['blog']['edit_comment'].'</a> <a href="'.$url_delete_post.'">'.$lang['blog']['delete_comment'].'</a>';
		
			}

			function options_ip($ip)
			{

				global $lang;

				return $lang['common']['ip'].': '.$ip;

			}

			function options_email($email)
			{

				global $lang;

				return $lang['common']['email'].': '.$email;

			}
		
		}
		else
		{
		
			function options_admin($idpage_blog, $idcomment)
			{
		
				return '';
		
			}

			function options_ip($ip)
			{

				return '';

			}

			function options_email($email)
			{

				return '';

			}
		
		}
		

		$lang['blog']['read_more']=$lang['blog']['permalink'];

		$result['date']=form_date( $result['date'], $user_data['format_date'] , $user_data['format_time']).' '.form_time( $result['date'], $user_data['format_time'], $user_data['ampm'] );
		
		//Obtain tags...
		$arr_tags=array();
		$num_tags=0;

		$query=webtsys_query('select tag_blog.tag,page_tag_blog.idtag, page_tag_blog.idpage_blog from tag_blog,page_tag_blog where idpage_blog = '.$_GET['IdPage_blog'].' and page_tag_blog.idtag=tag_blog.IdTag_blog');

		while(list($tag, $idtag, $idpage_blog_tag)=webtsys_fetch_row($query))
		{

			$arr_tags[$idpage_blog_tag][]='<a href="'.make_fancy_url($base_url, 'blog', 'search_tags', $tag, array('tag' => $idtag) ).'">'.$tag.'</a>';

			$num_tags++;

		}

		$tags=$lang['blog']['no_tags'];

		if(isset($arr_tags[$result['IdPage_blog']]))
		{

			$tags=implode(', ', $arr_tags[$result['IdPage_blog']]);

		}

		//Cut post

		echo load_view(array($result['author'], $result['IdPage_blog'], $result['private_nick'], $result['title'], $result['text'], $result['num_comments'], $result['date'], $tags), 'blog/postblog');
		//Comment form...

		$cont_index.=ob_get_contents();
			
		ob_clean(); 
		
		if($result['accept_comment']==1)
		{
	
			//Comments...
			?>
			<a name="comments"></a>
			<h1><?php echo $lang['blog']['comments']; ?></h1>
			<?php
			
			$z=0;
			$n_comment=$_GET['begin_page']+1;
			
			$num_comment=20;

			$query=webtsys_query('select comment_blog.*, user.avatar, user.date_register, user.hidden_status, user.num_messages, user.last_connection, user.signature, rank.name as rank_name, rank.image as rank_image from comment_blog, user, rank where comment_blog.idpage_blog='.$result['IdPage_blog'].' and comment_blog.idauthor=user.IdUser and user.rank=rank.IdRank order by comment_blog.date_comment ASC limit '.$_GET['begin_page'].', '.$num_comment);

			while($result_c=webtsys_fetch_array($query))
			{
			
				$result_c['date_comment']=form_date( $result_c['date_comment'], $user_data['format_date'] , $user_data['format_time']).' '.form_time( $result_c['date_comment'], $user_data['format_time'], $user_data['ampm'] );

				$hidden_status='';

				if($result_c['idauthor']>0)
				{
					
					$result_c['author']='<a href="'.make_fancy_url($base_url, 'user', 'profile', $result_c['author'], array('IdUser'=> $result_c['idauthor']) ).'">'.$result_c['author'].'</a>';

					if($result_c['avatar']!='')
					{

						$result_c['avatar']='<img src="'.$result_c['avatar'].'" />';

					}

					$result_c['date_register']=$lang['common']['registered'].': '.form_date( $result_c['date_register'], $user_data['format_date'] , $user_data['format_time']);
					

					$result_c['num_messages']=$lang['common']['messages'].': '.$result_c['num_messages'];

					if($config_data['accept_bbcode_signature']==0)
					{

						$result_c['signature']=$result_c['signature'];
				
					}

					$arr_status[0]=$lang['common']['offline'];
					$arr_status[1]=$lang['common']['hidden'];
					$time_check=time()-350;

					if($result_c['last_connection']>$time_check)
						{
							
							$arr_status[0]=$lang['common']['connected'];
				
						}
				
					$result_c['hidden_status']=$arr_status[$result_c['hidden_status']];

					if($result_c['rank_image']!='')
					{

						$result_c['rank_name'].='<br /><img src="'.$result_c['rank_image'].'" />';

					}

				}
				else
				{

					$result_c['date_register']='';
					$result_c['hidden_status']='';
					$result_c['num_messages']='';

				}

				$comment_url=make_fancy_url($base_url, 'blog', 'post', $result['title'], array('IdPage_blog' => $_GET['IdPage_blog'], 'begin_page' => $_GET['begin_page'].'#comment'.$result_c['IdComment_blog']) );

				echo load_view(array($result_c['author'], options_email($result_c['email']), $result_c['avatar'], $result_c['date_register'], $result_c['website'], $result_c['num_messages'], $n_comment, $result_c['subject'], $result_c['text'], $result_c['signature'], $result_c['date_comment'], $result_c['idauthor'], $result_c['hidden_status'], options_admin( $result['IdPage_blog'], $result_c['IdComment_blog']), $comment_url, $result_c['IdComment_blog'], options_ip($result_c['ip']), $result_c['rank_name']), 'post/comment');

				// CommentView($author, $email, $avatar, $date_register, $website, $num_messages, $x, $subject, $text, $signature, $posted, $iduser, $status, $options, $url, $num_comment, $ip, $rank)
			
				$z++;
				$n_comment++;
			
			}
			
			
			$total_comment=$model['comment_blog']->select_count('where idpage_blog='.$result['IdPage_blog'], 'IdComment_blog');

			$url_page=make_fancy_url($base_url, 'blog', 'post', $result['title'], array('IdPage_blog' => $_GET['IdPage_blog']) );
			
			$post_pages=pages( $_GET['begin_page'], $total_comment, $num_comment, $url_page, 20, 'begin_page', '#comments');
			
			echo '<p class="navigation">'.$lang['blog']['more'].': '.$post_pages.'</p>';

			$cont_index.=ob_get_contents();
			
			ob_clean(); 

			//Form for post
				
			$cont_form=form_comment();

			ob_clean();

			echo load_view(array($lang['blog']['send_post'], $cont_form), 'content');
		
			$cont_index.=ob_get_contents();
			
			ob_clean(); 

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

?>
