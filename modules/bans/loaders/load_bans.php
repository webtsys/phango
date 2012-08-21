<?php

if ( !defined( "PAGE" ) )
{
    die(  );
} 

load_lang('bans');

$message="";

$time_ban=TODAY;

$query=webtsys_query('delete from ban where time_ban<'.$time_ban.' and time_ban!=0');

$query=webtsys_query("select IdBan, message, modules_ban from ban where ( (iduser=\"".$user_data['IdUser']."\" and iduser>0) or ip=\"$ip\") and (time_ban=0 or time_ban>".$time_ban.")");

list($idban, $message, $ser_modules_ban)=mysql_fetch_row($query);

$modules_ban=unserialize($ser_modules_ban);

settype($modules_ban, 'array');

if($modules_ban[0]=='')
{

	$modules_ban[0]=0;

}


settype($idban, 'integer');

if($idban>0)
{

	$modules_path=array();

	$query=$model['module']->select('where IdModule IN ('.implode(',', $modules_ban).') order by order_module ASC', array('name') );

	while(list($page_module)=webtsys_fetch_row($query))
	{

		$modules_path[]=$page_module;
	}
	
	$path_page=$script_base_controller;
	
	if( in_array(0, $modules_ban) || in_array($path_page, $modules_path) )
	{

		$block_title['barr']=array();
		$block_content['barr']=array();
		$block_urls['barr']=array();

		ob_clean();

		echo load_view(array($lang['bans']['you_ban'],$message), 'content');

		$content=ob_get_contents();

		ob_end_clean();

		echo load_view(array($lang['bans']['you_ban'], $content, $block_title, $block_content, $block_urls, $block_type, $block_id, $config_data), 'none');
		
		die();

	}
}

?>
