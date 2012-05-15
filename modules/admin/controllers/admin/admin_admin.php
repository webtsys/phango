<?php

function AdminIndexAdmin()
{

	global $lang;
	
	echo load_view(array('title' => $lang['admin']['welcome_to_admin'], 'content' => $lang['admin']['welcome_text']), 'content');

}

?>