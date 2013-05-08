<?php

class NormalizeField extends TextField {

	
	public $form='HiddenForm';
	
	public function check($value)
	{
		
		$str_normalize=slugify(strip_tags($value));
		
		$arr_normalize=explode('-', $str_normalize);
		
		sort($arr_normalize);
		
		$value=implode('%', $arr_normalize);
		
		return $value;
		

	}
	

}

?>