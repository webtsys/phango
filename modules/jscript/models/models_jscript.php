<?php

global $base_url, $base_path;

load_libraries(array('fields/blobfield'));

$model['jscript_cache']=new Webmodel('jscript_cache');

$model['jscript_cache']->components['name']=new CharField(255);
$model['jscript_cache']->components['cache']=new BlobField();
$model['jscript_cache']->components['md5_hash']=new CharField(255);

$model['jscript_image']=new Webmodel('jscript_image');

$path=$base_path.'application/media/upload_images/';
$url_path=$base_url.'/media/upload_images/';

$model['jscript_image']->components['image']=new ImageField('upload', $path, $url_path, '');

$model['jscript_image']->components['image']->required=1;

$arr_module_insert['jscript']=array('name' => 'jscript', 'admin' => 0, 'admin_script' => array('jscript', 'jscript'), 'load_module' => '', 'order_module' => 0, 'required' => 1);

?>

