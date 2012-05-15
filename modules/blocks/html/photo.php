<?php
	global $result, $base_path;
	
	include_once($base_path.'models/photo.php');
	
	settype($id, 'integer');
	
	$query=$model['photo']->select('where idblock='.$id, array('image', 'foot') );
	
	list($image, $foot)=webtsys_fetch_row($query);

	?>
	<div align="center">
	<img src="<?php echo $image; ?>" />
	<br /><i><?php echo $foot; ?></i>
	</div>
	<?php

?>