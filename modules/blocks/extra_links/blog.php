<?php


$select_page[]=$lang['blog_admin']['blog_sections']; 
$select_page[]='optgroup';

//include_once($base_path.'models/blog.php');
load_model('blog');

$select_page[]=ucfirst($lang['blog_admin']['principal_page_blog']);
$select_page[]=make_fancy_url( $base_url, 'blog', 'index', $lang['blog_admin']['principal_page_blog'], array('IdBlog' => 0) );
$select_module[]=$lang['blog_admin']['principal_page_blog'];

$myquery=$model['blog']->select('order by title ASC', array('IdBlog', 'title') );;

$arr_select_page=array();

while(list($id, $title)=webtsys_fetch_row($myquery))
{
	
	$select_page[]=ucfirst($title);
	$select_page[]=make_fancy_url( $base_url, 'blog', 'index', $title, array('IdBlog' => $id) );
	$select_module[]=$title;

	$arr_select_page[]=ucfirst($title);
	$arr_select_page[]=make_fancy_url( $base_url, 'blog', 'rss2', $title, array('IdBlog' => $id) );

}


$select_page[]='';
$select_page[]='end_optgroup';

$select_page[]=$lang['blog_admin']['blog_rss']; 
$select_page[]='optgroup';

$select_page=array_merge($select_page, $arr_select_page);

$select_page[]='';
$select_page[]='end_optgroup';


?>
