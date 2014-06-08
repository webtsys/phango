<?php

class ban extends Webmodel {

	function __construct()
	{

		parent::__construct("ban");

	}	
	
}

$model['ban']=new ban();

$model['ban']->set_component('iduser', 'ForeignKeyField', array('user'));

$model['ban']->components['iduser']->yes_zero=1;

$model['ban']->set_component('description', 'CharField', array(255));

$model['ban']->components['description']->required=1;

$model['ban']->components['ip']=new CharField(255);

$model['ban']->set_component('message', 'TextField', array());

$model['ban']->components['message']->required=1;

$model['ban']->set_component('time_ban', 'DateField', array(10));

$model['ban']->components['time_ban']->set_default_time=1;

$model['ban']->set_component('dynamic', 'IntegerField', array(2));

$model['ban']->set_component('modules_ban', 'SerializeField', array());
$model['ban']->components['modules_ban']->form='SelectManyForm';

$arr_module_insert['bans']=array('name' => 'bans', 'admin' => 1, 'admin_script' => array('bans', 'bans'), 'load_module' => 'load_bans.php', 'order_module' => 4, 'required' => 1);

?>