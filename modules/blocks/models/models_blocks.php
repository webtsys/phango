<?php

load_libraries(array('i18n_fields'));

class blocks extends Webmodel {

	function __construct()
	{

		parent::__construct("blocks");

	}	
	
}

$model['blocks']=new blocks();

$model['blocks']->delete_func='delete_blocks';

$model['blocks']->set_component('title_block', 'I18nField', array(new TextField));
$model['blocks']->components['title_block']->required=1;
//$model['blocks']->components['title_block']->form='TextForm';

$model['blocks']->components['url_block']=new CharField(255);
$model['blocks']->components['url_block']->form='TextForm';

$model['blocks']->components['hierarchy_block']=new IntegerField(11);

$model['blocks']->components['activation']=new IntegerField(2);

$model['blocks']->components['module']=new CharField(40);

$model['blocks']->components['type_block']=new IntegerField(2);

$model['blocks']->set_component('parent', 'ParentField', array('blocks', 11) );

//$model['blocks']->components['language']=new CharField(40);

#Esto chequea si debe heredar o no los bloques por defecto este modulo...

class inheritance_blocks extends Webmodel {

	function __construct()
	{

		parent::__construct("inheritance_blocks");

	}	

}

$model['inheritance_blocks']=new inheritance_blocks();

$model['inheritance_blocks']->components['module']=new CharField(255);
$model['inheritance_blocks']->components['inheritance']=new CharField(255);

class total_blocks extends Webmodel {

	function __construct()
	{

		parent::__construct("total_blocks");

	}	

}

$model['total_blocks']=new total_blocks();

$model['total_blocks']->components['module']=new CharField(255);

$model['total_blocks']->components['num_blocks']=new IntegerField(10);

$model['total_blocks']->components['activation']=new IntegerField(2);

$model['total_blocks']->components['favourite']=new IntegerField(2);

//Config blocks

$model['config_blocks']=new Webmodel('config_blocks');

$model['config_blocks']->set_component('columns_to_edit', 'IntegerField', array(1));

$arr_module_insert['blocks']=array('name' => 'blocks', 'admin' => 1, 'admin_script' => array('blocks', 'blocks'), 'load_module' => 'load_blocks.php', 'order_module' => 2, 'required' => 1);

?>
