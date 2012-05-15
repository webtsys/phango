<?php

function check_admin($iduser)
{

	global $model;

	if(isset($_COOKIE['webtsys_admin']))
	{

		$count_adm=$model['user']->select_count('where key_privileges="'.sha1($_COOKIE['webtsys_admin']).'" and privileges_user=2 and IdUser='.$iduser, 'IdUser');

		if($count_adm==1)
		{
  
			return true;

		}
		else
		{

			return false;

		}

	}
	else
	{

		return false;

	}


}

function check_moderator($iduser, $moderators, $privileges_user)
{

	if( in_array($iduser, $moderators) && $privileges_user==1)
	{

		return 1;

	}

	return 0;

}

?>
