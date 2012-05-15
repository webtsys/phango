<?php

function Search_Tags()
{

	global $user_data, $model, $ip, $lang, $config_data, $base_path, $base_url, $cookie_path, $arr_block, $prefix_key, $block_title, $block_content, $block_urls, $block_type, $block_id, $config_data;

	$cont_index='';

	$arr_block='';

	$arr_block=select_view(array('blog'));

	load_model('blog');
	load_libraries(array('form_date', 'form_time', 'pages'));
	load_lang('blog');

	//Load page...
	
	settype($_GET['tag'], 'integer');

	echo '<h1>'.$lang['blog']['search_tags_results'].'</h1>';
	
	$arr_idpage_blog=array(0);

	$num_post=20;

	$query=$model['page_tag_blog']->select('where idtag='.$_GET['tag'], array('idpage_blog'));

	while(list($idpage_blog)=webtsys_fetch_row($query))
	{

		$arr_idpage_blog[]=$idpage_blog;

	}

	$select_count_post='where IdPage_blog IN ('.implode(', ', $arr_idpage_blog).')';

	$total_posts=$model['page_blog']->select_count($select_count_post, 'IdPage_blog');
	
	if($total_posts>0)
	{

		$select_post=' and IdPage_blog IN ('.implode(', ', $arr_idpage_blog).')';

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

			$arr_tags[$idpage_blog_tag][]='<a href="search_tags.php?tag='.$idtag.'">'.$tag.'</a>';

			$num_tags++;

		}


		
		if($num_tags==0)
		{

			$tags=$lang['blog']['no_tags'];

		}

		//Obtain last posts...

		$query=webtsys_query('select page_blog.*, user.private_nick from page_blog, user where user.Iduser=page_blog.author '.$select_post.' order by date DESC limit '.$_GET['begin_page'].', '.$num_post);
		
		while($result=webtsys_fetch_array($query))
		{
		
			$result['date']=form_date( $result['date'], $user_data['format_date'] , $user_data['format_time']).' | '.form_time( $result['date'], $user_data['format_time'], $user_data['ampm'] );	
			$result['text'].='<br /><br />';
			
			$tags=$lang['blog']['no_tags'];

			if(isset($arr_tags[$result['IdPage_blog']]))
			{

				$tags=implode(', ', $arr_tags[$result['IdPage_blog']]);

			}

			echo load_view(array($result['author'], $result['IdPage_blog'], $result['private_nick'], $result['title'], $result['entrance'], $result['num_comments'], $result['date'], $tags), 'blog/postblog');
		}
		
		$post_pages=pages( $_GET['begin_page'], $total_posts, $num_post, 'search_tags.php?tag='.$_GET['tag']);
		
		echo '<p>'.$lang['blog']['more'].': '.$post_pages.'</p>';

	}
	else
	{
		
		echo load_view(array($lang['blog']['no_contents'], $lang['blog']['no_search']), 'content');

	}

	$cont_index.=ob_get_contents();



	ob_end_clean();

	echo load_view(array($lang['blog']['search_tags'], $cont_index, $block_title, $block_content, $block_urls, $block_type, $block_id, $config_data, ''), $arr_block);

}

?>
