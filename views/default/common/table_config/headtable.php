<?php

function HeadTableView($fields, $cell_sizes=array())
{

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

}

?>