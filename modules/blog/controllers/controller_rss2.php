<?php

function Rss2()
{

	global $user_data, $model, $ip, $lang, $config_data, $base_path, $base_url, $cookie_path, $arr_block, $prefix_key, $block_title, $block_content, $block_urls, $block_type, $block_id, $config_data;

	$cont_index='';

	$arr_block='';

	$arr_block=select_view(array('blog'));

	load_model('blog');
	load_libraries(array('form_date', 'form_time'));
	load_lang('blog');

	settype($_GET['IdCategory_blog'], 'integer');
	settype($_GET['IdBlog'], 'integer');

	$where_sql='';

	if($_GET['IdCategory_blog']>0)
	{

		$arr_blog=array(0);

		$query=$model['category_blog']->select('where IdCategory_blog='.$_GET['IdCategory_blog'], array('title') );

		list($title_blog)=webtsys_fetch_row($query);

		$query=$model['blog']->select('where category='.$_GET['IdCategory_blog'], array('IdBlog'));

		while(list($idblog)=webtsys_fetch_row($query))
		{

			$arr_blog[]=$idblog;

		}

		$where_sql='where idblog IN ('.implode(', ', $arr_blog).') order by date DESC limit 20';

		

	}
	else
	if( $_GET['IdBlog']>0 )
	{

		$where_sql='where idblog='.$_GET['IdBlog'].' order by date DESC limit 20';

		$query=$model['blog']->select( 'where IdBlog='.$_GET['IdBlog'], array('title') );

		list($title_blog)=webtsys_fetch_row($query);

	}

	$query=$model['page_blog']->select($where_sql, array('date'));

	list($last_time)=webtsys_fetch_row($query);

	echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

	?>
	<!-- generator="Web-T-syS CMS" -->
	<rss version="2.0">
		<channel>
			<title><?php echo html_entity_decode($config_data['portal_name'], ENT_COMPAT, 'UTF-8'); ?></title>
			<description><?php echo html_entity_decode($config_data['meta_description'], ENT_COMPAT, 'UTF-8'); ?></description>
			<link><?php echo $base_url; ?></link>
			<lastBuildDate><?php echo date ( "D, j M Y H:i:s O", $last_time ); ?></lastBuildDate>

			<generator>Web-T-syS Phango</generator>
	<?php

	$query=$model['page_blog']->select($where_sql, array('IdPage_blog', 'title', 'entrance'));

	while(list($idpage, $title_page, $entrance)=webtsys_fetch_row($query))
	{

		?>
			<item>
				<title><?php echo html_entity_decode($title_page, ENT_QUOTES, 'UTF-8'); ?></title>
					<link><?php echo make_fancy_url($base_url, 'blog', 'post', 'title_page', array('IdPage_blog' => $idpage)); ?></link>
						<description>
							<?php 
								echo "<![CDATA[".$entrance."]]>"; 

							?>
						</description>
					<category><?php echo $title_blog; ?></category>
			</item>
		<?php
	}

	?>
			
		</channel>
	</rss>
			
	<?php

	$content=ob_get_contents();

	ob_end_clean();

	echo trim($content);
}

?>
