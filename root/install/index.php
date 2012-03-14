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
			array('GLOBAL_MODERATORS', 'u_view_stats'),
			array('ADMINISTRATORS', 'u_view_stats'),
			array('ADMINISTRATORS', 'a_manage_stats'),
		),

		'table_add' => array(
			array(phpbb_stats_modules, array(
				'COLUMNS' => array(
					'module_id' => array('UINT:3', '', 'auto_increment'),
					'module_classname' => array('VCHAR:64', ''),
					'module_parent' => array('UINT:3', ''),
					'module_order' => array('UINT:3', ''),
					'module_status' => array('BOOL', ''),
					'module_name' => array('VCHAR', ''),
				),

				'PRIMARY_KEY'	=> array('module_id', ''),

				'KEYS'		=> array(
					'' => array('PRIMARY', array('module_id')),
				),
			)),

			array(phpbb_stats_data, array(
				'COLUMNS' => array(
					'name' => array('VCHAR', ''),
					'data' => array('MTEXT_UNI', ''),
				),

				'PRIMARY_KEY'	=> array('name', ''),

				'KEYS'		=> array(
					'' => array('PRIMARY', array('name')),
				),
			)),

		),

		'table_column_add' => array(
			array('PROFILE_FIELDS_TABLE', 'field_stats_show', array('BOOL', '0')),
		),

		'config_add' => array(
			array('stats_enable', '1', 0),
			array('stats_advanced_pretend_version', '1', 0),
			array('stats_miscellaneous_hide_warnings', '1', 0),
			array('stats_activity_users_hide_anonymous', '1', 0),
			array('stats_resync', '1', 0),
			array('stats_resync_last_sync', '1', 0),
		),

		'module_add' => array(
			array('acp', 'ACP_CAT_DOT_MODS', 'ACP_STATS'),
			
			array('acp', 'ACP_STATS', array(
				
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
