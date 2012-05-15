<?php

function form_date( $date, $form_date , $format_time)
{

	return gmdate( $form_date, $date+$format_time );

} 

?>