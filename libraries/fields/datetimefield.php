<?php

class DateTimeField extends DateField
{

	function __construct()
	{

		$this->form='DateTimeForm';

	}

	function check($value)
	{
	
		$timestamp=parent::check($value);
		
		return date('YmdHis', $timestamp);
	
	}
	
	function show_formatted($value)
	{

		$timestamp=obtain_timestamp_datefield($value);
		
		return parent::show_formatted($timestamp);

	}

	function get_type_sql()
	{

		return 'VARCHAR(14) NOT NULL';
		

	}

}

function obtain_timestamp_datefield($value)
{

	$year=substr($value, 0, 4);
	$month=substr($value, 4, 6);
	$day=substr($value, 6, 8);
	$hour=substr($value, 8, 10);
	$minute=substr($value, 10, 12);
	$second=substr($value, 12, 14);

	settype($year, 'integer');
	settype($month, 'integer');
	settype($day, 'integer');
	settype($hour, 'integer');
	settype($minute, 'integer');
	settype($second, 'integer');
	
	$timestamp=gmmktime($hour, $minute, $second, $month, $day, $year);
	
	return $timestamp;
	
}


function DateTimeForm($field, $class='', $value='', $set_time=1)
{

	$timestamp=obtain_timestamp_datefield($value);
	
	return DateForm($field, $class, $timestamp, $set_time);

}

function DateTimeFormSet($post, $value)
{

	$timestamp=obtain_timestamp_datefield($value);
	
	return DateFormSet($post, $timestamp);

}

?>