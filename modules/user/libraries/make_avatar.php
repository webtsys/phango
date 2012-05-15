<?php

function make_avatar( $iduser_avatar, $form, $form_avatar,  $dir_prefix='')
{

    global $model, $config_data, $base_path, $base_url, $lang;

    $cont_index = '';

    settype($_GET['action_avatar'], "integer");

    echo '<h3 align="center">'.$lang['user']['choose_avatar'].'</h3>';

    switch ( $_GET['action_avatar'] )
    {
        default:
		
	    $form_see_avatars=add_extra_fancy_url($form_avatar, array('action_avatar' => 1));

	    ob_start();

	    set_csrf_key();

	    $csrf_key=ob_get_contents();

	    ob_end_clean();

            $cont_index = "<form action=\"" . $form_see_avatars . "\" method=\"post\">" . "<p align=\"center\">" .$csrf_key. "<select name=\"dir_avatars\">";
	    

            $num_galleries = 0;

            $dir = opendir( $dir_prefix."avatars" );

            while ( $directory = readdir( $dir ) )
            {
                if ( !preg_match('/^\./', $directory) && $directory != "index.html" )
                {
                    $cont_index .= "<option value=\"$directory\">$directory</option>";
                    $num_galleries++;
                } 
            } 

            closedir( $dir );

            if ( $num_galleries > 0 )
            {
                $cont_index .= "</select><p align=\"center\"><input type=\"submit\" value=\"" . $lang['user']['see_gallery'] . "\"></form>";
            } 
            else
            {
                $cont_index .= "<option value=\"nogallery\">" . $lang['user']['no_gallery'] . "</option></select><p align=\"center\"><a href=\"".$form."\">".$lang['user']['goback']."</a></p></form>";
            } 

            break;

        case 1:

		$x_avatar=$config_data['x_avatar'];
		$y_avatar=$config_data['y_avatar'];
		
            if ( file_exists( $dir_prefix."avatars/" . $_POST['dir_avatars'] ) )
            {
                $dir = opendir( $dir_prefix."avatars/" . $_POST['dir_avatars'] );

		$form_change_avatar=add_extra_fancy_url($form_avatar, array('action_avatar' => 2));

		ob_start();

		set_csrf_key();

		$csrf_key=ob_get_contents();

		ob_end_clean();

                $cont_index = "<h3 align=\"center\">" . $lang['user']['choose'] . "</h3><p><form action=\"" . $form_change_avatar . "\" method=\"post\">" . "<p align=\"center\">";
		$cont_index.=$csrf_key;
                $c = 1;

                $check = "checked";

                while ( $name_image = readdir( $dir ))
                {
                    $br = '';

                    if ( $name_image != "." && $name_image != ".." )
                    {
                        if ( $c == 5 )
                        {
                            $br = "<p align=\"center\">\n";
                            $c = 0;
                        } 
			
                        list( $x_image, $y_image, $type_image ) = getimagesize( $dir_prefix."avatars/" . $_POST['dir_avatars'] . "/$name_image" );

                        if ( ( $x_avatar == 0 || $x_image <= $x_avatar ) && ( $y_avatar == 0 || $y_image <= $y_avatar ) && $type_image < 4 )
                        {
                            $cont_index .= "<input $check type=\"radio\" name=\"avatar_total\" value=\"" . $_POST['dir_avatars'] . "/$name_image\"><img src=\"".$base_url."/media/avatars/" . $_POST['dir_avatars'] . "/$name_image\">$br";

                            $c++;

                            $check = "";
                        } 
                    } 
                } 

                closedir( $dir );

                $cont_index .= "<p align=\"center\"><input type=\"submit\" value=\"" . $lang['user']['choose'] . "\"></form>";
            } 
            else
            {
                $cont_index = "<p align=\"center\">" . $lang['user']['no_gallery'] . "</p>";
            } 
            break;

        case 2:

            $cont_index = '';
	
            if ( file_exists( $dir_prefix."avatars/" . $_POST['avatar_total'] ) )
            {
		
		$arr_avatar=array( 'avatar'=>$base_url . "/media/avatars/" . $_POST['avatar_total'] );
		
		$model['user']->components['private_nick']->required=0;
		$model['user']->components['email']->required=0;
		$model['user']->components['password']->required=0;
		
		if($model['user']->update($arr_avatar, 'where IdUser=' . $iduser_avatar))
		{
                	$cont_index = "<p align=\"center\"><img src=\"".$arr_avatar['avatar']. "\"><br>" . $lang['user']['success_avatar'];
		}
		else
		{

			$cont_index = "<p align=\"center\">" . $lang['user']['error_avatar'].': '.$model['user']->std_error;

		}
            } 
            else
            {
                $cont_index = "<p align=\"center\">" . $lang['user']['error_avatar'];
            } 

            $cont_index .= "<p align=\"center\"><a href=\"" . $form . "\">" . $lang['user']['comeback'] . "</a></p>";

            break;

    } 

    echo $cont_index;
} 


?>