<?php
/**
*
* @package phpBB Statistics
* @copyright (c) 2009 - 2010 Marc Alexander(marc1706) www.m-a-styles.de
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
* @based on: acp_stats.php included in the Board3 Portal package (www.board3.de)
*/

if (!defined('IN_PHPBB') || !defined('ADMIN_START'))
{
	exit;
}

/**
* @package module_install
*/
class acp_stats_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_stats',
			'title'		=> 'ACP_STATS_INFO',
			'version'	=> '2.0.0',
			'modes'		=> array(
				'settings'			=> array('title' => 'ACP_STATS_GENERAL_INFO', 'auth' => 'acl_a_board', 'cat' => array('ACP_BOARD_CONFIGURATION')),
				'modules'			=> array('title' => 'ACP_STATS_MODULES_INFO', 'auth' => 'acl_a_board', 'cat' => array('ACP_BOARD_CONFIGURATION')),
			),
		);
	}
}
