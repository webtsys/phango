<?php

class ban extends Webmodel {

	function __construct()
	{

		parent::__construct("ban");

	}	
	
}

$model['ban']=new ban();

$model['ban']->components['iduser']=new ForeignKeyField('user');

$model['ban']->components['description']=new CharField(255);

$model['ban']->components['description']->required=1;

$model['ban']->components['ip']=new CharField(255);

$model['ban']->components['message']=new TextField();

$model['ban']->components['message']->required=1;

$model['ban']->components['time_ban']=new DateField(10);

$model['ban']->components['time_ban']->set_default_time=1;

$model['ban']->components['dynamic']=new IntegerField(2);

$model['ban']->components['modules_ban']=new SerializeField();
$model['ban']->components['modules_ban']->form='SelectManyForm';

$arr_module_insert['bans']=array('name' => 'bans', 'admin' => 1, 'admin_script' => array('bans', 'bans'), 'load_module' => 'load_bans.php', 'order_module' => 4, 'required' => 1);

?>