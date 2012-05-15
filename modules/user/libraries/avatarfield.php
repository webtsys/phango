<?php

//AvatarField

class AvatarField extends CharField {

	function check($value)
	{
		
		global $config_data, $lang;
	
		load_libraries(array('check_image'));

		$value=form_text($value);
			
		if($value!='')
		{
			if(check_image($value, $config_data['x_avatar'], $config_data['y_avatar']))
			{
				
				return $value;

			}
			else
			{	
				$this->required=1;
				$this->std_error=$lang['user']['avatar_size_wrong'];
			}

		}

		return '';

	}

}

?>