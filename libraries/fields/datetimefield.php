<?php

class DateTimeField extends DateField
{

	public function __construct()
	{

		$this->form='DateTimeForm';

	}

	public function check($value)
	{
		global $user_data;
	
		$timestamp=parent::check($value);
		
		return date('YmdHis', $timestamp);
	
	}
	
	public function search_field($value)
	{
	
		$value_check=$this->check($value);
				
		return substr($value_check, 0, 8);
	
	}
	
	public function show_formatted($value)
	{

		$timestamp=$this->obtain_timestamp_datefield($value);
		
		return parent::show_formatted($timestamp);

	}

	public function get_type_sql()
	{

		return 'VARCHAR(14) NOT NULL';
		

	}

	static public function obtain_timestamp_datefield($value)
	{

		global $user_data;

		$year=substr($value, 0, 4);
		$month=substr($value, 4, 2);
		$day=substr($value, 6, 2);
		$hour=substr($value, 8, 2);
		$minute=substr($value, 10, 2);
		$second=substr($value, 12, 2);

		settype($year, 'integer');
		settype($month, 'integer');
		settype($day, 'integer');
		settype($hour, 'integer');
		settype($minute, 'integer');
		settype($second, 'integer');
		
		$timestamp=mktime($hour, $minute, $second, $month, $day, $year);
		
		return $timestamp;
		
	}
	
}


function DateTimeForm($field, $class='', $value='', $set_time=1)
{

	$timestamp=DateTimeField::obtain_timestamp_datefield($value);
	
	return DateForm($field, $class, $timestamp, $set_time);

}

function DateTimeFormSet($post, $value)
{
	
	global $user_data;
	
	//$value+=$user_data['format_time'];
	
	return $value;

}

?>