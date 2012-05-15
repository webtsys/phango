<?php
global $base_url;

class mprivate extends Webmodel {

	function __construct()
	{

		parent::__construct("mprivate");

	}	
	
}

$model['mprivate']=new mprivate();

$model['mprivate']->components['iduser_sender']=new CharField(255);
$model['mprivate']->components['iduser_sender']->required=1;

$model['mprivate']->components['iduser']=new CharField(255);
$model['mprivate']->components['iduser']->required=1;

$model['mprivate']->components['author']=new CharField();
$model['mprivate']->components['author']->required=1;

$model['mprivate']->components['subject']=new CharField(255);
$model['mprivate']->components['subject']->required=1;

$model['mprivate']->components['text']=new TextHTMLField();
$model['mprivate']->components['text']->allowedtags['a']=array('pattern' => '/&lt;a.*?href=&quot;(http:\/\/.*?)&quot;.*?&gt;(.*?)&lt;\/a&gt;/', 'replace' => '<a_tmp href="$1">$2</a_tmp>', 'example' => '<a href=""></a>');
$model['mprivate']->components['text']->allowedtags['p']=array('pattern' => '/&lt;p.*?&gt;(.*?)&lt;\/p&gt;/', 'replace' => '<p_tmp>$1</p_tmp>','example' => '<p></p>');
$model['mprivate']->components['text']->allowedtags['br']=array('pattern' => '/&lt;br.*?\/&gt;/', 'replace' => '<br_tmp />', 'example' => '<br />');
$model['mprivate']->components['text']->allowedtags['strong']=array('pattern' => '/&lt;strong.*?&gt;(.*?)&lt;\/strong&gt;/', 'replace' => '<strong_tmp>$1</strong_tmp>', 'example' => '<strong></strong>');
$model['mprivate']->components['text']->allowedtags['em']=array('pattern' => '/&lt;em.*?&gt;(.*?)&lt;\/em&gt;/', 'replace' => '<em_tmp>$1</em_tmp>', 'example' => '<em></em>');
$model['mprivate']->components['text']->allowedtags['i']=array('pattern' => '/&lt;i.*?&gt;(.*?)&lt;\/i&gt;/', 'replace' => '<i_tmp>$1</i_tmp>', 'example' => '<i></i>');
$model['mprivate']->components['text']->allowedtags['u']=array('pattern' => '/&lt;u.*?&gt;(.*?)&lt;\/u&gt;/', 'replace' => '<u_tmp>$1</u_tmp>', 'example' => '<u></u>');
$model['mprivate']->components['text']->allowedtags['blockquote']=array('pattern' => '/&lt;blockquote.*?&gt;(.*?)&lt;\/blockquote&gt;/s', 'replace' => '<blockquote_tmp>$1</blockquote_tmp>', 'example' => '<blockquote></blockquote>');
$model['mprivate']->components['text']->allowedtags['img']=array('pattern' => '/&lt;img.*?alt=&quot;([aA-zZ]+)&quot;.*?src=&quot;('.str_replace('/', '\/', $base_url).'\/media\/smileys\/[^\r\n\t<"].*?)&quot;.*?\/&gt;/', 'replace' => '<img_tmp alt="$1" src="$2"/>', 'example' => '<img alt="emoticon" src="" />');
$model['mprivate']->components['text']->required=1;

$model['mprivate']->components['date']=new DateField();
$model['mprivate']->components['date']->required=1;

$model['mprivate']->components['read_message']=new IntegerField(1);


?>