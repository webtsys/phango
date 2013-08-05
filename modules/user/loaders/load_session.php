<?php

if(!defined('PAGE'))
{

	die();

}

load_lang('user');

load_model('user', 'user/rank');

//load_libraries(array('timestamp_zone'));

//We need the ip

$ip = '';

if ( getenv( "HTTP_X_FORWARDED_FOR" ) )
{
	$ip = trim( nl2br( htmlentities( getenv( "HTTP_X_FORWARDED_FOR" ), ENT_QUOTES) ) );
} 
else
{
	$ip = trim( nl2br( htmlentities( getenv( "REMOTE_ADDR" ), ENT_QUOTES) ) );
}

//We need delete recovery_password...

$delete_time_rec=TODAY-7200;

$query=$model['recovery_password']->delete('where date_token<'.$delete_time_rec);

//We need delete anonymous...

$delete_time=TODAY-7200;

$query=webtsys_query("delete from anonymous where last_connection<".$delete_time);

$user_data=array('IdUser'=>0, 'privileges_user'=>0, 'format_date'=>$config_data['date_format'], 'format_time' => $config_data['time_format'], 'timezone' => $config_data['timezone'], 'ampm'=>$config_data['ampm'], 'nick' =>$config_data['name_guest'], 'private_nick' =>$config_data['name_guest'], 'website' =>'', 'email'=>'', 'before_last_connection'=>0, 'last_connection' => TODAY, 'language' => $language);

$webtsys_id=session_id();//sha1(uniqid(mt_rand(), true));

settype($_COOKIE[COOKIE_NAME], 'string');

$_COOKIE[COOKIE_NAME]=trim($_COOKIE[COOKIE_NAME]);

if($_COOKIE[COOKIE_NAME]!='')
{

	$webtsys_id=sha1($_COOKIE[COOKIE_NAME]);

}

$num_user=$model['user']->select_count('where key_connection="'.$webtsys_id.'" and activated_user=1', 'IdUser');

if($num_user>0)
{	
	$query=$model['user']->select('WHERE key_connection="'.$webtsys_id.'" and activated_user=1');

	$user_data=webtsys_fetch_array($query);
	
	//Updates
	$post=array('last_connection' => TODAY, 'visited_page' => $_SERVER['REQUEST_URI']);

	$model['user']->components['private_nick']->required=0;
	$model['user']->components['email']->required=0;
	$model['user']->components['password']->required=0;
	
	$model['user']->update($post,'where IdUser='.$user_data['IdUser']);
	
	$model['user']->components['private_nick']->required=1;
	$model['user']->components['email']->required=1;
	$model['user']->components['password']->required=1;
	
}
else
{
	
	
	$query=$model['anonymous']->select('WHERE key_connection="'.$webtsys_id.'"', array('IdAnonymous', 'write_message', 'key_csrf', 'language'));
	
	list($num_user, $user_data['write_message'], $csrf_token, $language_anom)=webtsys_fetch_row($query);
	
	settype($num_user, 'integer');
	
	if($num_user==0 && isset($_COOKIE[COOKIE_NAME.'_anonymous']))
	{
	
		$last_time=TODAY;

		$id=sha1(uniqid(mt_rand(), true));

		//$_SESSION['webtsys_id']=$id;

		setcookie(COOKIE_NAME, $id, 0, $cookie_path);

		$csrf_token=$prefix_key.'_'.sha1(uniqid(mt_rand(), true));
	
		$query=$model['anonymous']->insert(array('key_connection' => sha1($id), 'ip'=> $ip, 'last_connection' => TODAY, 'write_message' => 0 , 'visited_page' => $_SERVER['REQUEST_URI'], 'key_csrf' => $csrf_token, 'language' => $language ) );
		
		$webtsys_id=$id;

	}
	else
	{

		setcookie(COOKIE_NAME.'_anonymous', 1, 0, $cookie_path);

		$post=array('last_connection' => TODAY, 'visited_page' => $_SERVER['REQUEST_URI']);
		$model['anonymous']->update($post,'where key_connection="'.$webtsys_id.'"');

		$user_data['language']=$language_anom;
		

	}
	
	$user_data['timezone']=MY_TIMEZONE;

	$user_data['key_csrf']=$csrf_token;

}

$default_language=$language;

if(in_array($user_data['language'], $arr_i18n))
{
	
	$language=$user_data['language'];

}

//Set timezone 

date_default_timezone_set($user_data['timezone']);

$_SESSION['language']=$language;

//Settings any components from model_user

$model['user']->forms['format_date']->SetParameters( array( $config_data['date_format'], $lang['user']['option_date_dmy'], 'd-m-Y', $lang['user']['option_date_ymd'], 'Y-m-d') );

$model['user']->forms['ampm']->SetParameters( array( $config_data['ampm'], $lang['user']['ampm24'], 'H:i:s', $lang['user']['ampmAM'], 'h:i:s A') );

?>
