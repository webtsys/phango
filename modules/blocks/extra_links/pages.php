<?php

$select_page[]=$lang['pages_admin']['pages'];
$select_page[]='optgroup';

load_model('pages');

$myquery=$model['page']->select('', array('IdPage', 'name') );

while(list($id, $name)=webtsys_fetch_row($myquery))
{
	$name=ucfirst($model['page']->components['name']->show_formatted($name));
	$select_page[]=ucfirst($name);
	$select_page[]=make_fancy_url( $base_url, 'pages', 'index', $name, array('IdPage' => $id) );
	$select_module[]=$name;

}

$select_page[]='';
$select_page[]='end_optgroup';

?>
