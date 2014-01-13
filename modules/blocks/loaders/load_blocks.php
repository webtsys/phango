<?php

//We need optimize this. 

load_model('blocks');

$block_title=array();
$block_content=array();
$block_urls=array();
$block_type=array();
$block_id=array();

$block_children_title=array();
$block_children_content=array();
$block_children_urls=array();
$block_children_type=array();
$block_children_id=array();

function getblock($module, $order_mods='DESC')
{
	
	global $block_title;
	global $block_content;
	global $block_urls;
	global $block_type;
	global $block_id;
	
	global $block_children_title;
	global $block_children_content;
	global $block_children_urls;
	global $block_children_type;
	global $block_children_id;

	global $model;
	global $user_data;
	global $base_url;
	global $base_path;
	global $language;
	global $lang;
	/*
	$arr_column=array();

	$arr_column[0]="where activation=1";
	$arr_column[1]="where activation=2";
	$arr_column[2]="where activation=1 and activation=2";
	$arr_column[3]="where activation=3 and idblock=1";
	*/
	//$module[]='';

	$arr_check[0]='left';
	$arr_check[1]='right';
	$arr_check[2]='barr';

	$block_content['left']=array();
	$block_urls['left']=array();
	$block_type['left']=array();
	$block_id['left']=array();

	$block_content['right']=array();
	$block_urls['right']=array();
	$block_type['right']=array();
	$block_id['right']=array();

	$block_content['barr']=array();
	$block_urls['barr']=array();

	$arr_enable['left']=0;
	$arr_enable['right']=0;
	$arr_enable['barr']=0;

	$sql_mod='';
	
	foreach($module as $key => $mod)
	{

		$module[$key]='module="'.$mod.'"';

	}
	
	$module=array_reverse($module);

	//Select inherit

	$query=$model['inheritance_blocks']->select('where '.$module[0], array('inheritance') );

	list($inheritance)=webtsys_fetch_row($query);

	settype($inheritance, 'integer');

	if($inheritance==0)
	{

		$module[]='module="none"';

		$where[0]='module="none" and activation="0"';
		$where[1]='module="none" and activation="1"';

	}

	//Create query
	$no_herency=array(0 => 0, 1 => 0, 2 => 0);

	/*$where[0]='0=0';
	$where[1]='0=0';*/
	$where[2]='module="none" and activation="2"';
	
	foreach($module as $last_module)
	{
			
		if($model['blocks']->select_count('where '.$last_module.' and activation=0 and parent=0', 'IdBlocks')>0 && $no_herency[0]==0)
		{
			
			$module=array($last_module);
			$no_herency[0]=1;
			$where[0]=$last_module.' and activation="0" and parent=0';

		}
		
		if($model['blocks']->select_count('where '.$last_module.' and activation=1 and parent=0', 'IdBlocks')>0  && $no_herency[1]==0)
		{

			$module=array($last_module);
			$no_herency[1]=1;
			$where[1]=$last_module.' and activation="1" and parent=0';

		}

		if($model['blocks']->select_count('where '.$last_module.' and activation=2 and parent=0', 'IdBlocks')>0 && $no_herency[2]==0)
		{

			$module=array($last_module);
			$no_herency[2]=1;
			$where[2]=$last_module.' and activation="2" and parent=0';

		}

	}
	
	$arr_parent=array();
	
	$query=$model['blocks']->select('where '.implode(' or ', $where).'  and parent=0 order by module '.$order_mods.', hierarchy_block ASC');

	$result_q=array();
	
	while($result=webtsys_fetch_array($query))
	{
		//ereg('helper:/', $result['url_block'])
		if(preg_match('/^helper:\//', $result['url_block']))
		{

			$helper=str_replace('helper:/', '', $result['url_block']);

			$arr_helper[$helper]='block_title';
			$arr_helper['begin_block']='block_begin';
			$arr_helper['end_block']='block_end';
			
			$block_content[$arr_check[ $result['activation'] ]][]=$model['blocks']->components['title_block']->show_formatted($result['title_block']);
			$block_urls[$arr_check[ $result['activation'] ]][]='';
			$block_type[$arr_check[ $result['activation'] ]][]=$arr_helper[$helper];
			

		}
		else
		if(preg_match('/^static:\//', $result['url_block']))
		{
			
			$side=$result['activation'];

			$file_module=basename( str_replace('static:/', '', $result['url_block']) );

			$path_module=str_replace('static:/', '', str_replace($file_module, '', $result['url_block']) );
			
			$block_content[$arr_check[ $result['activation'] ]][]=$base_path.'modules/'.$path_module.'/blocks/html/'.$file_module;
			$block_urls[$arr_check[ $result['activation'] ]][]='';
			$block_type[$arr_check[ $result['activation'] ]][]='block_html';
			
		}
		else if($result['url_block']=='')
		{

			$block_content[$arr_check[ $result['activation'] ]][]=$model['blocks']->components['title_block']->show_formatted($result['title_block']);
			$block_urls[$arr_check[ $result['activation'] ]][]='';
			$block_type[$arr_check[ $result['activation'] ]][]='block_title';

		}
		else
		{
			$block_content[$arr_check[ $result['activation'] ]][]=$model['blocks']->components['title_block']->show_formatted($result['title_block']);
			$block_urls[$arr_check[ $result['activation'] ]][]=$result['url_block'];
			$block_type[$arr_check[ $result['activation'] ]][]='block_url';
		}
	
		$block_id[$arr_check[ $result['activation'] ]][]=$result['IdBlocks'];
		$arr_enable[$arr_check[ $result['activation'] ]]=1;
		
		$arr_parent[]=$result['IdBlocks'];
	}
	
	
	if($user_data['privileges_user']==2)
	{
	
		$block_content['barr'][]=$lang['common']['admin_panel'];
		$block_urls['barr'][]=make_fancy_url($base_url, 'admin', 'index', 'admin_zone', $arr_data=array());;

	}
	
	//Now child
	
	/*$query=$model['blocks']->select('where parent IN (\''.implode('\', \'', $arr_parent).'\')');
	
	while*/
	
	return $arr_enable['left'].$arr_enable['right'].$arr_enable['barr'];

}

function select_view($idmodule=array(''))
{
	global $model, $config_data, $lang;

	//load_lang('blocks_module');
	
	$arr_views['00']='/none';
	$arr_views['10']='/left';
	$arr_views['01']='/right';
	$arr_views['11']='/all';
	
	/*$query=$model['modules']->select('where IdModule='.$idmodule, array('IdModule'));

	list($idmodule)=webtsys_fetch_row($query);

	settype($idmodule, 'integer');*/

	foreach($idmodule as $key => $value)
	{
		$idmodule[$key]=form_text($value);
	}

	//Get the last block...
	/*end($idmodule);
	$module=array(current($idmodule));*/
	
	$arr_block=getblock($idmodule);
	
	$arr_block=substr($arr_block, 0, 2);
	
	return $arr_views[$arr_block];
	
}

function get_pages_module($module)
{

	global $base_path;

	while (false !== ($archivo = readdir($gestor))) 
	{
		echo "$archivo\n";
	}

}



?>