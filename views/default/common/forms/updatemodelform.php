<?php

function UpdateModelFormView($model_form, $arr_fields=array(), $url_post, $enctype='', $form_html_id='', $arr_categories=array())
{

	global $lang;

	load_libraries(array('generate_forms'));

	?>
	<form method="post" action="<?php echo $url_post; ?>" name="form" id="form<?php echo $form_html_id; ?>" <?php echo $enctype; ?>>
	<?php
	set_csrf_key();
	
	/*function generate_form($arr_fields, $order_fields=array(), $view='common/forms/modelform')
	{

		return load_view(array($arr_fields, $order_fields), $view);

	}*/
	
	echo load_view(array($model_form, $arr_fields), 'common/forms/modelform');
	//echo generate_form($model_form, $arr_fields, 'common/forms/modelform');

	?>
	<?php //echo set_csrf_key(); ?>
	<input type="submit" value="<?php echo $lang['common']['send']; ?>" />
	<p class="error"><?php echo $lang['common']['with_*_field_required']; ?></p>
	</form>
	<?php

}

?>
