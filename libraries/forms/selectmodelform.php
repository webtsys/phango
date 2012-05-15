<?php

function SelectModelForm($name, $class, $value, $model_name, $identifier_field, $where='')
{

	global $lang, $model;

	$model_select=$model_name;
	
	if(strpos($model_name, '|')!==false)
	{
		
		$arr_model_name=explode('|', $model_name);

		$model_select=$arr_model_name[count($arr_model_name)-1];

		$model_name=$arr_model_name[0];
		

	}

	if(!isset($model[$model_name]))
	{

		load_model($model_name);

	}
	
	$arr_model=array($value, $lang['common']['no_element_chosen'], 0);
	
	$query=$model[$model_select]->select($where, array($model[$model_select]->idmodel, $identifier_field));

	while($arr_field=webtsys_fetch_array($query))
	{

		$arr_model[]=$model[$model_select]->components[$identifier_field]->show_formatted($arr_field[ $identifier_field ]);
		$arr_model[]=$arr_field[ $model[ $model_select]->idmodel ];

	}
	
	return SelectForm($name, $class, $arr_model);

}

function SelectModelFormSet($post, $value)
{

	return $value;

}

?>