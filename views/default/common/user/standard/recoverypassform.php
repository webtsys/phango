<?php

function RecoveryPassFormView($model_login, $login)
{

	global $config_data, $base_url, $lang;

	?>
		<h3><?php echo $lang['user']['remember_password_explain']; ?></h3>
		<form method="post" action="<?php echo $login->url_recovery; ?>">
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