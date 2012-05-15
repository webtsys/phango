<?php

global $user_data, $base_url, $lang, $arr_check_table;

load_lang('user_block');

echo '<div align="center">';

if($user_data['IdUser']==0)
{

	?>
		<form method="post" action="<?php echo make_fancy_url($base_url, 'user', 'index', 'login', array('op' => 1)) ; ?>">
		<?php set_csrf_key(); ?>
		<p><?php echo $lang['common']['email']; ?>
			<br />
		<input type="text" name="email" size="10"/>
		</p>
		<p><?php echo $lang['common']['password']; ?>
			<br />
		<input type="password" name="password" size="10"/>
		</p>
		<p><input type="submit" value="<?php echo $lang['common']['send']; ?>"/></p>
		</form>
	<?php

}
else
{

	?>
		<p><strong><?php echo $lang['user_block']['welcome_user']; ?> <?php echo $user_data['private_nick']; ?></strong></p>
		<p><a href="<?php echo make_fancy_url($base_url, 'user', 'index', 'login', array('op' => 0)) ; ?>"><?php echo $lang['user_block']['go_to_zone_user']; ?></a></p>
	<?php

	if(isset($arr_check_table['mprivate']))
	{

		$query=webtsys_query('select count(IdMprivate) from mprivate where read_message=0 and IdUser='.$user_data['IdUser']);
		
		list($num_mprivate)=webtsys_fetch_row($query);

		settype($num_private, 'integer');

		if($num_mprivate>0)
		{

		?>
			<p><a href="<?php echo make_fancy_url($base_url, 'user', 'mprivate', 'view_private_messages', array()); ?>"><strong><?php echo $num_mprivate; ?> <?php echo $lang['user_block']['new_private_messages']; ?> </strong></a></p>
		<?php

		}

	}

}

?>
<p><strong><?php echo ucfirst($lang['user_block']['connected_users']); ?></strong></p>

<?php

$last_time=TODAY-300;

$arr_priv[0]='';
$arr_priv[1]='(M)';
$arr_priv[2]='(A)';

$num_user=0;

$sql_hidden=' and hidden_status=0';

if($user_data['privileges_user']==2)
{

	$sql_hidden='';

}

$arr_hidden[0]='';
$arr_hidden[1]=' style="font-style:italic;"';

$query=webtsys_query('select IdUser, private_nick, privileges_user, hidden_status from user where last_connection>'.$last_time.$sql_hidden);

while(list($iduser, $private_nick, $priv, $hidden_status)=webtsys_fetch_row($query))
{

	?>
		<a href="<?php echo make_fancy_url($base_url, 'user', 'profile', 'viewprofile', array('IdUser' => $iduser)); ?>"<?php echo $arr_hidden[$hidden_status]; ?>><?php echo $private_nick; ?> <?php echo $arr_priv[$priv]; ?></a><br />
	<?php

	$num_user++;

}

?>

	<p><?php echo $lang['user_block']['connected_users']; ?>: <?php echo $num_user; ?></p>

<?php

$query=webtsys_query('select count(IdUser) from user where last_connection>'.$last_time.' and hidden_status=1');

list($num_hidden)=webtsys_fetch_row($query);

?>

	<p><?php echo $lang['user_block']['connected_users_hidden']; ?>: <?php echo $num_hidden; ?></p>

<?php

$last_time=TODAY-300;

$query=webtsys_query('select count(IdAnonymous) from anonymous where last_connection>'.$last_time);

list($num_anom)=webtsys_fetch_row($query);

?>

	<p><?php echo $lang['user_block']['anonymous_connected_users']; ?>: <?php echo $num_anom; ?></p>

<?php


echo '</div>';

?>