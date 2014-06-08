<?php

global $arr_block;

$query=$model['page']->select('where IdPage='.$_GET['IdPage'], array('name'));

list($name_page)=webtsys_fetch_row($query);

$name_page=$model['page']->components['name']->show_formatted($name_page);

ob_start();

?>
<h3><?php echo $lang['templates_admin']['edit_property_from']; ?> &quot;<?php echo $name_page; ?>&quot;</h3>
<?php

settype($_GET['action'], 'integer');

switch($_GET['action'])
{

	default:

	load_model('templates');

	$query=$model['property_page']->select('where idpage='.$_GET['IdPage'].' and IdProperty_page='.$_GET['IdProperty_page'], array('options'));

	list($ser_options)=webtsys_fetch_row($query);
	
	$arr_options=@unserialize($ser_options);

	settype($arr_options, 'array');
	
	settype($arr_options['idtemplate'], 'integer'); 
	
	$query=$model['template']->select('', array('IdTemplate', 'name'));
	
	$arr_template=array($arr_options['idtemplate']);
	
	while(list($idtemplate, $name)=webtsys_fetch_row($query))
	{
	
		$arr_template[]=$model['template']->components['name']->show_formatted($name);
		$arr_template[]=$idtemplate;
	
	}
	

	$url_action=set_admin_link( 'edit_templates_prop', array('IdModule' => $_GET['IdModule'], 'IdPage' => $_GET['IdPage'], 'op' => '2', 'IdProperty_page' => $_GET['IdProperty_page'], 'action' => '1') );

	?>
	<p>Elija la template que desea que aparezca</p>	
	<form method="post" action="<?php echo $url_action; ?>">
	<?php
	set_csrf_key();
	echo SelectForm('idtemplate', '', $arr_template);

	
	?>
	<input type="submit" value="Enviar" />
	</form>
	<?php
	
	$cont=ob_get_contents();

	ob_end_clean();

	echo load_view(array($lang['common']['edit'], $cont), 'content');

	break;

	case 1:

		settype($_POST['idtemplate'], 'integer');
		

		$model['property_page']->update(array('options' => array('idtemplate' => $_POST['idtemplate']) ), 'where IdProperty_page='.$_GET['IdProperty_page']);

		$url_action=set_admin_link( 'edit_templates_prop', array('IdModule' => $_GET['IdModule'], 'op' => 1, 'IdPage' => $_GET['IdPage'], 'op' => '2', 'IdProperty_page' => 1, 'action' => '1') );

		ob_end_clean();
		ob_end_clean();

		load_libraries(array('redirect'));

		die( redirect_webtsys( set_admin_link( 'edit_templates_prop', array('IdModule' => $_GET['IdModule'], 'IdPage' => $_GET['IdPage'], 'op' => '2', 'IdProperty_page' => $_GET['IdProperty_page']) ), $lang['common']['redirect'], $lang['common']['success'], $lang['common']['press_here_redirecting'] , $arr_block) );
		

	break;

}

?>