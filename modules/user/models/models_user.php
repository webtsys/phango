<?php

global $allowedtags_signature, $base_url, $base_path, $arr_i18n, $config_data, $language, $lang;

load_libraries(array('avatarfield'), $base_path.'modules/user/libraries/');

$model['user']=new Webmodel("user");

$model['user']->change_id_default('IdUser');

$model['user']->components['private_nick']=new CharField(255);
$model['user']->components['password']=new CharField(255);
$model['user']->components['password']->form='PasswordForm';
$model['user']->components['email']=new EmailField(255);
$model['user']->components['name']=new CharField(255);
$model['user']->components['last_name']=new CharField(255);
$model['user']->components['address']=new CharField(255);
$model['user']->components['zip_code']=new CharField(255);
$model['user']->components['region']=new CharField(255);
$model['user']->components['city']=new CharField(255);
$model['user']->components['country']=new CharField(255);
$model['user']->components['phone']=new CharField(255);//Only for special effects...
$model['user']->components['fax']=new CharField(255);//Only for special effects...
$model['user']->components['nif']=new CharField(255);//Only for special effects...
$model['user']->components['enterprise_name']=new CharField(255);//Only for special effects...
$model['user']->components['website']=new CharField(255);
$model['user']->components['interests']=new CharField(255);
$model['user']->components['signature']=new CharField(255);
$model['user']->components['avatar']=new AvatarField(500);
$model['user']->components['theme_user']=new CharField(255);
$model['user']->components['rank']=new ForeignKeyField('rank', 11, 1, 1);
$model['user']->components['show_email']=new BooleanField(1);
$model['user']->components['hidden_status']=new BooleanField(1);
$model['user']->components['notify_private_messages']=new BooleanField(1);
$model['user']->components['num_messages']=new IntegerField(11);
$model['user']->components['last_message']=new IntegerField(11);
$model['user']->components['date_register']=new DateField();
$model['user']->components['last_connection']=new IntegerField(11);
$model['user']->components['before_last_connection']=new IntegerField(11);
$model['user']->components['format_date']=new ChoiceField(10, 'string', array('d-m-Y', 'Y-m-d'));
$model['user']->components['format_time']=new IntegerField(11);
$model['user']->components['timezone']=new ChoiceField(35, 'string', array(), MY_TIMEZONE);
$model['user']->components['ampm']=new ChoiceField(10, 'string', array('H:i:s', 'h:i:s A'), MY_TIMEZONE);
$model['user']->components['privileges_user']=new ChoiceField(2, 'integer', array(0, 1, 2));
$model['user']->components['privileges_user']->form='SelectForm';
$model['user']->components['key_connection']=new CharField(50);
$model['user']->components['key_privileges']=new CharField(50);
$model['user']->components['key_csrf']=new CharField(250);
$model['user']->components['ip']=new CharField(20);
$model['user']->components['write_message']=new IntegerField(11);
$model['user']->components['activated_user']=new BooleanField(1);
$model['user']->components['visited_page']=new CharField(255);
$model['user']->components['language']=new ChoiceField(255, 'string', $arr_i18n);

$model['user']->components['yes_list']=new BooleanField(1);

$model['user']->components['private_nick']->required=1;
$model['user']->components['email']->required=1;


$model['user']->func_update='User';

