<?php

function Browser_list_field()
{

	global $model, $base_path, $base_url, $config_data, $user_data, $lang, $arr_block, $arr_cache_header, $arr_cache_jscript;
	
	settype($_GET['op'], 'integer');
	
	load_lang('jscript');
	
	load_model('jscript');
	
	load_libraries(array('check_admin', 'generate_admin_ng'));
	
	$original_theme=$config_data['dir_theme'];

	$config_data['dir_theme']=$original_theme.'/admin';

	$arr_block='admin_none';
	
	$headers='';
	
	if(check_admin($user_data['IdUser']))
	{
	
		$arr_cache_jscript[]='jquery.min.js';
		
		$module=slugify($_GET['module']);
		$model_name=slugify($_GET['model']);
		$field_ident=slugify($_GET['field']);
		$field_fill=slugify($_GET['field_fill']);
	
		ob_start();

		?>
		<script language="javascript">
		
			parent_window=window.parent;
			
			$(document).ready( function () {
			
				$('.select_id').click( function () {
					
					var form_modify = $('#<?php echo $field_fill; ?>_field_form', window.opener.document);
					var form_text_modify = $('#select_window_form_<?php echo $field_fill; ?>', window.opener.document);
					
					//Obtain class
					
					var class_id=$(this).attr('class').replace('select_id select_id_', '');
					
					form_modify.val(class_id);
					
					//alert(form_text_modify.attr('id'));
					
					var name_id=$('#text_field_'+class_id).html();
					
					form_text_modify.html(name_id);
					
					window.close();
					
					return false;
					
					//alert(window.opener.$('#form_generate_admin'));
					/*var form_admin=window.parent.document;
					
					alert(JSON.stringify(form_admin));*/
				
				});
		
			
			});
		
		</script>
		<?php
		
		$arr_cache_header[]=ob_get_contents();
		
		ob_end_clean();
		
		load_model($module);
		
		ob_start();
		
		//Load model if exists...
		
		if(isset($model[$model_name]) && isset($model[$model_name]->components[$field_ident]))
		{
		
				
			$arr_fields=array($field_ident);
			$arr_fields_edit=array();
			
			$url_options=make_fancy_url($base_url, 'jscript', 'browser_list_field', 'browser_list_field', array('module' => $module, 'model' => $model_name, 'field' => $field_ident, 'field_fill' => $field_fill));
			
			ListModel($model_name, $arr_fields, $url_options, $options_func='ChooseOptionsListModel', $where_sql='', $arr_fields_form=array(), $type_list='Basic');
		
		}
		
		$content=ob_get_contents();
		
		ob_end_clean();
		
		$title=''; //$lang['jscript']['search_on_table'];
			
		echo load_view(array($title, $content, $block_title=array(), $block_content=array(), $block_urls=array(), $block_type=array(), $block_id=array(), $config_data, $headers), 'admin_none');
	}

}

function ChooseOptionsListModel($url_options, $model_name, $id, $arr_row)
{

	global $lang, $model;
	
	$field_ident=slugify($_GET['field']);
	
	$arr_options[]='<a href="#" class="select_id select_id_'.$id.'""><span style="display:none;" id="text_field_'.$id.'">'.$arr_row[$field_ident].'</span>'.$lang['common']['select'].'</a>';
	
	return $arr_options;

}


?>
