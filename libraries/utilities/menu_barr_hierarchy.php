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

function menu_barr_hierarchy($arr_menu, $name_get, $value_get, $yes_last_link=0)
{

	settype($_GET[$name_get], 'integer');
	
	$arr_final_menu=array();

	foreach($arr_menu as $key_menu => $menu)
	{
		if($_GET[$name_get]==$key_menu && $yes_last_link==0)
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

//$arr_menu[0]=array('module' => 'module', 'controller' => 'controller', 'text' => 'text', 'name_op' => , 'params' => array())

//$arr_menu[1]=array(0 => array('module' => 'module', 'controller' => 'controller', 'name_op' => name_op, 'text' => 'text', 'params' => array()), 1 => array('module' => 'module', 'controller' => 'controller', 'op' => op, 'text' => 'text', 'params' => array()) );

//With the hope that write a function that create a menu_barr_hierarchy automatically

function menu_barr_hierarchy_control($arr_menus)
{

	//Begin process
	global $base_url;
	
	$arr_final_menu=array();
	
	foreach($arr_menus as $pos_menu => $arr_menu)
	{
	
		if(!isset($arr_menu[0]))
		{
		
			list($arr_final_menu, $return_break)=check_arr_menu($arr_menu, $arr_final_menu);
			
			if($return_break==1)
			{
			
				break;
			
			}
			
		}
		else
		{
		
			foreach($arr_menu as $menu)
			{
			
				list($arr_final_menu, $return_break)=check_arr_menu($arr_menu, $arr_final_menu);
				
				if($return_break==1)
				{
				
					break;
				
				}
			
			}
		
		}
		
		if($return_break==1)
		{
		
			break;
		
		}
		
		
		
	}
	
	return implode(' &gt;&gt; ', $arr_final_menu);

}

function check_arr_menu($arr_menu, $arr_final_menu)
{

	global $base_url;

	$return_break=0;

	if($arr_menu['module']==PHANGO_SCRIPT_BASE_CONTROLLER && $arr_menu['controller']==PHANGO_SCRIPT_FUNC_NAME && $arr_menu['params'][$arr_menu['name_op']]==$_GET[$arr_menu['name_op']])
	{
	
		$arr_final_menu[]=$arr_menu['text'];
		$return_break=1;
	
	}
	else
	{
	
		$arr_final_menu[]='<a href="'.make_fancy_url($base_url, $arr_menu['module'], $arr_menu['controller'], $arr_menu['text'], $arr_menu['params']).'">'.$arr_menu['text'].'</a>';
	
	}
	
	return array($arr_final_menu, $return_break);

}

?>