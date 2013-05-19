<?php

global $arr_i18n;

load_libraries(array('i18n_fields'));

class page extends Webmodel {

	function __construct()
	{

		parent::__construct("page");

	}
	
	public function insert($post)
	{
	
		$post=$this->components['name']->add_slugify_i18n_post('name', $post);
	
		return parent::insert($post);
	
	}
	
	public function update($post, $conditions="")
	{
	
		$post=$this->components['name']->add_slugify_i18n_post('name', $post);
	
		return parent::update($post, $conditions);
	
	}
	
}

$model['page']=new page();

$model['page']->components['name']=new I18nField(new CharField(600));
//$model['page']->components['name']->form='TextForm';
$model['page']->components['name']->required=1;

$model['page']->components['text']=new I18nField(new TextHTMLField());

SlugifyField::add_slugify_i18n_fields('page', 'name');

foreach($arr_i18n as $lang_i18n)
{

	$model['page']->components['name_'.$lang_i18n]->type='VARCHAR(255)';
	$model['page']->components['name_'.$lang_i18n]->indexed=true;

}

//$model['page']->components['text']->

//$model['page']->components['text']->form='TextAreaBBForm';

class property_page extends Webmodel {

	function __construct()
	{

		parent::__construct("property_page");

	}	
	
}

$model['property_page']=new property_page();

$model['property_page']->components['name']=new CharField(255);
$model['property_page']->components['name']->required=1;

$model['property_page']->components['idpage']=new ForeignKeyField('page');

$model['property_page']->components['idpage']->required=1;

$model['property_page']->components['property']=new ChoiceField(255, 'string');

$model['property_page']->components['property']->required=1;

$model['property_page']->components['options']=new SerializeField();

$model['property_page']->components['order_page']=new IntegerField();

$arr_module_sql['pages']='pages.sql';

$arr_module_insert['pages']=array('name' => 'pages', 'admin' => 1, 'admin_script' => array('pages', 'pages'), 'load_module' => '', 'app_index' => 1, 'required' => 1);

?>
