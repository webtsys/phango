<?php

function make_template($model, $idmodel)
{

	global $base_path, $config_data;

	$pos=0;

	load_libraries_views('templates/two_template', array('contentleft', 'contentright'));

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
		{

			echo load_view(array($name, $text), 'contentright');	

			$pos=0;

		}

	}

}

?>
