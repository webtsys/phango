<?php

function MiddleTableView($fill, $cell_sizes=array())
{

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

}

?>