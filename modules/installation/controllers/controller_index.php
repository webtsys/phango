<?php

function Index()
{

	global $user_data, $model, $lang, $base_path, $base_url, $cookie_path, $arr_module_insert, $arr_module_sql, $arr_padmin_mod, $language, $config_data, $arr_i18n, $arr_check_table, $connection;
	
	if(!isset($_SESSION['language']))
	{
	
		$_SESSION['language']=$language;
		
	}
	
	$language=$_SESSION['language'];

	load_libraries(array('update_table', 'generate_forms', 'timestamp_zone', 'forms/userforms'));
	
	load_lang('installation', 'user', 'common');
	
	/*$datetime_default=new DateTime();
	
	$timezone_default=$datetime_default->getTimezone()->getName();*/
	
	//Obtain value from ini because phango put default value from config.php in America/New_York
	
	$timezone_default = ini_get('date.timezone');
	
	settype($_GET['op'], 'integer');

	$cont_index='';
	
	$arr_forms=array();
			
	$arr_lang=array($language);
	
	foreach($arr_i18n as $my_lang)
	{
	
		$arr_lang[]=$my_lang;
		$arr_lang[]=$my_lang;
	
	}
	
	$arr_forms['private_nick']=new ModelForm('user', 'private_nick', 'TextForm', $lang['user']['private_nick'], new CharField(255), $required=1, $parameters='');
		
	$arr_forms['email']=new ModelForm('user', 'email', 'TextForm', $lang['common']['email'], new EmailField(255), $required=1, $parameters='');
	
	$arr_forms['language']=new ModelForm('config', 'language', 'SelectForm', $lang['installation']['choose_language'], new ChoiceField(255, 'string', $arr_i18n), $required=1, $parameters=$arr_lang);
	
	$arr_forms_lang['language']=$arr_forms['language'];
	
	//Host_db,db, login_db, pass_db, cookie_path, COOKIE_NAME, $base_url, $base_path, $language, MY_TIMEZONE, app_index=pages, activated_controllers=array('admin', 'pages', 'blog', 'shop','jscript', 'user', 'templates', prefix_key
	
	$arr_forms['host_db']=new ModelForm('config', 'host_db', 'TextForm', $lang['installation']['host_db'], new CharField(255), $required=1, $parameters='localhost');
	
	$arr_forms['db']=new ModelForm('config', 'db', 'TextForm', $lang['installation']['db'], new CharField(255), $required=1, $parameters='');
	
	$arr_forms['login_db']=new ModelForm('config', 'login_db', 'TextForm', $lang['installation']['login_db'], new CharField(255), $required=1, $parameters='');
	
	$arr_forms['pass_db']=new ModelForm('config', 'pass_db', 'PasswordForm', $lang['installation']['pass_db'], new CharField(255), $required=1, $parameters='');
	
	$arr_forms['cookie_path']=new ModelForm('config', 'cookie_path', 'TextForm', $lang['installation']['cookie_path'], new CharField(255), $required=1, $parameters=$cookie_path);
	
	$arr_forms['cookie_name']=new ModelForm('config', 'cookie_name', 'TextForm', $lang['installation']['cookie_name'], new CharField(255), $required=1, $parameters=COOKIE_NAME);
	
	$arr_forms['cookie_name']=new ModelForm('config', 'cookie_name', 'TextForm', $lang['installation']['cookie_name'], new CharField(255), $required=1, $parameters=COOKIE_NAME);
	
	$arr_forms['base_url']=new ModelForm('config', 'base_url', 'TextForm', $lang['installation']['base_url'], new CharField(255), $required=1, $parameters=$base_url);
	
	$arr_forms['base_path']=new ModelForm('config', 'base_path', 'TextForm', $lang['installation']['base_path'], new CharField(255), $required=1, $parameters=$base_path);
	
	$arr_forms['timezone']=new ModelForm('config', 'timezone', 'TimeZoneForm', $lang['installation']['timezone'], new CharField(255), $required=1, $parameters=array($timezone_default));
	
	//$arr_forms['timezone']->SetForm(MY_TIMEZONE);

	switch($_GET['op'])
	{
	
		default:
			
			echo '<h1>'.$lang['installation']['create_config_file'].'</h1>';
			
			echo '<p>'.$lang['installation']['create_config_file_explain'].'</p>';
			
			//new ChoiceField(255, 'string', $arr_i18n);
			
			echo load_view(array($arr_forms_lang, $arr_fields=array(), make_fancy_url($base_url, 'installation', 'index', 'install_phango', array('op' => 1) )), 'common/forms/updatemodelform'); 
		
		break;
		
		case 1:
			
			$_POST['language']=@form_text($_POST['language']);
		
			if(in_array($_POST['language'], $arr_i18n))
			{
				
				$_SESSION['language']=$_POST['language'];
				
			
			}

			header('Location: '.make_fancy_url($base_url, 'installation', 'index', 'install_phango', array('op' => 2) ) );
			die;
		
		break;

		case 2:
			
			//echo $_SESSION['language'];
			
			echo '<h1>'.$lang['installation']['create_config_file'].'</h1>';
			
			echo '<p>'.$lang['installation']['create_config_file_explain_fields'].'</p><hr />';
			
			echo load_view(array($arr_forms, $arr_fields=array(), make_fancy_url($base_url, 'installation', 'index', 'install_phango', array('op' => 3) )), 'common/forms/updatemodelform'); 
		
		break;
		
		case 3:
		
			$post=ModelForm::check_form($arr_forms, $_POST);
			
			if($post==0)
			{
			
				echo '<h1>'.$lang['installation']['create_config_file'].'</h1>';
			
				echo '<p>'.$lang['installation']['create_config_file_explain_fields'].'</p>';
			
				echo '<p>'.$lang['installation']['error_fields'].'</p><hr />';
			
				SetValuesForm($_POST, $arr_forms, $show_error=1);

				echo load_view(array($arr_forms, $arr_fields=array(), make_fancy_url($base_url, 'installation', 'index', 'install_phango', array('op' => 3) )), 'common/forms/updatemodelform'); 
			
			}
			else
			{
			
				echo '<h1>'.$lang['installation']['finishing_install'].'</h1>';
				
				//Check db connection...
				
				$connection=@webtsys_connect( $post['host_db'], $post['login_db'], $post['pass_db'] );

				$select_db=@webtsys_select_db( $post['db'] );
				
				if($connection!=false  && $select_db==1) 
				{
				
					//Connect is up...
					
					//Check if is a clean db.
					
					$query=webtsys_query(SQL_SHOW_TABLES);

					while(list($table)=webtsys_fetch_row($query))
					{

						$arr_check_table[$table]=1;

					}
					
					if(count($arr_check_table)>0)
					{
						
						echo '<p>'.$lang['installation']['cannot_install_new_phango_in_db_no_clean'].'</p>';
						
						echo '<p>'.$lang['installation']['you_need_check_if_you_have_old_valid_config_file'].'</p>';
					
						$cont_index.=ob_get_contents();

						ob_end_clean();

						echo load_view(array('title' => $lang['installation']['install_phango'], 'content' => $cont_index), 'common/common');
						
						die;
					}
					
					//Create config and user...
					
					echo '<h3>'.$lang['installation']['installing_modules'].'</h3>';

					$arr_modules=array('modules', 'user', 'config', 'bans', 'blocks', 'pages', 'templates', 'jscript');

					update_models_from_module($arr_modules);

					$config_data=array();

					$query=$model['config_webtsys']->select();

					$config_data=webtsys_fetch_array($query);

					//$arr_modules=array('user');
					
					if(file_exists($base_path.'/modules/shop/'))
					{

						$arr_modules=array('shop');
						
						update_models_from_module($arr_modules);

					}

					$post['private_nick']=$_POST['private_nick'];
					$post['email']=$_POST['email'];
					
					$password=generate_random_password();
					$post['password']=sha1($password);

					$post['date_register']=TODAY;
					$post['format_date']=$config_data['date_format'];
					
					$post['timezone']=$_POST['timezone'];
					
					//Check if timezone exists.
					
					if(date_create($post['timezone'])===false)
					{
					
						$post['timezone']=$timezone_default;
					
					}
					
					$post['format_time']=obtain_timestamp_zone($_POST['timezone'], $timezone_default);
					
					$post['ampm']=$config_data['ampm'];
					$post['last_connection']=TODAY;
					$post['begin_last_connection']=TODAY;
					$post['privileges_user']=2;
					
					if(in_array($_POST['language'], $arr_i18n))
					{
						
						$post['language']=$_POST['language'];
						
					}
					else
					{
					
						$post['language']=$language;
					
					}
					
					
					$post['activated_user']=1;
					
					$post['rank']=1;

					if($model['user']->insert($post))
					{
						//If success insert, see login data

						echo "<h3><strong>".$lang['installation']['user_installed']."</strong>: </h3><p><strong>Email</strong>: ".
						$post['email']."</p><p><strong>Password</strong>: ".$password."</p>";
						
						echo '<h3>'.$lang['installation']['creating_config_file'].'</h3>';
						
						echo '<p>'.$lang['installation']['create_config_file_copy_paste'].'</p><hr />';
				
						$file_sample=file_get_contents($base_path.'config_sample.php');
						
						$file_sample=str_replace('$host_db = \'localhost\';', '$host_db = \''.$post['host_db'].'\';', $file_sample);
						
						$file_sample=str_replace('$db = \'phango\';', '$db = \''.$post['db'].'\';', $file_sample);
						
						$file_sample=str_replace('$login_db = \'root\';', '$login_db = \''.$post['login_db'].'\';', $file_sample);
						
						$file_sample=str_replace('$pass_db = \'\';', '$pass_db = \''.$post['pass_db'].'\';', $file_sample);
						
						$file_sample=str_replace('$cookie_path = \'/\';', '$cookie_path = \''.$post['cookie_path'].'\';', $file_sample);
						
						$file_sample=str_replace('define(\'COOKIE_NAME\', \'webtsys_id\');', 'define(\'COOKIE_NAME\', \''.$post['cookie_name'].'\');', $file_sample);
						
						$file_sample=str_replace('$base_url = \'http://www.example.com\';', '$base_url = \''.$post['base_url'].'\';', $file_sample);
					
						$file_sample=str_replace('$base_path = \'/var/www/htdocs/phango/\';', '$base_path = \''.$post['base_path'].'\';', $file_sample);
						
						$file_sample=str_replace('$language = \'en-US\';', '$language = \''.$post['language'].'\';', $file_sample);
					
						$file_sample=str_replace('define(\'MY_TIMEZONE\', \'America/New_York\');', 'define(\'MY_TIMEZONE\', \''.$post['timezone'].'\');', $file_sample);
						
						$file_sample=str_replace('$app_index = \'welcome\';', '$app_index = \'pages\';', $file_sample);
						
						$file_sample=str_replace('$activated_controllers = array(\'welcome\', \'installation\', \'jscript\');', 
						'$activated_controllers = array(\'admin\', \'pages\', \'blog\', \'shop\', \'jscript\', \'user\', \'templates\');', $file_sample);
						
						$rand_prefix=sha1( uniqid(generate_random_password(), true) );
					
						$file_sample=str_replace('$prefix_key = \'bc24ffaf6dd55be07423bf37bdc24d65d5f7b275\';', '$prefix_key = \''.$rand_prefix.'\';', $file_sample);
					
						echo '<div class="form">';
						
						echo TextAreaForm('config_file', '', $file_sample);
						
						echo '</div>';

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
					
					
				}
				else
				{
				
					echo '<h1>'.$lang['installation']['create_config_file'].'</h1>';
			
					echo '<p>'.$lang['installation']['create_config_file_explain_fields'].'</p>';
				
					echo '<p>'.$lang['installation']['error_fields_db'].'</p><hr />';
				
					SetValuesForm($_POST, $arr_forms, $show_error=1);
					
					$arr_forms['db']->std_error=$lang['installation']['is_correct_db'];
					$arr_forms['host_db']->std_error=$lang['installation']['is_correct_host_db'];
					$arr_forms['login_db']->std_error=$lang['installation']['is_correct_login_db'];
					$arr_forms['pass_db']->std_error=$lang['installation']['is_correct_pass_db'];

					echo load_view(array($arr_forms, $arr_fields=array(), make_fancy_url($base_url, 'installation', 'index', 'install_phango', array('op' => 3) )), 'common/forms/updatemodelform'); 
				
				}
			
			}
		
		break;
		
		/*case 3:

		echo '<h3>'.$lang['installation']['create_user'].'</h3>';

		echo '<p>'.$lang['installation']['create_user_explain'].'</p>';

		$arr_forms=array();

		$arr_forms['private_nick']=new ModelForm('user', 'private_nick', 'TextForm', $lang['user']['private_nick'], new CharField(255), $required=1, $parameters='');
		
		$arr_forms['email']=new ModelForm('user', 'email', 'TextForm', $lang['common']['email'], new EmailField(255), $required=1, $parameters='');
		
		//Host_db,db, login_db, pass_db, cookie_path, COOKIE_NAME, $base_url, $base_path, $language, MY_TIMEZONE, app_index=pages, activated_controllers=array('admin', 'pages', 'blog', 'shop','jscript', 'user', 'templates'
		//prefix_key
		
		
		echo load_view(array($arr_forms, $arr_fields=array(), make_fancy_url($base_url, 'installation', 'index', 'install_phango', array('op' => 1) )), 'common/forms/updatemodelform'); 

		break;

		case 3:

		echo '<h3>'.$lang['installation']['installing_modules'].'</h3>';

		$arr_modules=array('modules', 'config', 'bans', 'blocks', 'pages', 'templates', 'jscript');

		if(file_exists($base_path.'/modules/shop/'))
		{

			$arr_modules[]='shop';

		}

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

		break;*/

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