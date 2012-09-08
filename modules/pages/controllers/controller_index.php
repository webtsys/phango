<?php

function Index()
{

	global $user_data, $model, $ip, $lang, $config_data, $base_path, $base_url, $cookie_path, $arr_block, $prefix_key, $block_title, $block_content, $block_urls, $block_type, $block_id, $config_data;

	settype($_GET['IdPage'], 'integer');

	if($_GET['IdPage']==0)
	{
		settype($config_data['index_page'], 'integer');
		$_GET['IdPage']=$config_data['index_page'];

	}

	$cont_index_page='';

	$arr_block='';

	$arr_block=select_view(array('pages', 'page_'.$_GET['IdPage']));

	$header_js_pages='';

	//Load page...

	load_model('pages');

	$query=$model['page']->select('where IdPage='.$_GET['IdPage'], array('name', 'text'));

	list($name_page, $text)=webtsys_fetch_row($query);
	
	$name_page=$model['page']->components['name']->show_formatted($name_page);
	$text=$model['page']->components['text']->show_formatted($text);

	if($text!='')
	{
		
		echo load_view(array($name_page, $text), 'content');
	}

	$cont_index_page.=ob_get_contents();

	ob_clean();

	
	$query_prop=$model['property_page']->select('where idpage='.$_GET['IdPage'].' order by order_page ASC', array('property', 'options'));

	while(list($property, $ser_options)=webtsys_fetch_row($query_prop))
	{
		$arr_options=unserialize($ser_options);

		$arr_property=explode('|', $property);
		
		$property_path=$arr_property[0];
		$property=basename($arr_property[1]);
		
		include($base_path.'modules/'.$property_path.'/property/php/'.$property);

	}

	$cont_index_page.=ob_get_contents();

	ob_end_clean();

	echo load_view(array($name_page, $cont_index_page, $block_title, $block_content, $block_urls, $block_type, $block_id, $config_data, $header_js_pages), $arr_block);

}

?>
