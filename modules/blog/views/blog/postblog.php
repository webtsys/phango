<?php

function PostBlogView($idauthor, $idpost, $author, $title, $text, $num_comments, $date, $tags='')
{

	global $base_url, $lang;

	?>
	<div class="content">
		<div class="title"><?php echo $title; ?></div>
		<div class="cont">
			<div style="margin-bottom:10px;">
				<strong><?php echo $lang['blog']['posted_by']; ?> <?php echo $author; ?></strong>
			</div>
			<?php echo $text; ?>
			<p>
				<a href="<?php
				echo make_fancy_url( $base_url, 'blog', 'post', $title, array('IdPage_blog' => $idpost) );
				 ?>"><?php echo $lang['blog']['read_more']; ?></a> - <a href="<?php echo make_fancy_url( $base_url, 'blog', 'post', $title, array('IdPage_blog' => $idpost) ); ?>#comments"><?php echo $lang['blog']['comments']; ?>(<?php echo $num_comments; ?>)</a> - <?php echo $date; ?> - <?php echo $lang['blog']['tags']; ?>: <?php echo $tags; ?>
			<p>
		</div>
	</div>
	<?php

}

?>