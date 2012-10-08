<?php

//include($base_path.'models/banners.php');
load_model('banners');

function load_banner($type_banner)
{

	global $model;

	$where_banner='';
	$content_banner='';
	
	settype($type_banner, 'string');
	
	$type_banner=form_text($type_banner);
	
	$where_banner='where position_banner="'.$type_banner.'"';
	
	$num_banners_queue=$model['banners']->select_count($where_banner,'IdBanners');
	
	if($num_banners_queue==0)
	{
	
		$where_banner='where position_banner=0';
	
	}
	
	$num_banners_queue=$model['banners']->select_count($where_banner.' and check_banner=0','IdBanners');

	if($num_banners_queue==0)
	{

		$query=$model['banners']->update(array('check_banner' => 0),$where_banner);

	}

	$query=$model['banners']->select($where_banner.' and check_banner=0', array('IdBanners', 'content'));
	//echo $where_banner.' and check_banner=0';
	//echo mysql_error();
	list($idbanner, $content_banner)=webtsys_fetch_row($query);
	
	$query=$model['banners']->update(array('check_banner' => 1), 'where IdBanners='.$idbanner);
	
	return $content_banner;

}

?>
