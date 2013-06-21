<?php

//Variables

// First, define basic variables

//Basic variables for phango. 
//This variables is used for internal functions in phango

$arr_error_model=array();
$arr_error_model_text=array();
$arr_module_sql=array();
$arr_module_insert=array();
$arr_module_remove=array();

$utility_cli=0;
$yes_entities=1;

//This variable is needed for add new fields to models without lost when you execute load_model without extension. Is saved in optional file added_fields.php

$arr_models_loading=array();

//The timestam for this moment...

define("TODAY", time());

define("TODAY_FIRST", gmmktime(0, 0, 0));
define("TODAY_LAST", gmmktime(23, 59, 59));
define("TODAY_HOUR", gmmktime(date('H'), 0, 0));

//This variable is used for save general errors. 

$std_error=''; 

//Classes

//Webmodel is the base class for all models
//This class is the base for construct all models. Models are saved in $models array


class Webmodel {

	public $name;
	
	public $label;
	
	public $idmodel;

	//Components is a array for the fields of this model

	public $components;
	public $forms;

	//Components is a array for the fields forms of this model

	//This variables define differents functions for use in automatize functions how generate_admin
	//I prefer this method instead of overloading function methods

	public $func_update='Basic';

	//In this variable is store errors using the model...

	public $std_error='';

	//Variable for indicate to forms that this model have enctype...

	public $enctype='';

	//Variable for indicate how many extra fields for foreignkeyfield have created for clean components

	public $arr_trash_components=array();
	
	//Key is a model name, and the first element of array for the element is the connection.
	
	public $related_models=array();
	
	public $prev_check=0;

	//Construct the model

	
	//Basic constructor for model class.
	//Phango is a MVC Framework. The base of a MVC framework are the models. A Model is a representation of a database table, are used for create, update and delete information. 
	
	//@param $name_model is the name of the model
	
	public function __construct($name_model)
	{
		
		$this->name=$name_model;
		$this->idmodel='Id'.ucfirst($this->name);
		$this->components[$this->idmodel]=new PrimaryField();
		$this->label=$this->name;

	}

	public function change_id_default($name_id)
	{

		//Check if i create more components, if create more, die.

		if(count($this->components)>1)
		{

			show_error('<p>Error in a model for use ids.</p>', '<p>Error in model '.$this->name.' for use change_id_default. This method must be used before any component.</p>');

		}
		
		unset($this->components[$this->idmodel]);
		$this->idmodel=$name_id;
		$this->components[$this->idmodel]=new PrimaryField();

	}

	//This method insert a row in database using model data

	//Method for create a new row in the model.
	//@param $post is a array where each key is referred to a model field. 

	public function insert($post)
	{

		global $lang;
		
		$this->clean_extra_fields();
		
		//Check if minimal fields are fill and if fields exists in components.Check field's values.
		
		unset($post[$this->idmodel]);
		
		$arr_fields=array();
		
		if( $fields=$this->check_all($post) )
		{	
			
			//Foreach for create the query that comes from the $post array
			
			foreach($fields as $key => $field)
			{
			
				$quot_open=$this->components[$key]->quot_open;
				$quot_close=$this->components[$key]->quot_close;
			
				if(get_class($this->components[$key])=='ForeignKeyField' && $fields[$key]==NULL)
				{
				
					$quot_open='';
					$quot_close='';
					$fields[$key]='NULL';
				
				}
			
				$arr_fields[]=$quot_open.$fields[$key].$quot_close;
			
			}
		
			if( !( $query=webtsys_query('insert into '.$this->name.' (`'.implode("`, `", array_keys($fields)).'`) VALUES ('.implode(", ",$arr_fields).') ') ) )
			{
			
				$this->std_error.=$lang['error_model']['cant_insert'].' ';
				return 0;
			
			}
			else
			{
			
				return 1;
				
			}
		}
		else
		{	
			
			$this->std_error.=$lang['error_model']['cant_insert'].' ';

			return 0;

		}
		
	}

	//Method update a row in database using model data
	//@param $post is a array where each key is referred to a model field. 
	//@param $conditions is a sql sentence for specific conditions for the query Example: "where id=2"
	
	public function update($post, $conditions="")
	{

		global $lang;

		$this->clean_extra_fields();

		//Check if minimal fields are fill and if fields exists in components.

		$arr_fields=array();

		//Unset the id field from the model for security
		
		unset($post[$this->idmodel]);

		//Checking and sanitizing data from $post array for use in the query
		
		if( $fields=$this->check_all($post) )
		{
		
			//Foreach for create the query that comes from the $post array
			
			foreach($this->components as $key => $component)
			{
				if(isset($fields[$key]))
				{
				
					$quot_open=$component->quot_open;
					$quot_close=$component->quot_close;
				
					if(get_class($component)=='ForeignKeyField' && $fields[$key]==NULL)
					{
					
						$quot_open='';
						$quot_close='';
						$fields[$key]='NULL';
					
					}
				
					$arr_fields[]='`'.$key.'`='.$quot_open.$fields[$key].$quot_close;
					
				}
	
			}

			//Create the query..
		
			if(!($query=webtsys_query('update '.$this->name.' set '.implode(', ' , $arr_fields).' '.$conditions)))
			{
			
				$this->std_error.=$lang['error_model']['cant_update'].' ';
				return 0;
			
			}
			else
			{
			
				return 1;
			
			}
		}
		else
		{
			//Validation of $post fail, add error to $model->std_error
			
			$this->std_error.=$lang['error_model']['cant_update'].' ';

			return 0;

		}

	}

	//This method select a row in database using model data
	//You have use webtsys_fetch_row or alternatives for obtain data
	//Conditions are sql lang, more simple, more kiss

	public function select($conditions="", $arr_select=array(), $raw_query=0)
	{
		//Check conditions.., script must check, i can't make all things!, i am not a machine!

		global $model;

		 //$this->clean_extra_fields();

		if(count($arr_select)==0)
		{
		
			$arr_select=array_keys($this->components);
			

		}
		else
		{
			
			$arr_select=array_intersect($arr_select, array_keys($this->components));

		}

		//$arr_extra_select is an hash for extra fields from related models
		$arr_extra_select=array();
		//$arr_model is an array where are stored the tables used in the query, it is usually only referred to the model table
		$arr_model=array($this->name);
		//$arr_where is an array where is stored the relationship between models
		$arr_where=array('1=1');
		
		$arr_extra_model=array();

		foreach($arr_select as $key => $my_field)
		{
			//Check if field is a key from a related_model

			$arr_select[$key]=$this->name.'.`'.$my_field.'`';

			//Check if a field link with other field from another table...

			//list($arr_select, $arr_extra_select, $arr_model, $arr_where)=$this->recursive_fields_select($key, $this->name, $my_field, $raw_query, $arr_select, $arr_extra_select, $arr_model, $arr_where);
			if(get_class($this->components[$my_field])=='ForeignKeyField')
			{
			
				$arr_extra_model[$key]=$my_field; //$this->components[$my_field]->related_model;
			
			}
			
		}
		
		if($raw_query==0)
		{
		
			//Add fields defined on fields_related_model.
			
			foreach($arr_extra_model as $key => $my_field)
			{
			
				$model_name_related=$this->components[$my_field]->related_model;
				
				//Set the value for the component foreignkeyfield if name_field_to_field is set.
			
				if($this->components[$my_field]->name_field_to_field!='')
				{
				
					$arr_select[$key]=$model_name_related.'.`'.$this->components[$my_field]->name_field_to_field.'` as `'.$my_field.'`';
					
				}
				
				//Set the new fields added for related model...
				
				foreach($this->components[$my_field]->fields_related_model as $fields_related)
				{
				
					$arr_select[]=$model_name_related.'.`'.$fields_related.'` as `'.$model_name_related.'_'.$fields_related.'`';
				
				}
				
				$arr_model[]=$model_name_related;
				
				//Set the where connection
				
				$arr_where[]=$this->name.'.`'.$my_field.'`='.$model_name_related.'.`'.$model[$model_name_related]->idmodel.'`';
			
			}
			
			//Now define inverse relationship...
			
			foreach($this->related_models as $model_name_related => $fields_related)
			{
			
				foreach($fields_related as $field_related)
				{
				
					$arr_select[]=$model_name_related.'.`'.$field_related.'` as `'.$model_name_related.'_'.$field_related.'`';
					
				}
				
				$arr_model[]=$model_name_related;
				
				$arr_where[]=$this->name.'.`'.$this->idmodel.'`='.$model_name_related.'.`'.$fields_related[0].'`';
			
			}
		
		}

		//Final fields from use in query
		
		$fields=implode(", ", $arr_select);

		//The tables used in the query
		
		$arr_model=array_unique($arr_model, SORT_STRING);

		$selected_models=implode(", ", $arr_model);
		
		//Conditions for the select query for related fields in the model
		$where=implode(" and ", $arr_where);

		//$conditions is a variable where store the result from $arr_select and $arr_extra_select
		
		if(preg_match('/^where/', $conditions) || preg_match('/^WHERE/', $conditions))
		{
			
			$conditions=str_replace('where', '', $conditions);
			$conditions=str_replace('WHERE', '', $conditions);

			$conditions='WHERE '.$where.' and '.$conditions;

		}
		else
		{
			
			$conditions='WHERE '.$where.' '.$conditions;

		}

		//$this->create_extra_fields();
		
		//Make the query...
		
		return webtsys_query('select '.$fields.' from '.$selected_models.' '.$conditions);
		
	}


	public function clean_extra_fields()
	{

		//Clean components...

		foreach($this->arr_trash_components as $clean_field => $arr_related_field)
		{
			
			unset($this->components[$clean_field]);

		}

	}

	public function create_extra_fields()
	{

		global $model;

		//now create extra components for related models with foreignkeyfields for use in process functions of data...
		
		foreach($this->arr_trash_components as $clean_field => $arr_related_field)
		{
			
			$this->components[$clean_field]=&$model[$arr_related_field[0]]->components[ $arr_related_field[1] ];

		}

	}

	//This method count num rows for the sql condition

