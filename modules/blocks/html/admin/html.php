<?php

if(!defined("PAGE"))
{

die();

}

load_model('blocks/html');

function update_block($url)
{

	global $model, $base_path, $base_url, $arr_block, $yes_entities, $lang;
	
	settype($_GET['op_block'], 'integer');
	
	load_libraries(array('forms/textareabb'));

	$num_blocks=$model['html_block']->select_count('where idblock='.$_GET['IdBlocks'], 'IdHtml_block');
	echo mysql_error();
	$query=$model['blocks']->select('where IdBlocks='.$_GET['IdBlocks'], array('title_block') );
	
	list($title)=webtsys_fetch_row($query);
	$title=I18nField::show_formatted($title);
	?>
	<h2><?php echo $lang['blocks']['edit_html_block']; ?> <?php echo $title; ?></h2>
	<?php

	switch($_GET['op_block'])
	{
	
	default:
		
		$code='';

		if($num_blocks>0)
		{
		
			$query=$model['html_block']->select('where idblock='.$_GET['IdBlocks'], array('code'));
			
			list($code)=webtsys_fetch_row($query);
			
		}
	
		$url_post=add_extra_fancy_url($url, array('op_block' => 1));

		/*?>
		<form method="post" action="<?php echo $url_post; ?>">
		<?php set_csrf_key(); ?>
		<p><label for="code"><?php echo $lang['blocks']['html_code']; ?>:</label></p>
		<p><textarea name="code"><?php echo $code; ?></textarea></p>
		<p><input type="submit" value="<?php echo $lang['common']['send']; ?>"></p>
		</form>
		<?php*/
		
		$model['html_block']->create_form();
		
		$model['html_block']->forms['code']->form='TextAreaBBForm';
		
		$model['html_block']->forms['code']->label=$lang['blocks']['html_code'];
		
		echo load_view(array($model['html_block']->forms, $arr_fields=array('code'), $url_post, $enctype=''), 'common/forms/updatemodelform');
	
	break;

	case 1:

		$operation='update';

		settype($_GET['idhtml_block'], 'integer');

		if($num_blocks==0)
		{

			$operation='insert';

		}
		
		$_POST['module']=$_GET['module'];
		$_POST['idblock']=$_GET['IdBlocks'];

		$yes_entities=0;

		if($model['html_block']->$operation($_POST, 'where idblock='.$_GET['IdBlocks']))
		{
			ob_end_clean();

			load_libraries( array("redirect") );
			
			die( redirect_webtsys( make_fancy_url($base_url, 'admin', 'index', 'modify_blocks', array('IdModule' => $_GET['IdModule'], 'op' => 1, 'activation' => $_GET['activation'], 'module' => $_GET['module']) ), $lang['common']['redirect'], $lang['common']['success'], $lang['common']['press_here_redirecting'] , $arr_block) );

		}
		else
		{

			echo '<p>'.$lang['common']['error'].'</p>';
			echo '<p><a href="javascript:history.back();">'.$lang['common']['go_back'].'</a></p>';

		}

	break;

	}

}

function delete_block($idblock)
{

	global $model;

	$model['html_block']->delete('where idblock='.$idblock);
	
}

?>