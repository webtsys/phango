<?php

//Check if exists variable $key_captcha...

global $key_recaptcha, $key_recaptcha_private;

if(!isset($key_recaptcha) || !isset($key_recaptcha_private))
{

	show_error('Error: bad config for captcha', 'Error: bad config for captcha. You need $key_recaptcha and $key_recaptcha_private variable set in config.php or alternative file config');

}

function CaptchaForm($name="", $class='', $value='')
{
	global $key_recaptcha;

	?>
	  <script type="text/javascript" src="http://www.google.com/recaptcha/api/challenge?k=<?php echo $key_recaptcha; ?>"> </script>
	<noscript>
	<iframe src="http://www.google.com/recaptcha/api/noscript?k=<?php echo $key_recaptcha; ?>"
		height="300" width="500" frameborder="0"></iframe><br>
	<textarea name="recaptcha_challenge_field" rows="3" cols="40">
	</textarea>
	<input type="hidden" name="recaptcha_response_field"
		value="manual_challenge">
	</noscript>

	<?php

}

function CaptchaCheck($arr_post_value)
{

	global $key_recaptcha_private, $ip;

	/*
	    recaptcha_challenge_field is a hidden field that describes the CAPTCHA which the user is solving. It corresponds to the "challenge" parameter required by the reCAPTCHA verification API.
    recaptcha_response_field is a text field where the user enters their solution. It corresponds to the "response" parameter required by the reCAPTCHA verification API.
*/

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

	}

	return $arr_result;

}

function CaptchaSet($post, $value)
{

	return $value;

}


?>