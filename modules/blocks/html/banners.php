<?php

global $model;

$query_block=$model['banners']->select('where position_banner="blocks'.$result['IdBlocks'].'"', array('IdBanners', 'content') );
echo mysql_error();
while(list($idbanner, $content_banner)=webtsys_fetch_row($query_block))
{

	echo '<p>'.$content_banner.'<p>';

}


?>
