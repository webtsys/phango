<?php

function Upload_image()
{

	global $model, $base_path, $base_url, $config_data, $user_data, $lang;
	
	load_lang('jscript');
	
	load_libraries(array('check_admin', 'send_email'));
	
	load_model('jscript');
	
	if(check_admin($user_data['IdUser']))
	{
	
		settype($_FILES['name'], 'string');
		
		
		if( $model['jscript_image']->insert(array(  'image' =>  'upload')) )
		{
			
			echo '<p>'.$lang['jscript']['image_uploaded_successfully'].'</p>';
			
		}
		else
		{
		
			echo '<p>'.$lang['jscript']['image_no_uploaded_successfully'].'</p>';
		
		}
	
	}

}

?>
