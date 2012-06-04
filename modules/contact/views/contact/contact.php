<?php

function ContactView($description_contact, $form_contact, $url_send_mail)
{

	?>
	<p><?php echo $description_contact; ?></p>
	<?php

	echo load_view(array($form_contact, array(), $url_send_mail, ''), 'common/forms/updatemodelform');

}

?>