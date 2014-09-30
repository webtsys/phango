<?php

function ConfigAdmin()
{

	global $model, $config_data, $original_theme, $base_path, $base_url, $lang, $original_theme;

	load_lang('config');

	load_libraries(array('generate_admin_ng', 'admin/generate_admin_class', 'timestamp_zone', 'forms/textareabb'));

	$yes_entities=0;

	$model['config_webtsys']->create_form();

	$model['config_webtsys']->forms['portal_name']->SetForm($config_data['portal_name']);
	$model['config_webtsys']->forms['portal_email']->SetForm($config_data['portal_email']);

	$theme_check=$original_theme;
	
	if($config_data['module_theme']!='')
	{
	
		$theme_check=basename($config_data['module_theme']).'/'.$original_theme;
	
	}
	
	$arr_theme=array($theme_check);
	
	$dir = opendir( $base_path."views" );

	while ( $appearance = readdir( $dir ) )
	{
		if ( $appearance != "." && $appearance != ".." && !preg_match('/^\./', $appearance) && is_dir($base_path."views/".$appearance))
		{

			if( file_exists($base_path."views/".$appearance."/.info_theme") )
			{

				settype($appearance, "string");
				$arr_theme[]=ucwords( strtolower( $appearance ) );
				$arr_theme[count($arr_theme)]=$appearance;

			}
			
		} 
	} 
	closedir( $dir );
	
	//Check modules for see if theme views exists.
	
	$dir = opendir( $base_path."modules" );
	
	while ( $appearance = readdir( $dir ) )
	{
		if ( $appearance != "." && $appearance != ".." && !preg_match('/^\./', $appearance) && is_dir($base_path."modules/".$appearance."/views/".$appearance."/"))
		{
			
			if( file_exists($base_path."modules/".$appearance."/views/".$appearance."/.info_theme") )
			{
				
				settype($appearance, "string");
				$arr_theme[]=ucwords( strtolower( $appearance ) );
				$arr_theme[count($arr_theme)]=$appearance.'/'.$appearance;

			}
			
		} 
	} 
	
	closedir( $dir );

	$model['config_webtsys']->forms['dir_theme']->SetParameters($arr_theme);
	$model['config_webtsys']->forms['x_avatar']->SetForm($config_data['x_avatar']);
	$model['config_webtsys']->forms['y_avatar']->SetForm($config_data['y_avatar']);
	$model['config_webtsys']->forms['date_format']->SetParameters( array( $config_data['date_format'], $lang['config']['option_date_dmy'], 'd-m-Y', $lang['config']['option_date_ymd'], 'Y-m-d') );
	$model['config_webtsys']->forms['ampm']->SetParameters( array( $config_data['ampm'], $lang['config']['ampm24'], 'H:i:s', $lang['config']['ampmAM'], 'h:i:s A') );
	$model['config_webtsys']->forms['accept_bbcode_signature']->SetParameters( array( $config_data['accept_bbcode_signature'], $lang['common']['yes'], 0, $lang['common']['no'], 1) );
	$model['config_webtsys']->forms['name_guest']->SetForm($config_data['name_guest']);
	$model['config_webtsys']->forms['active_users']->SetParameters( array( $config_data['active_users'], $lang['common']['yes'], 1, $lang['common']['no'], 0) );

	$model['config_webtsys']->forms['metatags']->SetForm( $config_data['metatags'] );
	
	$model['config_webtsys']->forms['meta_description']->SetForm( $config_data['meta_description'] );

	$model['config_webtsys']->forms['meta_copyright']->SetForm( $config_data['meta_copyright'] );

	$model['config_webtsys']->forms['meta_author']->SetForm( $config_data['meta_copyright'] );

	$model['config_webtsys']->forms['foot']->SetForm( $config_data['foot'] );

	$model['config_webtsys']->forms['cond_register']->SetForm( $config_data['cond_register'] );

	$model['config_webtsys']->forms['wait_message']->SetForm( $config_data['wait_message'] );

	$model['config_webtsys']->forms['ssl_feature']->SetForm( $config_data['ssl_feature'] );

	//$model['config_webtsys']->forms['cookie_secure']->SetParameters( array( $config_data['cookie_secure'], $lang['common']['yes'], 1, $lang['common']['no'], 0) );

	$model['config_webtsys']->forms['censoring']->SetParameters( array( $config_data['censoring'], $lang['common']['yes'], 1, $lang['common']['no'], 0) );

	$model['config_webtsys']->forms['surveys']->SetParameters( array($config_data['surveys'], $lang['common']['yes'], 1, $lang['common']['no'], 0) );

	$arr_index=array( $config_data['index_page'] , $lang['config']['choose_page'], 0);

	//include($base_path.'models/page.php');
	load_model('pages');

	$query=$model['page']->select('order by name ASC', array('IdPage', 'name'));

	while(list($idpage, $name)=webtsys_fetch_row($query))
	{

		$arr_index[]=$model['page']->components['name']->show_formatted($name);
		$arr_index[]=$idpage;

	}
	
	$model['config_webtsys']->forms['index_page']->SetParameters( $arr_index );

	$model['config_webtsys']->forms['user_extra']->SetParameters( array( $config_data['user_extra'], $lang['common']['yes'], 1, $lang['common']['no'], 0) );

	$model['config_webtsys']->forms['create_user']->SetParameters( array( $config_data['create_user'], $lang['common']['yes'], 0, $lang['common']['no'], 1) );

	$arr_textbb_type=array( $config_data['textbb_type'], $lang['config']['no_textbb'], '');

	// Scripts javascripts for textbb in "/application/media/jscript/textbb"
	// Scripts for load in /libraries/textbb/

	$dir = opendir( $base_path. '/libraries/textbb/' );

	while ( $scriptbb = readdir( $dir ) )
	{
	if ( !preg_match('/^\./', $scriptbb) )
	{

		$arr_textbb_type[]=$scriptbb;
		$arr_textbb_type[]=$scriptbb;
		
	} 
	} 
	closedir( $dir );

	$model['config_webtsys']->forms['textbb_type']->SetParameters( $arr_textbb_type );

	$arr_captcha_type=array( $config_data['captcha_type'], $lang['config']['no_captcha'], '');
	$arr_captcha_check=array('');

	// Scripts javascripts for captcha in "/application/media/jscript/captcha"
	// Scripts for load in /libraries/captcha/

	$dir = opendir( $base_path. '/libraries/captchas/' );

	while ( $script_captcha = readdir( $dir ) )
	{
	if ( !preg_match('/^\./', $script_captcha) )
	{

		$script_captcha=preg_replace('/(.*)\.php/', '$1', $script_captcha);

		$arr_captcha_type[]=$script_captcha;
		$arr_captcha_type[]=$script_captcha;

		$arr_captcha_check[]=$script_captcha;
		
	} 
	} 
	closedir( $dir );

	$model['config_webtsys']->forms['captcha_type']->SetParameters( $arr_captcha_type );
	$model['config_webtsys']->components['captcha_type']->arr_values=$arr_captcha_check;
	
	//Type send email
	
	$arr_mailer_type=array( $config_data['mailer_type'], $lang['config']['no_mailer'], '');
	$arr_mailer_check=array('');
	
	$dir = opendir( $base_path. '/libraries/mailers/' );

	while ( $script_mailer = readdir( $dir ) )
	{
	if ( !preg_match('/^\./', $script_mailer) )
	{

		$script_mailer=preg_replace('/(.*)\.php/', '$1', $script_mailer);

		$arr_mailer_type[]=ucfirst($script_mailer);
		$arr_mailer_type[]=$script_mailer;

		$arr_mailer_check[]=$script_mailer;
		
	} 
	} 
	closedir( $dir );

	$model['config_webtsys']->forms['mailer_type']->SetParameters( $arr_mailer_type );
	$model['config_webtsys']->components['mailer_type']->arr_values=$arr_mailer_check;

	//Timezone
	
	$model['config_webtsys']->components['timezone']->arr_values=timezones_array();
	$model['config_webtsys']->forms['timezone']->SetParameters(timezones_list(MY_TIMEZONE));

	$model['config_webtsys']->forms['time_format']->form='HiddenForm';

	if(isset($_POST['time_format']))
	{

		//settype($_POST['time_format'], 'integer');

		$_POST['time_format']=obtain_timestamp_zone($_POST['timezone']);

	}
	
	//Check themes.
	
	if(isset($_POST['dir_theme']))
	{
		$arr_theme=explode('/', $_POST['dir_theme']);
		
		settype($arr_theme[1], 'string');
		
		$_POST['dir_theme']=$arr_theme[0];
		
		$_POST['module_theme']='';
		
		if($arr_theme[1]!='')
		{
			$_POST['module_theme']='modules/'.basename($arr_theme[1]).'/';
		}
		
	}
	
	//Labels

	$model['config_webtsys']->forms['dir_theme']->label=$lang['config']['dir_theme'];
	$model['config_webtsys']->forms['portal_name']->label=$lang['config']['portal_name'];
	$model['config_webtsys']->forms['portal_email']->label=$lang['config']['portal_email'];
	$model['config_webtsys']->forms['x_avatar']->label=$lang['config']['x_avatar'];
	$model['config_webtsys']->forms['y_avatar']->label=$lang['config']['y_avatar'];
	$model['config_webtsys']->forms['date_format']->label=$lang['config']['date_format'];
	$model['config_webtsys']->forms['ampm']->label=$lang['config']['ampm'];
	$model['config_webtsys']->forms['accept_bbcode_signature']->label=$lang['config']['accept_bbcode_signature'];
	$model['config_webtsys']->forms['name_guest']->label=$lang['config']['name_guest'];
	$model['config_webtsys']->forms['metatags']->label=$lang['config']['metatags'];
	$model['config_webtsys']->forms['meta_description']->label=$lang['config']['meta_description'];
	$model['config_webtsys']->forms['meta_author']->label=$lang['config']['meta_author'];
	$model['config_webtsys']->forms['meta_copyright']->label=$lang['config']['meta_copyright'];
	$model['config_webtsys']->forms['foot']->label=$lang['config']['foot'];
	$model['config_webtsys']->forms['active_users']->label=$lang['config']['active_users'];
	$model['config_webtsys']->forms['ssl_feature']->label=$lang['config']['ssl_feature'];
	//$model['config_webtsys']->forms['cookie_secure']->label=$lang['config']['cookie_secure'];
	$model['config_webtsys']->forms['censoring']->label=$lang['config']['censoring'];
	$model['config_webtsys']->forms['wait_message']->label=$lang['config']['wait_message'];
	$model['config_webtsys']->forms['surveys']->label=$lang['config']['surveys'];
	$model['config_webtsys']->forms['index_page']->label=$lang['config']['index_page'];
	$model['config_webtsys']->forms['user_extra']->label=$lang['config']['user_extra'];
	$model['config_webtsys']->forms['create_user']->label=$lang['config']['create_user'];
	$model['config_webtsys']->forms['textbb_type']->label=$lang['config']['textbb_type'];
	$model['config_webtsys']->forms['cond_register']->label=$lang['config']['cond_register'];
	
	$model['config_webtsys']->forms['timezone']->label=$lang['config']['timezone'];
	$model['config_webtsys']->forms['captcha_type']->label=$lang['config']['captcha_type'];
	$model['config_webtsys']->forms['mailer_type']->label=$lang['config']['mailer_type'];

	//Fields strips for now 'ssl_feature', 'cookie_secure', 'censoring', 'surveys'

	$arr_fields=array('dir_theme', 'portal_name', 'portal_email', 'x_avatar', 'y_avatar', 'date_format', 'time_format', 'timezone', 'ampm', 'accept_bbcode_signature', 'name_guest', 'meta_author', 'metatags', 'meta_description', 'foot', 'meta_copyright', 'active_users', 'wait_message', 'index_page', 'user_extra', 'create_user', 'textbb_type', 'captcha_type', 'mailer_type', 'cond_register');

	/*foreach($model['config_webtsys']->forms as $idfield => $field)
	{

		$arr_fields[]="'".$idfield."'";

	}*/

	//echo implode(", ", $arr_fields);

	$model['config_webtsys']->func_update='Config';

	//InsertModelForm('config_webtsys', set_admin_link( 'change_config', array('IdModule' => $_GET['IdModule']) ), set_admin_link( 'user', array('IdModule' => $_GET['IdModule']) ), $arr_fields, $id=0, $goback=1);

	$admin=new GenerateAdminClass('config_webtsys');
	
	$admin->url_options=set_admin_link( 'change_config', array('IdModule' => $_GET['IdModule']) );
	
	$admin->url_back=$admin->url_options;
	
	$admin->arr_fields_edit=$arr_fields;
	
	$admin->arr_categories=array('basic_config' => array('fields' => array('portal_name', 'portal_email', 'meta_author', 'metatags', 'meta_description', 'foot', 'meta_copyright', 'wait_message', 'cond_register'), 'name_fields' => 'Datos básicos' ), 'appaerance_data' => array('fields' => array('dir_theme'), 'name_fields' => 'Apariencia y configuraciones relacionadas') );
	
	$admin->show_config_mode();
	
}

?>