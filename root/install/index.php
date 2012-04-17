<?php
/**
 *
 * @author marc1706 (Marc Alexander) admin@m-a-styles.de
 * @copyright (c) 2012 Marc Alexander
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

/**
 * @ignore
 */
define('UMIL_AUTO', true);
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);

include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'stats/includes/constants.' . $phpEx);
$user->session_begin();
$auth->acl($user->data);
$user->setup();


if (!file_exists($phpbb_root_path . 'umil/umil_auto.' . $phpEx))
{
	trigger_error('Please download the latest UMIL (Unified MOD Install Library) from: <a href="http://www.phpbb.com/mods/umil/">phpBB.com/mods/umil</a>', E_USER_ERROR);
}

// The name of the mod to be displayed during installation.
$mod_name = 'phpBB Statistics';

/*
* The name of the config variable which will hold the currently installed version
* UMIL will handle checking, setting, and updating the version itself.
*/
$version_config_name = 'phpbb_statistics_version';


// The language file which will be included when installing
$language_file = 'mods/info_acp_stats';


/*
* Optionally we may specify our own logo image to show in the upper corner instead of the default logo.
* $phpbb_root_path will get prepended to the path specified
* Image height should be 50px to prevent cut-off or stretching.
*/
//$logo_img = 'styles/prosilver/imageset/site_logo.gif';

