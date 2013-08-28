<?php

function BlocksAdmin()
{
	global $lang, $language, $arr_i18n, $base_url, $base_path, $model, $user_data, $arr_block, $header;
	
	load_lang('blocks');
	load_libraries(array('generate_admin_ng', 'utilities/menu_selected'));

	$header='<script language="Javascript" src="'.make_fancy_url($base_url, 'jscript', 'load_jscript', 'script', array('input_script' => 'jquery.min.js')).'"></script>';

	echo '<h3>'.$lang['blocks']['edit_blocks'].'</h3>';

	settype($_GET['module'], 'string');
	settype($_GET['activation'], 'integer');
	settype($_GET['op'], 'integer');
	$_GET['module']=form_text($_GET['module']);

	?>
	<h3><?php echo $lang['blocks']['edit_blocks_module']; ?></h3>
	<form method="get" action="<?php echo make_fancy_url($base_url, 'admin', 'index', 'modify_blocks', $arr_data=array('IdModule' => $_GET['IdModule'])); ?>" name="form_block">
	<?php
	
	set_csrf_key();
	
	if($_GET['module']=='')
	{
	
		$_GET['module']='none';
	
	}

	$query=$model['module']->select('where app_index=1', array('IdModule', 'name'));

	$arr_modules=array($_GET['module'], $lang['blocks']['module_no'], 'none');
	$arr_check=array();
	
	while(list($idmodule, $module_name)=webtsys_fetch_row($query))
	{

		$arr_modules[]=ucfirst($module_name); //$lang_admin[$module_name];
		$arr_modules[]=$module_name;
		$arr_check[]=$module_name;
	
	}

	//Add pages 

	$arr_modules[]=$lang['blocks']['dinamyc_pages'];
	$arr_modules[]='optgroup';

	$query=webtsys_query('select IdPage, name from page');

	while( list($idpage,$name)=webtsys_fetch_row($query) )
	{

		$arr_modules[]=I18nField::show_formatted($name);
		$arr_modules[]='page_'.$idpage;
		$arr_check[]='page_'.$idpage;

	}

	$arr_modules[]='';
	$arr_modules[]='end_optgroup';

	$arr_model[]=$arr_modules;
	$arr_model[]='onclick="javascript:document.forms.form_block.submit();" multiple';
	
	$yes_module=in_array($_GET['module'], $arr_check);

	if($user_data['privileges_user']==0 && !in_array($_GET['module'], $arr_permit_mod))
	{

		echo '<h3>'.$lang['blocks']['moderator_cant_change_sections'].'</h3>';
		return;

	}
	else
	{
		
		echo SelectForm('module', $class = '', $value = $arr_modules, 'onclick="javascript:document.forms.form_block.submit();" multiple style="width:25%;height:250px;"' );

	}
	
	?>
	</form>
	<?php

	if($yes_module!='')
	{
		?>
		<form method="get" action="<?php echo make_fancy_url($base_url, 'admin', 'index', 'modify_blocks', $arr_data=array('IdModule' => $_GET['IdModule'], 'op' => 3)); ?>" name="form_block">
		<?php echo set_csrf_key(); ?>
		<input type="hidden" name="module" value="<?php echo $_GET['module']; ?>" />
		<input type="hidden" name="op" value="3" />
		<?php

		$query=$model['inheritance_blocks']->select('where module="'.$_GET['module'].'"', array('inheritance') );

		list($inheritance)=webtsys_fetch_row($query);

		settype($inheritance, 'integer');

		$arr_inheritance=array($inheritance, $lang['blocks']['inherit_by_default'], 0, $lang['blocks']['no_inherit_by_default'], 1);
		echo '<p>';
		//echo basic_select('inheritance', $arr_inheritance);
		echo SelectForm('inheritance', $class = '', $arr_inheritance);
		echo '</p>';
		
		?>
		<input type="submit" value="<?php echo $lang['blocks']['change_inherit_type']; ?>" />
		</form>
		<?php
	}

	if($_GET['module']=='')
	{

		$_GET['module']='none';

	}

	$url_blocks[0]['link']=make_fancy_url($base_url, 'admin', 'index', 'modify_blocks', array('IdModule' => $_GET['IdModule'], 'op' => 1, 'activation' => 0, 'module' => $_GET['module']));
	$url_blocks[0]['text']= $lang['blocks']['blocks_left'];

	$url_blocks[1]['link']=make_fancy_url($base_url, 'admin', 'index', 'modify_blocks', array('IdModule' => $_GET['IdModule'], 'op' => 1, 'activation' => 1, 'module' => $_GET['module']));
	$url_blocks[1]['text']= $lang['blocks']['blocks_right'];

	$url_blocks[2]['link']=make_fancy_url($base_url, 'admin', 'index', 'modify_blocks', array('IdModule' => $_GET['IdModule'], 'op' => 1, 'activation' => 2, 'module' => $_GET['module']));
	$url_blocks[2]['text']= $lang['blocks']['activate_barr'];

	menu_selected($_GET['activation'], $url_blocks, true);

	/*$url_blocks_left=make_fancy_url($base_url, 'admin', 'index', 'modify_blocks', array('IdModule' => $_GET['IdModule'], 'op' => 1, 'activation' => 0, 'module' => $_GET['module']));

	$url_blocks_right=make_fancy_url($base_url, 'admin', 'index', 'modify_blocks', array('IdModule' => $_GET['IdModule'], 'op' => 1, 'activation' => 1, 'module' => $_GET['module']));

	$url_blocks_barr=make_fancy_url($base_url, 'admin', 'index', 'modify_blocks', array('IdModule' => $_GET['IdModule'], 'op' => 1, 'activation' => 2, 'module' => $_GET['module']));
	
	$arr_select_left[$_GET['activation']]=link_block($url_blocks_left, $lang['blocks']['blocks_left']);
	$arr_select_left[0]=select_block($url_blocks_left, $lang['blocks']['blocks_left']);

	$arr_select_right[$_GET['activation']]=link_block($url_blocks_right, $lang['blocks']['blocks_right']);
	$arr_select_right[1]=select_block($url_blocks_right, $lang['blocks']['blocks_right']);

	$arr_select_barr[$_GET['activation']]=link_block($url_blocks_barr, $lang['blocks']['activate_barr']);
	$arr_select_barr[2]=select_block($url_blocks_barr, $lang['blocks']['activate_barr']);

	?>
	<ul>
		<li><?php echo $arr_select_left[$_GET['activation']]; ?></li>
		<li><?php echo $arr_select_right[$_GET['activation']]; ?></li>
		<li><?php echo $arr_select_barr[$_GET['activation']]; ?></li>
	</ul>

	<?php*/

	switch($_GET['op'])
	{

		default:
		
			settype($_GET['parent'], 'integer');
			
			$arr_block_parent=array();
			
			if($_GET['parent']>0)
			{
			
				$arr_block_parent=$model['blocks']->select_a_row($_GET['parent']);
			
			}
			
			$model['blocks']->label=$lang['blocks']['block'];
			
			settype($arr_block_parent['IdBlocks'], 'integer');
			
			$get_parent_sql=' and parent=0';
			
			if($arr_block_parent['IdBlocks']>0)
			{
			
				$get_parent_sql=' and parent='.$arr_block_parent['IdBlocks'];
			
			}

			$model['blocks']->func_update='Blocks';

			$model['blocks']->components['url_block']->form='BlockLinks';
			$model['blocks']->components['activation']->form='HiddenForm';
			$model['blocks']->components['module']->form='HiddenForm';
			$model['blocks']->components['parent']->form='HiddenForm';
			
			$model['blocks']->create_form();

			$model['blocks']->forms['title_block']->label=$lang['blocks']['title_block'];
			$model['blocks']->forms['url_block']->label=$lang['blocks']['url_block'];
			$model['blocks']->forms['hierarchy_block']->label=$lang['blocks']['hierarchy_block'];

			$model['blocks']->forms['activation']->SetForm($_GET['activation']);
			$model['blocks']->forms['module']->SetForm($_GET['module']);
			$model['blocks']->forms['parent']->SetForm($arr_block_parent['IdBlocks']);
			
			$arr_fields=array('title_block', 'url_block', 'hierarchy_block');

			$arr_fields_edit=array('title_block', 'url_block', 'activation', 'module', 'parent');

			$url_options_base=make_fancy_url($base_url, 'admin', 'index', 'modify_blocks', array('IdModule' => $_GET['IdModule'], 'op' => 1, 'activation' => $_GET['activation'], 'module' => $_GET['module']) );
			
			$url_options=make_fancy_url($base_url, 'admin', 'index', 'modify_blocks', array('IdModule' => $_GET['IdModule'], 'op' => 1, 'activation' => $_GET['activation'], 'module' => $_GET['module'], 'parent' => $arr_block_parent['IdBlocks']) );

			//?order_field=hierarchy_block&order_desc=0&search_word=&search_field=IdBlocks

			if(!isset($_GET['order_field']))
			{

				$_GET['order_field']='hierarchy_block';
				$_GET['order_desc']='0';

			}
			
			load_libraries(array('utilities/menu_barr_hierarchy'));
			
			//Obtain parents
			
			$arr_parent=$model['blocks']->components['parent']->obtain_parent_tree($arr_block_parent['IdBlocks'], 'title_block', $url_options_base);
			
			//array_unshift($arr_parent, array($lang['blocks']['parent_blocks'], ''));
			
			$arr_final_parent=array(0 => array($lang['blocks']['parent_blocks'], $url_options_base))+$arr_parent;
			
			echo menu_barr_hierarchy($arr_final_parent, 'parent', $arr_block_parent['IdBlocks']);
			
			generate_admin_model_ng('blocks', $arr_fields, $arr_fields_edit, $url_options, $options_func='LinksAdmin', $where_sql='where activation='.$_GET['activation'].' and module="'.$_GET['module'].'"'.$get_parent_sql, $arr_fields_form=array(), $type_list='Basic');

			echo '<p><a href="'.make_fancy_url($base_url, 'admin', 'index', 'modify_blocks', array('IdModule' => $_GET['IdModule'], 'op' => 2, 'activation' => $_GET['activation'],  'module' => $_GET['module']) ).'">'.$lang['blocks']['change_order'].'</a></p>';
		
		break;

		case 2:

			$url=make_fancy_url($base_url, 'admin', 'index', 'modify_blocks', array('IdModule' => $_GET['IdModule'], 'op' => 2, 'activation' => $_GET['activation'], 'module' => $_GET['module']) );

			GeneratePositionModel('blocks', 'title_block', 'hierarchy_block', $url, 'where activation='.$_GET['activation'].' and module="'.$_GET['module'].'"');

			echo '<p><a href="'.make_fancy_url($base_url, 'admin', 'index', 'modify_blocks', array('IdModule' => $_GET['IdModule'], 'op' => 1, 'activation' => $_GET['activation'],  'module' => $_GET['module']) ).'">'.$lang['common']['go_back'].'</a></p>';

		break;

		case 3:

			$url_action=make_fancy_url($base_url, 'admin', 'index', 'modify_blocks', array('IdModule' => $_GET['IdModule'], 'activation' => $_GET['activation'],  'module' => $_GET['module']) );

			$c=$model['inheritance_blocks']->select_count('where module="'.$_GET['module'].'"', 'IdInheritance_blocks' );

			settype($_GET['inheritance'], 'integer');

			$update='update';
			$post['module']=$_GET['module'];
			$post['inheritance']=$_GET['inheritance'];

			if($c==0)
			{

				$update='insert';

			}


			$model['inheritance_blocks']->$update($post, 'where module="'.$post['module'].'"');

			ob_clean();
	
			load_libraries( array("redirect") );
			
			die( redirect_webtsys( $url_action, $lang['common']['redirect'], $lang['common']['success'], $lang['common']['press_here_redirecting'] , $arr_block) );

		break;

		case 4:

				settype($_GET['IdBlocks'], 'integer');
				
				$query=$model['blocks']->select('where IdBlocks='.$_GET['IdBlocks'], array('IdBlocks', 'url_block'));

				list($idblock, $code_block)=webtsys_fetch_row($query);

				if($idblock>0)
				{
					
					$code_block=basename(str_replace('static:/', '', $code_block));
					
					if(file_exists($base_path.'modules/blocks/html/admin/'.$code_block))
					{

						include($base_path.'modules/blocks/html/admin/'.$code_block);

						$url_update=make_fancy_url($base_url, 'admin', 'index', 'modify_blocks', array('IdModule' => $_GET['IdModule'], 'op' => 4, 'activation' => $_GET['activation'], 'module' => $_GET['module'], 'IdBlocks' => $_GET['IdBlocks']) );

						update_block($url_update);

					}
					else
					{

						echo '<p>'.$lang['blocks']['block_module_no_editable'].'</p>';

					}

					echo '<p><a href="'.make_fancy_url($base_url, 'admin', 'index', 'modify_blocks', array('IdModule' => $_GET['IdModule'], 'op' => 1, 'activation' => $_GET['activation'],  'module' => $_GET['module']) ).'">'.$lang['common']['go_back'].'</a></p>';

				}
		break;

	}

}

