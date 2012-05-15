<?php

function UpdateModelFormView($model_form, $arr_fields=array(), $url_post, $enctype='')
{

	global $lang;

	load_libraries(array('generate_forms'));

	?>
	<form method="post" action="<?php echo $url_post; ?>" name="form" id="form" <?php echo $enctype; ?>>
	<?php
	set_csrf_key();
	//echo load_view(array($model_name, $arr_fields), 'common/forms/modelform');
	echo generate_form($model_form, $arr_fields, 'common/forms/modelform');

	?>
	<?php //echo set_csrf_key(); ?>
	<input type="submit" value="<?php echo $lang['common']['send']; ?>" />
	</form>
	<?php

}

?>
