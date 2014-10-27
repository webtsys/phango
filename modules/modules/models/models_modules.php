<?php

class module extends Webmodel {

	function __construct()
	{

		parent::__construct("module");

	}	
	
}

$model['module']=new module();

$model['module']->components['name']=new CharField(255);
$model['module']->components['admin']=new BooleanField();
$model['module']->components['admin_script']=new SerializeField();
$model['module']->components['load_module']=new CharField(255);
$model['module']->components['order_module']=new CharField(255);
$model['module']->components['app_index']=new BooleanField();
$model['module']->components['required']=new BooleanField();

$model['module']->set_component('yes_config', 'BooleanField', array());

$model['moderators_module']=new Webmodel('moderators_module');
$model['moderators_module']->components['moderator']=new ForeignKeyField('user');
$model['moderators_module']->components['moderator']->name_field_to_field='private_nick';
$model['moderators_module']->components['moderator']->fields_related_model=array('private_nick');
$model['moderators_module']->components['moderator']->required=1;
$model['moderators_module']->components['idmodule']=new ForeignKeyField('module');
$model['moderators_module']->components['idmodule']->required=1;


$arr_module_insert['modules']=array('name' => 'modules', 'admin' => 1, 'admin_script' => array('modules', 'modules'), 'load_module' => '', 'order_module' => 1, 'required' => 1);

?>