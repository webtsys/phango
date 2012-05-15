<?php

function menu_selected($activation, $arr_op, $type=0)
{

	switch($type)
	{

		case 0:

			$set_ul_open='set_ul';
			$set_ul_close='set_ul_end';
			$set_li='set_li';
			$set_implode_li='implode_li';

		break;

		case 1:

			$set_ul_open='no_set_ul';
			$set_ul_close='no_set_ul_end';
			$set_li='no_set_li';
			$set_implode_li='no_implode_li';

		break;

	}

	$arr_li=array();
	
	echo '<p>';

	echo $set_ul_open();

	foreach($arr_op as $op => $arr_value)
	{

		$arr_select[$op]=link_menu($arr_value['link'], $arr_value['text']);

		$arr_select[$activation]=select_menu($arr_value['link'], $arr_value['text']);

		$arr_li[]=$set_li($arr_select[$op]);

	}

	echo $set_implode_li($arr_li);

	echo $set_ul_close();
	
	echo '</p>';

}

function link_menu($link, $text_link)
{

	return '<a href="'.$link.'">'.$text_link.'</a> ';

}

function select_menu($link, $text_link)
{

	return $text_link;

}

function set_ul()
{

	return "<ul>\n";

}

function set_ul_end()
{

	return "</ul>\n";

}

function set_li($link)
{

	return '<li>'.$link."</li>\n";

}

function implode_li($arr_links)
{

	echo implode('', $arr_links);

}

function no_set_ul()
{

	return '';

}

function no_set_ul_end()
{

	return '';

}

function no_set_li($link)
{

	return $link."\n";

}

function no_implode_li($arr_links)
{

	echo implode(' - ', $arr_links);

}

?>