	public function select_count($conditions, $field)
	{
	
		$arr_model=array($this->name);
		$arr_where=array('1=1');
	
		foreach($this->related_models as $model_name_related => $fields_related)
		{
			
			$arr_model[]=$model_name_related;
			
			$arr_where[]=$this->name.'.`'.$this->idmodel.'`='.$model_name_related.'.`'.$fields_related[0].'`';
		
		}
		
		$where=implode(" and ", $arr_where);
		
		if(preg_match('/^where/', $conditions) || preg_match('/^WHERE/', $conditions))
		{
			
			$conditions=str_replace('where', '', $conditions);
			$conditions=str_replace('WHERE', '', $conditions);

			$conditions='WHERE '.$where.' and '.$conditions;
			
		}
		else
		{
			
			$conditions='WHERE '.$where.' '.$conditions;

		}

		$query=webtsys_query('select count('.$this->name.'.`'.$field.'`) from '.implode(', ', $arr_model).' '.$conditions);
		
		list($count_field)= webtsys_fetch_row($query);

		return $count_field;

	}

	//This method delete rows for the sql condition

	public function delete($conditions="")
	{
	
		foreach($this->components as $name_field => $component)
		{
		
			if(method_exists($component,  'process_delete_field'))
			{
			
				$component->process_delete_field($this, $name_field, $conditions);
			
			}
		
		}

 		return webtsys_query('delete from '.$this->name.' '.$conditions);
		
	}

	//A helper function for obtain names from components

	public function all_fields()
	{
	
		if(count($this->forms)==0)
		{
		
			$this->create_form();
		
		}
	
		return array_keys($this->forms);

	}
	
	public function stripped_all_fields($arr_strip)
	{
	
		$arr_total_fields=$this->all_fields();

		return array_diff($arr_total_fields, $arr_strip);

	}
	
	public function check_element($key, $value)
	{
	
		return $this->components[$key]->check($value);
	
	}
	
	public function no_check_element($key, $value)
	{
	
		return $value;
	
	}

	//Check if components are valid, if not fill $this->std_error

	public function check_all($post)
	{

		global $lang;
	
		//array where sanitized values are stored...
		
		$func_check='check_element';
		
		if($this->prev_check==1)
		{
		
			$func_check='no_check_element';
		
		}

		$arr_components=array();

		$set_error=0;

		$arr_std_error=array();

		//Make a foreach inside components, fields that are not found in components, are ignored
		
		foreach($this->components as $key => $value)
		{
			
			//If is set the variable for this component make checking

			if(isset($post[$key]))
			{

				//Check if the value is valid..

				$arr_components[$key]=$this->$func_check($key, $post[$key]);

				//If value isn't valid and is required set error for this component...

				if($this->components[$key]->required==1 && $arr_components[$key]=="")
				{	

					//Set errors...

					if($this->components[$key]->std_error=='')
					{

						$this->components[$key]->std_error=$lang['common']['field_required'];

					}

					$arr_std_error[]=$lang['error_model']['check_error_field'].' '.$key.' -> '.$this->components[$key]->std_error. ' ';
					$set_error++;
	
				}
		
			}
			else if($this->components[$key]->required==1)
			{
	
				//If isn't set the value and this value is required set std_error.

				$arr_std_error[]=$lang['error_model']['check_error_field_required'].' '.$key.' ';
	
				$set_error++;

			}

		}

		//Set std_error for the model where is stored all errors in checking...

		$this->std_error=implode(', ', $arr_std_error);

		//If error return false

		if($set_error>0)
		{

			return 0;

		}

		//If not return values sanitized...

		return $arr_components;

	}

	//Helper function for unset components if necessary

	public function unset_components($arr_components=array())
	{

		foreach($arr_components as $value)
		{
			$stack[$value]=$this->components[$value];
			unset($this->components[$value]);
		}

		return $stack;

	}

	public function create_form($fields_form=array())
	{

		//With function for create form, we use an array for specific order, after i can insert more fields in the form.

		$this->forms=array();
		
		$arr_form=array();
		
		if(count($fields_form)==0)
		{
		
			$fields_form=array_keys($this->components);
			
		}
		
		//foreach($this->components as $component_name => $component)
		foreach($fields_form as $component_name)
		{
		
			if(isset($this->components[$component_name]))
			{
			
				$component=&$this->components[$component_name];
			
				//Create form from model's components

				$this->forms[$component_name]=new ModelForm($this->name, $component_name, $component->form, set_name_default($component_name), $component, $component->required, '');

				//Set parameters to default
				$parameters='';

				/*if($this->forms[$component_name]->parameters[2]==0)
				{*/
					
				$parameters=$component->get_parameters_default();

				//}

				//Use method from ModelForm for set initial parameters...

				$this->forms[$component_name]->SetParameters($parameters);
				
			}

		}

	}

	//Method for obtain an array with all errors in components
	
	public function return_error_form()
	{

		$arr_error=array();

		foreach($this->components as $component_name => $component)
		{

			$arr_error[$component_name]=$component->std_error;

		}

		return $arr_error;

	}

	//Method for reset required fields from components. Use this if you need update a field from a model that you don't want update required fields.

	public function reset_require()
	{
		

		foreach($this->components as $component_name => $component)
		{

			$this->components[$component_name]->required=0;

		}

	}

	public function set_enctype_binary()
	{

		$this->enctype='enctype="multipart/form-data"';

	}
	
	//Here, define extension methods with call...
	
	public function __call($name_method, $arguments)
	{
	
		load_libraries(array('classes_extensions/'.$name_method));
	
		array_unshift($arguments, $this);
	
		return call_user_func_array($name_method.'_method_class', $arguments);
	
	}
	
	static public function check_where($arr_where)
	{
	
		foreach($arr_where as $key => $value)
		{
		
			$arr_where[$key]=$this->components[$key]->check($value);
		
		}
		
		return $arr_where;
	
	}
	
	public function set_component($name, $type, $arguments, $required=0)
	{
	
		$rc=new ReflectionClass($type);
		$this->components[$name]=$rc->newInstanceArgs($arguments);
		
		//Set first label...
		
		$this->components[$name]->label=set_name_default($name);
		$this->components[$name]->name_component=$name;
		$this->components[$name]->required=$required;
	
	}
	
	public function InsertAfterFieldForm($name_form_after, $name_form_new, $form_new)
	{
	
		$arr_form_new=array();
	
		foreach($this->forms as $form_key => $form_field)
		{
		
			$arr_form_new[$form_key]=$form_field;
			
			if($form_key==$name_form_after)
			{
				
				$arr_form_new[$name_form_new]=$form_new;
			
			}
		
		}
		
		$this->forms=$arr_form_new;
	
	}

}

//Fill a form with default values 
//@param $post is an array with values
//@param $arr_form is an array of ModelForms
//@param $show_error 

function SetValuesForm($post, $arr_form, $show_error=1)
{

	global $lang;
	
	//Foreach to $post values
	
	foreach($post as $name_field => $value)
	{
		
		//If exists a ModelForm into $arr_form with the same name to $name_field check if have a $component field how "type" and set error if exists

		if(isset($arr_form[$name_field]))
		{	
			
			if($arr_form[$name_field]->type->std_error!='' && $show_error==1)
			{
				
				/*if($arr_form[$name_field]->std_error!='')
				{
					
					$arr_form[$name_field]->std_error=$arr_form[$name_field]->txt_error;
					

				}
				else*/
				if($arr_form[$name_field]->std_error=='')
				{
					
					$arr_form[$name_field]->std_error=$arr_form[$name_field]->type->std_error;

				}

			}

			//Set value for ModelForm to $value
			
			$arr_form[$name_field]->SetForm($value);
	
		}
		else
		{

			unset($post[$name_field]);

		}

	}

}

//$arr_form_public is an array for save the field codified for use in public forms.

$arr_form_public=array();

//Class ModelForm is the base class for create forms...

class ModelForm {


	public $name_form;
	public $name;
	public $form;
	public $label;
	public $set_form;
	public $std_error;
	public $txt_error;
	public $html_field_name='';
	public $required=0;
	public $error_flag=0;

	function __construct($name_form, $name_field, $form, $label, $type, $required=0, $parameters='')
	{
		global $lang, $arr_form_public;

		$this->name_form = $name_form;
		$this->name = $name_field;
		$this->form = $form;
		$this->type = $type;
		$this->label = $label;
		$this->std_error = '';
		$this->txt_error = $lang['common']['error_in_field'];
		$this->required = $required;

		$html_field_name=$name_field;

		switch(DEBUG)
		{

			default:
				
				$html_field_name=sha1($name_field);
			
				$this->html_field_name[$name_field]=$html_field_name;

				if(isset($_POST[$html_field_name]))
				{

					$_POST[$name_field]=&$_POST[$html_field_name];

				}

				if(isset($_FILES[$html_field_name]))
				{

					$_FILES[$name_field]=&$_FILES[$html_field_name];

				}

			break;

			case 1:

				$this->html_field_name[$name_field]=$name_field;
				

			break;
		}

		$arr_form_public[$html_field_name]=$name_field;

		$this->parameters = array($html_field_name, $class='', $parameters);

	}

	function SetForm($value, $form_type_set='')
	{
		
		$func_setvalue=$this->form.'Set';
		
		$this->parameters[2]=$func_setvalue($this->parameters[2], $value, $form_type_set);
		
	}
	
	function SetValueForm($value, $form_type_set='')
	{
	
		$this->SetForm($value, $form_type_set);
	
	}

	function SetParameters($parameters)
	{
		
		$this->parameters[2]=$parameters;
		
	}
	
	function SetParametersForm($parameters)
	{
		
		$this->parameters=$parameters;
		
	}

	static function check_form($arr_form, $post)
	{

		$error=0;
		
		$num_form=0;
		
		foreach($post as $key_form => $value_form)
		{
			
			//settype($post[$key_form], 'string');
			
			if(isset($arr_form[$key_form]))
			{
			
				$form=$arr_form[$key_form];
			
				$post[$key_form]=$form->type->check($post[$key_form]);
				
				if($post[$key_form]=='' && $form->required==1)
				{
					
					if($form->type->std_error!='')
					{

						$form->std_error=$form->type->std_error;

					}
					else
					{

						$form->std_error=$form->txt_error;

					}
					
					$form->error_flag=1;

					if($form->required==1)
					{

						$error++;

					}
		
				}
				
			}
			
			$num_form++;

		}

		if($error==0 && $num_form>0)
		{

			return $post;

		}
		
		return 0;

	}

}

