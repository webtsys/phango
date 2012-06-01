<?php
ob_start();

load_model('templates');

$query=$model['template']->select('where IdTemplate='.$arr_options['idtemplate'], array('name', 'name_template'));

list($name_template, $template)=webtsys_fetch_row($query);

$template=basename($template);

$name_func_template='';

$cont_index.=ob_get_contents();

ob_clean();

if($template!='')
{

	include($base_path.'modules/templates/templates/'.$template);

	$name_func_template($model['template_content'], $arr_options['idtemplate']);

}

ob_end_flush();

?>