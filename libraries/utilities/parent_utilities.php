<?php

function recursive_list($arr_cat, $arr_list_father, $idfather, $url_cat, $arr_perm=array())
{

	global $base_url, $lang;

	$arr_hidden[0]='';
	$arr_hidden[1]='';
	
	foreach($arr_list_father[$idfather] as $idcat)
	{
		
		settype($arr_perm[$idcat], 'integer');
		
		$url_blog=add_extra_fancy_url($url_cat, array('IdBlog' => $idcat) );
		
		$arr_hidden[$arr_perm[$idcat]]='<span class="error">'.$arr_cat[$idcat].'</span>';
		$arr_hidden[0]='<a href="'.$url_blog.'">'.$arr_cat[$idcat].'</a>';
		$arr_hidden[1]='<span class="error">'.$arr_cat[$idcat].'</span>';
		
		$arr_url[$idcat]=$arr_hidden[$arr_perm[$idcat]];

		$arr_url[$_GET['IdBlog']]=$arr_cat[$idcat];
		
		echo '<li id="cat_blog'.$idcat.'"><b>'.$arr_url[$idcat].'</b>'."\n";

		//Here the blogs from category..

		echo '</li>';
		echo '<ul>';
			if(isset($arr_list_father[$idcat]))
			{
				
				recursive_list($arr_cat, $arr_list_father, $idcat, $url_cat, $arr_perm);
			}
		echo '</ul>';

	}

}

function recursive_select($arr_father, $arr_cat, $arr_list_father, $idfather, $separator='')
{

	$separator.=$separator;

	foreach($arr_list_father[$idfather] as $idcat)
	{
		
		$arr_father[]=$separator.$arr_cat[$idcat];
		$arr_father[]=$idcat;
		if(isset($arr_list_father[$idcat]))
		{
			$arr_father=recursive_select($arr_father, $arr_cat, $arr_list_father, $idcat, $separator.'--');
		}

	}
	
	return $arr_father;

}

?>