<?php

function up_table_config($fields, $cell_sizes=array())
{

	echo load_view(array($fields, $cell_sizes), 'common/table_config/headtable');
}

function middle_table_config($fill, $cell_sizes=array())
{
	echo load_view(array($fill, $cell_sizes), 'common/table_config/middletable');
}

function down_table_config()
{
	echo load_view(array(), 'common/table_config/bottomtable');
}

function pages_table($pages, $more_data='')
{

	global $lang;
	
	?>
	<div class="head_list">
		<?php echo $lang['common']['pages']; ?>: <?php echo $pages; ?>
	</div>

	<div class="head_list_right">
		<?php echo $more_data; ?>
	</div>
	<?php
	

}

?>