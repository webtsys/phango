<?php

function CommonView($title, $content)
{

global $base_url;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

	<html>
	<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<title><?php echo $title; ?></title>
	<link href="<?php echo $base_url; ?>/media/common/style/style.css" rel="stylesheet" type="text/css">
	</head>
	<body>

	<div id="center_body">
		<div id="header"><span id="title_phango">Phango</span> <span id="title_framework">Framework!</span></div>
		<div class="content big_content">
			<?php echo $content; ?>
		</div>
	</div>

<?php

}

?>
