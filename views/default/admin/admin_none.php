<?php

function Admin_NoneView($title, $content, $block_title, $block_content, $block_urls, 
$block_type, $block_id, $config_data, $headers='')
{

	global $base_url, $lang, $arr_cache_jscript;

	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

		<html>
		<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<?php echo $headers; ?>
		<?php 
		echo load_jscript_view(); 
		?>
		<title><?php echo $title; ?></title>
		<link href="<?php echo $base_url; ?>/media/common/style/style.css" rel="stylesheet" type="text/css">
		</head>
		<body>
		<div id="center_body">
			<div id="header"><span id="title_phango">Phango</span> <span id="title_framework">Framework!</span></div>
			<div class="content">
				<div class="cont none_cont">
					<?php echo $content; ?>
				</div>
			</div>
		</div>

	<?php

}

?>