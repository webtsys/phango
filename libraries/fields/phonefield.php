<?php

class PhoneField extends CharField{


	public function check($value)
	{
		
		if(!preg_match('/^[0-9]+$/', $value))
		{
			
			return '';
		
		}

		return $value;
		

	}


}

?>