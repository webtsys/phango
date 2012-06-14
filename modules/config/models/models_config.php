<?php

#include("../classes/webmodel.php");

class config_webtsys extends Webmodel {

	function __construct()
	{

		parent::__construct("config_webtsys");

	}	
	
}

$model['config_webtsys']=new config_webtsys();

$model['config_webtsys']->components['dir_theme']=new CharField(255);
$model['config_webtsys']->components['dir_theme']->form='SelectForm';
$model['config_webtsys']->components['dir_theme']->required=1;

$model['config_webtsys']->components['portal_name']=new CharField(255);
$model['config_webtsys']->components['portal_name']->required=1;

$model['config_webtsys']->components['portal_email']=new EmailField(255);
$model['config_webtsys']->components['portal_email']->required=1;

$model['config_webtsys']->components['x_avatar']=new IntegerField(10);

$model['config_webtsys']->components['y_avatar']=new IntegerField(10);

$model['config_webtsys']->components['date_format']=new CharField(10);
$model['config_webtsys']->components['date_format']->form='SelectForm';

$model['config_webtsys']->components['time_format']=new IntegerField(11);
$model['config_webtsys']->components['timezone']=new ChoiceField(50, 'string');
$model['config_webtsys']->components['timezone']->form='SelectForm';
$model['config_webtsys']->components['timezone']->required=1;

//$model['config_webtsys']->components['time_format']->form='SelectForm';

$model['config_webtsys']->components['ampm']=new CharField(10);
$model['config_webtsys']->components['ampm']->form='SelectForm';

$model['config_webtsys']->components['accept_bbcode_signature']=new IntegerField(2);
$model['config_webtsys']->components['accept_bbcode_signature']->form='SelectForm';

$model['config_webtsys']->components['total_users']=new IntegerField(10);

$model['config_webtsys']->components['total_messages']=new IntegerField(10);

$model['config_webtsys']->components['name_guest']=new CharField(255);

$model['config_webtsys']->components['metatags']=new TextField();
$model['config_webtsys']->components['metatags']->form='TextAreaForm';

$model['config_webtsys']->components['meta_description']=new CharField(255);
$model['config_webtsys']->components['meta_description']->form='TextAreaForm';

$model['config_webtsys']->components['meta_author']=new CharField(255);

$model['config_webtsys']->components['meta_copyright']=new CharField(255);
$model['config_webtsys']->components['meta_copyright']->form='TextForm';

$model['config_webtsys']->components['foot']=new TextHTMLField();
$model['config_webtsys']->components['foot']->form='TextAreaBBForm';

$model['config_webtsys']->components['active_users']=new IntegerField(2);
$model['config_webtsys']->components['active_users']->form='SelectForm';

$model['config_webtsys']->components['ssl_feature']=new CharField(255);

/*$model['config_webtsys']->components['cookie_secure']=new IntegerField(2);
$model['config_webtsys']->components['cookie_secure']->form='SelectForm';*/

$model['config_webtsys']->components['censoring']=new IntegerField(2);
$model['config_webtsys']->components['censoring']->form='SelectForm';

$model['config_webtsys']->components['wait_message']=new IntegerField(2);

$model['config_webtsys']->components['surveys']=new IntegerField(2);
$model['config_webtsys']->components['surveys']->form='SelectForm';

$model['config_webtsys']->components['index_page']=new IntegerField(2);
$model['config_webtsys']->components['index_page']->form='SelectForm';

$model['config_webtsys']->components['user_extra']=new IntegerField(2);
$model['config_webtsys']->components['user_extra']->form='SelectForm';

$model['config_webtsys']->components['create_user']=new IntegerField(2);
$model['config_webtsys']->components['create_user']->form='SelectForm';

$model['config_webtsys']->components['textbb_type']=new CharField(150);
$model['config_webtsys']->components['textbb_type']->form='SelectForm';

$model['config_webtsys']->components['captcha_type']=new ChoiceField(150, 'string');
$model['config_webtsys']->components['captcha_type']->form='SelectForm';

$model['config_webtsys']->components['cond_register']=new TextField();
$model['config_webtsys']->components['cond_register']->form='TextAreaForm';

$arr_module_insert['config']=array('name' => 'config', 'admin' => 1, 'admin_script' => array('config', 'config'), 'load_module' => 'load_config.php', 'order_module' => 0, 'required' => 1);

$arr_module_sql['config']='config.sql';

?>