/*****************************************

Now, we define components for use in models. I don't use a base model for charfields for have more flexibility: more simple(more verbose), more kiss.

******************************************/

class PhangoField {

	public $indexed=0;
	
	function search_field($value)
	{
	
		return $this->check($value);
	
	}

}

//Charfield is a field for short text.

class CharField extends PhangoField {

	//Basic variables that define the field

	//Size of field in database
	public $size=20;
	//Label is the name of field
	public $label="";
	//Value of field...
	public $value="";

	//Form define the function for use in forms...
	public $form="";
	public $set_form="";
	//Required define if this field is required when insert or update a row of this model...
	public $required=0;
	//$quote_* is used if you need a more flexible sql sentence, WARNING, USE THIS FUNCTION IF YOU KNOW WHAT YOU ARE DOING
	public $quot_open='\'';
	public $quot_close='\'';
	//$std_error contain error in field if exists...
	public $std_error='';
	//$multilang is a boolean value that define if this text field is multilingual
	public $multilang=0;

	//Construct field with basic data...

	function __construct($size=20, $multilang=0)
	{

		$this->size=$size;
		$this->form='TextForm';
 		$this->set_form='list_value';
		$this->multilang=$multilang;

	}

	//This function is for check if the value for field is valid

	function check($value)
	{

		//Delete Javascript tags and simple quotes.
		$this->value=form_text($value);
		return form_text($value);

	}

	function show_formatted($value)
	{

		return $value;

	}

	//This functions is used in padmin if you used field in a model...

	function get_type_sql()
	{

		return 'VARCHAR('.$this->size.') NOT NULL';

	}

	function get_parameters_default()
	{

		return '';

	}


}
//Selections always integers
//PrimaryField is used for primary keys for models

class PrimaryField extends PhangoField {
	
	public $value=0;
	public $label="";
	public $required=0;
	public $form="HiddenForm";
	public $set_form="list_value";
	public $quot_open='\'';
	public $quot_close='\'';
	public $std_error='';

	function check($value)
	{

		$this->value=form_text($value);
		settype($value, "integer");
		return $value;

	}

	function get_type_sql()
	{

		return 'INT PRIMARY KEY AUTO_INCREMENT';

	}

	function show_formatted($value)
	{

		return $value;

	}

	function get_parameters_default()
	{

		return '';

	}

}

//Integerfield is a field for integers values.

class IntegerField extends PhangoField {

	public $size=11;
	public $value=0;
	public $label="";
	public $required=0;
	public $form="";
	public $set_form='';
	public $quot_open='\'';
	public $quot_close='\'';
	public $std_error='';
	public $only_positive=false;
	public $min_num=0;
	public $max_num=0;

	function __construct($size=11, $only_positive=false, $min_num=0, $max_num=0)
	{

		$this->size=$size;
		$this->form='TextForm';
		$this->set_form='list_value';
		$this->only_positive=$only_positive;
		$this->min_num=$min_num;
		$this->max_num=$max_num;

	}

	function check($value)
	{

		$this->value=form_text($value);
		
		settype($value, "integer");
		
		if($this->only_positive==true && $value<0)
		{
		
			$value=0;
		
		}
		
		if($this->min_num<>0 && $value<$this->min_num)
		{
		
			$value=$this->min_num;
		
		}
		
		if($this->max_num<>0 && $value>$this->max_num)
		{
		
			$value=$this->max_num;
		
		}
		
		return $value;

	}

	function get_type_sql()
	{

		return 'INT('.$this->size.') NOT NULL';

	}

	function show_formatted($value)
	{

		return $value;

	}

	function get_parameters_default()
	{

		return 0;

	}

}

//Booleanfield is a field for boolean values.

class BooleanField extends PhangoField {

	public $size=1;
	public $value=0;
	public $label="";
	public $required=0;
	public $form="";
	public $set_form='';
	public $quot_open='\'';
	public $quot_close='\'';
	public $std_error='';

	function __construct()
	{

		$this->size=1;
		$this->form='SelectForm';
		$this->set_form='list_value';

	}

	function check($value)
	{

		//$this->value=form_text($value);
		settype($value, "integer");

		if($value!=0 && $value!=1)
		{

			$value=0;

		}

		return $value;

	}

	function get_type_sql()
	{

		//Int for simple compatibility with sql dbs.
	
		return 'INT('.$this->size.') NOT NULL';

	}

	function show_formatted($value)
	{

		global $lang;

		switch($value)
		{
			default:

				return $lang['common']['no'];

			break;

			case 1:

				return $lang['common']['yes'];

			break;

	
		}

	}

	function get_parameters_default()
	{

		global $lang;

		return array(0, $lang['common']['no'], 0, $lang['common']['yes'], 1);

	}

}

//Doublefield is a field for doubles values.

class DoubleField extends PhangoField {

	public $size=11;
	public $value=0;
	public $label="";
	public $required=0;
	public $form="";
	public $set_form='';
	public $quot_open='\'';
	public $quot_close='\'';
	public $std_error='';

	function __construct($size=11)
	{

		$this->size=$size;
		$this->form='TextForm';
		$this->set_form='list_value';

	}

	function check($value)
	{

		$this->value=form_text($value);
		settype($value, "double");
		return $value;

	}

	function get_type_sql()
	{

		return 'DOUBLE NOT NULL';

	}

	function show_formatted($value)
	{

		return $value;

	}

	function get_parameters_default()
	{

		return '0';

	}


}

class ChoiceField extends PhangoField {

	public $size=11;
	public $value=0;
	public $label="";
	public $required=0;
	public $form="";
	public $set_form='';
	public $quot_open='\'';
	public $quot_close='\'';
	public $std_error='';
	public $type='integer';
	public $arr_values=array();
	public $arr_formatted=array();
	public $default_value='';

	

	function __construct($size=11, $type='integer', $arr_values=array(), $default_value='')
	{

		$this->size=$size;
		$this->form='SelectForm';
		$this->type=$type;
		$this->arr_values=$arr_values;
		$this->default_value=$default_value;
		
		foreach($arr_values as $value)
		{
			
			$this->arr_formatted[$value]=$value;
		
		}
	
	}

	function check($value)
	{
		
		switch($this->type)
		{
		
			case 'integer':

				settype($value, "integer");

			break;

			case 'string':

				$value=form_text($value);

			break;

		}
		
		if(in_array($value, $this->arr_values))
		{	
			
			return $value;

		}
		else
		{

			return $this->default_value;

		}

	}

	function get_type_sql()
	{

		switch($this->type)
		{
		
			case 'integer':

			return 'INT('.$this->size.') NOT NULL';
			
			break;

			case 'string':

			return 'VARCHAR('.$this->size.') NOT NULL';

			break;

		 }	

	}

	function show_formatted($value)
	{
		
		return $this->arr_formatted[$value];

	}

	function get_parameters_default()
	{	

		if(count($this->arr_values)>0)
		{
			$arr_return=array($this->default_value);

			foreach($this->arr_values as $value)
			{

				$arr_return[]=$this->arr_formatted[$value];
				$arr_return[]=$value;

			}

			return $arr_return;

		}
		else
		{

			return array(0, 'Option 1', 0, 'Option 2', 1);

		}

	}

}

//Textfield is a field for long text values.

class TextField extends PhangoField {

	public $value="";
	public $label="";
	public $required=0;
	public $form="TextAreaForm";
	public $set_form='list_value';
	public $quot_open='\'';
	public $quot_close='\'';
	public $std_error='';
	public $multilang=0;
	public $br=1;

	function __construct($multilang=0)
	{

		$this->form='TextAreaForm';
		$this->multilang=$multilang;

	}

	function check($value)
	{
		
		//Delete Javascript tags and simple quotes.
		$this->value=$value;
		return form_text($value, $this->br);

	}

	//Function check_form

	function get_type_sql()
	{

		return 'TEXT NOT NULL';
		

	}

	function show_formatted($value)
	{

		return $value;

	}

	function get_parameters_default()
	{

		return ;

	}
	
}

//TextHTMLfield is a field for long text values based in html.

class TextHTMLField extends PhangoField {

	public $value="";
	public $label="";
	public $required=0;
	public $form="TextAreaForm";
	public $set_form='list_value';
	public $quot_open='\'';
	public $quot_close='\'';
	public $std_error='';
	public $multilang=0;

	//This variable is used for write rules what accept html tags

	public $allowedtags=array();

	function __construct($multilang=0)
	{

		$this->form='TextAreaForm';
		$this->multilang=$multilang;

	}

	function check($value)
	{
		global $config_data;
		
		//Delete Javascript tags and simple quotes.
		
		$txt_without_tags=str_replace('&nbsp;', '', strip_tags($value) );
		
		$txt_without_tags=trim(str_replace(' ', '', $txt_without_tags));
		
		if($txt_without_tags=='')
		{
		
			return '';
		
		}

		if($config_data['textbb_type']=='')
		{
			
			$this->value=unform_text($value);

		}
		else
		{
			
			$this->value=$value;

		}
		
		return form_text_html($value, $this->allowedtags);

	}

	//Methot for show the allowed html tags to the user

	function show_allowedtags()
	{

		$arr_example_tags=array();

		foreach($this->allowedtags as $tag => $arr_tag)
		{

			$arr_example_tags[]=htmlentities($arr_tag['example']);

		}
		
		return implode(', ', $arr_example_tags);

	}

	function get_type_sql()
	{

		return 'TEXT NOT NULL';
		

	}

	function show_formatted($value)
	{

		return $value;

	}

	function get_parameters_default()
	{

		return ;

	}

