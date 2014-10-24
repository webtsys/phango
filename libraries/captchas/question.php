<?php

//Check if exists variable $key_captcha...

if(!defined('QUESTION_CAPTCHA') || !defined('ANSWER_CAPTCHA'))
{

	show_error('Error: bad config for captcha', 'Error: bad config for captcha. You need QUESTION_CAPTCHA and ANSWER_CAPTCHA constants set in config.php or alternative file config');

}

function CaptchaForm($name="", $class='', $value='')
{
	global $key_recaptcha;

	echo QUESTION_CAPTCHA.'<br />'.TextForm($name, $class, $value);

}

function CaptchaCheck($value)
{
	
	$answer_captcha=form_text($value);
	
	if($answer_captcha==ANSWER_CAPTCHA)
	{
	
		return true;
		
	}
	
	return false;
	

}

function CaptchaFormSet($post, $value)
{

	return form_text($value);

}


?>