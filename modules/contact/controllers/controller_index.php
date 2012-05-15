<?php

function Index()
{

	global $user_data, $model, $ip, $lang, $config_data, $base_path, $base_url, $cookie_path, $arr_block, $prefix_key, $block_title, $block_content, $block_urls, $block_type, $block_id;

	$cont_index='';

	$arr_block='';

	$arr_block=select_view(array('contact'));

	settype($_GET['op'], 'integer');

	//Load page...

	load_lang('contact');
	load_model('contact');
	load_libraries(array('forms/textbbpost'));

	settype($_GET['IdContact'], 'integer');

	$query=$model['contact']->select('where IdContact='.$_GET['IdContact'], array('IdContact', 'name', 'email'));

	list($idcontact, $name_page, $emailto)=webtsys_fetch_row($query);

	settype($idcontact, 'integer');

	if($idcontact>0)
	{

		$form_contact=array();

		$arr_fields=array('TextField' => 'TextForm', 'TextHTMLField' => 'TextAreaBBPostForm');

		$name_page=$model['contact']->components['name']->show_formatted($name_page);

		$url_send_mail=make_fancy_url($base_url, 'contact', 'index', $name_page, array('op' => 1, 'IdContact' => $idcontact) );

		$query=$model['contact_field']->select('where contact_field.idcontact='.$idcontact.' order by `order` ASC', array('IdContact_field', 'name', 'type', 'order', 'required') );

		while(list($idfield, $name, $type, $order, $required)=webtsys_fetch_row($query))
		{

			$html_field=new TextHTMLField();
			$html_field->set_safe_html_tags();
			
			$form_contact['field'.$idfield]=new ModelForm('form'.$idcontact, 'field'.$idfield, $arr_fields[$type], I18nField::show_formatted($name), $html_field, $required, '');

		}

		if($config_data['captcha_type']!='')
		{

			load_libraries(array('captchas/'.$config_data['captcha_type']));

			$form_contact['captcha']=new ModelForm('captcha', 'captcha', 'CaptchaForm', $lang['common']['captcha'], new CharField(255), $required=0, $parameters='');

		}


		switch($_GET['op'])
		{

			default:

				ob_start();

				echo load_view(array($form_contact, array(), $url_send_mail, ''), 'common/forms/updatemodelform');

				$form_result=ob_get_contents();

				ob_end_clean();

				echo load_view(array($name_page, $form_result), 'content');

			break;

			case 1:

				//Check 
				
				$post=ModelForm::check_form($form_contact, $_POST);

				$check_captcha=1;

				if($config_data['captcha_type']!='')
				{

					$result_captcha=CaptchaCheck($_POST);
					
					if($result_captcha[0]=='false')
					{

						$check_captcha=0;

					}

				}

				if($post!='' && $check_captcha==1)
				{
	
					//Send email...

					load_libraries(array('send_email', 'forms/textplainform'));

					ob_start();

					foreach($form_contact as $key_form => $value_form)
					{

						$form_contact[$key_form]->form='TextPlainForm';
						$form_contact[$key_form]->required=0;

					}

					unset($form_contact['captcha']);

					SetValuesForm($post, $form_contact, $show_error=0);

					ob_start();
					//ModelFormView($model_form, $fields=array())
					echo load_view(array($form_contact, array()), 'common/forms/modelform');

					$message=ob_get_contents();
					
					ob_end_clean();

					if(send_mail($emailto, $name_page, $message, 'html'))
					{

						echo load_view(array($name_page, $lang['contact']['send_mail_form_success']), 'content');

					}
	
				}
				else
				{

					SetValuesForm($_POST, $form_contact, $show_error=1);

					ob_start();

					echo load_view(array($form_contact, array(), $url_send_mail, ''), 'common/forms/updatemodelform');

					$form_result=ob_get_contents();

					ob_end_clean();

					echo load_view(array($name_page, $form_result), 'content');

				}

			break;

		}

	}

	

	$cont_index.=ob_get_contents();

	ob_end_clean();

	echo load_view(array($name_page, $cont_index, $block_title, $block_content, $block_urls, $block_type, $block_id, $config_data, ''), $arr_block);

}

?>