<?php
global $base_url;

$query=webtsys_query('select * from tag_blog order by tag');

while(list($idtag, $tag_blog)=webtsys_fetch_row($query))
{

	$arr_tag[$idtag]=array(0 => $tag_blog, 1 => 8);

}

settype($arr_tag, 'array');

$query=webtsys_query('select idtag from page_tag_blog');

while(list($idtag)=webtsys_fetch_row($query))
{

	$arr_tag[$idtag][1]++;

	if($arr_tag[$idtag][1]>24)
	{

		$arr_tag[$idtag][1]=24;

	}

}

echo '<div align="center">';

foreach($arr_tag as $idtag => $arr_font_tag)
{

	$url_tags=make_fancy_url($base_url, 'blog', 'search_tags', 'search_tags', array('tag' => $idtag) );

	echo '<a href="'.$url_tags.'" style="font-size:'.$arr_font_tag[1].'px">'.$arr_font_tag[0].'</a> ';

}

echo '</div>';

?>
