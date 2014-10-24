<?php

function redirect_webtsys($direction,$l_text,$text,$ifno, $arr_block='')
{

	global $config_data, $base_path,$lang_user, $code_banner, $block_title, $block_content, $block_urls, $block_type, $block_id, $arr_block;

	//include_once($base_path."themes/".$config_data['dir_theme']."/$arr_block.php");

	$redirect="<meta http-equiv=\"refresh\" content=\"2;URL=$direction\">";

	ob_start();
	
	if($arr_block=='')
	{
	
		$arr_block=select_view(array());
	
		$arr_block='/none';
	
	}

	echo load_view(array($config_data['portal_name'].' / '.$l_text,'<p>'.$text.'<br><a href="'. $direction.'">'.$ifno.'</a>'), 'content');

	$cont_index=ob_get_contents();

	ob_end_clean();
	
	echo load_view(array($config_data['portal_name'].' / '.$l_text, $cont_index, $block_title, $block_content, $block_urls, $block_type, $block_id, $config_data, $redirect), $arr_block);

	die();

}

function simple_redirect($url_return, $l_text, $text, $ifno, $content_view='content')
{

	global $config_data, $arr_cache_header;
	
	$arr_cache_header[]="<meta http-equiv=\"refresh\" content=\"2;URL=$url_return\">";
	
	echo load_view(array($config_data['portal_name'].' / '.$l_text,'<p>'.$text.'<br><a href="'. $url_return.'">'.$ifno.'</a>'), $content_view);

}

?>