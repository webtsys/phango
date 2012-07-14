<?php

function Index()
{

	global $user_data, $model, $ip, $lang, $config_data, $base_path, $base_url, $cookie_path, $arr_block, $prefix_key, $block_title, $block_content, $block_urls, $block_type, $block_id, $config_data;

	$cont_index='';

	$arr_block='';

	$arr_block=select_view(array('blog'));

	load_model('blog');
	load_libraries(array('form_date', 'form_time', 'pages', 'utilities/hierarchy_links'));

	load_lang('blog');

	settype($_GET['IdBlog'], 'integer');

	$select_blog='where IdBlog='.$_GET['IdBlog'];

	$select_count_post='where idblog='.$_GET['IdBlog'];

	$select_post='and page_blog.idblog='.$_GET['IdBlog'];

	if($_GET['IdBlog']==0)
	{

		$select_blog='';

		$select_count_post='';

		$select_post='';

	}
	
	//Begin, put links..
	
	$arr_hierarchy_links=hierarchy_links('blog', 'blog_father', 'title', $_GET['IdBlog']);
	
	echo load_view(array($arr_hierarchy_links, 'blog', 'index', 'IdBlog', array(), 0), 'common/utilities/hierarchy_links');
	
	//Oh yeah, now load the posts...

	$query=$model['blog']->select($select_blog, array('title', 'num_post'));

	list($name_page,  $num_post)=webtsys_fetch_row($query);

	$total_posts=$model['page_blog']->select_count($select_count_post, 'IdPage_blog');

	if($total_posts>0)
	{

		$cont_index.=ob_get_contents();
		
		ob_clean();

		//Obtain ids...

		$arr_id_page=array();

		$query=$model['page_blog']->select($select_count_post.' order by date DESC limit '.$_GET['begin_page'].', '.$num_post, array('IdPage_blog'));
		
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

			$arr_tags[$idpage_blog_tag][]='<a href="'.make_fancy_url($base_url, 'blog', 'search_tags', $tag, array('tag' => $idtag) ).'">'.$tag.'</a>';

			$num_tags++;

		}

		//Obtain last posts...

		$query=webtsys_query('select page_blog.*, user.private_nick from page_blog, user where user.Iduser=page_blog.author '.$select_post.' order by date DESC limit '.$_GET['begin_page'].', '.$num_post);
		
		while($result=webtsys_fetch_array($query))
		{
			
			$result['date']=form_date( $result['date'], $user_data['format_date'] , $user_data['format_time']).' | '.form_time( $result['date'], $user_data['format_time'], $user_data['ampm'] );	
			//$result['entrance'].='<br /><br />';
			
			$tags=$lang['blog']['no_tags'];

			if(isset($arr_tags[$result['IdPage_blog']]))
			{

				$tags=implode(', ', $arr_tags[$result['IdPage_blog']]);

			}
		
			echo load_view(array($result['author'], $result['IdPage_blog'], $result['private_nick'], $result['title'], $result['entrance'], $result['num_comments'], $result['date'], $tags), 'blog/postblog');
		
		}
		
		$post_pages=pages( $_GET['begin_page'], $total_posts, $num_post, 'index.php?IdBlog='.$_GET['IdBlog']);
		
		echo '<p class="navigation">'.$lang['common']['more'].': '.$post_pages.'</p>';

	}
	else
	{

		echo load_view(array($lang['blog']['no_contents'], $lang['blog']['wait_admin']), 'content');

	}

	$cont_index.=ob_get_contents();



	ob_end_clean();

	echo load_view(array($name_page, $cont_index, $block_title, $block_content, $block_urls, $block_type, $block_id, $config_data, ''), $arr_block);

}

?>
