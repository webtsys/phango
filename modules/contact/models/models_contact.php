<?php

load_libraries(array('i18n_fields'));

$model['contact']=new Webmodel('contact');

$model['contact']->components['name']=new I18nField(new TextField());
$model['contact']->components['name']->required=1;
$model['contact']->components['email']=new EmailField();
$model['contact']->components['email']->required=1;

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

$arr_module_insert['contact']=array('name' => 'contact', 'admin' => 1, 'admin_script' => array('contact', 'contact'), 'load_module' => '', 'order_module' => 7, 'required' => 0);

$arr_module_remove['contact']=array('contact', 'contact_field');

?>