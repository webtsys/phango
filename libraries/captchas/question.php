<?php

//Check if exists variable $key_captcha...

if(!defined('QUESTION_CAPTCHA') || !defined('ANSWER_CAPTCHA'))
{

	show_error('Error: bad config for captcha', 'Error: bad config for captcha. You need QUESTION_CAPTCHA and ANSWER_CAPTCHA constants set in config.php or alternative file config');

}

function CaptchaForm($name="", $class='', $value='')
{
	global $key_recaptcha;

	echo QUESTION_CAPTCHA.'<br />'.TextForm('answer_captcha', $class, $value);

}

function CaptchaCheck($arr_post_value)
{

	/*global $key_recaptcha_private, $ip;

	//Access google server with curl...

	settype($arr_post_value['recaptcha_challenge_field'], 'string');
	settype($arr_post_value['recaptcha_response_field'], 'string');

	$curl_post=curl_init('http://www.google.com/recaptcha/api/verify');
	
	curl_setopt ( $curl_post , CURLOPT_HEADER,false );
	curl_setopt ( $curl_post , CURLOPT_POST, true );
	curl_setopt ( $curl_post , CURLOPT_POSTFIELDS, array('privatekey' => $key_recaptcha_private, 'remoteip' => $ip, 'challenge' => $arr_post_value['recaptcha_challenge_field'], 'response' => $arr_post_value['recaptcha_response_field']) );

	ob_start();

	curl_exec($curl_post);

	$result_captcha=ob_get_contents();

	ob_end_clean();

	$arr_result=explode("\n", $result_captcha);

	if($arr_result[0]=='true')
	{

		$arr_result[0]=1;

	}*/
	
	$arr_result[0]=0;
	
	$answer_captcha=form_text($arr_post_value['answer_captcha']);
	
	if($answer_captcha==ANSWER_CAPTCHA)
	{
	
		$arr_result[0]=1;
		
	}
	

	return $arr_result;

}

function CaptchaSet($post, $value)
{

	return form_text($value);

}


?>