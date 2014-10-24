<?php

function InsertUserFormView($model_user, $model_login)
{

	global $config_data, $base_url, $lang, $model;
	
	?>
	<form method="post" action="<?php echo $model_login->url_insert; ?>">
	<?php
	
	set_csrf_key();
	
	
	echo load_view(array($model_user->forms, $model_login->arr_user_insert), 'common/forms/modelform');
		

	?>
	<p><input type="submit" value="<?php echo $lang['user']['register']; ?>"/></p>
	</form>
	<?php

}

?>