function BlockLinks($name="", $class="", $value="")
{

	global $model, $base_path, $lang, $base_url, $module_admin;
	
	$field=$name;

	$select_page=array($value);

	$cont_tmp=ob_get_contents();

	ob_clean();

	//ob_start();

	$query=$model['module']->select('where app_index=1', array('IdModule', 'name'));

	while(list($idmodule, $module_name)=webtsys_fetch_row($query))
	{

		if(!include($base_path.'modules/'.$module_name.'/blocks/extra_links/'.basename($module_name).'.php'))
		{

			if(!include($base_path.'modules/blocks/extra_links/'.basename($module_name).'.php'))
			{

				$output=ob_get_contents();

				ob_end_clean();

				$arr_no_links[0]='<p>Don\'t exist links function</p>';
				$arr_no_links[1]='<p>Don\'t exist links function <strong>'.$module_name.'</strong> in '.$base_path.'modules/'.$module_name.'/blocks/extra_links/</p>';

				echo load_view(array('title' => 'Phango site is down', 'content' => $arr_no_links[DEBUG]), 'common/common');
			
				die;

			}

		}
	
	}

	ob_clean();
	
	echo $cont_tmp;

	if($_GET['activation']<2)
	{

		
		$select_page[]='Helpers';
		$select_page[]='optgroup';
	
		$select_page[]=$lang['blocks']['begin_block'];
		$select_page[]='helper:/begin_block';
	
		$select_page[]=$lang['blocks']['end_block'];
		$select_page[]='helper:/end_block';
	
		$select_page[]='';
		$select_page[]='end_optgroup';
	
		$select_page[]=$lang['blocks']['static_blocks'];
		$select_page[]='optgroup';

		$handle=opendir($base_path."modules/blocks/html");
	
		while ($file = readdir($handle))
		{
			if($file!="." && $file!=".." && $file!='admin')
			{
			
				$select_page[]=ucfirst(str_replace('.php', '', $file));
				$select_page[]='static:/'.$file;
	
			}
		}

		//Now in modules name...

		foreach($module_admin as $path_module)
		{

			$handle=@opendir($base_path."modules/".$path_module."/blocks/html");
			
			if($handle!==false)
			{	
	
				while ($file = readdir($handle))
				{
					if($file!="." && $file!=".." && $file!='admin')
					{

						//$file_base="modules/".$path_module."/blocks/html/".$file;
						$file_base=$path_module."/".$file;
					
						$select_page[]=ucfirst(str_replace('.php', '', $file));
						$select_page[]='static:/'.$file_base;
			
					}
				}

			}

		}
	
		$select_page[]='';
		$select_page[]='end_optgroup';
	

	}

	$select=SelectForm($field.'_option', $class, $select_page, 'onclick="javascript:document.forms[\'form\'].'.$field.'.value=this.value;" size="8" multiple style="width:300px"' );

	return TextForm($field, $class, $value).'<br />'.$select;

}