/*
* The array of versions and actions within each.
* You do not need to order it a specific way (it will be sorted automatically), however, you must enter every version, even if no actions are done for it.
*
* You must use correct version numbering.  Unless you know exactly what you can use, only use X.X.X (replacing X with an integer).
* The version numbering must otherwise be compatible with the version_compare function - http://php.net/manual/en/function.version-compare.php
*/
$versions = array(
	'2.0.0RC1' => array(

		'permission_add' => array(
			array('u_view_stats', 1),
			array('a_manage_stats', 1),
		),

		'permission_set' => array(
			array('GLOBAL_MODERATORS', 'u_view_stats', 'group'),
			array('ADMINISTRATORS', 'u_view_stats', 'group'),
			array('ADMINISTRATORS', 'a_manage_stats', 'group'),
		),

		'table_add' => array(
			array(STATS_MODULES_TABLE, array(
				'COLUMNS' => array(
					'module_id' => array('UINT:3', NULL, 'auto_increment'),
					'module_classname' => array('VCHAR:64', ''),
					'module_parent' => array('UINT:3', 0),
					'module_order' => array('UINT:3', 0),
					'module_status' => array('BOOL', true),
					'module_name' => array('VCHAR', ''),
				),

				'PRIMARY_KEY'	=> 'module_id',
			)),
		),
		
		'table_row_insert' => array(
			array(STATS_MODULES_TABLE, array(
				array(
					'module_id'			=> 1,
					'module_classname'	=> '',
					'module_parent'		=> 0,
					'module_order'		=> 0,
					'module_status'		=> 1,
					'module_name'		=> 'STATS_BASIC',
				),
				array(
					'module_id'			=> 2,
					'module_classname'	=> 'stats_basic_basic',
					'module_parent'		=> 1,
					'module_order'		=> 0,
					'module_status'		=> 1,
					'module_name'		=> 'STATS_BASIC_BASIC',
				),
				array(
					'module_id'			=> 3,
					'module_classname'	=> 'stats_basic_advanced',
					'module_parent'		=> 1,
					'module_order'		=> 1,
					'module_status'		=> 1,
					'module_name'		=> 'STATS_BASIC_ADVANCED',
				),
				array(
					'module_id'			=> 4,
					'module_classname'	=> 'stats_basic_miscellaneous',
					'module_parent'		=> 1,
					'module_order'		=> 2,
					'module_status'		=> 1,
					'module_name'		=> 'STATS_BASIC_MISCELLANEOUS',
				),
				array(
					'module_id'			=> 5,
					'module_classname'	=> '',
					'module_parent'		=> 0,
					'module_order'		=> 1,
					'module_status'		=> 1,
					'module_name'		=> 'STATS_ACTIVITY',
				),
				array(
					'module_id'			=> 6,
					'module_classname'	=> 'stats_activity_forums',
					'module_parent'		=> 5,
					'module_order'		=> 0,
					'module_status'		=> 1,
					'module_name'		=> 'STATS_ACTIVITY_FORUMS',
				),
				array(
					'module_id'			=> 7,
					'module_classname'	=> 'stats_activity_topics',
					'module_parent'		=> 5,
					'module_order'		=> 1,
					'module_status'		=> 1,
					'module_name'		=> 'STATS_ACTIVITY_TOPICS',
				),
				array(
					'module_id'			=> 8,
					'module_classname'	=> 'stats_activity_users',
					'module_parent'		=> 5,
					'module_order'		=> 2,
					'module_status'		=> 1,
					'module_name'		=> 'STATS_ACTIVITY_USERS',
				),
				array(
					'module_id'			=> 9,
					'module_classname'	=> '',
					'module_parent'		=> 0,
					'module_order'		=> 2,
					'module_status'		=> 1,
					'module_name'		=> 'STATS_CONTRIBUTIONS',
				),
				array(
					'module_id'			=> 10,
					'module_classname'	=> 'stats_contributions_attachments',
					'module_parent'		=> 9,
					'module_order'		=> 0,
					'module_status'		=> 1,
					'module_name'		=> 'STATS_CONTRIBUTIONS_ATTACHMENTS',
				),
				array(
					'module_id'			=> 11,
					'module_classname'	=> 'stats_contributions_polls',
					'module_parent'		=> 9,
					'module_order'		=> 1,
					'module_status'		=> 1,
					'module_name'		=> 'STATS_CONTRIBUTIONS_POLLS',
				),
				array(
					'module_id'			=> 12,
					'module_classname'	=> '',
					'module_parent'		=> 0,
					'module_order'		=> 3,
					'module_status'		=> 1,
					'module_name'		=> 'STATS_PERIODIC',
				),
				array(
					'module_id'			=> 13,
					'module_classname'	=> 'stats_periodic_daily',
					'module_parent'		=> 12,
					'module_order'		=> 0,
					'module_status'		=> 1,
					'module_name'		=> 'STATS_PERIODIC_DAILY',
				),
				array(
					'module_id'			=> 14,
					'module_classname'	=> 'stats_periodic_monthly',
					'module_parent'		=> 12,
					'module_order'		=> 1,
					'module_status'		=> 1,
					'module_name'		=> 'STATS_PERIODIC_MONTHLY',
				),
				array(
					'module_id'			=> 15,
					'module_classname'	=> 'stats_periodic_hourly',
					'module_parent'		=> 12,
					'module_order'		=> 2,
					'module_status'		=> 1,
					'module_name'		=> 'STATS_PERIODIC_HOURLY',
				),
				array(
					'module_id'			=> 16,
					'module_classname'	=> '',
					'module_parent'		=> 0,
					'module_order'		=> 4,
					'module_status'		=> 1,
					'module_name'		=> 'STATS_SETTINGS',
				),
				array(
					'module_id'			=> 17,
					'module_classname'	=> 'stats_settings_board',
					'module_parent'		=> 16,
					'module_order'		=> 0,
					'module_status'		=> 1,
					'module_name'		=> 'STATS_SETTINGS_BOARD',
				),
				array(
					'module_id'			=> 18,
					'module_classname'	=> 'stats_settings_profile',
					'module_parent'		=> 16,
					'module_order'		=> 1,
					'module_status'		=> 1,
					'module_name'		=> 'STATS_SETTINGS_PROFILE',
				),
			)),
		),

		'table_column_add' => array(
			array(PROFILE_FIELDS_TABLE, 'field_stats_show', array('BOOL', '0')),
		),

		'config_add' => array(
			array('stats_enable', '1', 0),
			array('stats_advanced_pretend_version', '1', 0),
			array('stats_advanced_security', '0', 0),
			array('stats_miscellaneous_hide_warnings', '1', 0),
			array('stats_activity_users_hide_anonymous', '1', 0),
			array('stats_resync', '1', 0),
			array('stats_resync_last_sync', '1', 0),
			array('stats_cache_time', '24', 0),
		),

		'module_add' => array(
			array('acp', 'ACP_CAT_DOT_MODS', 'ACP_STATS'),
			
			array('acp', 'ACP_STATS_INFO', array(
				
					'module_basename'	=> 'stats',
					'module_langname'	=> 'ACP_STATS_GENERAL_INFO',
					'module_mode' 		=> 'config',
					'module_auth'		=> 'acl_a_manage_stats',
				),
			),
			
			array('acp', 'ACP_STATS', array(
					'module_basename'	=> 'stats',
					'module_langname'	=> 'ACP_STATS_MODULES',
					'module_mode'		=> 'modules',
					'module_auth'		=> 'acl_a_manage_stats',
				),
			),
		),
		// @todo: add missing modules

	),
);

// Include the UMIL Auto file, it handles the rest
include($phpbb_root_path . 'umil/umil_auto.' . $phpEx);
