<?php
ob_start();

global $script_base_controller;

$script_base_controller_orig=$script_base_controller;

$script_base_controller='blog';

$cont_index_blog='';

load_model('blog');
load_libraries(array('form_date', 'form_time', 'pages'));
load_lang('blog');

settype($arr_options['idblog'], 'integer');
settype($arr_options['num_posts'], 'integer');

$select_blog='where IdBlog='.$arr_options['idblog'];

$select_count_post='where idblog='.$arr_options['idblog'];

$select_post='and page_blog.idblog='.$arr_options['idblog'];

if($arr_options['idblog']==0)
{

	$select_blog='';

	$select_count_post='';

	$select_post='';

}

//id pages

$arr_id_page=array(0);

$query=$model['page_blog']->select($select_count_post.' order by date DESC limit '.$_GET['begin_page'].', '.$arr_options['num_posts'], array('IdPage_blog'));

while(list($idpage_blog)=webtsys_fetch_row($query))
{

	$arr_id_page[]=$idpage_blog;
	
}

//Obtain tags...
$arr_tags=array();
$num_tags=0;

$query=webtsys_query('select tag_blog.tag,page_tag_blog.idtag, page_tag_blog.idpage_blog from tag_blog,page_tag_blog where idpage_blog IN ('.implode(', ', $arr_id_page).') and page_tag_blog.idtag=tag_blog.IdTag_blog');

while(list($tag, $idtag, $idpage_blog_tag)=webtsys_fetch_row($query))
{
	//http://localhost/phangofm/index.php/blog/show/search_tags/seach_tags/tag/linux
	$arr_tags[$idpage_blog_tag][]='<a href="'.make_fancy_url($base_url, 'blog', 'search_tags', $tag, array('tag' => $idtag) ).'">'.$tag.'</a>';

	$num_tags++;

}

//Obtain blog

$query=$model['blog']->select($select_blog, array('title'));

list($name_page)=webtsys_fetch_row($query);

//Obtain total posts of this blog...

$total_posts=$model['page_blog']->select_count($select_count_post, 'IdPage_blog');

if($total_posts>0)
{

	$cont_index_blog.=ob_get_contents();
	
	ob_end_clean();

	//Select post to show

	$query=webtsys_query('select page_blog.*, user.private_nick from page_blog, user where user.Iduser=page_blog.author '.$select_post.' order by date DESC limit '.$_GET['begin_page'].', '.$arr_options['num_posts']);
	
	while($result=webtsys_fetch_array($query))
	{
	
		$result['date']=form_date( $result['date'], $user_data['format_date'] , $user_data['format_time']).' | '.form_time( $result['date'], $user_data['format_time'], $user_data['ampm'] );	
		
		$tags=$lang['blog']['no_tags'];

		if(isset($arr_tags[$result['IdPage_blog']]))
		{

			$tags=implode(', ', $arr_tags[$result['IdPage_blog']]);

		}
	
		echo load_view(array($result['author'], $result['IdPage_blog'], $result['private_nick'], $result['title'], $result['entrance'], $result['num_comments'], $result['date'], $tags), 'blog/postblog');
	
	}
	
	$post_pages=pages( $_GET['begin_page'], $total_posts, $arr_options['num_posts'], 'index.php?IdBlog='.$arr_options['idblog']);
	
	echo '<p class="navigation">'.$lang['common']['more'].': '.$post_pages.'</p>';

}
else
{
	//Error: without any content...

	echo load_view(array($lang['blog']['no_contents'], $lang['blog']['wait_admin']), 'content');

}

$script_base_controller_orig=$script_base_controller;

?>