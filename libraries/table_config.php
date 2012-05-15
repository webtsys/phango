<?php

function up_table_config($fields, $cell_sizes=array())
{

	echo load_view(array($fields, $cell_sizes), 'common/table_config/headtable');

/*
	?>

		<table class="table_list">
		<tr class="title_list">
		<?php
		foreach($fields as $key_cell => $field)
		{	
			settype($cell_sizes[$key_cell], 'string');
			?>
			<td<?php echo $cell_sizes[$key_cell]; ?>><?php echo $field; ?></td>
			<?php

		}
		?>
		</tr>
	
	<?php
*/
}

function middle_table_config($fill, $cell_sizes=array())
{
	echo load_view(array($fill, $cell_sizes), 'common/table_config/middletable');

/*
			?>
			<tr class="row_list">
			<?php
			foreach($fill as $key_cell => $final_fill)
			{
				settype($cell_sizes[$key_cell], 'string');
				
			?>
				
				<td<?php echo $cell_sizes[$key_cell]; ?>><?php echo $final_fill; ?></td>
			<?php
			}
			?>
			</tr>
			<?php
*/
}

function down_table_config()
{
	echo load_view(array(), 'common/table_config/bottomtable');
/*
	?>
		</table> 
	<?php
*/

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