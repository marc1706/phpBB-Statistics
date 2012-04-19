<?php
/**
*
* @package phpBB Statistics
* @copyright (c) 2009 - 2010 Marc Alexander(marc1706) www.m-a-styles.de
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
* @translator (c) ( Marc Alexander - http://www.m-a-styles.de )
*/


if (!defined('IN_PHPBB'))
{
	exit;
}
if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'ACP_STATS_INFO'							=> 'phpBB Statistics',
	'ACP_STATS_GENERAL_INFO'					=> 'Settings',
	'ACP_STATS_GENERAL_INFO_EXPLAIN'			=> 'Thank you for choosing phpBB Statistics.',
	'ACP_STATS_MODULES_INFO'					=> 'Modules',
	'ACP_STATS_MODULES_INFO_EXPLAIN'			=> 'Manage phpBB Statistics modules.',
	'ACP_STATS_VERSION'							=> '<strong>phpBB Statistics v%s</strong>',
	'ACP_STATS_GENERAL_SETTINGS' 				=> 'General Settings',
	'ACP_STATS_GENERAL_SETTINGS_EXPLAIN'		=> 'On this page, you can change settings which concern the whole Statistics MOD',
	'ACP_STATS_MODULE_MOVE_SUCCESS'				=> 'Module moved successfully.',
	
	// settings
	'ACP_STATS_ENABLE'							=> 'Enable Statistics',
	'ACP_STATS_ENABLE_EXPLAIN'					=> 'Decide wether to enable the phpBB Statistics',
	'ACP_STATS_CACHE_TIME'						=> 'Stats cache time',
	'ACP_STATS_CACHE_TIME_EXPLAIN'				=> 'Enter the number of hours the stats should be cached. Entering 0 will disable this',
	
));
