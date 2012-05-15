<?php

global $base_url, $lang, $model;

load_model('blog');
load_libraries(array('blog_functions'),$base_path.'modules/blog/libraries/');
load_lang('blog');

settype($_GET['IdBlog'], 'integer');

$sql_father='order by blog_father ASC';

$query=$model['blog']->select($sql_father, array('IdBlog', 'title', 'blog_father', 'accept_comment'));

while(list($idcat, $title, $idfather, $accept_comment)=webtsys_fetch_row($query))
{

	settype($arr_list_father[$idfather], 'array');

	$arr_list_father[$idfather][]=$idcat;
	$arr_cat[$idcat]=$title;
	$arr_accept_comment[$idcat]=$accept_comment;

	$arr_perm[$idcat]=0;

}

$first_url[$_GET['IdBlog']]='<ul><li><a href="'.make_fancy_url($base_url, 'blog', 'index', 'blogs', array()).'">'.$lang['blog']['principal_category'].'</a><ul>';
$first_url[0]='<ul><li><strong>'.$lang['blog']['principal_category'].'</strong></li><ul>';

echo $first_url[$_GET['IdBlog']];

recursive_list($arr_cat, $arr_list_father, 0, make_fancy_url($base_url, 'blog', 'index', 'blogs', array() ), $arr_perm);

echo '</ul></li></ul>';

?>