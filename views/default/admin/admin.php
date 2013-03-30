<?php

function AdminView($header, $title, $content, $name_modules, $url_modules)
{

	global $base_url, $lang, $arr_cache_jscript, $arr_cache_jscript_gzipped, $arr_i18n, $language, $config_data;
	
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

		<html>
		<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title><?php echo $title; ?></title>
		<link href="<?php echo $base_url; ?>/media/common/style/style.css" rel="stylesheet" type="text/css">
		<?php 
			echo load_jscript_view(); 
		?>
		<?php echo $header; ?>
		</head>
		<body>
		<div id="languages_general">
		<?php

		$arr_selected=array();



		foreach($arr_i18n as $lang_item)
		{
			//set

			$arr_selected[slugify($lang_item)]='no_choose_flag_general';
			$arr_selected[slugify($language)]='choose_flag_general';

			?>
			<a class="<?php echo $arr_selected[slugify($lang_item)]; ?>" href="<?php echo make_fancy_url($base_url, 'user', 'change_lang', 'change_language', array('language' => $lang_item));?>"><img src="<?php echo $base_url; ?>/media/<?php echo $config_data['dir_theme']; ?>/images/languages/<?php echo $lang_item; ?>.png" alt="<?php echo $lang_item; ?>"/></a> 
			<?php

		}

		?>
		</div>

		<div id="center_body">
			<div id="header"><span id="title_phango">Phango</span> <span id="title_framework">Framework!</span> <?php echo $title; ?></div>
			<div class="content">
				<div id="menu">
					<div class="menu_title"><?php echo $lang['admin']['applications']; ?></div>
					<?php

					foreach($name_modules as $key_module => $name_module)
					{
						?>
						<a href="<?php echo $url_modules[$key_module]; ?>"><?php echo $name_module; ?></a>
						<?php
					}

					?>
				</div>
				<div class="cont right_cont">
					<?php echo $content; ?>
				</div>
			</div>
		</div>
	</body>
	</html>
	<?php

}

?>
