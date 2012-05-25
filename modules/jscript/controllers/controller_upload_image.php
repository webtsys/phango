<?php

function Upload_image()
{

	global $model, $base_path, $base_url, $config_data, $user_data, $lang;
	
	load_lang('jscript');
	
	load_libraries(array('check_admin', 'send_email'));
	
	load_model('jscript');
	
	if(check_admin($user_data['IdUser']))
	{
	
		//$_POST
		//settype($_FILE, 'string');
		//print_r($_FILE);
		/*ob_start();
		
		print_r($_FILES);
		
		$content=ob_get_contents();
		
		ob_end_clean();
		
		send_mail('webmaster@web-t-sys.com', 'cosas', $content, $content_type='plain', $bcc='');
		
		echo 'pepe';
		Array
(
    [upload] => Array
        (
            [name] => amigos.jpg
            [type] => image/jpeg
            [tmp_name] => /tmp/phphpBb2U
            [error] => 0
            [size] => 42964
        )

)
		*/
		
		settype($_FILES['name'], 'string');
		
		$path=$base_path.'application/media/upload_images/';
		$url_path=$base_url.'/media/upload_images/';
		
		//$image_field=new ImageField('upload', $path, $url_path);
		
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
