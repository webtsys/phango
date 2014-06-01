<?php

function BlogAdmin()
{

	global $lang, $base_url, $model, $base_path;

	settype($_GET['op'], 'integer');

	load_libraries(array('generate_admin_ng', 'admin/generate_admin_class', 'utilities/menu_selected'));
	load_libraries(array('blog_functions'),$base_path.'modules/blog/libraries/');
	load_model('blog');
	load_lang('blog');

	settype($_GET['IdBlog'], 'integer');

	?>
	<h2><?php echo $lang['blog']['blog_options']; ?></h2>
	<?php
	
	/*
	<p>
		<a href="<?php echo make_fancy_url($base_url, 'admin', 'index', 'blogs', array('IdModule' => $_GET['IdModule'], 'op' => 0)); ?>"><?php echo $lang['common']['home']; ?></a> -
		<a href="<?php echo make_fancy_url($base_url, 'admin', 'index', 'blogs', array('IdModule' => $_GET['IdModule'], 'op' => 3)); ?>"><?php echo $lang['blog']['config']; ?></a> - 
		<a href="<?php echo make_fancy_url($base_url, 'admin', 'index', 'blogs', array('IdModule' => $_GET['IdModule'], 'op' => 2)); ?>"><?php echo $lang['blog']['add_tags']; ?></a>
	</p>
	*/
	
	$arr_op[0]['link']=make_fancy_url($base_url, 'admin', 'index', 'blogs', array('IdModule' => $_GET['IdModule'], 'op' => 0));
	$arr_op[0]['text']=$lang['common']['home'];
	
	$arr_op[3]['link']=make_fancy_url($base_url, 'admin', 'index', 'blogs', array('IdModule' => $_GET['IdModule'], 'op' => 3));
	$arr_op[3]['text']=$lang['blog']['config'];
	
	$arr_op[2]['link']=make_fancy_url($base_url, 'admin', 'index', 'blogs', array('IdModule' => $_GET['IdModule'], 'op' => 2));
	$arr_op[2]['text']=$lang['blog']['add_tags'];
		
	menu_selected($_GET['op'], $arr_op, $type=1);
	
	switch($_GET['op'])
	{

		default:

			//More options...

			$where_sql='where blog_father='.$_GET['IdBlog'];

			$arr_father=array($_GET['IdBlog'], $lang['blog']['blog_father'], 0);
			
			$arr_list_father=array();
			$arr_cat=array();
			$sql_father='order by blog_father ASC';

			$query=$model['blog']->select($sql_father, array('IdBlog', 'title', 'blog_father'));

			while(list($idcat, $title, $idfather)=webtsys_fetch_row($query))
			{

				settype($arr_list_father[$idfather], 'array');
			
				$arr_list_father[$idfather][]=$idcat;
				$arr_cat[$idcat]=$title;

			}

			$array_url=array();

			echo '<h3>'.$lang['blog']['tree_category'].'</h3>';

			//Arbol de categorías
			
			/*$first_url[$_GET['IdBlog']]='<ul><li><a href="'.make_fancy_url($base_url, 'admin', 'index', 'blogs', array('IdModule' => $_GET['IdModule'])).'">'.$lang['blog']['principal_category'].'</a><ul>';
			$first_url[0]='<ul><li><strong>'.$lang['blog']['principal_category'].'</strong></li><ul>';*/
			
			//echo $first_url[$_GET['IdBlog']];

			if(count($arr_cat)>0)
			{

				$arr_blog=obtain_blogs_from_category();

				recursive_list('blog', $arr_cat, $arr_list_father, 0, make_fancy_url($base_url, 'admin', 'index', 'edit_son_blogs', array('IdModule' => $_GET['IdModule']) ));

			}

			//echo '</ul></ul>';

			if(count($arr_cat)>0)
			{
				$arr_father=recursive_select($arr_father, $arr_cat, $arr_list_father, 0);	
			}

			
			$model['blog']->create_form();

			$model['blog']->forms['accept_comment']->SetForm(1);

			$model['blog']->forms['blog_father']->SetParameters($arr_father);

			$model['blog']->forms['title']->label=$lang['common']['title'];
			$model['blog']->forms['num_post']->label=$lang['blog']['num_post'];
			$model['blog']->forms['accept_comment']->label=$lang['blog']['accept_comment'];
			$model['blog']->forms['blog_father']->label=$lang['blog']['blog_father'];
			$model['blog']->forms['num_words']->label=$lang['blog']['num_words'];

			$url_options=make_fancy_url($base_url, 'admin', 'index', 'admin_cat_blog', array('IdModule' => $_GET['IdModule'], 'IdBlog' => $_GET['IdBlog']));

			$arr_fields=array('title');
			$arr_fields_edit=array();
			$url_options=make_fancy_url($base_url, 'admin', 'index', 'admin_blog', array('IdModule' => $_GET['IdModule']));

			generate_admin_model_ng('blog', $arr_fields, $arr_fields_edit, $url_options, $options_func='BlogOptionsListModel', $where_sql, $arr_fields_form=array(), $type_list='Basic');
			
		break;

		case 1:

			?><h2><?php echo $lang['blog']['add_moderators_blog']; ?></h2><?php

			load_libraries(array('forms/selectmodelform'));

			$model['moderator_blog']->components['iduser']->name_field_to_field='private_nick';
			$model['moderator_blog']->components['iduser']->fields_related_model=array('private_nick');
		
			$url_options=make_fancy_url($base_url, 'admin', 'index', 'admin_cat_blog', array('IdModule' => $_GET['IdModule'], 'IdBlog' => $_GET['IdBlog'], 'op' => 1));

			$arr_fields=array('iduser');
			$arr_fields_edit=array('idblog', 'iduser');

			$model['moderator_blog']->create_form();

			$model['moderator_blog']->forms['idblog']->form='HiddenForm';
			$model['moderator_blog']->forms['idblog']->SetForm($_GET['IdBlog']);
			$model['moderator_blog']->forms['iduser']->form='SelectModelForm';

			$model['moderator_blog']->forms['iduser']->parameters=array('iduser', '', '', 'user', 'private_nick', $where='where IdUser>0 and privileges_user>0');

			$model['moderator_blog']->forms['iduser']->label=$lang['common']['user'];

			$query=$model['blog']->select('where IdBlog='.$_GET['IdBlog'], array('blog_father'));

			list($blog_father)=webtsys_fetch_row($query);

			//SelectModelForm($name, $class, $value, $model_name, $identifier_field, $where='')

			generate_admin_model_ng('moderator_blog', $arr_fields, $arr_fields_edit, $url_options, $options_func='BasicOptionsListModel', $where_sql='where moderator_blog.idblog='.$_GET['IdBlog'], $arr_fields_form=array(), $type_list='Basic');

			echo '<p><a href="'.make_fancy_url($base_url, 'admin', 'index', 'edit_son_blogs', array('IdModule' => $_GET['IdModule'], 'IdBlog' => $blog_father)).'">'.$lang['common']['go_back'].'</a></p>';
			
		
		break;

		case 2:
			
			$arr_fields=array('tag');
			$arr_fields_edit=array('tag');
			$url_options=make_fancy_url($base_url, 'admin', 'index', 'blogs', array('IdModule' => $_GET['IdModule'], 'op' => 2));

			$model['tag_blog']->create_form();

			$model['tag_blog']->forms['tag']->label=$lang['blog']['tag'];
			
			generate_admin_model_ng('tag_blog', $arr_fields, $arr_fields_edit, $url_options, $options_func='BasicOptionsListModel', $where_sql='', $arr_fields_form=array(), $type_list='Basic');

			echo '<p><a href="'.make_fancy_url($base_url, 'admin', 'index', 'edit_son_blogs', array('IdModule' => $_GET['IdModule'])).'">'.$lang['blog']['go_back_index_blog'].'</a></p>';

		break;
		
		case 3:
		
			echo '<h3>'.$lang['blog']['config'].'</h3>';
		
			$admin=new GenerateAdminClass('config_blog');
			
			$admin->url_options=make_fancy_url($base_url, 'admin', 'index', 'blogs', array('IdModule' => $_GET['IdModule'], 'op' => 3));
			
			$admin->url_back=$admin->url_options;
			
			$admin->show_goback=0;
			
			$admin->show_config_mode();
		
		break;

	}


}



?>