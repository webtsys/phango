<?php

global $lang;

$select_page[]=$lang['contact_admin']['contact_admin_name'];
$select_page[]='optgroup';

load_model('contact');

$myquery=$model['contact']->select('', array('IdContact', 'name') );

while(list($id, $name)=webtsys_fetch_row($myquery))
{
	$name=ucfirst($model['contact']->components['name']->show_formatted($name));
	$select_page[]=ucfirst($name);
	$select_page[]=make_fancy_url( $base_url, 'contact', 'index', $name, array('IdContact' => $id) );
	$select_module[]=$name;

}

$select_page[]='';
$select_page[]='end_optgroup';

?>
