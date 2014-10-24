<?php

class LoginClass {

	public $model_login;
	public $field_user;
	public $field_password;
	public $arr_user_session;
	public $arr_user_insert=array();
	public $field_key;
	public $session;
	public $url_login='';
	public $url_insert='';
	public $url_recovery='';
	public $login_view='common/user/standard/loginform';
	public $edit_fields=array();
	public $create_account_view='common/user/standard/insertuserform';
	public $recovery_pass_view='common/user/standard/recoverypassform';
	//public $method_crypt='sha256';
	
	public function __construct($model_login, $field_user, $field_password, $field_key, $arr_user_session=array(), $arr_user_insert=array())
	{
		global $model;
	
		$this->model_login=$model_login;
		$this->field_user=$field_user;
		$this->field_password=$field_password;
		$this->arr_user_session=$arr_user_session;
		$this->field_key=$field_key;
		$this->arr_user_insert=array($field_user, $field_password);
		
		if(count($this->arr_user_session)==0)
		{
		
			$this->arr_user_session[]=$model[$this->model_login]->idmodel;
			$this->arr_user_session[]=$this->field_key;
		
		}

	}
	
	public function automatic_login($iduser)
	{
	
		global $model;
	
		$arr_user=$model[$this->model_login]->select_a_row($iduser, array($this->field_user, $this->field_password));
	
		return $this->login($arr_user[$this->field_user], $arr_user[$this->field_password], 0, 1);
	
	}
	
	public function login($user, $password, $autologin=0, $yes_hash=0)
	{
		load_libraries(array('fields/passwordfield'));
	
		global $model, $lang, $cookie_path;
	
		$check_password=0;
	
		$user=form_text($user);
		
		$this->arr_user_session[]=$this->field_password;
		
		$arr_user=$model[$this->model_login]->select_a_row_where('where '.$this->field_user.'="'.$user.'"', $this->arr_user_session);
		
		settype($arr_user[$model[$this->model_login]->idmodel], 'integer');
		
		if($arr_user[$model[$this->model_login]->idmodel]==0)
		{
		
			ModelForm::SetValuesForm($_POST, $model[$this->model_login]->forms, 1);
		
			unset($arr_user[$this->field_password]);
			
			return false;
		
		}
		else
		{
		
			$yes_password=0;
		
			
			if($yes_hash==0)
			{
			
				if(PasswordField::check_password($password, $arr_user[$this->field_password]))
				{
				
					$yes_password=1;
				
				}
				
			}
			else
			{
			
				if($password==$arr_user[$this->field_password])
				{
				
					$yes_password=1;
				
				}
				
			}
			
			if($yes_password==1)
			{
		
				unset($arr_user[$this->field_password]);
			
				$this->session=$arr_user;
				
				//Create token
				
				$new_token=get_token();
				
				$model[$this->model_login]->reset_require();
				
				if( $model[$this->model_login]->update(array($this->field_key => $new_token), 'where `'.$model[$this->model_login]->idmodel.'`='.$arr_user[$model[$this->model_login]->idmodel]) )
				{
					$_SESSION[$model[$this->model_login]->idmodel]=$arr_user[$model[$this->model_login]->idmodel];
					$_SESSION[$this->field_key]=$new_token;
				
					$model[$this->model_login]->reload_require();
					
					if($autologin==1)
					{
						
						$lifetime=31536000;
						
						$arr_save=serialize( array( 'id' => $arr_user[$model[$this->model_login]->idmodel] , 'token' => $new_token ) );
						
						setcookie(COOKIE_NAME.'_'.sha1($this->field_key), $arr_save,time()+$lifetime, $cookie_path);
						//setcookie(session_name(),0,-31536000, $cookie_path);
					
					}
					
					return true;
					
				}
				else
				{
				
					ModelForm::SetValuesForm($_POST, $model[$this->model_login]->forms, 1);
				
					return false;
				
				}
				
			}
			else
			{
				
				ModelForm::SetValuesForm($_POST, $model[$this->model_login]->forms, 1);
				
				$model[$this->model_login]->forms[$this->field_password]->std_error=$lang['user']['user_error_nick_or_pass'];
			
				return false;
			
			}
		
		}
	
	}
	
