<?php

//Warning, blobfield is dangerous because don't check the binary value. If you use this you must make sure that you obtain data from safe sources.

class BlobField {

	public $value="";
	public $label="";
	public $required=0;
	public $form="TextAreaForm";
	public $quot_open='\'';
	public $quot_close='\'';
	public $std_error='';
	public $multilang=0;

	function __construct($multilang=0)
	{

		$this->form='TextAreaForm';
		$this->multilang=$multilang;

	}

	function check($value)
	{
		
		//Delete Javascript tags and simple quotes.
		$this->value=$value;
		return $value;

	}

	//Function check_form

	function get_type_sql()
	{

		return 'LONGBLOB NOT NULL';
		

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

?>