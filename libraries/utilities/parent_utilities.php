<?php

function recursive_list($model_name, $arr_cat, $arr_list_father, $idfather, $url_cat, $arr_perm=array())
{

	global $base_url, $lang, $model;

	$idfield=$model[$model_name]->idmodel;
	
	$arr_hidden[0]='';
	$arr_hidden[1]='';
	
	settype($_GET[$idfield], 'integer');
	
	$end_ul='';
	
	echo '<div id="list_ul">';
	
	if($idfather==0)
	{
	
		$first_url[$_GET[$idfield]]='<ul><li><a href="'.$url_cat.'">'.$lang['common']['home'].'</a><ul>';
		$first_url[0]='<ul><li><strong>'.$lang['common']['home'].'</strong></li><ul>';
		
		echo $first_url[$_GET[$idfield]];
		
		$end_ul= '</ul></ul>';
	}
	
	settype($arr_list_father[$idfather], 'array');
	
	foreach($arr_list_father[$idfather] as $idcat)
	{
		
		settype($arr_perm[$idcat], 'integer');
		
		$url_blog=add_extra_fancy_url($url_cat, array($idfield => $idcat) );
		
		$arr_hidden[$arr_perm[$idcat]]='<span class="error">'.$arr_cat[$idcat].'</span>';
		$arr_hidden[0]='<a href="'.$url_blog.'">'.$arr_cat[$idcat].'</a>';
		$arr_hidden[1]='<span class="error">'.$arr_cat[$idcat].'</span>';
		
		$arr_url[$idcat]=$arr_hidden[$arr_perm[$idcat]];

		$arr_url[$_GET[$idfield]]=$arr_cat[$idcat];
		
		echo '<li id="cat_blog'.$idcat.'"><b>'.$arr_url[$idcat].'</b>'."\n";

		//Here the blogs from category..

		echo '</li>';
		echo '<ul>';
			if(isset($arr_list_father[$idcat]))
			{
				
				recursive_list($model_name, $arr_cat, $arr_list_father, $idcat, $url_cat, $arr_perm);
			}
		echo '</ul>';

	}
	
	echo $end_ul;
	
	echo '</div>';

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

function obtain_parent_list($model_name, $title_field, $parent_field, $sql_father='')
{

	global $model;

	$arr_list_father=array();
	$arr_cat=array();
	//$sql_father.=' order by '.$parent_field.' ASC';
	
	$query=$model[$model_name]->select($sql_father, array($model[$model_name]->idmodel, $title_field, $parent_field));

	while(list($idcat, $title, $idfather)=webtsys_fetch_row($query))
	{
		settype($arr_list_father[$idfather], 'array');
	
		$arr_list_father[$idfather][]=$idcat;
	
		$title=$model[$model_name]->components[$title_field]->show_formatted($title);

		$arr_cat[$idcat]=$title;

	}

	return array($arr_list_father, $arr_cat);

}

?>
