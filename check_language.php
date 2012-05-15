#!/usr/bin/php
<?php
//Little script for create variables for i18n files.
//Format variable Lang: lang['file']['variable']
include('config.php');
//include('classes/webmodel.php');

function slugify($text)
{

	$from='àáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕñ';
	$to='aaaaaaaceeeeiiiidnoooooouuuyybyRrn';

	$text=strtolower($text);

	$text=str_replace(" ", "-", $text);

	$text = utf8_decode($text);    
	$text = strtr($text, utf8_decode($from), $to);
	$text = strtolower($text);

	return utf8_encode($text); 

}

echo "This script create language files...\n";
echo "Scanning files...\n";

$i18n_dir='./i18n/';

$arr_options=getopt('f:', array('status'));

//Creating language folders if exists...
/*var_dump($arr_options);
die;*/
if(isset($arr_options['status']))
{

	echo "Checking status...\n";
	scan_directory_status($base_path);
	
}
else
if($arr_options['f']!='')
{

	//scan_file($argv[1]);

	if(file_exists($arr_options['f']))
	{
		echo "Scanning ".$arr_options['f']."...\n";

		check_i18n_file($arr_options['f']);

	}
	else
	{

		echo $arr_options['f']." file don't exists...\n";

	}

}
else
{

	scan_directory($base_path);

}

function scan_directory($directory)
{
    
	global $base_path, $arr_i18n;

	foreach($arr_i18n as $language) 
	{

		if(!file_exists($base_path.'i18n/'.$language)) 
		{

			mkdir($base_path.'i18n/'.$language);

		}

	}
	if( false !== ($handledir=opendir($directory)) ) 
	{

		while (false !== ($file = readdir($handledir))) 
		{
			
			$path_file=$directory.$file;

			if( !preg_match ( '/(.*)\/i18n\/(.*)/' , $path_file ) )
			{    
				if(is_dir($path_file) && !preg_match("/^(.*)\.$/", $path_file) && !preg_match("/^\.(.*)$/", $path_file)) 
				{
					
					echo "Checking directory ".$file."...\n";
					scan_directory($path_file.'/');
					
				}
				else
				if(preg_match("/.*\.php$/", $file) && $file!="check_language.php" ) 
				{
	
					echo "Checking file $file...\n";

					//Check file...

					//First open file...
					
					check_i18n_file($directory.$file);

				}

			}
			else
			{

				echo "No checking i18n file $file...\n"; 

			}
				

		}
		
		closedir($handledir);

	}

}

function check_i18n_file($file_path)
{
	global $arr_i18n, $base_path;
	//Check file searching $lang variables...
	
	$file=file_get_contents($file_path);

	//Get $lang variables......
	$arr_match_lang=array();
	
	$pattern_file="|".preg_quote("\$lang")."\['(.*)'\]\['(.*)'\]|U";
		
	if(preg_match_all ( $pattern_file, $file, $arr_match_lang, PREG_SET_ORDER)) 
	{

	//Check if exists lang file for $lang variable

		$lang=array();

		foreach($arr_match_lang as $arr_lang) 
		{
	
			if(!isset($lang[$arr_lang[1]])) 
			{
	
				$lang[$arr_lang[1]]=array();
			
			}
	
			$lang[$arr_lang[1]][$arr_lang[2]]=slugify($arr_lang[2]);
		
		}
			
		foreach($arr_i18n as $language) 
		{
	
			$arr_files=array_unique(array_keys($lang));
				
			foreach($arr_files as $lang_file)
			{

				$path_lang_file=$base_path.'i18n/'.$language.'/'.$lang_file.'.php';

				$module_path=$lang_file;
				
				$pos=strpos($module_path, "_");
				//echo $module_path."\n";
				if($pos!==false)
				{

					$arr_path=explode('_', $module_path);

					$module_path=$arr_path[0];
					
				}

				if(file_exists($base_path.'/modules/'.$module_path))
				{

					/*foreach($arr_i18n as $lang_dir) 
					{*/

					if(!file_exists($base_path.'/modules/'.$module_path.'/i18n/'.$language)) 
					{
						//echo $base_path.'/modules/'.$lang_file.'/i18n/'.$language;
						mkdir($base_path.'/modules/'.$module_path.'/i18n/'.$language, 0755, true);

					}

					//}

					$path_lang_file=$base_path.'/modules/'.$module_path.'/i18n/'.$language.'/'.$lang_file.'.php';

				}
				
				include($path_lang_file);
					
				//print_r($lang);
					
				$arr_file_lang=array("<?php\n\n");

				foreach($lang[$lang_file] as $key_trad => $trad) 
				{
					
					$arr_file_lang[]="\$lang['".$lang_file."']['".$key_trad."']='".str_replace("'", "\'", $trad)."';\n\n";
					
				}
					
				/*foreach($lang as $file_lang => $value_lang) 
				{
					
					foreach($value_lang as $key_trad => $trad) 
					{
					
						$arr_file_lang[]="\$lang['".$file_lang."']['".$key_trad."']='".$trad."';\n\n";
						
					}
					
				}*/
				
				$arr_file_lang[]="?>\n";
				
				$file=fopen ($path_lang_file, 'w');
				
				if($file!==false) 
				{
				
					echo "--->Writing in this file: ".$path_lang_file."...\n";
				
					if(fwrite($file, implode('', $arr_file_lang))==false) 
					{
					
						echo "I cannot open this file: $path_lang_file\n";
						die;
					
					}
				
					fclose($file);
				
				}
				else
				{
				
					echo "I cannot open this file: $path_lang_file\n";
					die;
				
				}
				
			}
		
			
		}
		
	}

}

function scan_directory_status($directory)
{
    
	global $base_path, $arr_i18n;

	foreach($arr_i18n as $language) 
	{

		if(!file_exists($base_path.'i18n/'.$language)) 
		{

			mkdir($base_path.'i18n/'.$language);

		}

	}
	if( false !== ($handledir=opendir($directory)) ) 
	{

		while (false !== ($file = readdir($handledir))) 
		{
			
			$path_file=$directory.$file;

			//echo $path_file."\n";

			if(!preg_match("/^(.*)\.$/", $path_file) && !preg_match("/^\.(.*)$/", $path_file))
			{

				if(is_dir($path_file)) 
				{
						
					//echo "Checking directory ".$file."...\n";
					scan_directory_status($path_file.'/');

				}
				else if( preg_match ( '/(.*)\/i18n\/(.*)\.php$/' , $path_file ) )
				{
					$lang=array();
					echo "Checking file ".$path_file."...\n";

					include($path_file);

					//$file_lang=str_replace('.php', '', $file);

					$file_lang=key($lang);
	
					foreach($lang[$file_lang] as $key_lang => $cont_lang)
					{
						
						if($key_lang==$cont_lang)
						{

							echo "--- \$lang[".$file_lang."][".$key_lang."] need translation\n";

						}

					}

					echo "\n\n";

					//print_r($lang);
					

				}

			}
					

		}

	}

}

?>
