<?php
/**
*
* @package phpBB Statistics
* @copyright (c) 2009 - 2010 Marc Alexander(marc1706) www.m-a-styles.de
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
* @based on: info_acp_portal.php included in the Board3 Portal package (www.board3.de)
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
	'ACP_STATS_GENERAL_INFO'					=> 'Einstellungen',
	'ACP_STATS_ADDONS'							=> 'Add-Ons',
));