function BlockLinksSet($post, $value)
{
	
	return $value;

}

function LinksAdmin($url_options, $model_name, $id, $field_rows=array())
{

	global $lang, $base_url;

	$arr_links=BasicOptionsListModel($url_options, $model_name, $id);
	
	$arr_links[]='<a href="'.make_fancy_url($base_url, 'admin', 'index', 'modify_blocks_children', array('IdModule' => $_GET['IdModule'], 'op' => 0, 'activation' => $_GET['activation'], 'module' => $_GET['module'], 'parent' => $id) ).'">'.$lang['blocks']['add_children_blocks'].'</a>';

	if( preg_match('/^static:\//', $field_rows['url_block']) )
	{

		$arr_links[]='<a href="'.make_fancy_url($base_url, 'admin', 'index', 'modify_blocks', array('IdModule' => $_GET['IdModule'], 'op' => 4, 'activation' => $_GET['activation'], 'module' => $_GET['module'], 'IdBlocks' => $id) ).'">'.$lang['blocks']['edit_module_if_exists'].'</a>';

	}

	return $arr_links;

}

function BlocksInsertModel($model_name, $arr_fields, $post)
{

	return BasicInsertModel($model_name, $arr_fields, $post);

}

function BlocksUpdateModel($model_name, $arr_fields, $post, $id)
{

	return BasicUpdateModel($model_name, $arr_fields, $post, $id);

}

function BlocksDeleteModel($model_name, $id)
{
	global $base_path, $arr_block, $model;

	$query=$model[$model_name]->select('where '.$model[$model_name]->idmodel.'='.$id, array('url_block'));

	list($url_block)=webtsys_fetch_row($query);
	
	if(preg_match('/^static:\//', $url_block))
	{

		$code_block=basename(str_replace('static:/', '', $url_block));
		
		if(file_exists($base_path.'modules/blocks/html/admin/'.$code_block))
		{
			
			include($base_path.'modules/blocks/html/admin/'.$code_block);
	
			delete_block($id);

		}

	}

	$query=$model[$model_name]->delete('where '.$idmodel.'='.$_GET[$idmodel]);

	return BasicDeleteModel($model_name, $id);

}

function link_block($link, $text_link)
{

	return '<a href="'.$link.'">'.$text_link.'</a> ';

}

function select_block($link, $text_link)
{

	return $text_link;

}

?>
