<?php

class PhoneField extends CharField{


	public function check($value)
	{
		
		if(!preg_match('/^(?:\+?1)?[-. ]?\\(?[2-9][0-8][0-9]\\)?[-. ]?[2-9][0-9]{2}[-. ]?[0-9]{4}$/', $value))
		{
		
			return 0;
		
		}

		return $value;
		

	}


}

?>