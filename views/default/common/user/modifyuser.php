<?php

function ModifyUserView($arr_fields, $arr_fields_form)
{

	global $config_data, $base_url, $lang, $model;

	?>
		<h2><?php echo $lang['user']['modify_user']; ?></h2>
		<form method="post" action="<?php echo make_fancy_url($base_url, 'user', 'index', 'change_data', $arr_data=array('op' => 1, 'action' => 1)); ?>">
		<?php
			set_csrf_key();
			echo generate_form($arr_fields, $arr_fields_form, 'common/forms/modelform');

		?>
			
			<p><input type="submit" value="<?php echo $lang['user']['modify_user']; ?>" /></p>

		</form>

	<?php

}

?>