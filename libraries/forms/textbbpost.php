<?php

function TextAreaBBPostForm($name="", $class='', $value='')
{

	load_libraries(array('forms/textareabb'));
		
	 return TextAreaBBForm($name, $class, $value, 'comment');

}

function TextAreaBBPostFormSet($post, $value)
{
	
	return $value;

}

?>
