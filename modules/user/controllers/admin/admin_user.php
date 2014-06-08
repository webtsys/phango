<?php

function UserAdmin()
{
	global $lang, $model, $base_url, $base_path;

	settype($_GET['op'], "integer");
	settype($_GET['search'], "integer");
	settype($_GET['iduser'], "integer");

	load_libraries(array('table_config', 'pages', 'timestamp_zone', 'generate_admin_ng', 'forms/userforms', 'forms/textbbpost'));
	load_libraries(array('func_users'), $base_path.'modules/user/libraries/');

	//ob_start();

	settype($_GET['op'], 'integer');
	//'repeat_password',
	$arr_fields_form=array('IdUser', 'private_nick', 'email', 'password', 'repeat_password', 'privileges_user', 'name', 'last_name', 'enterprise_name', 'address', 'zip_code', 'city' ,'country' ,'phone' ,'fax' ,'nif' ,'website' ,'interests' ,'signature', 'avatar', 'rank' ,'show_email' ,'hidden_status' ,'notify_private_messages', 'format_date' ,'timezone' ,'ampm', 'activated_user', 'language', 'yes_list');

	$model['user']->components['timezone']->arr_values=timezones_array();
	$model['user']->components['password']->required=1;

	$model['user']->forms['password']->required=1;
	$model['user']->forms['repeat_password']->required=1;
	$model['user']->forms['avatar']->form='TextForm';
	
	$model['user']->forms['show_email']->SetForm(0);
	$model['user']->forms['hidden_status']->SetForm(0);

	switch($_GET['op'])
	{

		default:

			settype($_GET['op_edit'], 'integer');

			?>
			<h3><?php echo $lang['user']['admin_users']; ?></h3>
			<?php

			if($_GET['op_edit']==0)
			{
				
				?>
					<p><a href="<?php echo set_admin_link( 'edit_users', array('IdModule' => $_GET['IdModule'], 'op' => 1) ); ?>"><?php echo $lang['user']['create_user']; ?></a> - <a href="<?php echo set_admin_link( 'edit_users', array('IdModule' => $_GET['IdModule'], 'op' => 2) ); ?>"><?php echo $lang['user']['activate_users']; ?></a>
					</p>
					
				<?php 

			}

			$model['user']->components['password']->required=0;
			$model['user']->forms['password']->required=0;
			$model['user']->forms['repeat_password']->required=0;
			
			ListModel('user', array('private_nick', 'activated_user'), set_admin_link( 'edit_user', array('IdModule' => $_GET['IdModule']) ) , 'BasicOptionsListModel', 'where IdUser>0 and activated_user=1', $arr_fields_form);
			

		break;

		case 1:

			?>
				<h3><?php echo $lang['user']['create_user']; ?></h3>
				<?php

				

				$model['user']->forms['timezone']->SetForm(MY_TIMEZONE);
				$model['user']->forms['activated_user']->SetForm(1);
			
				InsertModelForm('user', set_admin_link( 'edit_users', array('IdModule' => $_GET['IdModule'], 'op' => 1) ), set_admin_link( 'edit_users', array('IdModule' => $_GET['IdModule'], 'op' => 1) ) , $arr_fields_form, 0, $lang['user']['create_user']);

				echo '<a href="'.set_admin_link( 'goback', array('IdModule' => $_GET['IdModule']) ).'">'.$lang['common']['go_back'].'</a>';

		break;

		case 2:

			?>
			<h3><?php echo $lang['user']['activate_users']; ?></h3>
			<?php

			$model['user']->components['password']->required=0;
			$model['user']->forms['password']->required=0;
			$model['user']->forms['repeat_password']->required=0;
			
			ListModel('user', array('private_nick', 'activated_user'), set_admin_link( 'edit_user', array('IdModule' => $_GET['IdModule']) ) , 'BasicOptionsListModel', 'where IdUser>0 and activated_user=0', $arr_fields_form);

			echo '<p><a href="'.set_admin_link( 'goback', array('IdModule' => $_GET['IdModule']) ).'">'.$lang['common']['go_back'].'</a></p>';

		break;

	}

}

?>