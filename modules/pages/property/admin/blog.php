<?php

load_lang('blog');

settype($_GET['IdPage'], 'integer');
settype($_GET['IdProperty_page'], 'integer');

$query=$model['page']->select('where IdPage='.$_GET['IdPage'], array('name'));

list($name_page)=webtsys_fetch_row($query);

$name_page=$model['page']->components['name']->show_formatted($name_page);

ob_start();

?>
<h3><?php echo $lang['blog']['edit_in_page_blog']; ?> &quot;<?php echo $name_page; ?>&quot;</h3>
<?php

settype($_GET['action'], 'integer');

switch($_GET['action'])
{

	default:

	load_model('blog');
	
	$query=$model['property_page']->select('where idpage='.$_GET['IdPage'].' and IdProperty_page='.$_GET['IdProperty_page'], array('options'));

	list($ser_options)=webtsys_fetch_row($query);
	
	$arr_options=unserialize($ser_options);

	settype($arr_options['idblog'], 'integer'); 
 	settype($arr_options['num_posts'], 'integer'); 
	
	$query=$model['blog']->select('', array('IdBlog', 'title'));
	
	$arr_blog=array($arr_options['idblog'], 'Todos los blogs', 0);
	
	while(list($idblog, $title)=webtsys_fetch_row($query))
	{
	
		$arr_blog[]=$title;
		$arr_blog[]=$idblog;
	
	}
	
	?>
	<p><?php echo $lang['blog']['choose_blog_to_show']; ?></p>	
	<form method="post" action="<?php echo make_fancy_url($base_url, 'admin', 'index', 'change_property_page', array('IdModule' => $_GET['IdModule'], 'op' => 2, 'action' => 1, 'IdPage' => $_GET['IdPage'], 'IdProperty_page' => $_GET['IdProperty_page'])); ?>">
	<?php
	set_csrf_key();
	echo SelectForm('idblog', '', $arr_blog);

	
	?>
	<p><?php echo $lang['blog']['num_post_home_index_blog']; ?>: <input type="text" name="num_posts" value="<?php echo $arr_options['num_posts']; ?>" /></p>
	<input type="submit" value="Enviar" />
	</form>
	<?php

	$cont=ob_get_contents();

	ob_end_clean();

	echo load_view(array($lang['common']['edit'], $cont), 'content');
	
	break;

	case 1:

		settype($_POST['idblog'], 'integer');
		settype($_POST['num_posts'], 'integer');

		$model['property_page']->update(array('options' => array('idblog' => $_POST['idblog'], 'num_posts' => $_POST['num_posts']) ), 'where IdProperty_page='.$_GET['IdProperty_page']);

		ob_end_clean();
		ob_end_clean();
		
		load_libraries( array("redirect") );

		$url_redirect= make_fancy_url($base_url, 'admin', 'index', 'change_property_page', array('IdModule' => $_GET['IdModule'], 'op' => 2, 'IdPage' => $_GET['IdPage'], 'IdProperty_page' => $_GET['IdProperty_page']));
			
		die( redirect_webtsys( $url_redirect, $lang['common']['redirect'], $lang['common']['success'], $lang['common']['press_here_redirecting'] , 'admin') );
		

	break;

}


?>