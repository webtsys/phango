<?php

function RankAdmin()
{

	global $base_url, $model, $lang;

	load_libraries(array('generate_admin_ng'));
	load_lang('user');

	$arr_fields=array('name', 'fixed');
	$arr_fields_edit=array();

	settype($_GET['op'], 'integer');

	$url_options_no_fixed=make_fancy_url($base_url, 'admin', 'index', 'admin_ranks', array('IdModule' => $_GET['IdModule'], 'op' => 1));

	$url_options_fixed=make_fancy_url($base_url, 'admin', 'index', 'admin_ranks', array('IdModule' => $_GET['IdModule'], 'op' => 2));
	
	?>
	<p><a href="<?php echo $url_options_no_fixed; ?>"><?php echo $lang['user']['admin_no_fixed_ranks']; ?></a> - <a href="<?php echo $url_options_fixed; ?>"><?php echo $lang['user']['admin_fixed_ranks']; ?></a>
	<?php

	$model['rank']->create_form();

	$model['rank']->forms['name']->label=$lang['common']['name'];
	$model['rank']->forms['num_posts']->label=$lang['user']['num_posts'];
	$model['rank']->forms['image']->label=$lang['common']['image'];

	switch($_GET['op'])
	{

		case 1:

			$arr_fields=array('name', 'num_posts', 'image');
			$arr_fields_edit=$arr_fields;
	
			$where_sql='where fixed=0 and IdRank>1';

			generate_admin_model_ng('rank', $arr_fields, $arr_fields_edit, $url_options_no_fixed, $options_func='BasicOptionsListModel', $where_sql, $arr_fields_form=array(), $type_list='Basic');

		break;

		case 2:

			$model['rank']->forms['fixed']->form='HiddenForm';

			$model['rank']->forms['fixed']->SetForm(1);

			$arr_fields=array('name', 'fixed', 'image');
			$arr_fields_edit=$arr_fields;
	
			$where_sql='where fixed=1 and IdRank>1';

			generate_admin_model_ng('rank', $arr_fields, $arr_fields_edit, $url_options_fixed, $options_func='BasicOptionsListModel', $where_sql, $arr_fields_form=array(), $type_list='Basic');

		break;

	}

}

?>