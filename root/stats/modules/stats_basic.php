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

class stats_basic_module
{
	/**
	* Default modulename
	*/
	public $name = 'STATS_BASIC';
	
	/**
	* module-language file
	* let's you define an additional language file that needs to be loaded for this module
	* file must be in "language/{$user->lang}/mods/"
	*/
	public $language = '';
	
	
	/**
	* load stats for this module
	*/
	public function get_stats($module_id)
	{
		global $db, $config, $temlate, $stats;
		
		/** 
		* Get total stats from config
		*/
		$total_forums = $stats->total_forums();
		$total_attachments = $stats->total_attachments();
		
		
		
		
		
		$template->assign_vars(array(
			'TOTAL_POSTS'		=> $config['num_posts'],
			'TOTAL_TOPICS'		=> $config['num_topics'],
			'TOTAL_USERS'		=> $config['num_users'],
			
		
		));
		
		
	}
	
	
	
}
