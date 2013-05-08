<?php

class I18nField {

	public $value="";
	public $label="";
	public $required=0;
	public $form="MultiLangForm";
	public $quot_open='\'';
	public $quot_close='\'';
	public $std_error='';
	public $related_field='';
	public $type_field='';

	//This method is used for check all members from serialize

	function __construct($type_field)
	{

		$this->type_field=&$type_field;

	}

	function check($value)
	{
		global $arr_i18n, $language, $lang;
		
		foreach($arr_i18n as $lang_item)
		{

			$value[$lang_item]=$this->type_field->check($value[$lang_item]);

		}

		if($this->required==1 && $value[$_SESSION['default_language']]=='')
		{

			$this->std_error=$lang['common']['error_you_need_this_language_field'].' '.$_SESSION['default_language'];

			return '';

		}
		
		$ser_value=addslashes(serialize($value));

		return $ser_value;

	}

	function get_type_sql()
	{

		return 'TEXT NOT NULL';
		

	}

	static function show_formatted($value)
	{
		global $language;

		$arr_lang=@unserialize($value);

		settype($arr_lang, 'array');
		
		settype($arr_lang[$language], 'string');

		settype($arr_lang[$_SESSION['default_language']], 'string');

		if($arr_lang[$language]=='' && $arr_lang[$_SESSION['default_language']]=='')
		{
			
			//Need  view var with text...
			
			//$arr_lang_first=array_unique($arr_lang);
			foreach($arr_lang as $key_lang => $val_lang)
			{
			
				if($val_lang!='')
				{
				
					return $val_lang;
				
				}
			
			}

		}
		else if($arr_lang[$language]=='')
		{
			
			return $arr_lang[$_SESSION['default_language']];
		
		}
		
		return $arr_lang[$language];

	}

	function get_parameters_default()
	{

		return ;

	}
	
	function add_slugify_i18n_post($field, $post)
	{
	
		global $model, $arr_i18n;
	
		foreach($arr_i18n as $lang_field)
		{
		
			$post[$field.'_'.$lang_field]=SlugifyField::check($post[$field][$lang_field]);
		
		}
		
		return $post;
	
	}
	
}

//Now, class slugifyfield. This class can be used for create orders or searchs in mysql if you need other thing distinct to default search of default order (default order don't work fine with serializefields how i18nfield). The programmer have the responsability of update this fields via update or insert method.

class SlugifyField {

	public $value="";
	public $label="";
	public $required=0;
	public $form="TextForm";
	public $quot_open='\'';
	public $quot_close='\'';
	public $std_error='';

	static function check($value)
	{
		
		return slugify($value);
	}

	function get_type_sql()
	{

		return 'TEXT NOT NULL';
		

	}

	function get_parameters_default()
	{

		return;

	}
	
	static function add_slugify_i18n_fields($model_name, $field)
	{
	
		global $model, $arr_i18n;
	
		foreach($arr_i18n as $lang_field)
		{

			$model[$model_name]->components[$field.'_'.$lang_field]=new SlugifyField();
			
		}
	
	}
	
}




function MultiLangForm($field, $class='', $arr_values=array(), $type_form='TextForm')
{
	//make a foreach with all langs
	//default, es_ES, en_US, show default if no exists translation for selected language.
	
	global $arr_i18n, $language, $base_url, $lang, $config_data;

	ob_start();

	//echo $type_form($field, 'hidden_form', 'control_field');

	if(gettype($arr_values)!='array')
	{

		$arr_values = @unserialize( $arr_values );
		
		if(gettype($arr_values)!='array')
		{

			$arr_values=array();
			
		}
		
	}
	
	
	foreach($arr_i18n as $lang_select)
	{

		$arr_selected[slugify($lang_select)]='hidden_form';
		$arr_selected[slugify($_SESSION['default_language'])]='no_hidden_form';
		
		settype($arr_values[$lang_select], 'string');
		echo '<div class="'.$arr_selected[slugify($lang_select)].'" id="'.$field.'_'.$lang_select.'">';
		echo $type_form($field.'['.$lang_select.']', '', $arr_values[$lang_select]);
		echo '</div>';

	}
	?>
	<div id="languages">
	<?php

	$arr_selected=array();

	foreach($arr_i18n as $lang_item)
	{
		//set

		$arr_selected[slugify($lang_item)]='no_choose_flag';
		$arr_selected[slugify($_SESSION['default_language'])]='choose_flag';

		?>
		<a class="<?php echo $arr_selected[slugify($lang_item)]; ?>" id="<?php echo $field.'_'.$lang_item; ?>_flag" href="#" onclick="change_form_language_<?php echo $field; ?>('<?php echo $field; ?>', '<?php echo $field.'_'.$lang_item; ?>'); return false;"><img src="<?php echo $base_url; ?>/media/<?php echo $config_data['dir_theme']; ?>/images/languages/<?php echo $lang_item; ?>.png" alt="<?php echo $lang_item; ?>"/></a>&nbsp;
		<?php

	}

	?>
	</div>
	<hr />
	<script language="Javascript">
		
		function change_form_language_<?php echo $field; ?>(field, lang_field)
		{

			if(typeof jQuery == 'undefined') 
			{
				alert('<?php echo $lang['common']['cannot_load_jquery']; ?>');
				return false;

			}

			<?php

			foreach($arr_i18n as $lang_item)
			{

				?>
				$("#<?php echo $field.'_'.$lang_item; ?>").hide();//removeClass("no_hidden_form").addClass("hidden_form");
				$("#<?php echo $field.'_'.$lang_item; ?>_flag").removeClass("choose_flag").addClass("no_choose_flag");
				<?php

			}

			?>
			
			lang_field=lang_field.replace('[', '\\[');
			lang_field=lang_field.replace(']', '\\]');

			$("#"+lang_field).show();//.removeClass("hidden_form").addClass("no_hidden_form");
			$("#"+lang_field+'_flag').removeClass("no_choose_flag").addClass("choose_flag");
			
		}

	</script>
	<?php


	$text_form=ob_get_contents();

	ob_end_clean();

	return $text_form;

}

function MultiLangFormSet($post, $value)
{
	
	if(!gettype($value)=='array')
	{

		settype($arr_value, 'array');

		$arr_value = @unserialize( $value );
		
		return $arr_value;

	}
	else
	{

		return $value;

	}

}


?>
