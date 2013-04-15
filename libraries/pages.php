<?php

function pages( $begin_page, $total_elements, $num_elements, $link ,$initial_num_pages=20, $variable='begin_page', $label='', $func_jscript='')
{

	global $lang_common;

	$pages='';

	if($begin_page>$total_elements)
	{

		$begin_page=0;

	}

	//Calculamos el total de todas las páginas

	$total_page=ceil($total_elements/$num_elements);
	
	//Calculamos en que página nos encontramos

	$actual_page=ceil($begin_page/$num_elements);

	//Calculamos el total de intervalos

	$total_interval=ceil($total_page/$initial_num_pages);

	//Calculamos el intervalo en el que estamos

	$actual_interval=floor($actual_page/$initial_num_pages);

	//Calculamos el elemento de inicio del intervalo

	$initial_page=ceil($actual_interval*$initial_num_pages*$num_elements);

	$last_page=ceil($actual_interval*$initial_num_pages*$num_elements)+ceil($initial_num_pages*$num_elements);

	if($last_page>$total_elements)
	{

		$last_page=$total_elements;

	}

	if($initial_page>0)
	{
		$initial_link=add_extra_fancy_url($link, array($variable =>0));
		$middle_link=add_extra_fancy_url($link, array($variable => ($initial_page-$num_elements).$label) );
		$pages .= "<a class=\"link_pages\" href=\"$initial_link\" onclick=\"$func_jscript\">1</a> <a class=\"link_pages\" href=\"$middle_link\">&lt;&lt;</a> ";

	}

	$arr_pages=array();



	for($x=$initial_page;$x<$last_page;$x+=$num_elements)
	{

		$middle_link=add_extra_fancy_url($link, array($variable=>$x.$label) );

		$num_page=ceil($x/$num_elements)+1;
		$arr_pages[$x]="<a class=\"link_pages\" href=\"$middle_link\" onclick=\"$func_jscript\">$num_page</a> ";
		$arr_pages[$begin_page]='<span class="selected_page">'.$num_page.'</span> ';
		$pages .= $arr_pages[$x];

	}
	
	if($last_page<$total_elements)
	{

		$middle_link=add_extra_fancy_url($link, array($variable=>$x.$label) );
		$last_link=add_extra_fancy_url($link, array( $variable=>( ( $total_page*$num_elements ) - $num_elements) ) );

		$pages .= "<a class=\"link_pages\" href=\"$middle_link\" onclick=\"$func_jscript\">&gt;&gt;</a> <a class=\"link_pages\" href=\"$last_link\" onclick=\"$func_jscript\">".$lang_common['last']."</a>";

	}
	
	return $pages;

} 

?>
