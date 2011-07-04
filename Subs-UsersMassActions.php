<?php

if (!defined('SMF'))
	die('Hacking attempt...');

function add_users_mass_action_settings (&$config_vars) {
	$config_vars[] = array('text', 'users_mass_action_ban_name');

	if (isset($_GET['save']))
	{
		$_POST['users_mass_action_ban_name'] = !empty($_POST['users_mass_action_ban_name']) ? $_POST['users_mass_action_ban_name'] : 'Mass bans';
	}
}
?>