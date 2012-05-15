<?php

function RankForm($name="", $class='', $value='')
{

	global $lang, $model;

	load_model('rank');
	
	$arr_rank=array($value[0], $lang['user']['without_special_rank'], 0);
	
	$query=$model['rank']->select('where IdRank>0 and fixed=1', array('IdRank', 'name'));

	while(list($idrank, $name_rank)=webtsys_fetch_row($query))
	{

		$arr_rank[]=$name_rank;
		$arr_rank[]=$idrank;

	}
	
	return SelectForm($name, $class, $arr_rank);

}

function RankFormSet($post, $value)
{
	
	$post[0]=$value;
	
	return $post;

}

function TimeZoneForm($name="", $class='', $value='')
{
	
	load_libraries(array('timestamp_zone'));

	$list_gmt=timezones_list($value[0]);

	return SelectForm($name, '', $list_gmt);

}

function TimeZoneFormSet($post, $value)
{

	$post[0]=$value;

	return $post;

}

?>