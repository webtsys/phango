<?php

load_libraries(array('utilities/parent_utilities'));

function BlogOptionsListModel($url_options, $model_name, $id)
{

	global $lang, $base_url;

	$arr_options=BasicOptionsListModel($url_options, $model_name, $id);

	$arr_options[]='<a href="'.set_admin_link( 'edit_son_blogs', array('IdModule' => $_GET['IdModule'], 'IdBlog' => $id)).'">'.$lang['blog']['edit_son_blogs'].'</a>';
	$arr_options[]='<a href="'.set_admin_link( 'edit_son_blogs', array('IdModule' => $_GET['IdModule'], 'IdBlog' => $id, 'op' => 1)).'">'.$lang['blog']['edit_moderators'].'</a>';
	$arr_options[]='<a href="'.make_fancy_url($base_url, 'blog', 'moderate', 'edit_blogs', array('IdBlog' => $id)).'">'.$lang['blog']['edit_articles'].'</a>';
	
	return $arr_options;

}

function ModerateOptionsListModel($url_options, $model_name, $id)
{

	global $lang, $base_url;

	$arr_options=BasicOptionsListModel($url_options, $model_name, $id);

	$arr_options[]='<a href="'.make_fancy_url($base_url, 'blog', 'moderate', 'add_tags', array('op' => 1, 'IdPage_blog' => $id)).'">'.$lang['blog']['add_tags'].'</a>';

	return $arr_options;

}

function obtain_blogs_from_category()
{

	global $model;

	$arr_blog=array();

	$query=$model['blog']->select( '', array('IdBlog', 'title', 'blog_father') );

	while(list($idblog, $title, $idcategory)=webtsys_fetch_row($query))
	{

		$arr_blog[$idcategory][$idblog]=$title;

	}

	return $arr_blog;

}



function form_comment($data_insert=array(), $check_captcha=1)
{

	global $user_data, $model, $config_data, $lang, $base_url;

	if(count($model['comment_blog']->forms)==0)
	{

		$model['comment_blog']->create_form();

	}
			
	//Send comment if active...

	//subscription form...

	$model['comment_blog']->forms['subscription']=new ModelForm('subscription', 'subscription', 'CheckBoxForm', $lang['blog']['subscript_post'], new BooleanField(), $required=0, $parameters='');

	if($user_data['IdUser']>0)
	{

		$captcha='';
		$save_data='';

		$arr_fields_form=array('text', 'subscription');
		
	}
	else 
	{

		if(isset($_COOKIE['webtsys_savedata']))
		{

			$query=$model['save_data']->select('where token="'.md5($_COOKIE['webtsys_savedata']).'"', array('author', 'email', 'website'));
	
			list($data_insert['author'], $data_insert['email'], $data_insert['website'])=webtsys_fetch_row($query);

		}

		$arr_fields_form=array('author', 'email', 'website', 'text', 'save_data', 'subscription');

		$model['comment_blog']->forms['save_data']=new ModelForm('save_data', 'save_data', 'CheckBoxForm', $lang['blog']['save_data'], new BooleanField(), $required=0, $parameters='');

		if($config_data['captcha_type']!='')
		{

			load_libraries(array('captchas/'.$config_data['captcha_type']));

			$model['comment_blog']->forms['captcha']=new ModelForm('captcha', 'captcha', 'CaptchaForm', $lang['common']['captcha'], new CharField(255), $required=0, $parameters='');

			if($check_captcha==0)
			{

				$model['comment_blog']->forms['captcha']->std_error=$lang['blog']['error_captcha'];

			}

			$arr_fields_form[]='captcha';

		}

	}
	
	$model['comment_blog']->forms['author']->label=$lang['blog']['author'];
	$model['comment_blog']->forms['email']->label=$lang['common']['email'];
	$model['comment_blog']->forms['website']->label=$lang['common']['website'];
	$model['comment_blog']->forms['text']->label=$lang['common']['text'];

	//here formulary for make a comment...

	SetValuesForm($data_insert, $model['comment_blog']->forms, $show_error=1);

	$url_post=make_fancy_url($base_url, 'blog', 'posting', 'posting', array('IdPage_blog' => $_GET['IdPage_blog']));

	echo load_view(array($model['comment_blog']->forms, $arr_fields_form, $url_post, $enctype=''), 'common/forms/updatemodelform');

	echo "<p>".$lang['common']['html_allowed'].": ".$model['comment_blog']->components['text']->show_allowedtags()."</p>";

	$cont_form=ob_get_contents();

	return $cont_form;

}

?>