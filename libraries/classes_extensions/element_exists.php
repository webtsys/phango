<?php

function element_exists_class($class, $idrow, $field_search='')
{

	if($field_search=='')
	{
	
		$field_search=$class->idmodel;
	
	}

	settype($idrow, 'integer');
	
	$num_elements=$class->select_count('where '.$field_search.'=\''.$idrow.'\'', $class->idmodel);
	
	return $num_elements;

}

?>