$allowedtags_signature['a']=array('pattern' => '/&lt;a.*?href=&quot;(http:\/\/.*?)&quot;.*?&gt;(.*?)&lt;\/a&gt;/', 'replace' => '<a_tmp href="$1" target="_blank">$2</a_tmp>', 'example' => '<a href=""></a>');
$allowedtags_signature['p']=array('pattern' => '/&lt;p.*?&gt;(.*?)&lt;\/p&gt;/s', 'replace' => '<p_tmp>$1</p_tmp>','example' => '<p></p>');
$allowedtags_signature['br']=array('pattern' => '/&lt;br.*?\/&gt;/', 'replace' => '<br_tmp />', 'example' => '<br />');
$allowedtags_signature['strong']=array('pattern' => '/&lt;strong.*?&gt;(.*?)&lt;\/strong&gt;/s', 'replace' => '<strong_tmp>$1</strong_tmp>', 'example' => '<strong></strong>');
$allowedtags_signature['em']=array('pattern' => '/&lt;em.*?&gt;(.*?)&lt;\/em&gt;/s', 'replace' => '<em_tmp>$1</em_tmp>', 'example' => '<em></em>');
$allowedtags_signature['i']=array('pattern' => '/&lt;i.*?&gt;(.*?)&lt;\/i&gt;/s', 'replace' => '<i_tmp>$1</i_tmp>', 'example' => '<i></i>');
$allowedtags_signature['u']=array('pattern' => '/&lt;u.*?&gt;(.*?)&lt;\/u&gt;/s', 'replace' => '<u_tmp>$1</u_tmp>', 'example' => '<u></u>');
$allowedtags_signature['blockquote']=array('pattern' => '/&lt;blockquote.*?&gt;(.*?)&lt;\/blockquote&gt;/s', 'replace' => '<blockquote_tmp>$1</blockquote_tmp>', 'example' => '<blockquote></blockquote>', 'recursive' => 1);

if(ini_get ( "allow_url_fopen" )==1)
{

	$allowedtags_signature['img']=array('pattern' => '/&lt;img.*?src=&quot;(http:\/\/.*?)&quot;.*?\/&gt;/e', 'replace' => 'check_image(\'$1\')', 'example' => '<img src="http://www.domain.com/images/image.png" />');

}

$model['user']->components['signature']->form='TextAreaBBPostForm';

$model['user']->create_form();

$model['user']->forms['repeat_password']=new ModelForm('user', 'repeat_password', 'PasswordForm', $lang['user']['repeat_password'], new CharField(), 0, '');
$model['user']->forms['automatic_login']=new ModelForm('user', 'automatic_login', 'CheckBoxForm', $lang['user']['automatic_login'], new IntegerField(), 0, 0);
$model['user']->forms['private_nick']->label=$lang['user']['private_nick'];
$model['user']->forms['email']->label=$lang['common']['email'];
$model['user']->forms['email']->txt_error=$lang['common']['error_email_format'];
$model['user']->forms['password']->label=$lang['common']['password'];
$model['user']->forms['privileges_user']->label=$lang['user']['privileges_user'];
$model['user']->forms['rank']->form='RankForm';

$model['user']->forms['show_email']->form='SelectForm';
$model['user']->forms['show_email']->SetForm(1);
$model['user']->forms['show_email']->label=$lang['user']['show_email'];

$model['user']->forms['hidden_status']->form='SelectForm';
$model['user']->forms['hidden_status']->SetForm(1);
$model['user']->forms['hidden_status']->label=$lang['user']['hidden_status'];

$model['user']->forms['notify_private_messages']->form='SelectForm';
$model['user']->forms['notify_private_messages']->SetForm(1);
$model['user']->forms['notify_private_messages']->label=$lang['user']['notify_private_messages'];

$model['user']->forms['privileges_user']->SetParameters(array(0, $lang['common']['without_privileges'], 0, $lang['common']['moderator'], 1, $lang['common']['administrator'], 2));
$model['user']->forms['privileges_user']->label=$lang['user']['privileges_user'];

$model['user']->forms['format_date']->form='SelectForm';

$model['user']->forms['format_date']->label=$lang['user']['format_date'];

$model['user']->forms['timezone']->form='TimeZoneForm';
$model['user']->forms['timezone']->label=$lang['user']['timezone'];

$model['user']->forms['ampm']->form='SelectForm';

$model['user']->forms['ampm']->label=$lang['user']['ampm'];

/*$model['user']->forms['activated_user']->form='SelectForm';
$model['user']->forms['activated_user']->SetParameters(array(0, $lang['common']['yes'], 0, $lang['common']['no'], 1));*/

