<?php

function Index()
{

	global $base_url;

	echo load_view(array('title' => 'Welcome to Phango!', 'content' => 'This is the phango framework!!!!. If you plan a standard installation you can begin database and modules install in <a href="'.make_fancy_url($base_url, 'installation', 'index', 'install_phango', array()).'">clicking here</a>'), 'common/common');

}

?>