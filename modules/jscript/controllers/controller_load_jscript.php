<?php

function Load_Jscript()
{

	global $model, $base_path, $base_url, $config_data;

	//This function is for load gzipped javascript from server, for use jscript from other server, use normal javascript html code and configure your server for serve jscript gzipped if you use big libraries.

	load_model('jscript');

	settype($_GET['input_script'], 'string');
	settype($_GET['no_compression'], 'integer');
	settype($_GET['module'], 'string');
	
	$module_theme_base=$config_data['module_theme'];
	
	if($_GET['module']!='')
	{
		
		$_GET['module']=slugify(basename($_GET['module']));
		
		$module_theme_base='modules/'.$_GET['module'].'/';
	
	}
	
	//$_GET['input_script']=form_text($_GET['input_script']);
	//$_GET['input_script']=str_replace('--', '/', $_GET['input_script']);

	//Checking input_script

	$check_input_script=explode('--', $_GET['input_script']);

	foreach($check_input_script as $key_file => $dir_file)
	{

		$check_input_script[$key_file]=str_replace('/', '', slugify($dir_file));

	}

	$_GET['input_script']=implode('/', $check_input_script);

	//$_GET['input_script']=str_replace('.', '', $_GET['input_script']);
	
	//Search script

	//application/media/jscript/libraries_jscript/

	settype($_SERVER ['HTTP_ACCEPT_ENCODING'], 'string');
	
	$jscript_source=$base_path.'application/media/'.$config_data['dir_theme'].'/jscript/libraries_jscript/'.$_GET['input_script'];

	$file_found=1;

	if(!file_exists($jscript_source))
	{

		$jscript_source=$base_path.'application/media/jscript/libraries_jscript/'.$_GET['input_script'];
		
		if(!file_exists($jscript_source))
		{
		
			//Check on theme
			
			$jscript_source=$base_path.$module_theme_base.'media/jscript/libraries_jscript/'.$_GET['input_script'];
			
			//Always compressed if jscript is in theme.
			
			$_GET['no_compression']=0;
		
		}

	}


	if( file_exists($jscript_source) && $_GET['input_script']!='')
	{

		$browser_support=explode(',', $_SERVER ['HTTP_ACCEPT_ENCODING']);

		foreach($browser_support as $support)
		{

			$arr_support[trim($support)]=1;

		}
		// && (isset($arr_support['gzip']) || isset($arr_support['deflate']) ) 
		if(function_exists('gzcompress') && $_GET['no_compression']==0)
		{

			//First, check if cache...

			$hash_jscript_file=md5_file($jscript_source);

			$query=$model['jscript_cache']->select('where name="'.$_GET['input_script'].'" and md5_hash="'.$hash_jscript_file.'"', array('cache'));

			list($cache_code)=webtsys_fetch_row($query);

			if($cache_code=='')
			{

				$file_jscript=trim(file_get_contents($jscript_source));
				
				$gzip_jscript=gzencode($file_jscript, 6);
				
				//Write cache in db

				$query=$model['jscript_cache']->delete( 'where name="'.$_GET['input_script'].'"' );

				$query=$model['jscript_cache']->insert( array('name' => $_GET['input_script'], 'cache' => $gzip_jscript, 'md5_hash' => $hash_jscript_file) );

			}
			else
			{
	
				$gzip_jscript=$cache_code;

			}

			header('Content-Encoding: gzip');
			header('Content-Type: application/x-javascript');
			header('Content-Length: '.strlen($gzip_jscript));

			ob_end_clean();

			//ob_start();
			
			echo $gzip_jscript;
				
			//ob_end_flush();
			die;

		}
		else
		{

			die(header('Location: '.$base_url.'/media/jscript/libraries_jscript/'.$_GET['input_script']));

		}
		
	}
	else
	{

		header('HTTP/1.1 404 Not Found');

		show_error('Error: cannot found javascript code', 'Error: cannot found javascript code in '.$jscript_source);

	}

	//If exists ok if not, error 404.

	//Check if exists gzip support, if not, send normal script.

	//Check if exists cache gzipped. 

	//If not, create cache gzipped

	//Send javascript code gzipped.

}

?>