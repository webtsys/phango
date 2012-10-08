<?php

class banners extends Webmodel {

	function __construct()
	{

		parent::__construct("banners");

	}	
	
}

$model['banners']=new banners();

$model['banners']->components['title']=new CharField(255);

$model['banners']->components['title']->required=1;

$model['banners']->components['content']=new TextHTMLField();

$model['banners']->components['content']->required=1;

$model['banners']->components['position_banner']=new ChoiceField(255, 'string');

$model['banners']->components['check_banner']=new CharField(255);

//Sql to execute for this module...

$arr_module_insert['banners']=array('name' => 'banners', 'admin' => 1, 'admin_script' => array('banners', 'banners'), 'load_module' => 'load_banners.php', 'order_module' => 5, 'required' => 1);

?>