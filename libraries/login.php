<?php

class LoginClass {

	public $model_login;
	public $field_user;
	public $field_password;
	public $arr_user_session;
	public $arr_user_insert;
	public $key_field;
	public $session;
	public $url_login='';
	public $login_view;
	
	public function __construct($model_login, $field_user, $field_password, $key_field, $arr_user_session=array(), $arr_user_insert=array())
	{
	
		$this->model_login=$model_login;
		$this->field_user=$field_user;
		$this->field_password=$field_password;
		$this->arr_user_session=$arr_user_session;
		$this->key_field=$key_field;
		$this->login_view='';
		
		if(count($this->arr_user_session)>0)
		{
		
			$this->arr_user_session[]=$model[$this->model_login]->idmodel;
			$this->arr_user_session[]=$this->key_field;
		
		}

	}
	
	public function login($user, $password)
	{
	
		global $model;
	
		$user=form_text($user);
	
		$password=sha1($password);
		
		$arr_user=$model[$this->model_login]->select_a_row('where '.$this->field_user.'="'.$user.'" and '.$this->field_password.'="'.$password.'"', $this->arr_user_session);
	
		unset($arr_user[$this->field_password]);
		
		settype($arr_user[$model[$this->model_login]->idmodel], 'integer');
		
		if($arr_user[$model[$this->model_login]->idmodel]==0)
		{
		
			return false;
		
		}
		else
		{
		
			$this->session=$arr_user;
			
			//Create token
			
			$new_token=get_token();
			
			$model[$this->model_login]->reset_require();
			
			if( $model[$this->model_login]->update(array($this->key_field => $new_token)) )
			{
				$_SESSION[$model[$this->model_login]->idmodel]=$this->session[$model[$this->model_login]->idmodel];
				$_SESSION[$this->key_field]=$new_token;
			
				$model[$this->model_login]->reload_require();
			
				return true;
				
			}
			else
			{
			
				return false;
			
			}
		
		}
	
	}
	
	public function check_login()
	{
	
		if(isset($_SESSION[$this->key_field]) && isset($_SESSION[$model[$this->model_login]->idmodel]))
		{
	
			$arr_user=$model[$this->model_login]->select_a_row('where '.$this->key_field.'="'.$_SESSION[$this->key_field].'" and '.$model[$this->model_login]->idmodel.'='.$_SESSION[$model[$this->model_login]->idmodel], $this->arr_user_session);
			
			settype($arr_user[$model[$this->model_login]->idmodel], 'integer');
			
			if($arr_user[$model[$this->model_login]->idmodel]==0)
			{
			
				return false;
			
			}
			else
			{
			
				$this->session=$arr_user;
			
				return true;
			
			}
		
		}
		else
		{
		
			return false;
		
		}
	
	}
	
	public function login_form()
	{
		
		echo load_view(array('model' => $model[$this->model_login]), $this->login_view);
	
	}
	
	public function recovery_password_form()
	{
	
		
	
	}
	
	public function recovery_password()
	{
	
		
	
	}
	
	public function create_account_form()
	{
	
		
	
		echo load_view(array('model' => $model[$this->model_login], 'login_model' => $this), $this->create_account_view);
	
	}
	
	public function create_account()
	{
		
		global $model;
			
		$post=filter_fields_array($this->arr_user_insert, $_POST);
	
		if(ModelForm::check_form($model[$this->model_login], $post))
		{
		
			if($model[$this->model_login]->insert($post))
			{
			
				return true;
			
			}
		
		}
	
		
	
	}
	
	
}

?>