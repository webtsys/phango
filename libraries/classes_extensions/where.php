<?php

/**
*
* A simple extension for create where strings with checking.
*
*/

function where_method_class($class, $arr_where, $initial_sql='WHERE', $parenthesis=0)
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
		
		$arr_par[0]=array('open' => '', 'close' => '');
		$arr_par[1]=array('open' => '(', 'close' => ')');
	
		switch($type)
		{
		
			//Default is and
			default:
			case 'AND':
			
				foreach($where as $field => $value)
				{
				
					$field_select=set_safe_name_field($class, $field);
				
					$arr_sql[]=$field_select.'=\''.$class->components[$field]->check($value).'\'';
				
				}
				
				$initial_sql.=' '.$arr_par[$parenthesis]['open'].implode(' AND ', $arr_sql).$arr_par[$parenthesis]['close'];
			
			break;
			
			case 'OR':
			
				foreach($where as $field => $value)
				{
				
					$field_select=set_safe_name_field($class, $field);
				
					$arr_sql[]=$field_select.'=\''.$class->components[$field]->check($value).'\'';
				
				}
				
				$initial_sql.=' '.$arr_par[$parenthesis]['open'].implode(' OR ', $arr_sql).$arr_par[$parenthesis]['close'];
			
			break;
			
			case 'IN_AND':
			case 'IN_OR':
			case 'NOT_IN_AND':
			case 'NOT_IN_OR':
			
				$arr_in=array();
				
				$arr_key_in['IN_AND']='IN';
				$arr_key_in['IN_OR']='IN';
				$arr_key_in['NOT_IN_AND']='NOT IN';
				$arr_key_in['NOT_IN_OR']='NOT IN';
			
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
					
					$field_select=set_safe_name_field($class, $field);
					
					$arr_in[]=$field_select.' '.$arr_key_in[$type].' (\''.implode('\', \'', $arr_value).'\')';
				
				}
				
				$arr_union['IN_AND']='AND';
				$arr_union['IN_OR']='OR';
				$arr_union['NOT_IN_AND']='AND';
				$arr_union['NOT_IN_OR']='OR';
				
				$initial_sql.=' '.$arr_par[$parenthesis]['open'].implode(' '.$arr_union[$type].' ', $arr_in).$arr_par[$parenthesis]['close'];
			
			break;
			
			case 'LIKE_OR':
			
				foreach($where as $field => $value)
				{
				
					$field_select=set_safe_name_field($class, $field);
				
					$arr_sql[]=' '.$field_select.' LIKE \'%'.$class->components[$field]->check($value).'%\'';
				
				}
				
				$initial_sql.=' '.$arr_par[$parenthesis]['open'].implode(' OR ', $arr_sql).$arr_par[$parenthesis]['close'];
			
			break;
			
			case 'LIKE_AND':
			
				foreach($where as $field => $value)
				{
				
					$field_select=set_safe_name_field($class, $field);
				
					$arr_sql[]=$field_select.' LIKE \'%'.$class->components[$field]->check($value).'%\'';
				
				}
				
				$initial_sql.=' '.$arr_par[$parenthesis]['open'].implode(' AND ', $arr_sql).$arr_par[$parenthesis]['close'];
			
			break;
		
		}
	
	}
	
	return $initial_sql;
	
}

function set_safe_name_field($class, $field)
{

	/*if(get_class($class->components[$field])=='ForeignKeyField')
	{
	
		
	
	}*/
	if(strpos($field, '.')!=='false')
	{
	
		$field_select='`'.$class->components[$field]->related_model.'`.`'.$field.'`';
	
	}
	else
	{
	
		$field_select='`'.$class->name.'`.`'.$field.'`';
		
	}
	
	return $field_select;

}

?>