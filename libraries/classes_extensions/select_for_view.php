<?php

function select_for_view_method_class($class, $conditions="", $arr_select=array(), $raw_query=0)
{

	$query=$class->select($conditions, $arr_select, $raw_query);

}

?>