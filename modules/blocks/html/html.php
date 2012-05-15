<?php
	if(!defined('PAGE'))
	{
	
		die();
	
	}

	global $result, $base_path, $model;
	
	#include_once($base_path.'/modules/blocks/models/html.php');
	load_model('blocks/html');
	
	$code='';
	
	settype($id, 'integer');
	
	$query=$model['html_block']->select('where idblock='.$id, array('code') );
	
	list($code)=webtsys_fetch_row($query);

	?>
	<?php echo $code; ?>
	<?php

?>
