<?php

function InsertUserFormView($arr_fields, $arr_fields_form)
{

	global $config_data, $base_url, $lang, $model;

	?>
		<h2><?php echo $lang['user']['register']; ?></h2>
		<form method="post" action="<?php echo make_fancy_url($base_url, 'user', 'index', 'login_user', $arr_data=array('op' => 2, 'action' => 1)); ?>" onsubmit="if(check_condition()==false) return false;">
		<?php
			set_csrf_key();
			echo generate_form($arr_fields, $arr_fields_form, 'common/forms/modelform');

		?>
			
			<p><input type="submit" value="<?php echo $lang['common']['register_user']; ?>" /></p>

		</form>

	<?php

}

?>