	function set_safe_html_tags()
	{

		global $base_url;

		$this->allowedtags['a']=array('pattern' => '/&lt;a.*?href=&quot;(http:\/\/.*?)&quot;.*?&gt;(.*?)&lt;\/a&gt;/', 'replace' => '<a_tmp href="$1">$2</a_tmp>', 'example' => '<a href=""></a>');
		$this->allowedtags['p']=array('pattern' => '/&lt;p.*?&gt;(.*?)&lt;\/p&gt;/s', 'replace' => '<p_tmp>$1</p_tmp>','example' => '<p></p>');
		$this->allowedtags['br']=array('pattern' => '/&lt;br.*?\/&gt;/', 'replace' => '<br_tmp />', 'example' => '<br />');
		$this->allowedtags['strong']=array('pattern' => '/&lt;strong.*?&gt;(.*?)&lt;\/strong&gt;/s', 'replace' => '<strong_tmp>$1</strong_tmp>', 'example' => '<strong></strong>');
		$this->allowedtags['em']=array('pattern' => '/&lt;em.*?&gt;(.*?)&lt;\/em&gt;/s', 'replace' => '<em_tmp>$1</em_tmp>', 'example' => '<em></em>');
		$this->allowedtags['i']=array('pattern' => '/&lt;i.*?&gt;(.*?)&lt;\/i&gt;/s', 'replace' => '<i_tmp>$1</i_tmp>', 'example' => '<i></i>');
		$this->allowedtags['u']=array('pattern' => '/&lt;u.*?&gt;(.*?)&lt;\/u&gt;/s', 'replace' => '<u_tmp>$1</u_tmp>', 'example' => '<u></u>');
		$this->allowedtags['blockquote']=array('pattern' => '/&lt;blockquote.*?&gt;(.*?)&lt;\/blockquote&gt;/s', 'replace' => '<blockquote_tmp>$1</blockquote_tmp>', 'example' => '<blockquote></blockquote>', 'recursive' => 1);
		$this->allowedtags['img']=array('pattern' => '/&lt;img.*?alt=&quot;([aA-zZ]+)&quot;.*?src=&quot;('.str_replace('/', '\/', $base_url).'\/media\/smileys\/[^\r\n\t<"].*?)&quot;.*?\/&gt;/', 'replace' => '<img_tmp alt="$1" src="$2"/>', 'example' => '<img alt="emoticon" src="" />');	

	}
	
}

//Serializefield is a field if you need save serialize values

class SerializeField extends PhangoField {

	public $value="";
	public $label="";
	public $required=0;
	public $form="TextForm";
	public $set_form='list_serialize';
	public $quot_open='\'';
	public $quot_close='\'';
	public $std_error='';
	public $related_type='';
	
	//type_data can be any field type that is loaded IntegerField, etc..
	
	function __construct($related_type='TextField')
	{
	
		$this->related_type=$related_type;
		
	}
	
	public $type_data='';

	//This method is used for check all members from serialize

	function recursive_form($value)
	{

		if(gettype($value)=="array")
		{

			foreach($value as $key => $value_key)
			{

				if(gettype($value_key)=="array")
				{

					$value[$key]=$this->recursive_form($value_key);

				}
				else
				{

					//Create new type.
					$type_field=new $this->related_type();
				
					$value[$key]=$type_field->check($value_key);

				}

			}

		}

		return $value;

	}

	function check($value)
	{
		
		
		$value=$this->recursive_form($value);

		$this->value=$value;
		
		return webtsys_escape_string(serialize($value));

	}

	function get_type_sql()
	{

		return 'TEXT NOT NULL';
		

	}

	function show_formatted($value)
	{

		$real_value=unserialize($value);
		
		return implode(', ', $return_value);

	}

	function get_parameters_default()
	{

		return ;

	}
	
}

//Datefield is a field for save dates in timestamp, this value is a timestamp and you need use form_date or form_time for format DateField

class DateField extends PhangoField {

	public $size=11;	
	public $value="";	
	public $required=0;
	public $form="";
	public $label="";
	public $set_form='';
	public $quot_open='\'';
	public $quot_close='\'';
	public $set_default_time=0;
	public $std_error='';

	function __construct($size=11)
	{

		$this->size=$size;
		$this->form='DateForm';
		$this->set_form='list_date';

	}

	//The check have 3 parts, in a part you have a default time, other part if you have an array from a form, last part if you send a timestamp directly.
	
	function check($value)
	{

		global $user_data;

		$final_value=0;

		if($this->set_default_time==0)
		{

			$final_value=time();
		
		}
		
		if(gettype($value)=='array')
		{
			
			settype($value[0], 'integer');
			settype($value[1], 'integer');
			settype($value[2], 'integer');
			settype($value[3], 'integer');
			settype($value[4], 'integer');
			settype($value[5], 'integer');
		
			if($value[0]>0 && $value[1]>0 && $value[2]>0)	
			{

				$substr_time=$user_data['format_time']/3600;
	
				$value[3]-=$substr_time;

				$final_value=gmmktime ($value[3], $value[4], $value[5], $value[1], $value[0], $value[2] );
	
			}

		}
		else if(strpos($value, '-')!==false)
		{
		
			$arr_time=explode('-',trim($value));
			
			settype($arr_time[0], 'integer');
			settype($arr_time[1], 'integer');
			settype($arr_time[2], 'integer');
			
			$final_value=gmmktime (0, 0, 0, $arr_time[1], $arr_time[0], $arr_time[2] );
			
			if($final_value===false)
			{
			
				$final_value=gmmktime (0, 0, 0, $arr_time[1], $arr_time[2], $arr_time[0] );
			
			}
		
		}
		else
		if(gettype($value)=='string' || gettype($value)=='integer')
		{
			
			settype($value, 'integer');
			$final_value=$value;

		}
		
		$this->value=form_text($final_value);

		return $final_value;

	}

	function get_type_sql()
	{

		return 'INT('.$this->size.') NOT NULL';
		

	}

	function show_formatted($value)
	{

		global $user_data;

		load_libraries(array('form_date'));
		
		return form_date( $value, $user_data['format_date'] , $user_data['format_time']);

	}

	function get_parameters_default()
	{

		return time();

	}
	
}

class FileField extends PhangoField {

	public $value="";
	public $label="";
	public $required=0;
	public $form="FileForm";
	public $name_file="";
	public $path="";
	public $url_path="";
	//public $type='';
	public $quot_open='\'';
	public $quot_close='\'';
	public $std_error='';

	function __construct($name_file, $path, $url_path)
	{

		$this->name_file=$name_file;
		$this->path=$path;
		$this->url_path=$url_path;
		//$this->type=$type;

	}

	//Check if the file is correct..
	
	function check($file)
	{	
		
		global $lang;
		
		$file_field=$this->name_file;

		settype($_POST['delete_'.$file_field], 'integer');
		
		if($_POST['delete_'.$file_field]==1)
		{

			$file_delete=form_text($_POST[$file_field]);

			if($file_delete!='')
			{

				@unlink($this->path.'/'.$file_delete);

				$file='';

			}

		}
		
		if(isset($_FILES[$file_field]['tmp_name']))
		{
				
			if($_FILES[$file_field]['tmp_name']!='')
			{
	
				if( move_uploaded_file ( $_FILES[$file_field]['tmp_name'] , $this->path.'/'.$_FILES[$file_field]['name'] ) )
				{

					return $_FILES[$file_field]['name'];

					//return $this->path.'/'.$_FILES[$file]['name'];
					
				}
				else
				{

					$this->std_error=$lang['common']['error_cannot_upload_this_file_to_the_server'];

					return '';

				}
					

			}
			else if($file!='')
			{

				return $file;

			}

		}

		$this->value='';
		
		return '';


	}


	function get_type_sql()
	{

		return 'VARCHAR(255) NOT NULL';

	}

	function show_formatted($value)
	{

		return $value;

	}
	
	function show_file_url($value)
	{

		return $this->url_path.'/'.$value;

	}

	function get_parameters_default()
	{

		return ;

	}
	
	function process_delete_field($model, $name_field, $conditions)
	{
	
		$query=$model->select($conditions, array($name_field));
		
		while(list($file_name)=webtsys_fetch_row($query))
		{
		
			if(!unlink($this->path.'/'.$file_name))
			{
			
				$this->std_error=$lang['common']['cannot_delete_file'];
			
			}
		
		}
	
	}

}

//Imagefield is a field for upload images
//This field don't have for now a maximum width and height. To fix in next releases.

class ImageField extends PhangoField {

	public $value="";
	public $label="";
	public $required=0;
	public $form="ImageForm";
	public $name_file="";
	public $path="";
	public $url_path="";
	public $type='';
	public $thumb=0;
	public $img_width=100;
	public $quot_open='\'';
	public $quot_close='\'';
	public $std_error='';
	public $quality_jpeg=75;
	public $min_size=array(0, 0);

	function __construct($name_file, $path, $url_path, $type, $thumb=0, $img_width=array('mini' => 150), $quality_jpeg=85)
	{

		$this->name_file=$name_file;
		$this->path=$path;
		$this->url_path=$url_path;
		$this->type=$type;
		$this->thumb=$thumb;
		$this->img_width=$img_width;
		$this->quality_jpeg=$quality_jpeg;

	}

	//Check if the image is correct..
	
