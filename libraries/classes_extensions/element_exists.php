<?php

function element_exists_class($class, $idrow)
{

	settype($idrow, 'integer');
	
	$num_elements=$class->select_count('where '.$class->idmodel.'=\''.$idrow.'\'', $class->idmodel);
	
	return $num_elements;

}

?>