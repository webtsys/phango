<?php

function Search()
{

	global $user_data, $model, $ip, $lang, $config_data, $base_path, $base_url, $cookie_path, $arr_block, $prefix_key, $block_title, $block_content, $block_urls, $block_type, $block_id, $config_data;

	$cont_index='';

	$arr_block='';

	$arr_block=select_view(array('blog'));

	settype($_GET['tag'], 'integer');

	load_model('blog');
	load_libraries(array('form_date', 'form_time', 'pages'));
	load_lang('blog');

	echo '<h1>'.$lang['blog']['search_results'].'</h1>';

	$arr_idpage_blog=array(0);

	$num_post=20;

	$text=@form_text($_GET['search_text']);

	//Search the phrase, slice phrase in words, order in alphabetic order, quit words with only one or to symbols.

	$text=strtolower($text);

	$arr_text=explode(" ",$text);

	$arr_final=array();

	$loc_array[]="IF(LOCATE(\"$text\",text),2,0)+IF(LOCATE(\"$text\",title),2,0)";

	foreach($arr_text as $value)
	{
		$arr_final[]=" title like \"%$value%\" or text like \"%$value%\""; 
		$loc_array[]="IF(LOCATE(\"$value\",text),1,0)+IF(LOCATE(\"$value\",title),1,0)";
	}

	$location="(".implode("+",$loc_array).")";

	$text_final=implode(" or ",$arr_final); 

	$sql_text="and (title like \"% $text%\" or title like \"%$text %\" or text like \"%$text %\" or text like \"% $text %\" or  text like \"% $text%\"  or  text like \"$text%\" or  text like \"%$text\"  or ".$text_final.")";

	//Begin querys

	$select_count_post=$sql_text;
	
	$total_posts=$model['page_blog']->select_count('WHERE 1=1 '.$select_count_post, 'IdPage_blog');

	if($total_posts>0)
	{

		$select_post=$sql_text;

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

		$query=webtsys_query('select page_blog.*, user.private_nick from page_blog, user where user.Iduser=page_blog.author '.$select_post.' order by date DESC, '.$location.' ASC limit '.$_GET['begin_page'].', '.$num_post);
		
		while($result=webtsys_fetch_array($query))
		{
		
			$result['date']=form_date( $result['date'], $user_data['format_date'] , $user_data['format_time']).' | '.form_time( $result['date'], $user_data['format_time'], $user_data['ampm'] );	
			$result['text'].='<br /><br />';
			
			$tags=$lang['blog']['no_tags'];

			if(isset($arr_tags[$result['IdPage_blog']]))
			{

				$tags=implode(', ', $arr_tags[$result['IdPage_blog']]);

			}
		
			echo load_view(array($result['author'], $result['IdPage_blog'], $result['private_nick'], $result['title'], $result['entrance'], $result['num_comments'], $result['date'], $lang['blog'], $tags), 'blog/postblog');
		
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


	echo load_view(array($lang['blog']['search_blog'], $cont_index, $block_title, $block_content, $block_urls, $block_type, $block_id, $config_data, ''), $arr_block);

}

?>
