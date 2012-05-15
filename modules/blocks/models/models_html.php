<?php

class html_block extends Webmodel {

	function __construct()
	{

		parent::__construct("html_block");

	}	
	
}

$model['html_block']=new html_block();

$model['html_block']->components['idblock']=new IntegerField(10);
$model['html_block']->components['idblock']->required=1;

$model['html_block']->components['code']=new TextField();
$model['html_block']->components['code']->required=1;


?>