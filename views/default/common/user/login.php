<?php

function LoginView($arr_fields, $register_page_final, $field_user='email')
{

	global $config_data, $base_url, $lang;

	?>

		<form method="post" action="<?php echo make_fancy_url($base_url, 'user', 'index', 'login_user', $arr_data=array('op' => 1)); ?>">
		<?php
			set_csrf_key();
			
			echo generate_form($arr_fields, array($field_user, 'password', 'automatic_login'), 'common/forms/modelform');

			?>
			<input type="hidden" name="register_page" value="<?php echo $register_page_final; ?>">
			<p><input type="submit" value="<?php echo $lang['common']['login']; ?>" /></p>
			<?php

			if($config_data['create_user']==0)
			{
				?>
				<p><a href="<?php echo make_fancy_url($base_url, 'user', 'index', 'register_user', $arr_data=array('op' => 2)); ?>"><?php echo $lang['user']['register']; ?></a></p>
				<?php
			}
			?>
			<p><a href="<?php echo make_fancy_url($base_url, 'user', 'index', 'remember_password', $arr_data=array('op' => 3)); ?>"><?php echo $lang['user']['push_remember']; ?></a></p>
		</form>

	<?php

}

?>