	function check($image)
	{	
		//Only accept jpeg, gif y png
		
		global $lang;
		
		$file=$this->name_file;
		$image=basename($image);

		settype($_POST['delete_'.$file], 'integer');

		if($_POST['delete_'.$file]==1)
		{

			//Delete old_image

			$image_file=form_text($_POST[$file]);

			if($image_file!='')
			{

				@unlink($this->path.'/'.$image_file);
				
				foreach($this->img_width as $key => $value)
				{

					@unlink($this->path.'/'.$key.'_'.$image_file);
				
				}

				$image='';

			}

		}
		
		if(isset($_FILES[$file]['tmp_name']))
		{
				
			if($_FILES[$file]['tmp_name']!='')
			{
				$arr_image=getimagesize($_FILES[$file]['tmp_name']);
				
				$_FILES[$file]['name']=form_text($_FILES[$file]['name']);
				$this->value=$_FILES[$file]['name'];
				
				//Check size
				
				if($this->min_size[0]>0 && $this->min_size[1]>0)
				{
				
					if($arr_image[0]<$this->min_size[0] || $arr_image[1]<$this->min_size[1])
					{
					
						$this->std_error=$lang['common']['image_size_is_not_correct'].'<br />'.$lang['common']['min_size'].': '.$this->min_size[0].'x'.$this->min_size[1];
						
						$this->value='';
						return '';
						
					
					}
				
				}
				
				/*//Check if exists a image with same name.
				
				if(file_exists($this->path.'/'.$_FILES[$file]['name']))
				{
				
					$this->std_error=$lang['common']['a_image_with_same_name_exists'];
					
					return $image;
				
				}*/
				
				//Delete other image if exists..
				
				if($image!='')
				{
				
					unlink($this->path.'/'.$image);
				
				}
				
				//gif 1
				//jpg 2
				//png 3
				//Only gifs y pngs...
				
				//Need checking gd support...
				
				$func_image[1]='imagecreatefromgif';
				$func_image[2]='imagecreatefromjpeg';
				$func_image[3]='imagecreatefrompng';
				
				if($arr_image[2]==1 || $arr_image[2]==2 || $arr_image[2]==3)
				{
				
					$image_func_create='imagejpeg';

					switch($arr_image[2])
					{

						case 1:

							//$_FILES[$file]['name']=str_replace('.gif', '.jpg', $_FILES[$file]['name']);
							$image_func_create='imagegif';

						break;

						case 3:

							//$_FILES[$file]['name']=str_replace('.png', '.jpg', $_FILES[$file]['name']);
							$image_func_create='imagepng';
							//Make conversion to png scale
							$this->quality_jpeg=floor($this->quality_jpeg/10);
							
							if($this->quality_jpeg>9)
							{
							
								$this->quality_jpeg=9;
							
							}

						break;

					}

					
					if( move_uploaded_file ( $_FILES[$file]['tmp_name'] , $this->path.'/'.$_FILES[$file]['name'] ))
					{
						
						//Make jpeg.

						$func_final=$func_image[$arr_image[2]];

						$img = $func_final($this->path.'/'.$_FILES[$file]['name']);
						
						//imagejpeg ( $img, $this->path.'/'.$_FILES[$file]['name'], $this->quality_jpeg );
						
						/*$mini_photo=$_FILES[$file]['name'];
				
						$mini_photo=str_replace('.gif', '.jpg', $mini_photo);
						$mini_photo=str_replace('.png', '.jpg', $mini_photo);*/
						
						//Reduce size for default if $this->img_width['']
						
						if(isset($this->img_width['']))
						{
							if($arr_image[0]>$this->img_width[''])
							{
								$width=$this->img_width[''];
							
								$ratio = ($arr_image[0] / $width);
								$height = round($arr_image[1] / $ratio);
							
								$thumb = imagecreatetruecolor($width, $height);
								
								imagecopyresampled ($thumb, $img, 0, 0, 0, 0, $width, $height, $arr_image[0], $arr_image[1]);
								
								$image_func_create ( $thumb, $this->path.'/'.$_FILES[$file]['name'], $this->quality_jpeg );
								
							}
							
							unset($this->img_width['']);
						}

						//Make thumb if specific...
						if($this->thumb==1)
						{
							
							//Convert to jpeg.
							
							foreach($this->img_width as $name_width => $width)
							{
							
								$ratio = ($arr_image[0] / $width);
								$height = round($arr_image[1] / $ratio);
							
								$thumb = imagecreatetruecolor($width, $height);
								
								imagecopyresampled ($thumb, $img, 0, 0, 0, 0, $width, $height, $arr_image[0], $arr_image[1]);
								
								$image_func_create ( $thumb, $this->path.'/'.$name_width.'_'.$_FILES[$file]['name'], $this->quality_jpeg );
								;
								//imagepng ( resource $image [, string $filename [, int $quality [, int $filters ]]] )

							}

						}
						
						//unlink($_FILES[$file]['tmp_name']);
						
						return $_FILES[$file]['name'];

						//return $this->path.'/'.$_FILES[$file]['name'];
						
					}
					else
					{

						$this->std_error=$lang['common']['error_cannot_upload_this_image_to_the_server'];

						return '';

					}
					

				}
				else
				{

					$this->std_error.=$lang['error_model']['img_format_error'];

				}

			}
			else if($image!='')
			{

				return $image;

			}


		}
		else if($image!=='')
		{
			
			
			if(file_exists($this->path.'/'.$image))
			{
				$this->value=$this->path.'/'.$image;
				return $image;

			}

		}

		$this->value='';
		return '';


	}


	function get_type_sql()
	{

		return 'VARCHAR(255) NOT NULL';

	}

	function show_formatted($value)
	{

		return $value;

	}
	
	function show_image_url($value)
	{

		return $this->url_path.'/'.$value;

	}

	function get_parameters_default()
	{

		return ;

	}
	
	function process_delete_field($model, $name_field, $conditions)
	{
	
		global $lang;
	
		//die;
		$query=$model->select($conditions, array($name_field));
		
		while(list($image_name)=webtsys_fetch_row($query))
		{
		
			if(unlink($this->path.'/'.$image_name))
			{
			
				//Unlink mini_images
				
				unset($this->img_width['']);
				
				foreach($this->img_width as $key => $value)
				{
				
					if(!unlink($this->path.'/'.$key.'_'.$image_name))
					{
						
						$this->std_error.=$lang['common']['cannot_delete_image'].': '.$key.'_'.$image_name;
					
					}
				
				}
			
				$this->std_error.=$lang['common']['cannot_delete_image'].': '.$image_name;
			
			}
			else
			{
			
				$this->std_error.=$lang['common']['cannot_delete_image'].': '.$key.'_'.$image_name;
			
			}
		
		}
	
	}

}

//Keyfield is a indexed field in a sql statement...

class KeyField extends PhangoField {

	public $size=11;
	public $value=0;
	public $label="";
	public $required=0;
	public $form="";
	public $set_form='';
	public $quot_open='\'';
	public $quot_close='\'';
	public $fields=array();
	public $table='';
	public $model='';
	public $ident='';
	public $std_error='';

	function __construct($size=11)
	{

		$this->size=$size;
		$this->form='TextForm';
		$this->set_form='list_value';

	}

	function check($value)
	{

		$this->value=form_text($value);

		settype($value, "integer");
		return $value;

	}

	function get_type_sql()
	{

		return 'INT('.$this->size.') NOT NULL';

	}

	function show_formatted($value)
	{

		return $value;

	}

	function get_parameters_default()
	{

		return ;

	}

}

//ForeignKeyfield is a relantioship between two models...

class ForeignKeyField extends IntegerField{

	//field related in the model...
	public $related_model='';
	public $container_model='';
	public $null_relation=1;
	public $params_loading_mod=array();

	function __construct($related_model, $size=11, $null_relation=1)
	{

		$this->size=$size;
		$this->form='SelectForm';
		$this->related_model=$related_model;
		$this->container_model=$this->related_model;
		//Fields obtained from related_model if you make a query...
		$this->fields_related_model=array();
		//Representative field for related model...
		$this->name_field_to_field='';
		$this->null_relation=$null_relation;
		

	}

	function check($value)
	{
		
		global $model;
		
		settype($value, "integer");

		//Reload related model if not exists, if exists, only check cache models...

		if(!isset($model[$this->related_model]))
		{

			load_model($this->container_model);

		}

		//Need checking if the value exists with a select_count
		
		$num_rows=$model[$this->related_model]->select_count('where '.$this->related_model.'.'.$model[$this->related_model]->idmodel.'='.$value, $model[$this->related_model]->idmodel);
		
		if($num_rows>0)
		{
		
			if($value==0)
			{
			
				return NULL;
			
			}

			return $value;

		}
		else
		{
			
			return NULL;

		}
		

	}
	
	function get_type_sql()
	{
	
		$arr_null[0]='NOT NULL';
		$arr_null[1]='NULL';

		return 'INT('.$this->size.') '.$arr_null[$this->null_relation];

	}

	function show_formatted($value)
	{
	
		global $model;
		
		return $model[$this->related_model]->components[$this->name_field_to_field]->show_formatted($value);

		//return $value;

	}

	function get_parameters_default()
	{
		global $lang;
		
		load_libraries(array('forms/selectmodelform'));
		
		//SelectModelForm($name, $class, $value, $model_name, $identifier_field, $where='')
		
		//Prepare parameters for selectmodelform
		
		/*if(isset($this->name_component) && $this->name_field_to_field!='')
		{
		
			$this->parameters=array($this->name_component, '', '', $this->related_model, $this->name_field_to_field, '');

			return '';
			
		}
		else
		{*/
		
		return array('', $lang['common']['any_option_chosen'], '');
			
		//}

	}
	
	function get_all_fields()
	{
	
		global $model;
		
		return array_keys($model[$this->related_model]->components);
	
	}

}

class ParentField extends IntegerField{

	//field related in the model...
	public $parent_model='';

	function __construct($parent_model, $size=11)
	{

		$this->parent_model=$parent_model;
		$this->size=$size;
		$this->form='SelectForm';

	}

	function check($value)
	{
		
		global $model;
		
		settype($value, "integer");

		//Check model
		
		$num_rows=$model[$this->parent_model]->select_count('where '.$model[$this->parent_model]->idmodel.'='.$value, $model[$this->parent_model]->idmodel);
		
		if($num_rows>0)
		{

			return $value;

		}
		else
		{
			
			return 0;

		}
		

	}

	function show_formatted($value)
	{

		return $value;

	}

	function get_parameters_default()
	{
		global $lang;

		return array('', $lang['common']['any_option_chosen'], '');

	}

}

//Emailfield is a field that only accepts emails

class EmailField extends PhangoField {

	public $size=200;
	public $value="";
	public $label="";
	public $form="TextForm";
	public $set_form="";
	public $class="";
	public $required=0;
	public $quot_open='\'';
	public $quot_close='\'';
	public $std_error='';

	function __construct($size=200)
	{

		$this->size=$size;
 		$this->set_form='list_value';

	}

	//Method for accept valid emails only
	
	function check($value)
	{
		
		//Delete Javascript tags and simple quotes.

		global $lang;

		$value=form_text($value);

		$this->value=$value;

		$email_expression='([\w\!\#$\%\&\'\*\+\-\/\=\?\^\`{\|\}\~]+\.)*(?:[\w\!\#$\%\'\*\+\-\/\=\?\^\`{\|\}\~]|&amp;)+@((((([a-z0-9]{1}[a-z0-9\-]{0,62}[a-z0-9]{1})|[a-z])\.)+[a-z]{2,6})|(\d{1,3}\.){3}\d{1,3}(\:\d{1,5})?)';
		
		if(preg_match('/^'.$email_expression.'$/i', $value))
		{
			
			return $value;

		}
		else
		{
			
			$this->std_error.=$lang['error_model']['email_format_error'].' ';
			
			return '';

		}
		

	}

