<?php

function HomeView($title, $content, $block_title, $block_content, $block_urls)
{

global $base_url, $config_data;

//extract($arr_variables);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

	<html>
	
	<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<title><?php echo $config_data['portal_name'].' - '.$title; ?></title>
	<link href="<?php echo $base_url; ?>/media/admin/style/style.css" rel="stylesheet" type="text/css">
	</head>
	<body>
<div id="center_body">
<div id="header"><img src="<?php echo $base_url; ?>/media/admin/images/icon_admin.png" id="logo_header"/> <?php echo $config_data['portal_name']; ?></div>
<div id="menu">
	<ul>
	<?php
	$c_title=count($block_title['left']);

	for($i=0;$i<$c_title;$i++)
	{

	?>
	<?php

	$c=count($block_content['left'][$i]);

		for($x=0;$x<$c;$x++)
		{
		
		?>
		
			<li><a href="<?php echo $block_urls['left'][$i][$x]; ?>"><?php echo $block_content['left'][$i][$x]; ?></a></li>
		
		<?php
		
		}
	}

		?>
	</ul>
	</div>
	<div id="content">
		<h1><?php echo $title; ?></h1>
		<?php echo $content; ?>
	</div>
</div>
</body>
</html>

<?php

}

?>
