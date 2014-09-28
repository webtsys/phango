<?php

function UpdateModelFormView($model_form, $arr_fields=array(), $url_post, $enctype='', $form_html_id='', $arr_categories=array('default' => array()))
{

	global $lang, $arr_cache_jscript, $arr_cache_header;

	load_libraries(array('generate_forms'));
	
	$arr_cache_jscript[]='jquery.min.js';
	
	$hide_button_tab=1;
	
	if(isset($arr_categories['default']))
	{
	
		$arr_categories['default']=&$arr_fields;
	
		$hide_button_tab=1;
	
	}

	ob_start();
	?>
	<script language="javascript">
	
	$(document).ready( function () {
	
		<?php
		
		if($hide_button_tab==1)
		{
		
		?>
		
		$('.form_button_tab').hide();
		
		<?php
		
		}
		else
		{
		
		?>
	
		$('.form_button_tab').click( function () {
		
			
		
		});
		
		<?php
		
		}
		
		?>
	
	});
	
	</script>
	<?php
	
	$arr_cache_header[]=ob_get_contents();

	ob_end_clean();
	
	?>
	<form method="post" action="<?php echo $url_post; ?>" name="form" id="form<?php echo $form_html_id; ?>" <?php echo $enctype; ?>>
	<?php
	set_csrf_key();
	
	foreach($arr_categories as $category => $arr_fields_tab)
	{
	
	
		?>
		<div id="<?php echo $category; ?>_tag" class="form_tab">
		<?php
		
		echo load_view(array($model_form, $arr_fields_tab), 'common/forms/modelform');
	
		?>
		</div>
		<?php
	}
	?>
	<?php //echo set_csrf_key(); ?>
	<input type="submit" value="<?php echo $lang['common']['send']; ?>" />
	<p class="error"><?php echo $lang['common']['with_*_field_required']; ?></p>
	</form>
	<?php

}

?>