	function get_type_sql()
	{

		return 'VARCHAR('.$this->size.') NOT NULL';

	}

	function show_formatted($value)
	{

		return $value;

	}

	function get_parameters_default()
	{

		return '';

	}


}

//in older versions of php, get_magic_quotes_gpc function was to add quotes automatically for certain operations, make_slashes is used to prevent this.

if(function_exists('get_magic_quotes_gpc'))
{

	if ( !get_magic_quotes_gpc() )
	{
		function make_slashes( $string )
		{
			return addslashes( $string );
		} 

		function unmake_slashes( $string )
		{
			return stripslashes( $string );
		}

	} 
	else
	{
		function make_slashes( $string )
		{
			return $string;
		}

		function make_slashes( $string )
		{
			return $string;
		} 
	
	}

} 

//this function is used to clean up the text of undesirable elements

function form_text( $text ,$br=1)
{

    global $yes_entities;
	
    settype( $text, "string" );

    $text = trim( $text );

    $arr_tags=array('/</', '/>/', '/"/', '/\'/', "/  /");
    $arr_entities=array('&lt;', '&gt;', '&quot;', '&#39;', '&nbsp;');
	
    if($br==1)
    {

	$text = preg_replace($arr_tags, $arr_entities, $text);
	
	$arr_text = explode("\n\r\n", $text);

	$c=count($arr_text);

	if($c>1)
	{
		for($x=0;$x<$c;$x++)
		{

			$arr_text[$x]='<p>'.trim($arr_text[$x]).'&nbsp;</p>';

		}
	}


	$text=implode('', $arr_text);

	$arr_text = explode("\n", $text);

	$c=count($arr_text);

	if($c>1)
	{
		for($x=0;$x<$c;$x++)
		{

			$arr_text[$x]=trim($arr_text[$x]).'<br />';

		}
	}

	$text=implode('', $arr_text);
	
    }
    
	
    $text = make_slashes( $text );
    
    return $text;
}

//this function is used to clean up the text of undesirable html tags

function form_text_html( $text , $allowedtags=array())
{

	global $yes_entities, $config_data;

	settype( $text, "string" );
	
	//If no html editor \r\n=<p>

	/*$text=preg_replace("/<br.*?>/", "\n", $text);*/
	
	if($config_data['textbb_type']!='')
	{
		
		$text=str_replace("\r", '', $text);
		$text=str_replace("\n", '', $text);

	}
	else
	{

		//Make <p>

		$arr_text = explode("\n\r\n", $text);

		$c=count($arr_text);

		if($c>1)
		{
			for($x=0;$x<$c;$x++)
			{

				$arr_text[$x]='<p>'.trim($arr_text[$x]).'&nbsp;</p>';

			}
		}


		$text=implode('', $arr_text);

		$arr_text = explode("\n", $text);

		$c=count($arr_text);

		if($c>1)
		{
			for($x=0;$x<$c;$x++)
			{

				$arr_text[$x]=trim($arr_text[$x]).'<br />';

			}
		}

		$text=implode('', $arr_text);

	}
	/*echo htmlentities($text);
	die;*/
		
	//Check tags

	//Bug : tags deleted ocuppied space.

	//First strip_tags

	$text = trim( $text );

	//Trim html

	$text=str_replace('&nbsp;', ' ', $text);

	while(preg_match('/<p>\s+<\/p>$/s', $text))
	{

		$text=preg_replace('/<p>\s+<\/p>$/s', '', $text);
	
	}

	//Now clean undesirable html tags
	
	if(count($allowedtags)>0)
	{

		$text=strip_tags($text, '<'.implode('><', array_keys($allowedtags)).'>' );
		
		$arr_tags=array('/</', '/>/', '/"/', '/\'/', "/  /");
		$arr_entities=array('&lt;', '&gt;', '&quot;', '&#39;', '&nbsp;');
		
		$text=preg_replace($arr_tags, $arr_entities, $text);
		
		$text=str_replace('  ', '&nbsp;&nbsp;', $text);
		
		$arr_tags_clean=array();
		$arr_tags_empty_clean=array();

		//Close tags. 

		//Filter tags

		$final_allowedtags=array();
		
		foreach($allowedtags as $tag => $parameters)
		{
			//If mark how recursive, make a loop

			settype($parameters['recursive'], 'integer');

			$c_count=0;
			$x=0;

			if($parameters['recursive']==1)
			{

				$c_count = substr_count( $text, '&lt;'.$tag.'&gt;');

			}
			
			for($x=0;$x<=$c_count;$x++)
			{

				$text=preg_replace($parameters['pattern'], $parameters['replace'], $text);
				
			}
			
			$pos_=strpos($tag, '_');
			
			if($pos_!==false)
			{

				$tag=substr($tag, 0, $pos_);

			}
			
			$final_allowedtags[]=$tag.'_tmp';

			//Destroy open tags.
			
			$arr_tags_clean[]='/&lt;(.*?)'.$tag.'(.*?)&gt;/';
			
			$arr_tags_empty_clean[]='';
			$arr_tags_empty_clean[]='';

		}
		
		$text=preg_replace($arr_tags_clean, $arr_tags_empty_clean, $text);
	}

	//With clean code, modify <tag_tmp
	
	$text=str_replace('_tmp', '', $text);
	
	//Close tags
	
	$text = unmake_slashes( $text );
	
	return $text;

}

//Functión for clean newlines

function unform_text( $text )
{

	$text = preg_replace( "/<p>(.*?)<\/p>/s", "$1\n\r\n", $text );
	$text = str_replace( "<br />", "", $text );

	return $text;

}

function replace_quote_text( $text )
{

	$text = str_replace( "\"", "&quot;", $text );

	return $text;

}


/***********************************************************

Functions used for generate forms from models 
This functions are called via $model->form

************************************************************/

//Create a input text

function TextForm($name="", $class='', $value='')
{

	return '<input type="text" name="'.$name.'" id="'.$name.'_field_form" class="'.$class.'" value="'.$value.'" />';

}

//Prepare a value for input text

function TextFormSet($post, $value)
{

	$value = replace_quote_text( $value );
	return $value;

}

//Create a input password

function PasswordForm($name="", $class='', $value='')
{

	$value = replace_quote_text( $value );

	return '<input type="password" name="'.$name.'" class="'.$class.'" id="'.$name.'_field_form" value="'.$value.'" />';

}

//Prepare a value for input password

function PasswordFormSet($post, $value)
{

	$value = ''; //replace_quote_text( $value );

	return $value;

}

//Create a input file

function FileForm($name="", $class='', $value='', $delete_inline=0, $path_file='')
{
	
	global $base_url, $base_path, $lang;

	$file_url=$path_file.'/'.$value;
	
	$file_exist='';

	if($value!='')
	{

		$file_exist='<a href="'.$file_url.'">'.basename($value).'</a> ';
		
		if($delete_inline==1)
		{

			$file_exist.=$lang['common']['delete_file'].' <input type="checkbox" name="delete_'.$name.'" class="'.$class.'" value="1" />';

		}

	}

	return '<input type="hidden" name="'.$name.'" value="'.$value.'"/><input type="file" name="'.$name.'" class="'.$class.'" value="" /> '.$file_exist;

}

//Prepare a value for input password

function FileFormSet($post, $value)
{
	
	$value = replace_quote_text( $value );

	return $value;

}


//Create a special form for a image

function ImageForm($name="", $class='', $value='', $delete_inline=0, $path_image='')
{
	
	global $base_url, $base_path, $lang;

	$image_url=$path_image.'/'.$value;
	
	$image_exist='';

	if($value!='')
	{

		$image_exist='<a href="'.$image_url.'">'.basename($value).'</a> ';
		
		if($delete_inline==1)
		{

			$image_exist.=$lang['common']['delete_image'].' <input type="checkbox" name="delete_'.$name.'" class="'.$class.'" value="1" />';

		}

	}

	return '<input type="hidden" name="'.$name.'" value="'.$value.'"/><input type="file" name="'.$name.'" class="'.$class.'" value="" /> '.$image_exist;

}

//Prepare a value for input password

function ImageFormSet($post, $value)
{
	
	$value = replace_quote_text( $value );

	return $value;

}

//Create a textarea 

function TextAreaForm($name="", $class='', $value='')
{

	return '<textarea name="'.$name.'" class="'.$class.'" id="'.$name.'_field_form">'.$value.'</textarea>';

}

//Prepare the value for the textarea

function TextAreaFormSet($post, $value)
{

	$value = replace_quote_text( $value );

	return $value;

}

//Create a input hidden

function HiddenForm($name="", $class='', $value='')
{

	return '<input type="hidden" name="'.$name.'" value="'.$value.'" id="'.$name.'_field_form"/>';

}

//Prepare the value for a input hidden

function HiddenFormSet($post, $value)
{

	$value = replace_quote_text( $value );

	return $value;

}

//Create a input checkbox

function CheckBoxForm($name="", $class='', $value='')
{
	
	$arr_checked[$value]='';

	$arr_checked[0]='';
	$arr_checked[1]='checked';
	
	return '<input type="checkbox" name="'.$name.'" value="1" id="'.$name.'_field_form" class="'.$class.'" '.$arr_checked[$value].'/>';

}

//Prepare the value for the checkbox

function CheckBoxFormSet($post, $value)
{

	settype($value, 'integer');
	
	return $value;

}

//Create a select

function SelectForm($name="", $class='', $value='', $more_options='')
{
	
	$select='<select name="'.$name.'" id="'.$name.'_field_form" class="'.$class.'" '.$more_options.'>'."\n";

	list($key, $default)= each($value);

	$arr_selected=array();

	$arr_selected[$default]="selected=\"selected\"";

	//Check if array is safe. 
	
	$z=count($value);
	
	for($x=1;$x<$z;$x+=2)
	{
		
		$val=$value[$x+1];
		
		settype($val, "string");
		settype($arr_selected[$val], "string");

		if($val=='optgroup')
		{
    			$select.='<optgroup label="'.$value[$x].'">';
		}
		else 
		if($val=="end_optgroup")
		{

			$select.='</optgroup>';

		}
		else
		{

			$select.= '<option value="'.$val.'" '.$arr_selected[$val].'>'.$value[$x].'</option>'."\n";

		}
	}

	$select.='</select>'."\n";

	return $select;

}

