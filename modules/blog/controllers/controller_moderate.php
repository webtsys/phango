<?php

function Moderate()
{

	global $model, $lang, $base_url, $base_path, $user_data, $arr_module_admin, $config_data, $arr_block, $arr_cache_jscript;
	
	$content='';
	
	load_lang('blog');
	load_libraries(array('check_admin', 'generate_admin_ng', 'forms/textareabb', 'forms/textbbpost', 'utilities/set_admin_link'));
	load_libraries(array('blog_functions'),$base_path.'modules/blog/libraries/');
	load_model('blog'); 
	
	settype($_GET['op'], 'integer');
	settype($_GET['IdBlog'], 'integer');

	$arr_config=$model['config_blog']->select_a_row_where('limit 1');
	
	$original_theme=$config_data['dir_theme'];
	//$config_data['dir_theme']=$original_theme.'/admin';
	$arr_block='admin/admin_none';
	$name_modules=array();
	$urls=array();

	$yes_admin=0;

	$num_mod=$model['moderator_blog']->select_count('where iduser='.$user_data['IdUser'].' and iduser>0', 'IdModerator_blog');

	settype($num_mod, 'integer');

	$arr_list_father=array();

	//Blog list...

	$arr_perm=array();

	$arr_cat=array();

	$arr_accept_comment=array();

	$arr_accept_comment[$_GET['IdBlog']]=0;
	
	$sql_father='order by blog_father ASC';

	$query=$model['blog']->select($sql_father, array('IdBlog', 'title', 'blog_father', 'accept_comment'));

	while(list($idcat, $title, $idfather, $accept_comment)=webtsys_fetch_row($query))
	{

		settype($arr_list_father[$idfather], 'array');
	
		$arr_list_father[$idfather][]=$idcat;
		$arr_cat[$idcat]=$title;
		$arr_accept_comment[$idcat]=$accept_comment;

		$arr_perm[$idcat]=1;

	}
	
	if(check_admin($user_data['IdUser']))
	{
		
		$yes_admin=1;
		$sql_user='';
		$arr_perm=array();
		
		$query=$model['module']->select('where name="blog" limit 1', array('IdModule'));

		list($idmodule)=webtsys_fetch_row($query);

		settype($idmodule, 'integer');

		echo '<p><a href="'.set_admin_link( 'admin_blog', array('IdModule' => $idmodule)).'">'.$lang['blog']['goback_admin_blog'].'</a></p>';

	}
	else if($num_mod>0)
	{
		
		$query=$model['moderator_blog']->select('where iduser='.$user_data['IdUser'].' and iduser>0', array('idblog'), true);

		while(list($idblog)=webtsys_fetch_row($query))
		{

			$arr_perm[$idblog]=0;

		}

		$yes_admin=1;
			
		if($_GET['IdBlog']>0)
		{
			
			$num_mod=$model['moderator_blog']->select_count('where iduser='.$user_data['IdUser'].' and iduser>0 and idblog='.$_GET['IdBlog'], 'IdModerator_blog');

			settype($num_mod, 'integer');

			if($num_mod>0)
			{

				$yes_admin=1;

			}
			else
			{

				$yes_admin=0;

			}

		}

	}
	
	if($yes_admin===1)
	{
	
		$query=$model['blog']->select('where IdBlog='.$_GET['IdBlog'], array('IdBlog'));
		
		list($idblog)=webtsys_fetch_row($query);
		
		settype($idblog, 'integer');
		
		//variables for define titles for admin page
		switch($_GET['op'])
		{
			default:

			settype($_GET['op'], 'integer');

			echo '<h3>'.$lang['blog']['tree_category'].'</h3>';

			//Arbol de categorías

			recursive_list('blog', $arr_cat, $arr_list_father, 0, make_fancy_url($base_url, 'blog', 'moderate', 'edit_blogs', array() ), $arr_perm);

			if($_GET['IdBlog']==0)
			{

				echo '<p>'.$lang['blog']['choose_the_blog_and_edit'].'</p>';

			}
			else
			{

				$arr_fields=array('title', 'date');
				//'subtitles'
				$arr_fields_edit=array('idblog', 'title', 'text', 'entrance', 'author', 'date');
				
				if($arr_config['comments']==1)
				{
				
					$arr_fields_edit[]='accept_comment';
				
				}
				
				$url_options=make_fancy_url($base_url, 'blog', 'moderate', 'edit_blogs', array('IdBlog' => $_GET['IdBlog']) );

				$model['page_blog']->create_form();
				
				$model['page_blog']->forms['idblog']->SetForm($_GET['IdBlog']);
				$model['page_blog']->forms['accept_comment']->SetForm($arr_accept_comment[$_GET['IdBlog']]);

				$arr_user_blog=array($user_data['IdUser'], $user_data['private_nick'], $user_data['IdUser']);

				$query=$model['user']->select('where IdUser!='.$user_data['IdUser'].' and privileges_user=2', array('IdUser', 'private_nick'));

				while($arr_user=webtsys_fetch_array($query))
				{

					$arr_user_blog[]=$arr_user['private_nick'];
					$arr_user_blog[]=$arr_user['IdUser'];

				}

				$query=$model['moderator_blog']->select('where moderator_blog.iduser!='.$user_data['IdUser'].' and moderator_blog.iduser>0 and idblog='.$_GET['IdBlog'], array('iduser'));

				while($arr_user=webtsys_fetch_array($query))
				{

					$arr_user_blog[]=$arr_user['user_private_nick'];
					$arr_user_blog[]=$arr_user['iduser'];

				}

				$model['page_blog']->forms['author']->SetParameters($arr_user_blog);

				$model['page_blog']->forms['title']->label=$lang['common']['title'];
				$model['page_blog']->forms['text']->label=$lang['common']['text'];
				$model['page_blog']->forms['entrance']->label=$lang['blog']['entrance'];
				$model['page_blog']->forms['author']->label=$lang['blog']['author'];
				$model['page_blog']->forms['date']->label=$lang['common']['date'];
				$model['page_blog']->forms['accept_comment']->label=$lang['blog']['accept_comment'];

				generate_admin_model_ng('page_blog', $arr_fields, $arr_fields_edit, $url_options, $options_func='ModerateOptionsListModel', 
				$where_sql='where idblog='.$idblog, $arr_fields_form=array(), $type_list='Basic');

			}

			break;

			case 1:

				load_libraries(array('forms/selectmodelform'));
				
				?>
				<h3><?php echo $lang['blog']['edit_tags_for_post']; ?></h3>
				<?php

				settype($_GET['IdPage_blog'], 'integer');

				$query=$model['page_blog']->select('where IdPage_blog='.$_GET['IdPage_blog'], array('idblog'));

				list($idblog)=webtsys_fetch_row($query);

				$model['page_tag_blog']->create_form();

				$arr_fields=array('idtag');
				$arr_fields_edit=array();
				$url_options=make_fancy_url($base_url, 'blog', 'moderate', 'edit_tags_for_post', array('IdPage_blog' => $_GET['IdPage_blog'], 'op' => 1) );
				$model['page_tag_blog']->forms['idpage_blog']->SetForm($_GET['IdPage_blog']);

				$model['page_tag_blog']->forms['idtag']->form='SelectModelForm';
				$model['page_tag_blog']->forms['idtag']->parameters=array('idtag', '', '', 'tag_blog', 'tag', $where='order by tag ASC');

				//$model['page_tag_blog']->components['idtag']->fields_related_model=array('tag');
				$model['page_tag_blog']->components['idtag']->name_field_to_field='tag';

				$model['page_tag_blog']->forms['idtag']->label=$lang['blog']['tag'];

				//SelectModelForm($name, $class, $value, $model_name, $identifier_field, $where='')

				generate_admin_model_ng('page_tag_blog', $arr_fields, $arr_fields_edit, $url_options, $options_func='BasicOptionsListModel', $where_sql='where page_tag_blog.idpage_blog='.$_GET['IdPage_blog'], $arr_fields_form=array(), $type_list='Basic');

				echo '<p><a href="'.make_fancy_url($base_url, 'blog', 'moderate', 'edit_blogs', array('IdBlog' => $idblog) ).'">'.$lang['common']['go_back'].'</a></p>';

			break;

			//Options for comments...

			case 2:

				settype($_GET['IdComment_blog'], 'integer');
				
				$url_edit=make_fancy_url($base_url, 'blog', 'moderate', 'edit_post', array('IdComment_blog' => $_GET['IdComment_blog'], 'op' => 2));

				$query=$model['comment_blog']->select('where IdComment_blog='.$_GET['IdComment_blog'], array(), 1);

				$comment_post=webtsys_fetch_array($query);
				
				settype($comment_post['IdComment_blog'], 'integer');
				
				if($comment_post['IdComment_blog']>0)
				{
				
					$query=$model['page_blog']->select('where IdPage_blog='.$comment_post['idpage_blog'], array('title'), 1);

					list($title_page_blog)=webtsys_fetch_row($query);

					echo '<h3>'.$lang['blog']['edit_comment_from_page_blog'].': '.$title_page_blog.'</h3>';
					
					$model['comment_blog']->create_form();

					unset($_POST['idpage_blog']);

					$idpage_blog=$comment_post['idpage_blog'];

					unset($comment_post['idpage_blog']);

					$model['comment_blog']->components['idpage_blog']->required=0;
					
					$model['comment_blog']->forms['author']->label=$lang['blog']['author'];
					$model['comment_blog']->forms['text']->label=$lang['common']['text'];
					$model['comment_blog']->forms['email']->label=$lang['common']['email'];
					$model['comment_blog']->forms['website']->label=$lang['common']['website'];
					$model['comment_blog']->forms['ip']->label=$lang['common']['ip'];
					$model['comment_blog']->forms['date_comment']->label=$lang['common']['date'];
					
					SetValuesForm($comment_post, $model['comment_blog']->forms, 0);

					InsertModelForm('comment_blog', $url_edit, $url_edit, $arr_fields=array('author', 'text', 'email', 'website', 'ip', 'date_comment'), $_GET['IdComment_blog'], $goback=0);

					echo '<p><a href="'.make_fancy_url($base_url, 'blog', 'post', $title_page_blog, array('IdPage_blog' => $idpage_blog) ).'">'.$lang['common']['go_back'].'</a></p>';
					
				}

			break;

			case 3:
			
				settype($_GET['yes_delete'], 'integer');
				
				settype($_GET['IdComment_blog'], 'integer');

				$query=$model['comment_blog']->select('where IdComment_blog='.$_GET['IdComment_blog'], array(), 1);

				$result=webtsys_fetch_array($query);
				
				settype($result['IdComment_blog'], 'integer');
				settype($result['idpage_blog'], 'integer');
				
				$query=$model['page_blog']->select('where IdPage_blog='.$result['idpage_blog'], array('title'));
					
				list($title_page_blog)=webtsys_fetch_row($query);
				
				if($_GET['yes_delete']==1)
				{
					
					$model['page_blog']->reset_require();

					$model['page_blog']->components['num_comments']=new CharField(255);
					$model['page_blog']->components['num_comments']->quot_open='';
					$model['page_blog']->components['num_comments']->quot_close='';

					$model['page_blog']->update(array('num_comments' => 'num_comments-1'), 'where IdPage_blog='.$result['idpage_blog']);

					$model['comment_blog']->delete('where IdComment_blog='.$_GET['IdComment_blog']);

					load_libraries(array('redirect'));
					
					ob_end_clean();

					die( redirect_webtsys( make_fancy_url($base_url, 'blog', 'post', $title_page_blog, array('IdPage_blog' => $result['idpage_blog']) ),  $lang['common']['redirect'], $lang['common']['success'], $lang['common']['press_here_redirecting'] , $arr_block) );
					
				}
				else
				{
					//http://localhost/phangodev/index.php/blog/show/moderate/moderate_comment/IdComment_blog/4/IdBlog//op/3
					
					echo '<h3>'.$lang['blog']['do_you_want_delete_post'].'</h3>';
					
					?>
					<form method="get" action="<?php echo make_fancy_url($base_url, 'blog', 'moderate', 'moderate_comment', array('IdComment_blog' => $result['IdComment_blog'], 'IdBlog' => $result['idpage_blog'], 'op' => 3, 'yes_delete' => 1)); ?>">
					<?php
					
					echo load_view(array($result['author'], $result['text']), 'content');
					
					?>
					<input type="submit" value="<?php echo $lang['common']['yes']; ?>" />
					<input type="button" value="<?php echo $lang['common']['no']; ?>" onclick="javascript:location.href='<?php echo make_fancy_url($base_url, 'blog', 'post', $title_page_blog, array('IdPage_blog' => $result['idpage_blog']) ); ?>';"/>
					</form>
					<?php
					
				
				}

			break;

		}

		$content=ob_get_contents();
	
		ob_end_clean();
		
		echo load_view(array('title' => $lang['blog']['moderate_blogs'], 'content' => $content, 'name_modules' => $name_modules, 'urls' => $urls ), 'common/common');

	}
	else
	{

		die(header('Location: '.$base_url));

	}

}

?>
