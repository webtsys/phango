<?php

function set_admin_link($text_admin, $parameters)
{
	global $base_url;
	
	if(!isset($parameters['IdModule']))
	{
		$parameters['IdModule']=$_GET['IdModule'];
	}
	
	return make_fancy_url($base_url, ADMIN_FOLDER, 'index', $text_admin, $parameters);

}

?>
