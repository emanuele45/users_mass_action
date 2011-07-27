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

function createGroupsList(){
	global $context, $smcFunc, $user_info, $sourcedir, $txt;

	require_once($sourcedir . '/Profile-Modify.php');
	loadLanguage('Profile');
	profileLoadGroups();

	// Better remove admin membergroup...and set it to a "remove all"
	$context['member_groups'][1] = array(
						'id' => -1,
						'name' => $txt['remove_all'],
						'is_primary' => 0,
	);
	$selects = '';
	// no primary is tricky...
	$context['member_groups'][0] = array(
						'id' => 0,
						'name' => '',
						'is_primary' => 1,
	);

	foreach($context['member_groups'] as $member_group){
		$selects .= '
									<option value="' . $member_group['id'] . '"' . ($member_group['is_primary'] ? ' selected="selected"' : '') . '>
										' . $member_group['name'] . '
									</option>';
	}
	return $selects;
}

function users_mass_action_checkExistingTriggerIP($ip_array, $fullip = '')
{
	global $smcFunc, $user_info;

	if (count($ip_array) == 4)
		$values = array(
			'ip_low1' => $ip_array[0]['low'],
			'ip_high1' => $ip_array[0]['high'],
			'ip_low2' => $ip_array[1]['low'],
			'ip_high2' => $ip_array[1]['high'],
			'ip_low3' => $ip_array[2]['low'],
			'ip_high3' => $ip_array[2]['high'],
			'ip_low4' => $ip_array[3]['low'],
			'ip_high4' => $ip_array[3]['high'],
		);
	else
		return true;

	// Again...don't ban yourself!!
	if (!empty($fullip) && ($user_info['ip'] == $fullip || $user_info['ip2'] == $fullip))
		return true;

	$request = $smcFunc['db_query']('', '
		SELECT bg.id_ban_group, bg.name
		FROM {db_prefix}ban_groups AS bg
		INNER JOIN {db_prefix}ban_items AS bi ON
			(bi.id_ban_group = bg.id_ban_group)
			AND ip_low1 = {int:ip_low1} AND ip_high1 = {int:ip_high1}
			AND ip_low2 = {int:ip_low2} AND ip_high2 = {int:ip_high2}
			AND ip_low3 = {int:ip_low3} AND ip_high3 = {int:ip_high3}
			AND ip_low4 = {int:ip_low4} AND ip_high4 = {int:ip_high4}
		LIMIT 1',
		$values
	);
	if ($smcFunc['db_num_rows']($request) != 0)
		$ret = true;
	else
		$ret = false;
	$smcFunc['db_free_result']($request);

	return $ret;
}
?>