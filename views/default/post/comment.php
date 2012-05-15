<?php

function CommentView($author, $email, $avatar, $date_register, $website, $num_messages, $x, $subject, $text, $signature, $posted, $iduser, $status, $options, $url, $num_comment, $ip, $rank)
{

	?>
	<a name="comment<?php echo $num_comment; ?>"></a>
	<div class="title">
		<div style="float:left;width:50%"><a href="<?php echo $url; ?>"> <?php echo $posted; ?> </a></div>  <div style="text-align:right;">#<?php echo $x; ?></div>
	</div>
	<!--<div style="border:solid #ffffff;border-width:1px;padding:5px;margin-bottom:4px;background-color:#0066b9;"><?php echo $subject; ?></div>-->
	<div class="cont">
	<div style="float:left;width:25%;border: solid #ffffff;border-width:0px 1px 0px 0px;height:100%;padding:10px;position:relative;"><?php echo $author; ?><br /><?php echo $rank; ?><br /><?php echo $avatar; ?><br /><?php echo $date_register; ?><br /><?php echo $num_messages; ?><br /><?php echo $status; ?><a target="_blank" href="<?php echo $website; ?>"><?php echo $website; ?></a><br /><br /><?php echo $ip; ?>
	</div>
	<div style="padding:10px;display:block;position:relative;float:left;width:66%">
	<?php echo $text; ?>
	<hr />
	<?php echo $signature; ?>
	</div>
	</div>
	<div align="right" style="padding:5px;"><?php echo $options; ?></div> 
	<?php

}

?>