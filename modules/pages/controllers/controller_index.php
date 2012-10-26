<?php

function Index()
{

	global $user_data, $model, $ip, $lang, $config_data, $base_path, $base_url, $cookie_path, $arr_block, $prefix_key, $block_title, $block_content, $block_urls, $block_type, $block_id, $config_data;

	ob_start();
	
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

	ob_end_clean();
      
	ob_start();
	
	$arr_arr_options=array();
	$arr_property_path=array();
	$arr_property=array();
  
	$query_prop=$model['property_page']->select('where idpage='.$_GET['IdPage'].' order by order_page ASC', array('IdProperty_page', 'property', 'options'));

	while(list($idprop, $property, $ser_options)=webtsys_fetch_row($query_prop))
	{
		$arr_arr_options[$idprop]=unserialize($ser_options);
		
		$arr_property_check=explode('|', $property);
		
		$arr_property_path[$idprop]=$arr_property_check[0];
		
		$arr_property[$idprop]=basename($arr_property_check[1]);
		

	}
	
	foreach($arr_arr_options as $idprop => $arr_options)
	{
	
		$property_path=$arr_property_path[$idprop];
		$property=$arr_property[$idprop];
	
		include_once($base_path.'modules/'.$property_path.'/property/php/'.$property);
		
		$func_property=str_replace('.php', '', $property);
		
		if(function_exists($func_property))
		{
			echo $func_property($arr_options);
		}
		
	}

	$cont_index_page.=ob_get_contents();

	ob_end_clean();

	echo load_view(array($name_page, $cont_index_page, $block_title, $block_content, $block_urls, $block_type, $block_id, $config_data, $header_js_pages), $arr_block);

}

?>
