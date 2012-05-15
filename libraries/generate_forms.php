<?php

function generate_form($arr_fields, $order_fields=array(), $view='common/forms/modelform')
{

	return load_view(array($arr_fields, $order_fields), $view);

}

?>