<?php


function make_template_subtitle($model, $idmodel)
{

	global $base_path;

	$pattern=array();
	$replace=array();
	
	list($pattern[], $replace[])=bb_html();

	$query=$model->select('where idtemplate='.$idmodel.' order by position ASC', array('name', 'subtitle', 'text'));

	while(list($name, $subtitle, $text)=webtsys_fetch_row($query))
	{

		$name=$model->components['name']->show_formatted($name);
		$subtitle=$model->components['subtitle']->show_formatted($subtitle);
		$text=$model->components['text']->show_formatted($text);

		content_subtitle($name, $subtitle, $text);	

	}

}

$name_func_template='make_template_subtitle';

function content_subtitle($title, $subtitle, $content)
{

?>
	<div id="box5" class="box-style">
		<div class="title title-style1">
			<h1><?php echo $title; ?></h1>
			<h2><?php echo $subtitle; ?></h2>
		</div>
		<div class="entry">
			<?php echo $content; ?>
		</div>
	</div>
	<br clear="all"/>
	<br />
<?php

}

?>
