<?php

global $base_path;

load_libraries(array('avatarfield'), $base_path.'modules/user/libraries/');

class rank extends Webmodel {

	function __construct()
	{

		parent::__construct("rank");

	}

	function choose_rank($idrank, $num_posts)
	{
		
		$query=$this->select('where IdRank='.$idrank, array('num_posts', 'fixed'));

		$arr_rank=webtsys_fetch_array($query);
		
		if($arr_rank['fixed']==0)
		{

			//Update 

			$query=$this->select('where num_posts<='.$num_posts.' and IdRank!=1 and fixed=0 order by num_posts DESC limit 1', array('IdRank'));

			list($idrank)=webtsys_fetch_row($query);

		}

		return $idrank;

	}
	
}

$model['rank']=new rank();

$model['rank']->components['name']=new CharField(255);
$model['rank']->components['name']->required=1;
$model['rank']->components['num_posts']=new IntegerField(11);
$model['rank']->components['fixed']=new BooleanField();
$model['rank']->components['image']=new AvatarField(255);

$arr_module_insert['rank']=array('name' => 'rank', 'admin' => 1, 'admin_script' => array('user', 'rank'), 'load_module' => '', 'order_module' => 1, 'required' => 1);

$arr_module_sql['rank']='rank.sql';

?>

