<?php
/**
*
* @package phpBB Statistics
* @copyright (c) 2012 Marc Alexander(marc1706) www.m-a-styles.de
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

class stats_activity_forums_module
{
	/**
	* Default modulename
	*/
	public $name = 'STATS_ACTIVITY_FORUMS';
	
	/**
	* module-language file
	* let's you define an additional language file that needs to be loaded for this module
	* file must be in "language/{$user->lang}/mods/"
	*/
	public $language = '';
	
	
	/**
	* load stats for this module
	*/
	public function get_stats()
	{
		global $db, $config, $template, $stats, $user;

		$template->assign_vars(array(
			'TOTAL_POSTS'			=> $config['num_posts'],

		));
		
		return 'activity_forums.html';
	}
	
	/**
	* return acp settings
	*/
	public function load_acp()
	{
		return array();
	}
	
	/**
	* API Functions
	*/
	
	public function install()
	{
		return true;
	}
	
	public function uninstall()
	{
		return true;
	}
}
