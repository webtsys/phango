<?php

load_model('config');

$config_data=array();

$query=$model['config_webtsys']->select();

$config_data=webtsys_fetch_array($query);

?>