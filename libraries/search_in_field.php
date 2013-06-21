<?php

function search_in_field($model_name, $fields, $phrase_search)
{

	global $model;

	//Need fix this function with foreignkeyfields...
	
	//Search in the fields in a model
	print_r($fields);
	$phrase_search=form_text($phrase_search);

	$arr_text=explode(" ",$phrase_search);
			
	$c=count($arr_text);

	$final_text='';
	$location='';
	$sql_text_final='';
	$loc_array=array();

	if($c<31 && $phrase_search!='')
	{

		$sql_text=array();

		$arr_final=array();
		
		$arr_loc=array();

		foreach($fields as $field)
		{
		
			//$check_phrase_search=$model[$model_name]->forms[$field]->type->check($phrase_search);
			

			$arr_loc[]="IF(LOCATE(\"$phrase_search\",".$model_name.".`".$field."`),2,0)";

		}

		$loc_array[]=implode('+', $arr_loc);
		
		foreach($arr_text as $value)
		{
			if(strlen($value)>2)
			{

				foreach($fields as $field)
				{

					$arr_final[]=" ".$model_name.".`".$field."` LIKE \"%$value%\""; 
					$arr_loc[]="IF(LOCATE(\"$value\",".$model_name.".`".$field."`),1,0)";

				}

				$loc_array[]=implode('+', $arr_loc);

				//$loc_array[]="IF(LOCATE(\"$value\",child.subject),1,0)+IF(LOCATE(\"$value\",child.text),1,0)";
				
			}
			
		}

		$location="(".implode(" + ",$loc_array).")";

		$final_text=implode(" or ",$arr_final);
		
		/*if(isset($model[$model_name]->components[$field]->related_model))
		{

			

		}*/

		foreach($fields as $field)
		{

			$sql_text[]="( ".$model_name.".`".$field."` like \"%$phrase_search%\" or ".$model_name.".`".$field."` like \"$phrase_search%\" or  ".$model_name.".`".$field."` like \"%$phrase_search\"";

		}

		if($final_text!='')
		{

			$sql_text[(count($sql_text)-1)].='or '.$final_text;

		}

		$sql_text[(count($sql_text)-1)].=' )';

		$sql_text_final=implode(' and ', $sql_text);
		

	}

	//$query=model_select($model_name, $sql_text_final);
	/*echo $location.'<p>';
	echo $sql_text_final;*/

	return array($location, $sql_text_final);
}

?>