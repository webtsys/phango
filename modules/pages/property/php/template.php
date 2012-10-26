<?php

global $arr_cache_func_template, $name_func_template;

load_model('templates');

$query=$model['template']->select('where IdTemplate='.$arr_options['idtemplate'], array('name', 'name_template'));

list($name_template, $template)=webtsys_fetch_row($query);

$template=basename($template);

if($template!='')
{
	if(!isset($arr_cache_func_template[$template]))
	{
		include($base_path.'modules/templates/templates/'.$template);
		
		$arr_cache_func_template[$template]=1;
		
	}
	
	ob_start();
	
	echo $name_func_template($model['template_content'], $arr_options['idtemplate']);
	
	$return_content=ob_get_contents();
	
	ob_end_clean();
	
	echo $return_content;
	
}

?>