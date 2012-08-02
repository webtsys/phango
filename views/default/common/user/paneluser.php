<?php

function PanelUserView()
{

	global $config_data, $base_url, $lang, $arr_check_table;

	global $base_url;

	?>
	<div class="panel">

	<p>
		<a href="<?php echo make_fancy_url($base_url, 'user', 'index', 'change_data', $arr_data=array('op' => 1)); ?>">
		<img border="0" src="<?php echo $base_url; ?>/media/<?php echo $config_data['dir_theme']; ?>/images/users.png"/>
		<strong><?php echo $lang['user']['change_data']; ?></strong></a>
	</p>

	</div>
<?php

if(isset($arr_check_table['mprivate']))
{

?>

	<div class="panel">

	<p>
		<a href="<?php echo make_fancy_url($base_url, 'user', 'mprivate', 'private_messages', $arr_data=array()); ?>">
		<img border="0" src="<?php echo $base_url; ?>/media/<?php echo $config_data['dir_theme']; ?>/images/mprivate.png">
		<strong><?php echo $lang['user']['private_messages']; ?></strong>
	</a>

	</p>
	</div>

<?php

}

?>

	<div class="panel">

	<p>
		<a href="<?php echo make_fancy_url($base_url, 'user', 'index', 'logout', $arr_data=array('op' => 2)); ?>">
		<img border="0" src="<?php echo $base_url; ?>/media/<?php echo $config_data['dir_theme']; ?>/images/logout.png">
		<strong><?php echo $lang['common']['logout']; ?></strong>
	</a>

	</p>
	</div>

	<?php

}

?>