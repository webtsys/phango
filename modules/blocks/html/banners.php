<?php

global $model;

settype($id, 'integer');

$query_block=$model['banners']->select('where position_banner="blocks'.$id.'"', array('IdBanners', 'content') );

while(list($idbanner, $content_banner)=webtsys_fetch_row($query_block))
{

	echo $content_banner;

}


?>
