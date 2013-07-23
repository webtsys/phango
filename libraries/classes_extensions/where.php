<?php

/**
*
* A simple extension for create where strings with checking.
*
*/

function where_method_class($class, $arr_where, $initial_sql='WHERE')
{
	
	foreach($arr_where as $type => $where)
	{
	
		/*//Checking
	
		foreach($where as $field => $value)
		{
		
			$where[$field]=$class->components[$field]->check($value);
		
		}*/
		
		$arr_sql=array();
		
		$sql_string='';
	
		switch($type)
		{
		
			//Default is and
			default:
			
				foreach($where as $field => $value)
				{
				
					$arr_sql[]='`'.$class->name.'`'.'.'.'`'.$field.'`'.'=\''.$class->components[$field]->check($value).'\'';
				
				}
				
				$initial_sql.=' '.implode(' AND ', $arr_sql);
			
			break;
			
			case 'OR':
			
				foreach($where as $field => $value)
				{
				
					$arr_sql[]='`'.$class->name.'`'.'.'.'`'.$field.'`'.'=\''.$class->components[$field]->check($value).'\'';
				
				}
				
				$initial_sql.=' '.implode(' OR ', $arr_sql);
			
			break;
			
			case 'IN':
			
				/*foreach($where as $field => $value)
				{
					
					$where[$field]=$class->components[$field]->check($value);
				
				}
				
				$initial_sql.=' '.'`'.$class->name.'`'.'.'.'`'.$field.'`'.' IN (\''.implode('\', ', $where).'\')';*/
				
				//$where=array( 'IN' => array( 'IdUser' => array(1, 2, 3) ) );
				
				foreach($where as $field => $arr_value)
				{
					
					//$where[$field]=$class->components[$field]->check($value);
					
					foreach($arr_value as $key_value => $value)
					{
					
						$arr_value[$key_value]=$class->components[$field]->check($value);
					
					}
				
				}
			
			break;
			
			case 'NOT IN':
			
				foreach($where as $field => $value)
				{
				
					//$arr_sql[]='`'.$class->name.'`'.'.'.'`'.$field.'`'.' IN \''.$class->components[$field]->check($value).'\'';
					
					$where[$field]=$class->components[$field]->check($value);
				
				}
				
				$initial_sql.=' '.'`'.$class->name.'`'.'.'.'`'.$field.'`'.' NOT IN (\''.implode('\', ', $where).'\')';
			
			break;
			
			case 'LIKE_OR':
			
				foreach($where as $field => $value)
				{
				
					$arr_sql[]=' '.'`'.$class->name.'`'.'.'.'`'.$field.'`'.' LIKE \'%'.$class->components[$field]->check($value).'%\'';
				
				}
				
				$initial_sql.=' '.implode(' OR ', $arr_sql);
			
			break;
			
			case 'LIKE_AND':
			
				foreach($where as $field => $value)
				{
				
					$arr_sql[]='`'.$class->name.'`'.'.'.'`'.$field.'`'.' LIKE \'%'.$class->components[$field]->check($value).'%\'';
				
				}
				
				$initial_sql.=' '.implode(' AND ', $arr_sql);
			
			break;
		
		}
	
	}
	
	return $initial_sql;
	
}

?>