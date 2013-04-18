<?php
global $base_url;

/*class category_blog extends Webmodel {

	function __construct()
	{

		parent::__construct("category_blog");

	}
	
}

$model['category_blog']=new category_blog();

$model['category_blog']->components['title']=new CharField(255);
$model['category_blog']->components['title']->required=1;

$model['category_blog']->components['idfather']=new IntegerField(11);
$model['category_blog']->components['idfather']->form='SelectForm';*/

class blog extends Webmodel {

	function __construct()
	{

		parent::__construct("blog");

	}	
	
}

$model['blog']=new blog();

$model['blog']->components['title']=new CharField(255);
$model['blog']->components['title']->required=1;

$model['blog']->components['num_post']=new IntegerField(10);
$model['blog']->components['num_post']->required=1;

$model['blog']->components['accept_comment']=new BooleanField();
$model['blog']->components['accept_comment']->form='SelectForm';

$model['blog']->components['blog_father']=new IntegerField(11);
$model['blog']->components['blog_father']->form='SelectForm';

$model['blog']->components['num_words']=new IntegerField(10);
$model['blog']->components['num_words']->required=1;

class page_blog extends Webmodel {

	function __construct()
	{

		parent::__construct("page_blog");

	}	
	
}

$model['page_blog']=new page_blog();

$model['page_blog']->components['title']=new CharField(255);
$model['page_blog']->components['title']->form='TextForm';
$model['page_blog']->components['title']->required=1;

$model['page_blog']->components['text']=new TextHTMLField();
$model['page_blog']->components['text']->form='TextAreaBBForm';
$model['page_blog']->components['text']->required=1;

$model['page_blog']->components['entrance']=new TextHTMLField();
$model['page_blog']->components['entrance']->form='TextAreaBBForm';
$model['page_blog']->components['entrance']->required=1;

$model['page_blog']->components['subtitles']=new CharField(255);

$model['page_blog']->components['author']=new ForeignKeyField('user');

$model['page_blog']->components['author']->required=1;

$model['page_blog']->components['idblog']=new ForeignKeyField('blog');
$model['page_blog']->components['idblog']->form='HiddenForm';
$model['page_blog']->components['idblog']->required=1;

$model['page_blog']->components['date']=new DateField();
$model['page_blog']->components['date']->form='DateForm';
$model['page_blog']->components['date']->required=1;

$model['page_blog']->components['accept_comment']=new BooleanField();

$model['page_blog']->components['num_comments']=new IntegerField(10);



class comment_blog extends Webmodel {

	function __construct()
	{

		parent::__construct("comment_blog");

	}	
	
}

$model['comment_blog']=new comment_blog();

$model['comment_blog']->components['idauthor']=new ForeignKeyField('user');

$model['comment_blog']->components['author']=new CharField(255);
$model['comment_blog']->components['author']->required=1;

$model['comment_blog']->components['subject']=new CharField(255);

$model['comment_blog']->components['text']=new TextHTMLField();
$model['comment_blog']->components['text']->form='TextAreaBBPostForm';
$model['comment_blog']->components['text']->allowedtags['a']=array('pattern' => '/&lt;a.*?href=&quot;(http:\/\/.*?)&quot;.*?&gt;(.*?)&lt;\/a&gt;/', 'replace' => '<a_tmp href="$1">$2</a_tmp>', 'example' => '<a href=""></a>');
$model['comment_blog']->components['text']->allowedtags['p']=array('pattern' => '/&lt;p.*?&gt;(.*?)&lt;\/p&gt;/s', 'replace' => '<p_tmp>$1</p_tmp>','example' => '<p></p>');
$model['comment_blog']->components['text']->allowedtags['br']=array('pattern' => '/&lt;br.*?\/&gt;/', 'replace' => '<br_tmp />', 'example' => '<br />');
$model['comment_blog']->components['text']->allowedtags['strong']=array('pattern' => '/&lt;strong.*?&gt;(.*?)&lt;\/strong&gt;/s', 'replace' => '<strong_tmp>$1</strong_tmp>', 'example' => '<strong></strong>');
$model['comment_blog']->components['text']->allowedtags['em']=array('pattern' => '/&lt;em.*?&gt;(.*?)&lt;\/em&gt;/s', 'replace' => '<em_tmp>$1</em_tmp>', 'example' => '<em></em>');
$model['comment_blog']->components['text']->allowedtags['i']=array('pattern' => '/&lt;i.*?&gt;(.*?)&lt;\/i&gt;/s', 'replace' => '<i_tmp>$1</i_tmp>', 'example' => '<i></i>');
$model['comment_blog']->components['text']->allowedtags['u']=array('pattern' => '/&lt;u.*?&gt;(.*?)&lt;\/u&gt;/s', 'replace' => '<u_tmp>$1</u_tmp>', 'example' => '<u></u>');
$model['comment_blog']->components['text']->allowedtags['blockquote']=array('pattern' => '/&lt;blockquote.*?&gt;(.*?)&lt;\/blockquote&gt;/s', 'replace' => '<blockquote_tmp>$1</blockquote_tmp>', 'example' => '<blockquote></blockquote>', 'recursive' => 1);
//$model['comment_blog']->components['text']->allowedtags['img']=array('pattern' => '/&lt;img.*?alt=&quot;([aA-zZ]+)&quot;.*?src=&quot;('.str_replace('/', '\/', $base_url).'\/media\/smileys\/[^\r\n\t<"].*?)&quot;.*?\/&gt;/', 'replace' => '<img_tmp alt="$1" src="$2"/>', 'example' => '<img alt="emoticon" src="" />');

