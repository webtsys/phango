<?php

function TextAreaBBPostForm($name="", $class='', $value='')
{

	load_libraries(array('forms/textareabb'));
		
	 return '<p>'.TextAreaBBForm($name, $class, $value, 'comment').'</p>';

}

function TextAreaBBPostFormSet($post, $value)
{
	
	return $value;

}

?>
