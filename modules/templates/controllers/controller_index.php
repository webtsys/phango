<?php

function Index()
{

	global $user_data, $model, $ip, $lang, $config_data, $base_path, $base_url, $cookie_path, $arr_block, $prefix_key, $block_title, $block_content, $block_urls, $block_type, $block_id;

	$cont_index='';

	$arr_block='';

	$arr_block=select_view(array('template'));

	//Load page...

	load_model('templates');

	settype($_GET['IdTemplate'], 'integer');

	$query=$model['template']->select('where IdTemplate='.$_GET['IdTemplate'], array('name', 'name_template'));

	list($name_page, $template)=webtsys_fetch_row($query);

	$name_page=$model['template']->components['name']->show_formatted($name_page);

	$template=basename($template);

	$cont_index.=ob_get_contents();

	ob_clean();

	if($template!='')
	{

		include($base_path.'modules/templates/templates/'.$template);

		make_template($model['template_content'], $_GET['IdTemplate']);

	}

	$cont_index.=ob_get_contents();

	ob_end_clean();

	echo load_view(array($name_page, $cont_index, $block_title, $block_content, $block_urls, $block_type, $block_id, $config_data, ''), $arr_block);

}

?>
