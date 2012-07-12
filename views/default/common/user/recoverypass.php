<?php

function RecoveryPassView()
{

	global $config_data, $base_url, $lang;

	?>
		<h3><?php echo $lang['user']['remember_password_explain']; ?></h3>
		<form method="post" action="<?php echo make_fancy_url($base_url, 'user', 'index', 'recovery_pass', $arr_data=array('op' => 3, 'action' => 1)); ?>">
			<?php set_csrf_key(); ?>
			<label for="email"></label>
			<?php
				echo TextForm('email', '');
			?>
			<p><input type="submit" value="<?php echo $lang['user']['remember_password']; ?>" /></p>
		</form>

	<?php

}

?>