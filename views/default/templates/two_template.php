<?php

function ContentLeftView($title, $content)
{

?>
	<div style="float:left;width:50%;border:solid #ffffff;border-width:0px;padding:0px;">
		<div class="title">
			<?php echo $title; ?>
		</div>
		<div class="cont">
			<?php echo $content; ?>
		</div>
	</div>

<?php

}

function ContentRightView($title, $content)
{

?>
	
	<div style="float:left;width:49.4%;border:solid #ffffff;border-width:0px;padding-left:4px;">
		<div class="title">
			<?php echo $title; ?>
		</div>
		<div class="cont">
			<?php echo $content; ?>
		</div>
	</div>
	<br clear="left" />
<?php

}

function ContentAllView($title, $content)
{

?>
	
	<div style="float:left;width:100%;border:solid #ffffff;border-width:0px;padding-left:4px;">
		<div class="title">
			<?php echo $title; ?>
		</div>
		<div class="cont">
			<?php echo $content; ?>
		</div>
	</div>
	<br clear="left" />
<?php

}

?>