	public function check_login()
	{
	
		global $model;
		
		$check_user=0;
		
		if(isset($_SESSION[$this->field_key]) && isset($_SESSION[$model[$this->model_login]->idmodel]))
		{
		
			$check_user=1;
			
		}	
		else
		if(isset( $_COOKIE[COOKIE_NAME.'_'.sha1($this->field_key)] ))
		{
			
			$arr_token=$_COOKIE[COOKIE_NAME.'_'.sha1($this->field_key)];
			
			$arr_set=@unserialize($arr_token);
			
			settype($arr_set['id'], 'integer');
			
			if($arr_set['id']>0)
			{
			
				$_SESSION[$model[$this->model_login]->idmodel]=$arr_set['id'];
			
				$_SESSION[$this->field_key]=$arr_set['token'];
				
				$check_user=1;
				
			}
			
		
		}
		
		if($check_user==1)
		{
			
			$arr_user=$model[$this->model_login]->select_a_row_where('where '.$this->field_key.'="'.$_SESSION[$this->field_key].'" and '.$model[$this->model_login]->idmodel.'='.$_SESSION[$model[$this->model_login]->idmodel], $this->arr_user_session);
			
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
		
		global $model;
		
		echo load_view(array($model[$this->model_login], $this), $this->login_view);
	
	}
	
	public function recovery_password_form()
	{
	
		global $model;
		
		echo load_view(array($model[$this->model_login], $this), $this->recovery_pass_view);
	
	}
	
	public function recovery_password()
	{
	
		
	
	}
	
	public function create_account_form()
	{
	
		global $model;
		
		if(!isset($model[$this->model_login]->forms['accept_conditions']))
		{
		
			$this->prepare_insert_user();
		
		}
	
		echo load_view(array('model' => $model[$this->model_login], 'login_model' => $this), $this->create_account_view);
	
	}
	
	public function create_account()
	{
		
		global $model, $config_data, $lang;
		
		$this->prepare_insert_user();
					
		$post=filter_fields_array($this->arr_user_insert, $_POST);
		
		$no_user=0;
		
		$check_user=$model[$this->model_login]->components[$this->field_user]->check($post[$this->field_user]);
		
		$no_user=$model[$this->model_login]->select_count('where `'.$model[$this->model_login]->name.'`.`'.$this->field_user.'`="'.$check_user.'"');
		
		if(ModelForm::check_form($model[$this->model_login]->forms, $post) && $no_user==0)
		{
		
			/*if($_POST['repeat_password']==$post[$this->field_password] && $check_captcha==1 && $no_user==0)
			{*/
			
			$model[$this->model_login]->reset_require();
			
			foreach($this->arr_user_insert as $field_require)
			{
			
				if(isset($model[$this->model_login]->components[$field_require]))
				{
					$model[$this->model_login]->components[$field_require]->require=1;
				}
			}
			
			if($model[$this->model_login]->insert($post))
			{
			
				return true;
			
			}
			else
			{
			
				ModelForm::SetValuesForm($_POST, $model[$this->model_login]->forms, 1);
			
			
				return false;
				
			}
		}
		else
		{
		
			if($no_user>0)
			{
				
				$model[$this->model_login]->forms[$this->field_user]->std_error=$lang['user']['user_or_email_exists'];
			
			}
		
			ModelForm::SetValuesForm($_POST, $model[$this->model_login]->forms, 1);
		
			return false;
		
		}
	
	}
	
	public function prepare_insert_user()
	{
	
		global $model, $config_data, $lang;
		
		//$this->arr_user_insert[]='accept_conditions';
		
		$model[$this->model_login]->forms['repeat_password']=new ModelForm('repeat_password', 'repeat_password', 'PasswordForm', $lang['user']['repeat_password'], new PasswordField(), $required=1, $parameters='');
	
		$this->arr_user_insert[]='repeat_password';
			
		if($config_data['captcha_type']!='')
		{

			load_libraries(array('fields/captchafield'));

			$model[$this->model_login]->forms['captcha']=new ModelForm('captcha', 'captcha', 'CaptchaForm', $lang['common']['captcha'], new CaptchaField(), $required=1, $parameters='');

			$this->arr_user_insert[]='captcha';
			
		}
		
		$model[$this->model_login]->forms['accept_conditions']=new ModelForm('form_login', 'accept_conditions', 'CheckBoxForm', $lang['user']['accept_cond_register']	, new BooleanField(), $required=1, $parameters='');
		
		$this->arr_user_insert[]='accept_conditions';
	
	}
	
	
}

?>