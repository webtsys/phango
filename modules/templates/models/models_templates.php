<?php

load_libraries(array('i18n_fields'));

class template extends Webmodel {

        function __construct()
        {

                parent::__construct("template");

        }

}

$model['template']=new template();

$model['template']->components['name']=new I18nField(new TextField());
$model['template']->components['name']->required=1;

$model['template']->components['name_template']=new ChoiceField(255, 'string');
$model['template']->components['name_template']->required=1;
//$model['template']->components['name_template']->form='SelectForm';

class template_content extends Webmodel {

        function __construct()
        {

                parent::__construct("template_content");

        }

}

$model['template_content']=new template_content();

$model['template_content']->components['name']=new I18nField(new TextField());
$model['template_content']->components['name']->required=1;

//$model['template_content']->components['subtitle']=new I18nField(new TextField());

$model['template_content']->components['text']=new I18nField(new TextHTMLField());
$model['template_content']->components['text']->required=1;
//$model['template_content']->components['text']->form='TextAreaBBForm';

$model['template_content']->components['idtemplate']=new IntegerField(10);
$model['template_content']->components['idtemplate']->required=1;
//$model['template_content']->components['idtemplate']->form='HiddenForm';

$model['template_content']->components['position']=new IntegerField(10);

$arr_module_insert['templates']=array('name' => 'templates', 'admin' => 1, 'admin_script' => array('templates', 'templates'), 'load_module' => '', 'app_index' => 1, 'required' => 0);


$arr_module_remove['templates']=array('template', 'template_content');

?>
