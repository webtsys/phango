<?php

function wrap_words($text, $num_words, $text_explain='...')
{

	$arr_text=explode(' ', $text);
	
	$final_text='';

	$total_num_text=count($arr_text);
	
	if($total_num_text<$num_words)
	{

		$num_words=$total_num_text;

	}

	for($x=0;$x<$num_words;$x++)
	{

		$arr_final_text[]=$arr_text[$x];

	}

	return implode(' ', $arr_final_text).' '.$text_explain;

}

?>