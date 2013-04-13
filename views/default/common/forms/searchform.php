<?php

function SearchFormView($arr_search_field, $arr_order_field, $arr_order_select, $url_options)
{

	global $lang;

	$form_search='<form method="get" action="'.add_extra_fancy_url( $url_options, array() ).'">';
	$form_search.=set_csrf_key();
	$form_search.=$lang['common']['order_by'].': '.SelectForm('order_field', '', $arr_order_field).' '.$lang['common']['in_order'].': '.SelectForm('order_desc', '', $arr_order_select);

	$arr_order_field[0]=$_GET['search_field'];

	$form_search.='<p>'.$lang['common']['search'].': '.TextForm('search_word', '', $_GET['search_word']).' '.$lang['common']['search_by'].': '.SelectForm('search_field', '', $arr_search_field).'</p><p><input type="submit" value="'.$lang['common']['send'].'"/> <input type="button" value="'.$lang['common']['reset'].'" onclick="javascript:location.href=\''.$url_options.'\'"/>';

	$form_search.='</form></p>';
	
	echo load_view(array($lang['common']['order_and_search'], $form_search), 'content');

}

?>