<?php

function send_mail($email, $subject, $message, $content_type='plain', $bcc='')
{

	global $config_data;

	$portal_name=html_entity_decode($config_data['portal_name']);
	
	$header = "From:" .$portal_name."<".$config_data['portal_email'].">\r\n";
	$header .= "Reply-To:".$config_data['portal_email']."\r\n";
	$header .= "Content-Type: text/".$content_type."; charset=UTF-8"."\r\n";
	$header .= "X-Mailer: PHP5";

	if($bcc!='')
	{

		$bcc="\r\nBcc: ".$bcc;

	}

	$header .= $bcc;
	
	if(!mail($email  , $subject  , $message  , $header))
	{

		return 0;

	}

	return 1;

}

?>

