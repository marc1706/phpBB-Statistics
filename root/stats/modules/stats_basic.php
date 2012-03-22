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
	public function get_stats()
	{
		global $db, $config, $template, $stats, $user;
		
		/** 
		* Get total stats from config
		*/
		$total_forums = $stats->total_forums();
		$total_attachments = $stats->total_attachments();
		$total_polls = $stats->total_polls();
		$total_topic_views = $stats->total_topic_views();
		
		// get board age -- always round down
		$board_age = array(
			'seconds'		=> time() - $config['board_startdate'],
			'minutes'		=> floor((time() - $config['board_startdate']) / 60),
			'hours'			=> floor((time() - $config['board_startdate']) / 3600),
			'days'			=> floor((time() - $config['board_startdate']) / 86400),
		);
		
		$forum_types = $stats->forum_type_count();
		$topic_types = $stats->topic_type_count();
		$user_accounts_data = $stats->user_accounts_data();
		
		$template->assign_vars(array(
			'TOTAL_POSTS'			=> $config['num_posts'],
			'TOTAL_TOPICS'			=> $config['num_topics'],
			'TOTAL_USERS'			=> $config['num_users'],
			'TOTAL_FORUMS'			=> $total_forums,
			'TOTAL_ATTACHMENTS'		=> $total_attachments,
			'TOTAL_POLLS'			=> $total_polls,
			'TOTAL_TOPIC_VIEWS'		=> $total_topic_views,
			'MAX_USERS'				=> $config['record_online_users'],
			'MAX_USERS_DATE'		=> $user->format_date($config['record_online_date']),
			'AVG_POSTS_PER_DAY'		=> ($board_age['days'] > 0) ? sprintf('%.2f',($config['num_posts'] / $board_age['days'])) : $config['num_posts'],
			'AVG_TOPICS_PER_DAY'	=> ($board_age['days'] > 0) ? sprintf('%.2f',($config['num_topics'] / $board_age['days'])) : $config['num_topics'],
			'AVG_REGS_PER_DAY'		=> ($board_age['days'] > 0) ? sprintf('%.2f',($config['num_users'] / $board_age['days'])) : $config['num_users'],
			'AVG_ATTACH_PER_DAY'	=> ($board_age['days'] > 0) ? sprintf('%.2f',($total_attachments / $board_age['days'])) : $total_attachments,
			'AVG_POSTS_PER_MONTH'	=> ($board_age['days'] > 0) ? sprintf('%.2f',($config['num_posts'] / ($board_age['days'] / (365 / 12)))) : $config['num_posts'], // a year has 365 days split over 12 months
			'AVG_TOPICS_PER_MONTH'	=> ($board_age['days'] > 0) ? sprintf('%.2f',($config['num_topics'] / ($board_age['days'] / (365 / 12)))) : $config['num_topics'], // a year has 365 days split over 12 months
			'TOTAL_FORUM_CAT'		=> $forum_types[FORUM_CAT],
			'TOTAL_FORUM_POST'		=> $forum_types[FORUM_POST],
			'TOTAL_FORUM_LINK'		=> $forum_types[FORUM_LINK],
			'TOTAL_POST_NORMAL'		=> $topic_types[POST_NORMAL],
			'TOTAL_POST_STICKY'		=> $topic_types[POST_STICKY],
			'TOTAL_POST_ANNOUNCE'	=> $topic_types[POST_ANNOUNCE],
			'TOTAL_POST_GLOBAL'		=> $topic_types[POST_GLOBAL],
			'UNAPPROVED_TOPICS'		=> $topic_types['unapproved'],
			'UNAPPROVED_POSTS'		=> $stats->unapproved_posts(),
			'ACTIVE_USERS'			=> $user_accounts_data['active'],
			'ACTIVE_USERS_EXPLAIN'	=> sprintf($user->lang['USERS_ACTIVE_EXPLAIN'], 30),
			'INACTIVE_USERS'		=> $user_accounts_data['inactive'],
			'INACTIVE_USERS_EXPLAIN'=> sprintf($user->lang['USERS_INACTIVE_EXPLAIN'], 30),
			'REGISTERED_BOTS'		=> $user_accounts_data['registered_bots'],
			'VISITED_BOTS'			=> $user_accounts_data['visited_bots'],
		));
		
		return 'stats_basic.html';
	}
	
	
	
}