$model['user']->forms['language']->form='SelectForm';
$model['user']->forms['language']->label=$lang['common']['language'];

$model['user']->forms['yes_list']->form='SelectForm';
$model['user']->forms['yes_list']->SetParameters(array(0, $lang['common']['yes'], 0, $lang['common']['no'], 1));
$model['user']->forms['yes_list']->label=$lang['user']['yes_list'];

$model['user']->forms['avatar']->form='AvatarForm';

$model['user']->forms['avatar']->label=$lang['user']['avatar'];

$model['user']->forms['rank']->label=$lang['user']['rank'];

$model['user']->forms['signature']->label=$lang['user']['signature'];

$model['user']->forms['interests']->label=$lang['user']['interests'];

$model['user']->forms['website']->label=$lang['common']['website'];

$model['user']->forms['nif']->label=$lang['user']['nif'];

$model['user']->forms['name']->label=$lang['common']['name'];

$model['user']->forms['last_name']->label=$lang['common']['last_name'];

$model['user']->forms['enterprise_name']->label=$lang['user']['enterprise_name'];

$model['user']->forms['address']->label=$lang['common']['address'];

$model['user']->forms['zip_code']->label=$lang['user']['zip_code'];

$model['user']->forms['city']->label=$lang['common']['city'];

$model['user']->forms['country']->label=$lang['common']['country'];

$model['user']->forms['phone']->label=$lang['common']['phone'];

$model['user']->forms['fax']->label=$lang['common']['fax'];

$model['user']->forms['activated_user']->label=$lang['user']['activated_user'];

$arr_i18n_lang=array($language);

foreach($arr_i18n as $my_lang)
{

	$arr_i18n_lang[]=ucfirst($my_lang);
	$arr_i18n_lang[]=$my_lang;

}

$model['user']->forms['language']->SetParameters( $arr_i18n_lang );

class anonymous extends Webmodel {

	function __construct()
	{

		parent::__construct("anonymous");

	}	
	
}

$model['anonymous']=new anonymous();

$model['anonymous']->components['key_connection']=new CharField(50);
$model['anonymous']->components['key_csrf']=new CharField(250);
$model['anonymous']->components['ip']=new CharField(50);
$model['anonymous']->components['last_connection']=new IntegerField(11);
$model['anonymous']->components['visited_page']=new CharField(255);
$model['anonymous']->components['write_message']=new IntegerField(11);
$model['anonymous']->components['language']=new ChoiceField(255, 'string', $arr_i18n);

class login_tried extends Webmodel {

	function __construct()
	{

		parent::__construct("login_tried");

	}	
	
}

$model['login_tried']=new login_tried();

$model['login_tried']->components['ip']=new CharField(255);
$model['login_tried']->components['num_tried']=new IntegerField(11);
$model['login_tried']->components['time']=new IntegerField(11);

$model['recovery_password']=new Webmodel('recovery_password');

$model['recovery_password']->components['iduser']=new ForeignKeyField('user', 11);
$model['recovery_password']->components['token_recovery']=new CharField(255);
$model['recovery_password']->components['date_token']=new DateField();

//Variable for more options in user...

function user_options($iduser)
{
	global $base_path;
	
	$arr_options=array();

	include($base_path.'modules/user/admin/options/options.php');

	return implode('<br />', $arr_options);

}

function generate_random_password($length_pass=14)
{

	$x=0;
	$z=0;

	$abc = array( 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '*', '+', '!', '-', '_');
	
	shuffle($abc);
	
	$c_chars=count($abc)-1;

	$password_final='';

	for($x=0;$x<$length_pass;$x++)
	{

		$z=mt_rand(0, $c_chars);
		
		$password_final.=$abc[$z];

	}

	return $password_final;

}

$arr_module_insert['user']=array('name' => 'user', 'admin' => 1, 'admin_script' => array('user', 'user'), 'load_module' => 'load_session.php', 'order_module' => 1, 'app_index' => 1, 'required' => 1);

$arr_module_sql['user']='user.sql';

?>