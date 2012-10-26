<?php

function obtain_timestamp_zone($timezone, $default_timezone=MY_TIMEZONE)
{

	$dateTimeZone=new DateTimeZone($timezone);
	
	if($dateTimeZone==false)
	{

		$dateTimeZone=new DateTimeZone($default_timezone);

	}

	$dateTimeNow=new DateTime("now", $dateTimeZone);
	return $dateTimeZone->getOffset($dateTimeNow);

}

function timezones_list($timezone_chosen)
{
	
	$list_gmt=array();

	$timezone_identifiers = DateTimeZone::listIdentifiers();

	foreach($timezone_identifiers as $timezone)
	{

		$arr_gmt[]=$timezone;

	}
	
	$list_gmt=array($timezone_chosen);

	foreach($arr_gmt as $key)
	{

		$list_gmt[]=$key;
		$list_gmt[]=$key;

	}

	return $list_gmt;

	
}

function timezones_array()
{

	$list_gmt=array();

	$timezone_identifiers = DateTimeZone::listIdentifiers();

	foreach($timezone_identifiers as $timezone)
	{

		$arr_gmt[]=$timezone;

	}

	$list_gmt=array();

	foreach($arr_gmt as $key)
	{

		$list_gmt[]=$key;

	}

	return $list_gmt;

	
}

?>
