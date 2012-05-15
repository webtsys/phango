<?php

global $lang;

load_lang('user');

$select_page[]=$lang['user_admin']['user'];
$select_page[]=make_fancy_url($base_url, 'user', 'index', 'user_zone', $arr_data=array());



?>
