<?php

class PasswordField extends CharField {

	
	function __construct($size=255)
	{

		$this->size=$size;
		$this->form='PasswordForm';

	}

	public function check($value)
	{

		$token_pass=generate_random_password();
		
		$hash_password=$token_pass.'_'.sha1($token_pass.'_'.$value);
		
		return $hash_password;

	}
	
	//I load the password with the username and check here.
	
	static public function check_password($value, $hash_password_check)
	{
	
		$token_pass=preg_replace('/(.*)[_].*/', '$1', $value);
		
		$hash_password=$token_pass.'_'.sha1($token_pass.'_'.$value);
		
		if($hash_password==$hash_password_check)
		{
		
			return true;
		
		}
		
		return false;
	
	}

}

?>