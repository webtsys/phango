<?php

function form_time( $time, $format_time, $ampm )
{
    return date( $ampm, $format_time + $time );
} 

?>