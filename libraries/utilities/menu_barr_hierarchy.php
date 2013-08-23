<?php

//First element -> second element -> third element -> fourth element 
//                                                 -> fourth element 2 -> five element (name_get) 
// 'link0' => array('link1' => link1, 'link2'=> link2, 'link3' => link3)
// 'link1' => array('link4' => link4)
/*
function menu_barr_hierarchy($name_get, $value_get, $arr_menu, $first_father)
{

	settype($_GET[$name_get], 'integer');

	
	
	$arr_father=array();
	$arr_final_menu=array();
	$father=$first_father;
	
	//Make the parent tree
  
	$arr_father=obtain_fathers($arr_father, $arr_menu, $father);
 
	print_r($arr_father);
 
}

function obtain_fathers($arr_father, $arr_menu, $father)
{

	foreach($arr_menu as $key_menu => $menu)
	{
	
		$arr_father[$key_menu]=$father;
		
		$father=$key_menu;
		
		$arr_father=obtain_fathers($arr_father, $menu, $father);
	
	}
	
	return $arr_father;

}
*/

// $arr_menu[0]=array(0 => menu1, 1 => menu2)

function menu_barr_hierarchy($arr_menu, $name_get, $value_get)
{

	settype($_GET[$name_get], 'integer');
	
	$arr_final_menu=array();

	foreach($arr_menu as $key_menu => $menu)
	{
		if($_GET[$name_get]==$key_menu)
		{
			
			$arr_final_menu[]=$menu[0];
		
			break;
		
		}
		else
		{
		
			$arr_final_menu[]='<a href="'.$menu[1].'">'.$menu[0].'</a>';
		
		}
	
	}
	
	return implode(' &gt;&gt; ', $arr_final_menu);

}

?>