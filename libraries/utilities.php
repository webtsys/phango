<?php

function show_variables_check($post_name, $fields)
{

	$arr_value=array();

	echo 'check_variables( $'.$post_name.', array( ';

	foreach($fields as $key => $field)
	{

		$arr_value[]='\''.$key.'\'';

	}

	echo implode(', ', $arr_value);

	echo ' ) )';

}

function show_variables_check_model($model, $post_name, $fields)
{

    $arr_value=array();

    //model_check_post('user', $_POST, array( 'email', 'passwd' ,'pconnect'));

    echo 'model_check_post( '.$model.', $'.$post_name.', array( ';

    foreach($fields as $key => $field)
    {

        $arr_value[]='\''.$key.'\'';

    }

    echo implode(', ', $arr_value);

    echo ' ) )';

}

function show_edit_fields($model_name)
{

	global $model;

	echo 'array( ';

	foreach($model[$model_name]->components as $key => $field)
	{

		$arr_value[]='\''.$key.'\'';

	}

	echo implode(', ', $arr_value);

	echo ' )';

}


?>