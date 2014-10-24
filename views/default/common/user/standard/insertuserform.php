<?php

function InsertUserFormView($model_user, $model_login)
{

	global $config_data, $base_url, $lang, $model;

	$model_user->forms['repeat_password']=new ModelForm('repeat_password', 'repeat_password', 'PasswordForm', $lang['user']['repeat_password'], new PasswordField(), $required=1, $parameters='');
	
	$model_login->arr_user_insert[]='repeat_password';
	
	if($config_data['captcha_type']!='')
	{

		load_libraries(array('captchas/'.$config_data['captcha_type']));

		$model_user->forms['captcha']=new ModelForm('captcha', 'captcha', 'CaptchaForm', $lang['common']['captcha'], new CharField(), $required=1, $parameters='');

		$model_login->arr_user_insert[]='captcha';
		
	}
	
	?>
	<form method="post" action="<?php echo make_fancy_url($base_url, 'shop', 'cart', 'register', array('action' => 'get_address_save')); ?>">
	<?php
	
	set_csrf_key();
	
	
	echo load_view(array($model_user->forms, $model_login->arr_user_insert), 'common/forms/modelform');
		

	?>
	<p><input type="submit" value="<?php echo $lang['user']['register']; ?>"/></p>
	</form>
	<?php

}

?>