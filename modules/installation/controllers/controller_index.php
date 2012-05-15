<?php

function Index()
{

	global $user_data, $model, $lang, $base_path, $base_url, $cookie_path, $arr_module_insert, $arr_module_sql, $arr_padmin_mod, $language, $config_data;

	load_libraries(array('update_table', 'generate_forms', 'timestamp_zone'));
	load_lang('installation', 'user', 'common');

	settype($_GET['op'], 'integer');

	$cont_index='';

	switch($_GET['op'])
	{

		default:

		echo '<h3>'.$lang['installation']['create_user'].'</h3>';

		echo '<p>'.$lang['installation']['create_user_explain'].'</p>';

		$arr_forms=array();

		$arr_forms['private_nick']=new ModelForm('user', 'private_nick', 'TextForm', $lang['user']['private_nick'], new CharField(255), $required=1, $parameters='');
		/*$arr_forms['password']=new ModelForm('user', 'password', 'PasswordForm', $lang['common']['password'], new CharField(255), $required=1, $parameters='');
		$arr_forms['repeat_password']=new ModelForm('user', 'repeat_password', 'PasswordForm', $lang['user']['repeat_password'], new CharField(255), $required=1, $parameters='');*/
		$arr_forms['email']=new ModelForm('user', 'email', 'TextForm', $lang['common']['email'], new EmailField(255), $required=1, $parameters='');

		echo load_view(array($arr_forms, $arr_fields=array(), make_fancy_url($base_url, 'installation', 'index', 'install_phango', array('op' => 1) )), 'common/forms/updatemodelform'); 


		break;

		case 1:

		echo '<h3>'.$lang['installation']['installing_modules'].'</h3>';

		$arr_modules=array('modules', 'config', 'bans', 'blocks', 'pages', 'templates', 'jscript');

		update_models_from_module($arr_modules);

		$config_data=array();

		$query=$model['config_webtsys']->select();

		$config_data=webtsys_fetch_array($query);

		$arr_modules=array('user');

		update_models_from_module($arr_modules);

		$post['private_nick']=$_POST['private_nick'];
		$post['email']=$_POST['email'];
		
		$password=generate_random_password();
		$post['password']=sha1($password);

		$post['date_register']=TODAY;
		$post['format_date']=$config_data['date_format'];
		$post['format_time']=obtain_timestamp_zone(MY_TIMEZONE);
		$post['timezone']=MY_TIMEZONE;
		$post['ampm']=$config_data['ampm'];
		$post['last_connection']=TODAY;
		$post['begin_last_connection']=TODAY;
		$post['privileges_user']=2;
		$post['language']=$language;
		$post['activated_user']=1;

		if($model['user']->insert($post))
		{
			//If success insert, see login data

			echo "<p>Admin user is created: </p><p>Email: ".$post['email']."</p><p>Password: ".$password."</p>";

		}
		else
		{

			//If error, clean database and out error...
			$std_error=$model['user']->std_error;

			deleting_db($model);

			echo "<p>Error: cleaning database for new install..., please, check your install</p>";

		}

		?>
		<p><?php echo $lang['installation']['phango_is_installed_if_not_error']; ?></p>
		<p><?php echo $lang['installation']['you_can_remove_welcome_and_installation']; ?></p>
		<p><a href="<?php echo $base_url.'/index.php'; ?>"><?php echo $lang['installation']['go_to_phango']; ?></a></p>
		<?php

		break;

	}

	$cont_index.=ob_get_contents();

	ob_end_clean();

	echo load_view(array('title' => $lang['installation']['install_phango'], 'content' => $cont_index), 'common/common');
}

function deleting_db($model_list)
{

	foreach($model_list as $table => $model_select)
	{

		$query=webtsys_query('drop table '.$table);

	}

}


?>