<?php

function Browser_image()
{

	global $model, $base_path, $base_url, $config_data, $user_data, $lang;
	
	load_lang('jscript');
	
	load_libraries(array('check_admin', 'send_email'));
	
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
		
		ob_start();
		
		
		
		$content=ob_get_contents();
		
		ob_end_clean();
	
		$path=$base_path.'application/media/upload_images/';
		$url_path=$base_url.'/media/upload_images/';
		
		$headers='';
		$title=$lang['jscript']['search_images'];
		
		echo load_view(array($title, $content, $block_title=array(), $block_content=array(), $block_urls=array(), $block_type=array(), $block_id=array(), $config_data, $headers), 'admin/admin_none');
		
	}

}

?>
