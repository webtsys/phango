<?php

function LoginFormView($model_user, $model_login)
{

	global $config_data, $base_url, $lang, $model;
	
	$model_user->forms['no_expire_session']=new ModelForm('form_login', 'no_expire_session', 'CheckBoxForm', $lang['user']['automatic_login']	, new BooleanField(), $required=1, $parameters='');

	$arr_fields_login=array($model_login->field_user, $model_login->field_password, 'no_expire_session');
	
	?>
	<form method="post" action="<?php echo $model_login->url_login; ?>">
	<?php
		//ModelFormView($model_form, $fields=array(), $html_id='')
		echo load_view(array($model_user->forms, $arr_fields_login), 'common/forms/modelform');
		

	?>
	<p><input type="submit" value="<?php echo $lang['common']['login']; ?>" /></p>
	</form>
	<?php

}

?>