//Prepare the value for the select

function SelectFormSet($post, $value)
{
	
	$value = preg_replace('/<(.*?)\/(.*?)option(.*?)>/', '', $value);

	$post[0]=$value;
	
	return $post;

}

//Crate a multiple select

function SelectManyForm($name="", $class='', $value='', $more_options='' )
{
	
	$select='<select name="'.$name.'[]" id="'.$name.'_field_form" class="'.$class.'" '.$more_options.' multiple>'."\n";

	list($key, $arr_values)= each($value);

	$arr_selected=array();
	
	foreach($arr_values as $default)
	{

		$arr_selected[$default]="selected";

	}

	//Check if array is safe. 
	
	$z=count($value);
	
	for($x=1;$x<$z;$x+=2)
	{
		
		$val=$value[$x+1];
		
		settype($val, "string");
		settype($arr_selected[$val], "string");

		if($val=='optgroup')
		{
    			$select.='<optgroup label="'.$value[$x].'">';
		}
		else 
		if($val=="end_optgroup")
		{

			$select.='</optgroup>';

		}
		else
		{

			$select.= '<option value="'.$val.'" '.$arr_selected[$val].'>'.$value[$x].'</option>'."\n";

		}
	}

	$select.='</select>'."\n";

	return $select;

}

//Prepare the value for the multiple select

function SelectManyFormSet($post, $value)
{

	$arr_value=unserialize($value);

	//$value = preg_replace('/<(.*?)\/(.*?)option(.*?)>/', '', $value);
	
	$post[0]=$arr_value;
	
	return $post;

}

//A special form for dates in format day/month/year

function DateForm($field, $class='', $value='', $set_time=1)
{

	global $lang, $user_data;

	if($value==0)
	{

		$day='';
		$month='';
		$year='';
		$hour='';
		$minute='';
		$second='';

	}
	else
	{
		
		$value+=$user_data['format_time'];
		
		$day=gmdate('j', $value);
		$month=gmdate('n', $value);
		$year=gmdate('Y', $value);
		$hour=gmdate('G', $value);
		$minute=gmdate('i', $value);
		$second=gmdate('s', $value);
	}
		

	$date='<span id="'.$field.'_field_form"><input type="text" name="'.$field.'[]" value="'.$day.'" size="2"/>'."\n";
	$date.='<input type="text" name="'.$field.'[]" value="'.$month.'" size="2"/>'."\n";
	$date.='<input type="text" name="'.$field.'[]" value="'.$year.'" size="4"/>'."\n&nbsp;&nbsp;&nbsp;</span>";
	
	if($set_time==1)
	{

		$date.=$lang['common']['hour'].' <input type="text" name="'.$field.'[]" value="'.$hour.'" size="2"/>'."\n";
		$date.=$lang['common']['minute'].' <input type="text" name="'.$field.'[]" value="'.$minute.'" size="2"/>'."\n";
		$date.=$lang['common']['second'].' <input type="text" name="'.$field.'[]" value="'.$second.'" size="2"/>'."\n";
		
	}

	return $date;

}

//Prepare value form dateform

function DateFormSet($post, $value)
{

	if(gettype($value)=='array')
	{
		foreach($value as $key => $val)
		{

			settype($value[$key], 'integer');

		}
		
		settype($value[3], 'integer');
		settype($value[4], 'integer');
		settype($value[5], 'integer');
		
		$final_value=gmmktime ($value[3], $value[4], $value[5], $value[1], $value[0], $value[2] );

	}
	else
	{

		settype($value, 'integer');

		$final_value=$value;

	}


	return $final_value;

}

//Function for make pretty urls...

//If active fancy urls...
	
//Url don't have final slash!!

function make_fancy_url($url, $controller, $func_controller, $description_text, $arr_data=array(), $respect_upper=0)
{
	global $arr_func_encode_url;

	$description_text=slugify($description_text, $respect_upper);

	$arr_get=array();

	foreach($arr_data as $key => $value)
	{

		$arr_get[]=$key.'/'.$value;//$arr_func_encode_url[DEBUG]($key).'/'.$value;

	}
	
	$get_final=implode('/', $arr_get);
	
	return $url.'/index.php/'.$controller.'/show/'.$func_controller.'/'.$description_text.'/'.$get_final;

}

function add_extra_fancy_url($url_fancy, $arr_data)
{

	global $arr_func_encode_url;

	$arr_get=array();

	foreach($arr_data as $key => $value)
	{

		$arr_get[]=$key.'/'.$value;//$arr_func_encode_url[DEBUG]($key).'/'.$value;

	}

	$get_final=implode('/', $arr_get);

	$sep='/';

	if(preg_match('/\/$/', $url_fancy))
	{

		$sep='';

	}

	return $url_fancy.$sep.$get_final;

}

function controller_fancy_url($func_name, $description_text, $arr_data=array(), $respect_upper=0)
{

	global $base_url;

	return make_fancy_url($base_url, PHANGO_SCRIPT_BASE_CONTROLLER, $func_name, $description_text, $arr_data, $respect_upper);

}

//Function for normalize texts...

function slugify($text, $respect_upper=0, $replace='-')
{

	$from='àáâãäåæçèéêëìíîïðòóôõöøùúûýþÿŕñÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÐÒÓÔÕÖØÙÚÛÝỲŸÞŔÑ/?¿"';
	$to=  'aaaaaaaceeeeiiiidoooooouuuybyrnAAAAAACEEEEIIIIDOOOOOOUUUYYYBRN----';

	$text=trim(str_replace(" ", $replace, $text));

	$text = utf8_decode($text);    
	$text = strtr($text, utf8_decode($from), $to);
	
	//Used for pass base64 via GET that use upper, for example.
	
	if($respect_upper==0)
	{
	
		$text = strtolower($text);
		
	}

	return utf8_encode($text); 

}

//Load_view is a very important function. Phango is an MVC framework and has separate code and html.

$cache_template=array();

function load_view($arr_template, $template, $module_theme='', $load_if_no_cache=0)
{

	//First see in controller/view/template, if not see in /views/template

	global $base_path, $base_url, $lang, $language, $index_set, $title_page, $cache_template, $config_data, $script_base_controller;
	
	$theme=$config_data['dir_theme'];
	
	$view='';
	
	if(!isset($cache_template[$template])) 
	{

		//First, load view from module...

		ob_start();
		
		//Load view from theme...
		
		if(!include($base_path.'views/'.$theme.'/'.strtolower($template).'.php')) 
		{

			$output_error_view=ob_get_contents();

			ob_clean();

			//No exists view in theme, load view respect to the $script_base_controller views
			
			if(!include($base_path.'modules/'.$script_base_controller.'/views/'.strtolower($template).'.php')) 
			{

				$output_error_view.=ob_get_contents();

				ob_clean();

				//No exists view in module where , load view respect to the module_theme variable...

				if(!include($base_path.'modules/'.$module_theme.'/views/'.strtolower($template).'.php')) 
				{

					//No exists view, see error from phango framework

					$output=ob_get_contents();

					ob_clean();
					
					include($base_path.'views/default/common/common.php');
				
					$template=@form_text($template);

					CommonView('Phango Framework error','<p>Error while loading template <strong>'.$template.'</strong>, check config.php or that template exists... </p><p>Output: '.$output_error_view.'<p>'.$output.'</p>');
					
					ob_end_flush();
					
					die;

				}

			}

		}

		ob_end_flush();

		//If load view, save function name for call write the html again without call include view too
		
		$cache_template[$template]=basename($template).'View';

	}
	else 
	if($load_if_no_cache!=0)
	{
			
		return  '';
		
	
	}
	
	ob_start();

	$func_view=$cache_template[$template];
	
	//Load function from loaded view with his parameters

	call_user_func_array($func_view, $arr_template);

	$out_template=ob_get_contents();

	ob_end_clean();
	
	return $out_template;

}

//Function for load multiple views for a only source file, useful for functions where you need separated views for the same thing

function load_libraries_views($template, $func_views=array())
{

	global $base_path, $base_url, $lang, $language, $index_set, $title_page, $cache_template, $config_data, $script_base_controller;
	
	$theme=$config_data['dir_theme'];

	$view='';

	//Load views from a source file...
	
	//Check func views...
	
	$no_loaded=0;

	foreach($func_views as $template_check)
	{

		if(isset($cache_template[$template_check]))
		{
			//Function view loaded, return because load_view load the function automatically.
		
			$no_loaded++;
		
		}

	}
	
	if($no_loaded==0)
	{	
		if(!include_once($base_path.'views/'.$theme.'/'.strtolower($template).'.php')) 
		{
			
			$output_error_view=ob_get_contents();

			ob_clean();

			if(!include_once($base_path.'modules/'.$script_base_controller.'/views/'.strtolower($template).'.php')) 
			{

				$output=ob_get_contents();

				ob_clean();

				include($base_path.'views/default/common/common.php');
				
				CommonView('Phango Framework error','<p>Error while loading template library <strong>'.$template.'</strong>, check config.php or that template library exists... </p><p>Output: '.$output_error_view.$output.'</p>');
				
				ob_end_flush();
				
				die;

			}

		}
		
	}
	
	//Forever register views if the code use different functions in a same library.
	
	foreach($func_views as $template)
	{

		$cache_template[$template]=basename($template).'View';

	}


}

//Checking if a model exists searching in arr_check_table array created in framework.php file.

$arr_check_table=array();

function check_model($model_name)
{
	global $arr_check_table, $model;

	// || !isset( $model[$model_name] ) 

	if( !isset($arr_check_table[$model_name]) )
	{

		/*$output=ob_get_contents();

		$no_exists[1]='<p>Don\'t exists '.$model_name.' models or model don\'t loaded. Please use <strong>php padmin.php model_container or function load_model(\''.$model_name.'\')</strong>.</p><p>Output: '.$output.'</p>';

		$no_exists[0]='<p>Don\'t exists the model or model don\'t loaded.</p>';

		ob_clean();
		
		echo load_view(array('Phango site is down', '<p>'.$no_exists[DEBUG].'</p>'), 'common/common');

		ob_end_flush();

		die;*/


		return 1;

	}

	return 0;

}

