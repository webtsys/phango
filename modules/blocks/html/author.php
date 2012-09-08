<a name="list_pages"></a>
<?php

include_once($base_path.'libraries/pages.php');

settype($GLOBALS['author'], 'integer');
settype($_GET['begin_page_article'], 'integer');

if($_GET['begin_page_article']<0)
{

	$_GET['begin_page_article']=0;

}

$query=webtsys_query('select count(IdPage_blog) from page_blog where author='.$GLOBALS['author']);

list($total_elements)=webtsys_fetch_row($query);

$num_elements=20;

$link='article.php?IdPage_blog='.$_GET['IdPage_blog'];

$total_pages= pages( $_GET['begin_page_article'], $total_elements, $num_elements, $link , 20, 'begin_page_article', '#list_pages');

$query=webtsys_query('select IdPage_blog, title from page_blog where author='.$GLOBALS['author'].' order by date DESC limit '.$_GET['begin_page_article'].', '.$num_elements);

while(list($idpage_blog, $title_page)=webtsys_fetch_row($query))
{

	?>
	•<a href="article.php?IdPage_blog=<?php echo $idpage_blog; ?>"><?php echo $title_page; ?></a><br />
	<?php

}
?>
<br />
<?php echo $lang['common']['more']; ?>: <?php echo $total_pages; ?>

