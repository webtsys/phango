<?php

function check_image($url, $max_x=0, $max_y=0)
{
	$url=trim($url);

	if(preg_match('/^http:\/\//', $url))
	{
	
		if($url != "")
		{

			list( $x_image, $y_image, $type_image ) = @getimagesize( $url );
			
			if ( $x_image != "")
			{	
				if ( $type_image>0 && $type_image < 4)
				{	
					
					if($max_x>0 && $x_image>$max_x)
					{

						return 0;

					}

					if($max_y>0 && $y_image>$max_y)
					{

						return 0;

					}
					
					return '<img_tmp src="'.$url.'" alt="'.slugify(basename($url)).'" width="'.$x_image.'" height="'.$y_image.'"/>';
				} 
				else
				{
					return 0;
				} 
			} 
			else
			{
			
				return 0;

			} 
		}

	}

	return 0;

}

?>