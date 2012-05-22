<?php
/*********************

# Example config for Web-T-syS Phango 1.0

Well, I think that variables don't need explain but...

**********************/

//No touch, define a constant neccesary for diverses scripts...

define('PAGE', '1');

//Db config variables

//Host database
$host_db = 'localhost';

//Database name
$db = 'phango';

//Username for database
$login_db = 'root';
//Password for database
$pass_db = '';
//Connection type, normally you don't need to change this
$con_persistente='webtsys_connect';
//Type database server, for now, mysql or derivated
define('TYPE_DB','mysql');

#Path variables

//Cookie_path, normally coincides with the first server directory path
$cookie_path = '/';

//The name of session...
define('COOKIE_NAME', 'webtsys_id');

//base url, without last slash
$base_url = 'http://www.example.com';

//base path, the REAL PATH in the server
$base_path='/var/www/htdocs/phango/';

//DEBUG, if you active DEBUG, phango send messages with error to stdout
define('DEBUG', '1');

#Language variables

//Language, define display language, you can create many new languages using check_language.php
$language = 'en-US';
//Avaliables languages
$arr_i18n=array('es-ES','en-US');

//You don't need to touch this variables
$arr_i18n_ckeditor=array('es-ES' => 'es.js','en-US' => 'en.js');
$arr_i18n_tinycme=array('es-ES' => 'es.js','en-ES' => 'en.js');

//Timezone, define timezone, you can choose timezones from 
define('MY_TIMEZONE', 'America/New_York');

//App index

$app_index='welcome';

$page_404='';

$activated_controllers=array('welcome', 'installation');

//Theme by default, neccesary if you don't use utilities for phango...

$config_data['dir_theme']='default';

//A key for use in different encryption methods..., change for other, a trick is make a sha1sum with a random file.

$prefix_key='bc24ffaf6dd55be07423bf37bdc24d65d5f7b275';

?>
