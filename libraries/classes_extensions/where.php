<?php

/**
*
* A simple extension for create where strings with checking.
*
*/

function where_method_class($class, $arr_where)
{
	
	foreach($arr_where as $type => $where)
	{
	
		//Checking
	
		foreach($where as $field => $value)
		{
		
			$where[$field]=$class->components[$field]->check($value);
		
		}
	
		switch($type)
		{
		
			//Default is and
			default:
			
				
			
			break;
			
			case 'OR':
			
				
			
			break;
			
			case 'IN':
			
				
			
			break;
			
			case 'NOT IN':
			
				
			
			break;
			
			case 'LIKE':
			
				
			
			break;
			
			case 'GROUP BY':
			
				
			
			break;
			
			case 'ORDER BY':
			
				
			
			break;
		
		}
	
	}
	
}

?>