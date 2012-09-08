<?php

if(!defined("PAGE"))
{

	die();

}

load_model('photo');
load_lang('photo');

function update_block($url)
{

	global $model, $base_path, $arr_block, $lang;

	settype($_GET['op_block'], 'integer');

	$num_blocks=$model['photo']->select_count('where module="'.$_GET['module'].'" and idblock='.$_GET['IdBlocks'], 'IdPhoto');
	
	$query=$model['blocks']->select('where IdBlocks='.$_GET['IdBlocks'], array('title_block') );
	
	list($title)=webtsys_fetch_row($query);
	
	?>
	<h2><?php echo $lang['photo']['edit_photo']; ?> <?php echo $title; ?></h2>
	<?php

	switch($_GET['op_block'])
	{
	
	default:
		
		$image=$lang['photo']['any_photo'];
		$form_image='';
		$foot='';

		if($num_blocks>0)
		{
		
			$query=$model['photo']->select('where module="'.$_GET['module'].'" and idblock='.$_GET['IdBlocks'], array('image', 'foot'));
			
			list($myimage, $foot)=webtsys_fetch_row($query);
			
			$image='<a href="'.$myimage.'">'.$myimage.'</a>';
			$form_image=$myimage;
			
		}
	
		$url_post=add_extra_fancy_url($url, array('op_block' => 1));
	
		?>
		<form method="post" action="<?php echo $url_post; ?>">
		<?php set_csrf_key(); ?>
		<p><label for="image"><?php echo $lang['photo']['http_url_for_img']; ?>:</label></p>
		<p><input type="text" name="image" value="<?php echo $form_image; ?>"></p>
		<p><label for="image"><?php echo $lang['photo']['foot_photo']; ?>:</label></p>
		<p><input type="text" name="foot" value="<?php echo $foot; ?>" size="40"></p>
		<p><input type="submit" value="<?php echo $lang['common']['send']; ?>"></p>
		</form>
		<?php
	
	break;

	case 1:

		$operation='update';

		settype($_GET['idphoto'], 'integer');

		if($num_blocks==0)
		{

			$operation='insert';

		}
		
		$_POST['module']=$_GET['module'];
		$_POST['idblock']=$_GET['IdBlocks'];

		if($model['photo']->$operation($_POST, 'where module="'.$_GET['module'].'" and idblock='.$_GET['IdBlocks']))
		{
			ob_end_clean();

			load_libraries( array("redirect") );
			
			die( redirect_webtsys(  make_fancy_url($base_url, 'admin', 'index', 'modify_blocks', array('IdModule' => $_GET['IdModule'], 'op' => 1, 'activation' => $_GET['activation'], 'language' =>  $_GET['language'], 'module' => $_GET['module']) ), $lang['common']['redirect'], $lang['common']['success'], $lang['common']['press_here_redirecting'] , $arr_block) );

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

	$model['photo']->delete('where idblock='.$idblock);
	
}

?>