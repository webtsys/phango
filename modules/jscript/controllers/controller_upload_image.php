<?php

function Upload_image()
{

	global $model, $base_path, $base_url, $config_data, $user_data, $lang;
	
	load_lang('jscript');
	
	load_libraries(array('check_admin', 'send_email'));
	
	load_model('jscript');
	
	if(check_admin($user_data['IdUser']))
	{
		
		settype($_FILES['upload']['name'], 'string');
		
		$update='insert';
		$update_text=$lang['jscript']['image_uploaded_successfully'];
		$no_update_text=$lang['jscript']['image_no_uploaded_successfully'];
		
		$name_image=str_replace('.gif', '.jpg', $_FILES['upload']['name']);

		$name_image=str_replace('.png', '.jpg', $_FILES['upload']['name']);
		
		$query=$model['jscript_image']->select('where image="'.form_text(basename($name_image)).'"', array('IdJscript_image'));
		
		list($idimage)=webtsys_fetch_row($query);
		
		settype($idimage, 'integer');
		
		if($idimage>0)
		{
		
			$update='update';
			$update_text=$lang['jscript']['image_updated_successfully'];
			$no_update_text=$lang['jscript']['image_no_updated_successfully'];
		
		}
		
		if( $model['jscript_image']->$update( array(  'image' =>  'upload') , 'where IdJscript_image='.$idimage ) )
		{
			
			echo '<p>'.$update_text.'</p>';
			
		}
		else
		{
		
			echo '<p>'.$no_update_text.'</p>';
		
		}
	
	}

}

?>
