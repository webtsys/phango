<?php

function make_template_mixed($model, $idmodel)
{

	global $base_path, $config_data;

	$pos=0;

	load_libraries_views('templates/two_template', array('contentleft', 'contentright', 'contentall'));

	$query=$model->select('where idtemplate='.$idmodel.' order by position ASC', array('name', 'text'));

	while(list($name, $text)=webtsys_fetch_row($query))
	{
		
		$name=$model->components['name']->show_formatted($name);
		$text=$model->components['text']->show_formatted($text);

		if($pos==0)
		{

			echo load_view(array($name, $text), 'contentleft');	

			$pos=1;
	
		}
		else
		if($pos==1)
		{

			echo load_view(array($name, $text), 'contentright');	

			$pos=2;

		}
		else
		if($pos==2)
		{

			echo load_view(array($name, $text), 'contentall');	

			$pos=0;
	
		}

	}

}

$name_func_template='make_template_mixed';

?>
