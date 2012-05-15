<?php

load_libraries(array('fields/blobfield'));

$model['jscript_cache']=new Webmodel('jscript_cache');

$model['jscript_cache']->components['name']=new CharField(255);
$model['jscript_cache']->components['cache']=new BlobField();
$model['jscript_cache']->components['md5_hash']=new CharField(255);

$arr_module_insert['jscript']=array('name' => 'jscript', 'admin' => 0, 'admin_script' => array('jscript', 'jscript'), 'load_module' => '', 'order_module' => 0, 'required' => 1);

?>

