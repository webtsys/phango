<?php

global $arr_i18n;

load_libraries(array('i18n_fields'));

class Contact extends Webmodel {

	function __construct()
	{

		parent::__construct("contact");

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

$model['contact']=new Contact('contact');

$model['contact']->components['name']=new I18nField(new TextField());
$model['contact']->components['name']->required=1;

SlugifyField::add_slugify_i18n_fields('contact', 'name');

foreach($arr_i18n as $lang_i18n)
{

	$model['contact']->components['name_'.$lang_i18n]->type='VARCHAR(255)';
	$model['contact']->components['name_'.$lang_i18n]->indexed=true;

}

$model['contact']->components['description']=new I18nField(new TextHTMLField());
$model['contact']->components['email']=new EmailField();
$model['contact']->components['email']->required=1;

$model['contact']->set_component('template', 'CharField', array(255));

$model['contact_field']=new Webmodel('contact_field');

$arr_options_fields=array('TextField', 'TextHTMLField');

$model['contact_field']->components['name']=new I18nField(new TextField());
$model['contact_field']->components['name']->required=1;
$model['contact_field']->components['type']=new ChoiceField(64, 'string', $arr_options_fields, $default_value='TextField');
$model['contact_field']->components['type']->required=1;
$model['contact_field']->components['idcontact']=new ForeignKeyField('contact');
$model['contact_field']->components['idcontact']->form='HiddenForm';
$model['contact_field']->components['type']->required=1;
$model['contact_field']->components['order']=new IntegerField();
$model['contact_field']->components['required']=new BooleanField();

$arr_module_insert['contact']=array('name' => 'contact', 'admin' => 1, 'admin_script' => array('contact', 'contact'), 'load_module' => '', 'order_module' => 7, 'app_index' => 1, 'required' => 0);

$arr_module_remove['contact']=array('contact', 'contact_field');

?>