function check_model_exists()
{

	global $arr_check_table, $model;
	
	$arr_keys_model=array_keys($model);

	$error_model=array();

	$c_model=0;

	foreach($arr_keys_model as $key)
	{

		if(!isset($arr_check_table[$key]))
		{

			$error_model[]=$key;
			$c_model++;

		}

	}


	if( $c_model >0 ) 
	{

		$output=ob_get_contents();

		$no_exists[1]='<p>Don\'t exists '.implode(',', $error_model).' models. Please use <strong>php padmin.php model_container</strong>.</p><p>Output: '.$output.'</p>';

		$no_exists[0]='<p>Don\'t exists the model.</p>';

		ob_clean();
		
		echo load_view(array('Phango site is down', '<p>'.$no_exists[DEBUG].'</p>'), 'common/common');

		ob_end_flush();

		die;

	}

}

//Function for load the models..., if the model_file != models_path.php put model in format path/model_file

$cache_model=array();

function load_model()
{
	
	global $base_path, $model, $lang, $cache_model, $arr_module_insert, $arr_extension_model;
	
	$names=func_get_args();
	
	//Load a source file only	
	
	foreach($names as $my_model)
	{

		$arr_file=explode('/', $my_model);

		$my_path=$arr_file[0];

		if(count($arr_file)>1)
		{

			$my_model=$arr_file[1];

		}

		
		if( !isset($cache_model[$my_model]) )
		{

			$path_model=$base_path.'modules/'.$my_path.'/models/models_'.$my_model.'.php';
		
			if(!include($path_model)) 
			{

				$arr_error_sql[0]='<p>Error: Cannot load a file model.</p>';    
				$arr_error_sql[1]='<p>Error: Cannot load '.$my_model.' file model.</p>';
				
				$output=ob_get_contents();

				$arr_error_sql[1].='<p>Output: '.$output.'</p>';

				ob_clean();
			
				echo load_view(array('Phango site is down', $arr_error_sql[DEBUG]), 'common/common');

				die();
			
			}
			else
			{
				
				$cache_model[$my_model]=1;

			}
			
			//Now, load extension if necessary
			
			if(isset($arr_extension_model[$my_model]))
			{
				
				load_extension($my_model);
			
			}
			

		}

	
	}
	//Check if model and db is synced

	check_model_exists();

}

function load_extension()
{

	global $base_path, $model, $lang, $cache_model;
	
	$names=func_get_args();
	
	foreach($names as $my_model)
	{

		$arr_file=explode('/', $my_model);

		$my_path=$arr_file[0];

		if(count($arr_file)>1)
		{

			$my_model=$arr_file[1];

		}
		
	}
	
	if( !isset($cache_model['extension_'.$my_model]) )
	{
		
		$path_model=$base_path.'modules/'.$my_path.'/models/extension_'.$my_model.'.php';
		
		if(!include($path_model)) 
		{
		
			$arr_error_sql[0]='<p>Error: Cannot load a file extension model.</p>';    
			$arr_error_sql[1]='<p>Error: Cannot load '.$my_model.' file extension model.</p>';
			
			$output=ob_get_contents();

			$arr_error_sql[1].='<p>Output: '.$output.'</p>';

			ob_clean();
		
			echo load_view(array('Phango site is down', $arr_error_sql[DEBUG]), 'common/common');

			die();
		
		}
		else
		{
		
			$cache_model['extension_'.$my_model]=1;
		
		}
	
	}

}

//Load libraries, well, simply a elegant include

$cache_libraries=array();

function load_libraries($names, $path='')
{

	global $base_path, $cache_libraries;
	
	if(gettype($names)!='array')
	{
		ob_clean();
		$check_error_lib[1]='Error: You need an array how parameter in load_libraries. Return value: '.$names;
		$check_error_lib[0]='Error';
		
		echo load_view(array('Load libraries error', $check_error_lib[DEBUG]), 'common/common');
		die();

	}

	if($path=='')
	{

		$path=$base_path.'libraries/';

	}
	
	foreach($names as $library) 
	{
		

		if(!isset($cache_libraries[$library]))
		{
		
			if(!include($path.$library.'.php')) 
			{

				$output=ob_get_contents();

				$check_error_lib[1]='Error: Don\'t exists '.$library.'.<p>Output: '.$output.'</p>';
				$check_error_lib[0]='Error loading library.';

				ob_end_clean();
			
		
				echo load_view(array('Load libraries error', $check_error_lib[DEBUG]), 'common/common');
				die();
			
			}
			else
			{

				$cache_libraries[$library]=1;

			}

		}

	}

	return true;

}

//Load a language file...
//Other elegant include...

$cache_lang=array();

function load_lang()
{
	
	global $base_path, $lang, $base_url, $cache_lang, $script_base_controller, $language;
	
	if(isset($_SESSION['language']))
	{

		$language=$_SESSION['language'];

	}
	
	$arg_list = func_get_args();
	
	foreach($arg_list as $lang_file)
	{

		$lang_file=basename($lang_file);

		if(!isset($cache_lang[$lang_file]))
		{

			//First search in module, after in root i18n.

			//echo $base_path.'modules/'.$lang_file.'/i18n/'.$language.'/'.$lang_file.'.php';

			ob_start();

			$module_path=$lang_file;
				
			$pos=strpos($module_path, "_");
			
			if($pos!==false)
			{

				$arr_path=explode('_', $module_path);

				$module_path=$arr_path[0];
				
			}
			
			if(!@include($base_path.'modules/'.$module_path.'/i18n/'.$language.'/'.$lang_file.'.php'))
			{

				$output_error_lang=ob_get_contents();
			
				if(!include($base_path.'i18n/'.$language.'/'.$lang_file.'.php')) 
				{
					
					$output=ob_get_contents();
				
					ob_end_clean();
					ob_end_clean();
					//'.$output_error_lang.' '.$output.'
					$check_error_lang[1]='Error: Don\'t exists $lang['.$lang_file.']variable. Do you execute <strong>check_language.php</strong>?.<p></p>';
					$check_error_lang[0]='Error: Do you execute <strong>check_language.php</strong>?.';

					/*echo load_view(array('Internationalization error', $check_error_lang[DEBUG]), 'common/common');
					die();*/
					show_error($check_error_lang[0], $check_error_lang[1], $output);
				
				}

			}

			ob_end_clean();

			$cache_lang[$lang_file]=1;

		}

	}

}

//Set raw variables from a array

function check_variables($arr_variables, $fields=array())
{

	if(count($fields)==0)
	{

		$fields=array_keys($arr_variables);

	}

	$arr_final=array();

	foreach($fields as $field) 
	{
		settype($arr_variables[$field], 'string');
		$arr_final[$field]=unmake_slashes( form_text( urldecode( $arr_variables[$field] ) ) );

	}

	return $arr_final;

}

//Fill arr_check_table for check if exists model

function load_check_model()
{

	$table='';
	$arr_check_table=array();

	$query=webtsys_query(SQL_SHOW_TABLES);

	while(list($table)=webtsys_fetch_row($query))
	{

		$arr_check_table[$table]=1;

	}

	return $arr_check_table;

}

//Function for strip values with keys inside $array_strip

function strip_fields_array($array_strip, $array_source)
{

	$array_source=array();

	foreach($array_strip as $field_strip)
	{

		unset($array_source[$field_strip]);

	}

	return $array_source;

}

//Function for strip values without keys inside $array_strip

function filter_fields_array($array_strip, $array_source)
{

	$array_final=array();
	
	foreach($array_strip as $field_strip)
	{

		$array_final[$field_strip]=@$array_source[$field_strip];

	}

	return $array_final;

}

function set_csrf_key()
{

	global $user_data;

        echo "\n".HiddenForm('csrf_token', '', $user_data['key_csrf'])."\n";

}

function show_error($txt_error_normal, $txt_error_debug, $output_external='')
{

	global $utility_cli;
	
	$arr_error[0]='<p>'.$txt_error_normal.'</p>';    
	$arr_error[1]='<p>'.$txt_error_debug.'</p>';
	
	$output=ob_get_contents();

	$arr_error[1].="\n\n".'<p>Output: '.$output."\n".$output_external.'</p>';

	$arr_view[0]='common';
	$arr_view[1]='commontxt';
	
	if($utility_cli==0)
	{

		ob_clean();

	}

	echo load_view(array('Phango site is down', $arr_error[DEBUG]), 'common/'.$arr_view[$utility_cli]);

	die();

}

$arr_cache_jscript=array();
$arr_cache_jscript_gzipped=array();

function load_jscript_view()
{

	global $arr_cache_jscript, $arr_cache_jscript_gzipped, $base_url;

	//Delete repeat scripts...

	$arr_cache_jscript=array_unique($arr_cache_jscript, SORT_STRING);

	$arr_final_jscript=array();

	foreach($arr_cache_jscript as $idscript => $jscript)
	{

		settype($arr_cache_jscript_gzipped[$idscript], 'integer');

		$arr_final_jscript[]='<script type="text/javascript" src="'.make_fancy_url($base_url, 'jscript', 'load_jscript', 'script', array('no_compression' => $arr_cache_jscript_gzipped[$idscript], 'input_script' => $jscript)).'"></script>'."\n";

	}

	return implode("\n", $arr_final_jscript);

}

$arr_cache_header=array();

function load_header_view()
{

	global $arr_cache_header, $base_url;

	//Delete repeat scripts...

	$arr_cache_header=array_unique($arr_cache_header, SORT_STRING);

	return implode("\n", $arr_cache_header);

}


function urlencode_redirect($url)
{

	$base64_url=base64_encode( $url );
	
	$arr_char_ugly='+/=';
	$arr_char_cool='-_.';
	
	$replace=strtr($base64_url, $arr_char_ugly, $arr_char_cool);
	
	return $replace;

}

function urldecode_redirect($url_encoded)
{

	$arr_char_cool='-_.';
	$arr_char_ugly='+/=';

	$url_encoded=strtr($url_encoded, $arr_char_cool, $arr_char_ugly);
	
	$url=base64_decode( $url_encoded , true);
	
	return $url;

}

function set_name_default($name)
{

	return ucfirst(str_replace('_', ' ', $name));

}

?>