$model['comment_blog']->components['text']->allowedtags['img_emoticon']=array('pattern' => '/&lt;img.*?alt=&quot;([aA-zZ]+)&quot;.*?src=&quot;('.str_replace('/', '\/', $base_url).'\/media\/smileys\/[^\r\n\t<"].*?)&quot;.*?\/&gt;/', 'replace' => '<img_tmp alt="$1" src="$2"/>', 'example' => '<img alt="emoticon" src="" />');

if(ini_get ( "allow_url_fopen" )==1)
{

	load_libraries(array('check_image'));

	$model['comment_blog']->components['text']->allowedtags['img']=array('pattern' => '/&lt;img.*?src=&quot;(http:\/\/.*?)&quot;.*?\/&gt;/e', 'replace' => 'check_image(\'$1\')', 'example' => '<img src="http://www.domain.com/images/image.png" />');

}


$model['comment_blog']->components['text']->required=1;

$model['comment_blog']->components['email']=new EmailField(255);
$model['comment_blog']->components['email']->required=1;

$model['comment_blog']->components['website']=new CharField(255);

$model['comment_blog']->components['ip']=new CharField(255);
$model['comment_blog']->components['ip']->required=1;

$model['comment_blog']->components['idpage_blog']=new ForeignKeyField('page_blog');
$model['comment_blog']->components['idpage_blog']->required=1;

$model['comment_blog']->components['date_comment']=new DateField(255);
$model['comment_blog']->components['date_comment']->required=1;

$model['comment_blog']->components['approved']=new BooleanField();

class moderator_blog extends Webmodel {

	function __construct()
	{

		parent::__construct("moderator_blog");

	}
	
}

$model['moderator_blog']=new moderator_blog();

$model['moderator_blog']->components['iduser']=new ForeignKeyField('user');
$model['moderator_blog']->components['iduser']->required=1;

$model['moderator_blog']->components['idblog']=new ForeignKeyField('blog');
$model['moderator_blog']->components['idblog']->required=1;

class subscription extends Webmodel {

	function __construct()
	{

		parent::__construct("subscription");

	}	
	
}

$model['subscription']=new subscription();

$model['subscription']->components['idpage_blog']=new ForeignKeyField('page_blog');

$model['subscription']->components['email']=new CharField(250);

$model['subscription']->components['token']=new CharField(100);

class save_data extends Webmodel {


	function __construct()
	{

		parent::__construct("save_data");

	}	


}

$model['save_data']=new save_data(); 

$model['save_data']->components['author']=new CharField(255);

$model['save_data']->components['author']->required=1;

$model['save_data']->components['email']=new CharField(255);

$model['save_data']->components['website']=new CharField(255);

$model['save_data']->components['time']=new CharField(255);

$model['save_data']->components['time']->required=1;

$model['save_data']->components['token']=new CharField(255);

$model['save_data']->components['token']->required=1;

class tag_blog extends Webmodel {


	function __construct()
	{

		parent::__construct("tag_blog");

	}	

	function delete($conditions="")
	{

		$query=webtsys_query('select DISTINCT idtag from page_tag_blog '.$conditions);

		list($idtag)=webtsys_fetch_row($query);
		
		settype($idtag, 'integer');

		if($idtag>0)
		{

			webtsys_query('delete from page_tag_blog where idtag='.$idtag);

		}

 		return webtsys_query('delete from '.$this->name.' '.$conditions);
		
	}


}

$model['tag_blog']=new tag_blog();

$model['tag_blog']->components['tag']=new CharField(255);

class page_tag_blog extends Webmodel {


	function __construct()
	{

		parent::__construct("page_tag_blog");

	}	


}

$model['page_tag_blog']=new page_tag_blog();

$model['page_tag_blog']->components['idtag']=new ForeignKeyField('tag_blog', 11);

$model['page_tag_blog']->components['idpage_blog']=new ForeignKeyField('page_blog', 11);

$model['page_tag_blog']->components['idpage_blog']->form='HiddenForm';


$arr_module_insert['blog']=array('name' => 'blog', 'admin' => 1, 'admin_script' => array('blog', 'blog'), 'load_module' => '', 'order_module' => 0, 'required' => 0, 'app_index' => 1);

$arr_module_remove['blog']=array('category_blog', 'blog', 'page_blog', 'comment_blog', 'moderator_blog', 'subscription', 'save_data', 'tag_blog', 'page_tag_blog');

?>
