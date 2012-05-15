<?php

function MailRegisterView($private_nick, $email, $password)
{

global $lang, $base_url;

?>
<html>
	<head></head>
	<body>
		<h3><?php echo $lang['user']['text_welcome']; ?> <?php $private_nick; ?></h3>
		<p><?php echo $lang['user']['text_answer']; ?></p>
		<p><?php echo $lang['common']['email']; ?>: <?php echo $email; ?></p>
		<p><?php echo $lang['common']['password']; ?>: <?php echo $password; ?></p>
		<p><?php echo $lang['user']['thanks']; ?></p>
	</body>
</html>
<?php

}

?>