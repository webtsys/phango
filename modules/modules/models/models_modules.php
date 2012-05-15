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

$arr_module_insert['modules']=array('name' => 'modules', 'admin' => 1, 'admin_script' => array('modules', 'modules'), 'load_module' => '', 'order_module' => 1, 'required' => 1);

?>