<?php


$select_page[]=$lang['templates_admin']['templates'];
$select_page[]='optgroup';

load_model('templates');

$myquery=$model['template']->select('order by name ASC', array('IdTemplate', 'name') );;

while(list($id, $title)=webtsys_fetch_row($myquery))
{
	
	$title=$model['template']->components['name']->show_formatted($title);
	$select_page[]=ucfirst($title);
	$select_page[]=make_fancy_url($base_url, 'templates', 'index', $title, array('IdTemplate' => $id));
	$select_module[]=$title;

}

$select_page[]='';
$select_page[]='end_optgroup';

?>
