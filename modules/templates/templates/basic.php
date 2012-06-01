<?php


function make_template_basic($model, $idmodel)
{

	global $base_path, $config_data;

	$query=$model->select('where idtemplate='.$idmodel.' order by position ASC', array('name', 'text'));

	while(list($name, $text)=webtsys_fetch_row($query))
	{
		
		$name=$model->components['name']->show_formatted($name);
		$text=$model->components['text']->show_formatted($text);

		echo load_view(array($name, $text), 'content');	

	}

}

$name_func_template='make_template_basic';

?>
