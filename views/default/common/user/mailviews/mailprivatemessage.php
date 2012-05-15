<?php

function MailPrivateMessageView()
{

global $lang, $base_url;

?>
<html>
	<head></head>
	<body>
		<h3><?php echo $lang['user']['message_send_user']; ?></h3>
		<p><?php echo $lang['user']['see_message_user']; ?>: <a href="<?php echo make_fancy_url($base_url, 'user', 'mprivate', 'go_back_private_messages', array() ); ?>"><?php echo make_fancy_url($base_url, 'user', 'mprivate', 'go_back_private_messages', array() ); ?></a></p>
	</body>
</html>
<?php

}

?>