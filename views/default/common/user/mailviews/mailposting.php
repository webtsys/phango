<?php

function MailPostingView($nick, $text, $url_post, $url_unsubscribe)
{

global $lang;

?>
<html>
	<head></head>
	<body>
		<p><?php echo $lang['blog']['inform_comment']; ?></p>
		<p><a href="<?php echo $url_post; ?>"><?php echo $url_post; ?></a></p>
		<p><?php echo $lang['blog']['comment_made']." $nick:\n\n"; ?></p>
		<p><?php echo $text; ?></p>
		<hr />
		<p><?php echo $lang['blog']['down_article']; ?>: </p>
		<p><a href="<?php echo $url_unsubscribe; ?>"><?php echo $url_unsubscribe; ?></a></p>
	</body>
</html>
<?php

}

?>