<?php

function AllView($title, $content, $block_title, $block_content, $block_urls, 
$block_type, $block_id, $config_data, $headers='')
{

global $base_url, $base_path, $arr_i18n, $language, $lang, $user_data, $arr_cache_jscript, $arr_currency, $arr_check_table;

settype($_COOKIE['webtsys_shop'], 'string');

$token=$_COOKIE['webtsys_shop'];

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html>
	<head>
	<title><?php echo $config_data['portal_name'].' - '.$title; ?></title>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<?php 
		$arr_cache_jscript[]='jquery.min.js';
		echo load_jscript_view(); 
		
		$arr_cache_css[]='style.css';
		
		echo load_css_view();
		echo load_header_view();
	?>
	<script language="Javascript" src="<?php echo make_fancy_url($base_url, 'shop/ajax', 'functions_jscript', 'functions_jscript', array()); ?>"></script>
	<script language="Javascript">
		obtain_data_cart();
	</script>
	<?php echo $headers; ?>
	<meta name="keywords" content="<?php echo $config_data['metatags']; ?>" />
	</head>
<body>
<div id="center_body">

<div id="header">
<img id="logo_img" src="<?php echo get_url_image('logo.png'); ?>" />
<div id="your_account">
	<?php
	if($user_data['IdUser']==0)
	{
	?>
		<a href="<?php echo make_fancy_url($base_url, 'user', 'index', $lang['user']['login'], array()); ?>"><?php echo $lang['user']['my_account']; ?></a>

	<?php
	}
	else
	{
	?>
		<a href="<?php echo make_fancy_url($base_url, 'user', 'index', $lang['common']['logout'], array('op' => 2)); ?>"><?php echo $lang['common']['logout']; ?></a>

	<?php
	}
	?>
</div>
<div id="languages">
<?php

$arr_selected=array();



foreach($arr_i18n as $lang_item)
{
	//set

	$arr_selected[slugify($lang_item)]='no_choose_flag';
	$arr_selected[slugify($language)]='choose_flag';

	?>
	<a class="<?php echo $arr_selected[slugify($lang_item)]; ?>" href="<?php echo make_fancy_url($base_url, 'user', 'change_lang', 'change_language', array('language' => $lang_item));?>"><img src="<?php echo get_url_image('languages/'.$lang_item.'.png'); ?>" alt="<?php echo $lang_item; ?>"/></a>
	<?php

}

?>
</div>
<?php

if(isset($arr_check_table['product']))
{

load_lang('shop');

?>
<div id="cart">
<a href="<?php echo make_fancy_url($base_url, 'shop', 'cart', $lang['shop']['cart'], array()); ?>"><?php echo $lang['shop']['cart']; ?></a> <span id="cart_content"></span>
</div>
<div id="change_currency">
	<form method="get" action="<?php echo make_fancy_url($base_url, 'shop', 'changecurrency', 'change_currency', array()); ?>">
	<p><?php echo $lang['shop']['change_currency']; ?>: 
	<?php  

	settype($_SESSION['idcurrency'], 'integer');

	$arr_select_currency=array($_SESSION['idcurrency']);

	if(!isset($arr_currency))
	{

		$query=webtsys_query('select idcurrency, symbol from currency');

		while(list($idcurrency, $symbol_currency)=webtsys_fetch_row($query))
		{

			$arr_currency[$idcurrency]=$symbol_currency;

		}

	}

	foreach($arr_currency as $key_currency => $name_currency)
	{

		$arr_select_currency[]=$name_currency;
		$arr_select_currency[]=$key_currency;

	}
	

	echo SelectForm('idcurrency', '', $arr_select_currency); 
	?>
	<input type="submit" value="<?php echo $lang['common']['send']; ?>" />
	</form>
</div>
<?php

}

?>
</div>
<div id="menu_barr">
<?php
$c_barr=count($block_content['barr']);
$arr_barr=array();

for($i=0;$i<$c_barr;$i++)
{

	//echo $block_content['left'][$i];
	
	$arr_barr[]='<a href="'.$block_urls['barr'][$i].'">'.$block_content['barr'][$i].'</a>';
	
	//echo $block_type['left'][$i];

}

echo implode(' - ', $arr_barr);

?>
</div>

<div id="menu" class="menu_left">

<?php

$c_title=count($block_content['left']);

for($i=0;$i<$c_title;$i++)
{

	//echo $block_content['left'][$i];
	$block_type['left'][$i]($block_content['left'][$i], $block_urls['left'][$i], $block_id['left'][$i]);
	//echo $block_type['left'][$i];

}
?>

</div>

<div id="content" class="content_all">
<?php echo $content; ?>
</div>

<div id="menu" class="menu_right">

<?php

$c_title=count($block_content['right']);

for($i=0;$i<$c_title;$i++)
{

	//echo $block_content['right'][$i];
	$block_type['right'][$i]($block_content['right'][$i], $block_urls['right'][$i], $block_id['right'][$i]);
	//echo $block_type['right'][$i];

}
?>

</div>
<br clear="all" />
<div id="footer">
	<div id="footer-align">
		<?php echo $config_data['foot']; ?>
	</div>
</div>
</div>

</body>	
</html>

<?php

}

function block_html($title, $content, $id)
{
	global $base_path; 
	
	?>
	<div class="static_block"><?php include($title); ?></div>
	<?php

}

function block_title($title)
{

	?>
	<div class="title_block"><?php echo $title; ?></div>
	<?php

}


function block_url($title, $url)
{

	?>
	<div class="url_block"><a href="<?php echo $url; ?>"><?php echo $title; ?></a></div>
	<?php

}


function block_begin($title, $url)
{

	?>
	<!--begin block-->
	<?php

}

function block_end($title, $url)
{

	?>
	<!--end block-->
	<div class="block_end">&nbsp;</div>
	<?php

}

?>
