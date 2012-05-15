<?php

function update_table($model)
{

	global $arr_i18n;

	foreach($model as $key => $thing)
	
	{
		
		$arr_table=array();
		
		$arr_etable=array();
		
		$allfields=array();
		$fields=array();
		$types=array();
	
		$field="";
		$type=""; 
		$null=""; 
		$key_db=""; 
		$default=""; 
		$extra="";
		$key_field_old=$model[$key]->idmodel;
		$arr_sql_index=array();
		
		//$arr_multilang=array();
	
		$query=webtsys_query("show tables");
		
		while(list($table)=webtsys_fetch_row($query))
		{
		
			$arr_etable[$table]=1;
		
		}
		
		if(!isset($arr_etable[$key]))
		{
			//If table not exists make this
			
			foreach($model[$key]->components as $key_data => $data)
			{
				$arr_table[]='`'.$key_data.'` '.$model[$key]->components[$key_data]->get_type_sql();

				//Check if foreignkeyfield...
				if(isset($model[$key]->components[$key_data]->related_model))
				{

					//Create indexes...

					$arr_sql_index[$key_data]='CREATE INDEX index_'.$key.'_'.$key_data.' ON '.$key.'('.$key_data.');';

				}
			}
			
			$sql_query="create table $key (\n".implode(",\n", $arr_table)."\n) DEFAULT CHARSET=utf8;\n";
			
			echo "Creating table $key\n";
			
			$query=webtsys_query($sql_query);

			foreach($arr_sql_index as $key_data => $sql_index)
			{

				echo "---Creating index for ".$key_data."\n";

				$query=webtsys_query($sql_index);

			}

		}
		else
		{
			//Obtain all fields of model
		
			foreach($model[$key]->components as $kfield => $value)
			{
		
				$allfields[$kfield]=1;
				
			}
		
			//unset($allfields['Id'.ucfirst($key)]);

			unset($allfields[$model[$key]->idmodel]);
		
			$query=webtsys_query("describe ".$key);
			
			list($key_field_old, $type, $null, $key_db, $default, $extra)=webtsys_fetch_row($query);
		
			while(list($field, $type, $null, $key_db, $default, $extra)=webtsys_fetch_row($query))
			{
		
				$fields[]=$field;
				$types[$field]=$type;
				$keys[$field]=$key_db;
	
			}
			
			foreach($fields as $field)
			{
		
				if(isset($allfields[$field]))
				{
		
					$type=strtoupper($types[$field]);
					
					unset($allfields[$field]);
					
					if($model[$key]->components[$field]->get_type_sql()!=($type." NOT NULL"))
					{
						
						$query=webtsys_query('alter table '.$key.' modify `'.$field.'` '.$model[$key]->components[$field]->get_type_sql());
						
						echo "Upgrading ".$field." from ".$key."...\n";
						
				
					}

					//Set index

					if(isset($model[$key]->components[$field]->related_model) && $keys[$field]=='')
					{

						echo "---Creating index for ".$field." from ".$key."\n";

						$query=webtsys_query('CREATE INDEX index_'.$key.'_'.$field.' ON '.$key.'('.$field.')');

					}

					if(!isset($model[$key]->components[$field]->related_model) && $keys[$field]!='')
					{
						
						echo "---Delete index for ".$field." from ".$key."\n";
						
						$query=webtsys_query('DROP INDEX index_'.$key.'_'.$field.' ON '.$key);

					}
		
				}
		
				else
				
				{
		
					$allfields[$field]=0;
		
				}
		
			}
		
		}

		//Check if new id...

		if($key_field_old!=$model[$key]->idmodel)
		{

			$query=webtsys_query('alter table '.$key.' change `'.$key_field_old.'` `'.$model[$key]->idmodel.'` INT NOT NULL AUTO_INCREMENT');

			echo "Renaming id for this model to ".$model[$key]->idmodel."...\n";

		}

		//Check if new fields...
	
		foreach($allfields as $new_field => $new)
		{
				
			if($allfields[$new_field]==1)
			{
		
				$query=webtsys_query('alter table '.$key.' add `'.$new_field.'` '.$model[$key]->components[$new_field]->get_type_sql());

				echo "Adding ".$new_field." to ".$key."...\n";

				if(isset($model[$key]->components[$new_field]->related_model) )
				{

					echo "---Creating index for ".$new_field." from ".$key."\n";

					$query=webtsys_query('CREATE INDEX index_'.$key.'_'.$new_field.' ON '.$key.'('.$new_field.')');

				}
		
			}
		
			else
		
			{

				$query=webtsys_query('alter table '.$key.' drop `'.$new_field.'`');
		
				echo "Deleting ".$new_field." from ".$key."...\n";
		
			}
		
		}
	
	}

}

function add_module($arr_modules)
{

	global $base_path, $model, $arr_module_insert, $arr_module_sql;

	$return=1;
	
	foreach($arr_modules as $path_module => $module)
	{

		if(isset($arr_module_insert[$module]))
		{
			
			$num_modules=$model['module']->select_count('where name="'.$module.'"', 'IdModule');

			if($num_modules==0)
			{

				echo 'Creating row module for '.$module."\n";
			
				$model['module']->insert($arr_module_insert[$module]);

				//Execute sql for this module...

				if(isset($arr_module_sql[$module]))
				{

					$file_sql=$base_path.'modules/'.$path_module.'/sql/'.$arr_module_sql[$module];
					
					if(file_exists($file_sql))
					{
						
						$arr_sql=file( $file_sql, FILE_SKIP_EMPTY_LINES );		

						echo 'Inserting '.$file_sql."\n";

						foreach($arr_sql as $sql_code)
						{

							$sql_code=trim($sql_code);

							if($sql_code!='')
							{

								if(!($query=webtsys_query($sql_code)))
								{

									echo 'Error: in sentence '.$sql_code."\n";

									$return=0;

								}

							}

						}

					}
					else
					{

						echo 'Error: don\'t exists '.$base_path.'modules/'.$path_module.'/sql/'.$arr_module_sql[$module]."\n";

						$return=0;

					}

				}
			
			}

		}

	}

	return $return;

}

function update_models_from_module($arr_modules)
{
	global $arr_padmin_mod, $model, $base_path,$arr_module_insert, $arr_module_sql, $lang;
	
	foreach($arr_modules as $module)
	{

		$path_modules=$base_path.'modules/'.$module.'/models/';

		if ($dh = opendir($path_modules)) 
		{
			while ($file = readdir($dh))
			{

				if( is_file($path_modules.$file) && !preg_match('/^\./', $file) && preg_match('/\.php$/', $file) )
				{
					$my_model=preg_replace( '/^models_([aA-zZ]+)\.php/' , '$1', $file);

					settype($arr_padmin_mod[$module], 'array');

					$arr_padmin_mod[$my_model][$module]=str_replace('.php', '', $my_model);
					
					include($base_path.'modules/'.$module.'/models/models_'.$my_model.'.php');


				}

			}

		}

	}
	
	echo '<p>';
	echo '<pre>';
	update_table($model);
	echo '</pre>';
	echo '<pre>';
	foreach($arr_padmin_mod as $padmin_mod)
	{
		add_module($padmin_mod);
	}
	echo '</pre>';
	echo '</p>';

}

?>