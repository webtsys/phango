<?php

function make_template($model, $idmodel)
{

	global $base_path, $config_data;

	$pos=0;

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

?>
