<?php

function SelectWindowForm($name="", $class='', $value='', $module='', $model_name='', $field='')
{

	global $model, $lang, $base_url;
	
	settype($value, 'integer');

	if($value==0)
	{
	
		$value=$lang['common']['no_element_chosen'];
	
	}
	else
	{
	
		$arr_model=$model[$model_name]->select_a_row( $value, array($field) );
		
		if(isset($arr_model[$field]))
		{
		
			$url_choose_option=make_fancy_url($base_url, 'jscript', 'browser_list_field', 'browser_list_field', array('module' => $module, 'model' => $model_name, 'field' => $field, 'field_fill' => $name));
			
			//window.open('','','width=200,height=100');
		
			return '<span id="select_window_form_'.$name.'">'.$arr_model[$field].'</span><input type="hidden" name="'.$name.'" class="'.$class.'", id="'.$name.'_field_form"/> <a href="#" onclick="window.open(\''.$url_choose_option.'\', \'\', \'width=800,height=600\'); return false;">'.$lang['common']['any_option_chosen'].'</a>';
		
		}
	
	}

	return $value;


}

function SelectWindowFormSet($post, $value)
{

	settype($value, 'integer');
	
	return $value;

}

?>