<?php

class LoginClass {

	

	public $model_login;
	public $field_user;
	public $field_password;
	
	public function __construct($model_login, $field_user, $field_password)
	{
	
		$this->model_login=$model_login;
		$this->field_user=$field_user;
		$this->field_password=$field_password;

	}
	
	public login($user, $password)
	{
	
		global $model;
	
		$password=sha1($password);
		
		
	
	}
	
	public begin_session()
	{
	
		
	
	}
	
}

?>