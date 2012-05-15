<?php

function form_time( $time, $format_time, $ampm )
{
    return gmdate( $ampm, $format_time + $time );
} 

?>