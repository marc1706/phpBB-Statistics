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

		// create sort by drop-down list
		$sort_by = request_var('top_ct', 10);

		$options = array(
			1		=> 1,
			3		=> 3,
			5		=> 5,
			10		=> 10,
			15		=> 15,
		);
		
		$stats->create_sort_by($options, 'top_ct_row', $sort_by);
		
		// get total forum counts
		$forum_types = $stats->forum_type_count();
		$total_forums = $forum_types[FORUM_CAT] + $forum_types[FORUM_POST] + $forum_types[FORUM_LINK];
		
		// get top forums by topics

		$template->assign_vars(array(
			'TOTAL_FORUMS'			=> $total_forums,
			'TOTAL_CAT_FORUMS'		=> $forum_types[FORUM_CAT],
			'TOTAL_POSTING_FORUMS'	=> $forum_types[FORUM_POST],
			'TOTAL_LINK_FORUMS'		=> $forum_types[FORUM_LINK],
			'SORT_BY_PROMPT'		=> sprintf($user->lang['LIMIT_PROMPT'], $user->lang['FORUMS']),

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
