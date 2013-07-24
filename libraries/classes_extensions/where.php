<?php

/**
*
* A simple extension for create where strings with checking.
*
*/

function where_method_class($class, $arr_where, $initial_sql='WHERE', $parenthesis=0)
{
	
	global $model;
	
	foreach($arr_where as $type => $where)
	{
	
		/*//Checking
	
		foreach($where as $field => $value)
		{
		
			$where[$field]=$model[$model_name]->components[$field_name]->check($value);
		
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
				
					list($field_select, $model_name, $field_name)=set_safe_name_field($class, $field);
					
					$arr_sql[]=$field_select.'=\''.$model[$model_name]->components[$field_name]->check($value).'\'';
				
				}
				
				$initial_sql.=' '.$arr_par[$parenthesis]['open'].implode(' AND ', $arr_sql).$arr_par[$parenthesis]['close'];
			
			break;
			
			case 'OR':
			
				foreach($where as $field => $value)
				{
				
					list($field_select, $model_name, $field_name)=set_safe_name_field($class, $field);
				
					$arr_sql[]=$field_select.'=\''.$model[$model_name]->components[$field_name]->check($value).'\'';
				
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
					
					$where[$field]=$model[$model_name]->components[$field_name]->check($value);
				
				}
				
				$initial_sql.=' '.'`'.$class->name.'`'.'.'.'`'.$field.'`'.' IN (\''.implode('\', ', $where).'\')';*/
				
				//$where=array( 'IN' => array( 'IdUser' => array(1, 2, 3) ) );
				
				foreach($where as $field => $arr_value)
				{
					
					list($field_select, $model_name, $field_name)=set_safe_name_field($class, $field);
					
					foreach($arr_value as $key_value => $value)
					{
					
						$arr_value[$key_value]=$model[$model_name]->components[$field_name]->check($value);
					
					}
					
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
				
					list($field_select, $model_name, $field_name)=set_safe_name_field($class, $field);
				
					$arr_sql[]=' '.$field_select.' LIKE \'%'.$model[$model_name]->components[$field_name]->check($value).'%\'';
				
				}
				
				$initial_sql.=' '.$arr_par[$parenthesis]['open'].implode(' OR ', $arr_sql).$arr_par[$parenthesis]['close'];
			
			break;
			
			case 'LIKE_AND':
			
				foreach($where as $field => $value)
				{
				
					list($field_select, $model_name, $field_name)=set_safe_name_field($class, $field);
				
					$arr_sql[]=$field_select.' LIKE \'%'.$model[$model_name]->components[$field_name]->check($value).'%\'';
				
				}
				
				$initial_sql.=' '.$arr_par[$parenthesis]['open'].implode(' AND ', $arr_sql).$arr_par[$parenthesis]['close'];
			
			break;
		
		}
	
	}
	
	return $initial_sql;
	
}

function set_safe_name_field($class, $field)
{
	
	$pos_dot=strpos($field, '.');
	
	$model_name='';
	$field_name='';
	
	if($pos_dot!==false)
	{
	
		//The model need to be loading previously.
		
		//substr ( string $string , int $start [, int $length ] )
		
		$model_name=substr($field, 0, $pos_dot);
		$field_name=substr($field, $pos_dot+1);
		
		$field_select='`'.$model_name.'`.`'.$field_name.'`';
	
	}
	else
	{
		
		$model_name=$class->name;
		$field_name=$field;
		
		$field_select='`'.$class->name.'`.`'.$field.'`';
		
	}
	
	return array($field_select, $model_name, $field_name);

}

?>