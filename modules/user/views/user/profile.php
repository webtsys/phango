<?php

function ProfileView($result)
{
	global $base_url, $lang, $arr_check_table;

	?>
	<div class="form">
		<p><label for="Avatar"><?php echo $lang['common']['avatar']; ?>: </label><?php echo $result['avatar']; ?></p>
		<p><label for="Avatar"><?php echo $lang['common']['rank_name']; ?>: </label><?php echo $result['name']; ?></p>
		<p><label for="email"><?php echo $lang['common']['email']; ?>: </label><?php echo $result['email']; ?></p>
		<p><label for="Website"><?php echo $lang['common']['website']; ?>: </label><?php echo $result['website']; ?></p>
		<p><label for="Interests"><?php echo $lang['common']['interests']; ?>: </label><?php echo $result['interests']; ?></p>
		<p><label for="Signature"><?php echo $lang['common']['signature']; ?>: </label><?php echo $result['signature']; ?></p>
		<p><label for="Num_messages"><?php echo $lang['common']['num_messages']; ?>: </label><?php echo $result['num_messages']; ?></p>
		<p><label for="Date_register"><?php echo $lang['common']['date_register']; ?>: </label><?php echo $result['date_register']; ?></p>
		<p><label for="Connected"><?php echo $lang['common']['status']; ?>: </label><?php echo $result['hidden_status']; ?></p>
		<?php
		if(isset($arr_check_table['mprivate']))
		{
		?>
		<hr />
		<p><a href="<?php echo make_fancy_url($base_url, 'user', 'sendprivate', 'sendprivate', array('IdUser' => $result['IdUser'])); ?>"><?php echo $lang['user']['send_private']; ?></a></p>
		<?php
		}
		?>
		<hr />
		<p><a href="<?php echo make_fancy_url($base_url, 'user', 'profiles', 'profiles_list', array()); ?>p"><?php echo $lang['user']['go_profiles']; ?></a>
	</div>
	<?php
}

?>