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

/**
* future functions should be ordered by their name (a-z)
*/

class phpbb_stats
{
	private $cache_time;
	public $modules;
	public $stats_link;

	/**
	* initialise variables for following actions
	*/
	public function __construct()
	{
		global $config, $phpbb_root_path, $phpEx;
		
		// cache time is in hours
		$this->cache_time = $config['stats_cache_time'] * 3600;
		
		// append_sid will be run later
		$this->stats_link = $phpbb_root_path . 'stats.' . $phpEx;
	}
	
	/**
	*
	* all functions follow below
	*
	*/
	
	/**
	* get the count of each forum type
	*
	* param $type (string):	choose wether you want the whole array or just one forum type
	* 						getting only one type will not decrease the server load
	*/
	public function forum_type_count($type = '')
	{
		global $db, $cache;
		
		$ret = $cache->get('stats_forum_types');
		if ($ret === false)
		{
			$ret = array(
				FORUM_CAT	=> 0,
				FORUM_POST	=> 0,
				FORUM_LINK	=> 0,
			);

			$sql = 'SELECT * FROM ' . FORUMS_TABLE;
			$result = $db->sql_query($sql);
			
			while ($row = $db->sql_fetchrow($result))
			{
				if (isset($ret[$row['forum_type']]))
				{
					++$ret[$row['forum_type']];
				}
				else
				{
					// other mods might have added additional forum types
					$ret[$row['forum_type']] = 1;
				}
			}
			$db->sql_freeresult($result);
			
			$cache->put('stats_forum_types', $ret, $this->cache_time);
		}
		
		if (!empty($type) && isset($ret[$type]))
		{
			return $ret[$type];
		}
		else
		{
			return $ret;
		}
	}
	
	/**
	* function get_time_string --- returns the formatted time string like 3 months 20 days etc.
	* @param $time : the timestamp
	* Copyright (c) TheUniqueTiger - Nayan Ghosh
	*/
	public function get_time_string($time, $current = 0)
	{
		global $user;
		$time_ary = getdate($time);	
		$current_time_ary = array();
		$diff_ary = array(
			'seconds' 			=> 0,
			'minutes'			=> 0,
			'hours'				=> 0,
			'days'				=> 0,
			'months'			=> 0,
			'years'				=> 0
		);
		$decrement_ary = array(
			'seconds' 			=> false,
			'minutes'			=> false,
			'hours'				=> false,
			'days'				=> false,
			'months'			=> false,
			'years'				=> false
		);
		if (!$current)
		{
			$current = time();
		}
		$temp_time_ary = $time_ary;
		if (isset($current)) //do subtraction and get the difference
		{
			$current_time_ary = getdate($current);
			
			//do seconds
			$diff_ary['seconds'] = $current_time_ary['seconds'] - $time_ary['seconds'];
			if ($diff_ary['seconds'] < 0)
			{
				$diff_ary['seconds'] = 60 + $diff_ary['seconds'];
				$decrement_ary['minutes'] = true;
			}
			
			//do minutes
			$diff_ary['minutes'] = $current_time_ary['minutes'] - $time_ary['minutes'];
			if (($decrement_ary['minutes']) == true)
			{
				--$diff_ary['minutes'];
			}
			if ($diff_ary['minutes'] < 0)
			{
				$diff_ary['minutes'] = 60 + $diff_ary['minutes'];
				$decrement_ary['hours'] = true;
			}
			
			//do hours
			$diff_ary['hours'] = $current_time_ary['hours'] - $time_ary['hours'];
			if (($decrement_ary['hours']) == true)
			{
				--$diff_ary['hours'];
			}
			if ($diff_ary['hours'] < 0)
			{
				$diff_ary['hours'] = 24 + $diff_ary['hours'];
				$decrement_ary['days'] = true;
			}
			
			//do days
			$diff_ary['days'] = $current_time_ary['mday'] - $time_ary['mday'];
			if (($decrement_ary['days'])  == true)
			{
				--$diff_ary['days'];
			}
			if ($diff_ary['days'] < 0)
			{
				$diff_ary['days'] = 30 + $diff_ary['days'];
				$decrement_ary['months'] = true;
			}
			
			//do months
			$diff_ary['months'] = $current_time_ary['mon'] - $time_ary['mon'];
			if (($decrement_ary['months']) == true)
			{
				--$diff_ary['months'];
			}
			if ($diff_ary['months'] < 0)
			{
				$diff_ary['months'] = 12 + $diff_ary['months'];
				$decrement_ary['years'] = true;
			}
			
			//do years
			$diff_ary['years'] = $current_time_ary['year'] - $time_ary['year'];
			if (($decrement_ary['years'])  == true)
			{
				--$diff_ary['years'];
			}
			
		}
		$result = '';	
		$result .= (isset($diff_ary['years'])) ? $diff_ary['years'] . ' ' . (($diff_ary['years'] > 1) ? $user->lang['YEARS'] . ' ' : $user->lang['YEAR'] . ' ') : '';
		$result .= (isset($diff_ary['months'])) ? $diff_ary['months'] . ' ' . (($diff_ary['months'] > 1) ? $user->lang['MONTHS'] . ' ' : $user->lang['MONTH'] . ' ') : '';
		$result .= (isset($diff_ary['days'])) ? $diff_ary['days'] . ' ' . (($diff_ary['days'] > 1) ? $user->lang['DAYS'] . ' ' : $user->lang['DAY'] . ' ') : '';
		$result .= (isset($diff_ary['hours'])) ? $diff_ary['hours'] . ' ' . (($diff_ary['hours'] > 1) ? $user->lang['HOURS'] . ' ' : $user->lang['HOUR'] . ' ') : '';
		$result .= (isset($diff_ary['minutes'])) ? $diff_ary['minutes'] . ' ' . (($diff_ary['minutes'] > 1) ? $user->lang['MINUTES'] . ' ' : $user->lang['MINUTE'] . ' ') : '';
		$result .= (isset($diff_ary['seconds'])) ? $diff_ary['seconds'] . ' ' . (($diff_ary['seconds'] > 1) ? $user->lang['SECONDS'] . ' ' : $user->lang['SECOND'] . ' ') : '';
		
		return $result;
	}

	/**
	* grab all stats modules from the database
	*/
	public function obtain_modules()
	{
		global $db, $cache;
		
		$ret = $cache->get('stats_modules');
		if ($ret === false)
		{
			$sql = 'SELECT * FROM ' . STATS_MODULES_TABLE . ' ORDER BY module_id ASC';
			$result = $db->sql_query($sql);
			
			while ($row = $db->sql_fetchrow($result))
			{
				$ret[] = $row;
			}
			$db->sql_freeresult($result);
			
			$cache->put('stats_modules', $ret, $this->cache_time);
		}
		
		$this->modules = $ret;
	}
	
	/**
	* parse the menu
	*
	* param $module_id (int): the currently active module
	*/
	public function parse_menu($module)
	{
		global $template, $user;

		if (!is_array($module))
		{
			// @todo: add real error message
			trigger_error('GENERAL_ERROR');
		}
		
		foreach ($this->modules as $cur_module)
		{
			// we have a parent module
			if ($cur_module['module_parent'] == false)
			{
				$template->assign_block_vars('t_block1', array(
					'U_TITLE'		=> append_sid($this->stats_link, 'p=' . $cur_module['module_id']),
					'L_TITLE'		=> (isset($user->lang[$cur_module['module_name']])) ? $user->lang[$cur_module['module_name']] : $cur_module['module_name'],
					'S_SELECTED'	=> ($module['module_parent'] == $cur_module['module_id']) ? true : false,
				));
			}
			// and we have a sub-module
			elseif ($cur_module['module_parent'] == $module['module_parent'])
			{
				$template->assign_block_vars('t_block2', array(
					'U_TITLE'		=> append_sid($this->stats_link, 'p=' . $cur_module['module_parent'] . '&amp;id=' . $cur_module['module_id']),
					'L_TITLE'		=> (isset($user->lang[$cur_module['module_name']])) ? $user->lang[$cur_module['module_name']] : $cur_module['module_name'],
					'S_SELECTED'	=> ($module['module_id'] == $cur_module['module_id']) ? true : false,
				));
			}
		}
	}
	
	/**
	* get the count of each topic type
	*
	* param $type (string):	choose wether you want the whole array or just one topic type
	* 						getting only one type will not decrease the server load
	*/
	public function topic_type_count($type = '')
	{
		global $db, $cache;
		
		$ret = $cache->get('stats_topic_types');
		if ($ret === false)
		{
			$ret = array(
				POST_NORMAL		=> 0,
				POST_STICKY		=> 0,
				POST_ANNOUNCE	=> 0,
				POST_GLOBAL		=> 0,
				'unapproved'	=> 0,
			);

			$sql = 'SELECT * FROM ' . TOPICS_TABLE;
			$result = $db->sql_query($sql);
			
			while ($row = $db->sql_fetchrow($result))
			{
				if (isset($ret[$row['topic_type']]))
				{
					++$ret[$row['topic_type']];
				}
				else
				{
					// other mods might have added additional topic types
					$ret[$row['topic_type']] = 1;
				}
				
				if ($row['topic_approved'] == false)
				{
					++$ret['unapproved'];
				}
			}
			$db->sql_freeresult($result);
			
			$cache->put('stats_topic_types', $ret, $this->cache_time);
		}
		
		if (!empty($type) && isset($ret[$type]))
		{
			return $ret[$type];
		}
		else
		{
			return $ret;
		}
	}

	/**
	* get the total number of forums
	*/
	public function total_forums()
	{
		global $db, $cache;
		
		$ret = $cache->get('total_forums');
		if ($ret === false)
		{
			$sql = 'SELECT COUNT(forum_id) AS total_forums
					FROM ' . FORUMS_TABLE;
			$result = $db->sql_query($sql);
			$ret = $db->sql_fetchfield('total_forums');
			$db->sql_freeresult($result);
			
			$cache->put('total_forums', $ret, $this->cache_time);
		}
		
		return $ret;
	}

	/**
	* get the total number of polls
	*/
	public function total_polls()
	{
		global $db, $cache;
		
		$ret = $cache->get('total_polls');
		if ($ret === false)
		{
			$sql = 'SELECT COUNT(DISTINCT(topic_id)) AS total_polls
					FROM ' . POLL_OPTIONS_TABLE;
			$result = $db->sql_query($sql);
			$ret = $db->sql_fetchfield('total_polls');
			$db->sql_freeresult($result);
			
			$cache->put('total_polls', $ret, $this->cache_time);
		}
		
		return $ret;
	}
	
	/**
	* get the total topic views in all topics
	*/
	public function total_topic_views()
	{
		global $db, $cache;
		
		$ret = $cache->get('total_topic_views');
		if ($ret === false)
		{
			$sql = 'SELECT SUM(topic_views) AS total_topic_views
					FROM ' . TOPICS_TABLE;
			$result = $db->sql_query($sql);
			$ret = $db->sql_fetchfield('total_topic_views');
			$db->sql_freeresult($result);
			
			$cache->put('total_topic_views', $ret, $this->cache_time);
		}
		
		return $ret;
	}

	/**
	* get the total number of unapproved posts
	*/
	public function unapproved_posts()
	{
		global $db, $cache;
		
		$ret = $cache->get('stats_unapproved_posts');
		if ($ret === false)
		{
			$sql = 'SELECT COUNT(DISTINCT(post_id)) AS unapproved_posts
					FROM ' . POSTS_TABLE . '
					WHERE post_approved = 0';
			$result = $db->sql_query($sql);
			$ret = $db->sql_fetchfield('unapproved_posts');
			$db->sql_freeresult($result);
			
			$cache->put('stats_unapproved_posts', $ret, $this->cache_time);
		}
		
		return $ret;
	}

	/**
	* get the count of each user type
	* we only count active/inactive users and bots that have visited/haven't visited
	*
	* param $type (string):	choose wether you want the whole array or just one type
	* 						getting only one type will not decrease the server load
	*/
	public function user_accounts_data($type = '')
	{
		global $db, $cache, $config;
		
		$ret = $cache->get('stats_user_accounts_data');
		if ($ret === false)
		{
			$ret = array(
				'active'			=> 0,
				'inactive'			=> 0,
				'visited_bots'		=> 0,
				'registered_bots'	=> 0,
			);
			
			// user last visit shouldn't be more than 30 days away in order to call him/her active
			// @todo: should be an ACP option
			$last_visit_min = time() - (30 * 86400);

			$sql = 'SELECT COUNT(user_id) AS active_users 
					FROM ' . USERS_TABLE . '
					WHERE user_lastvisit >= ' . $last_visit_min . '
					AND ' . $db->sql_in_set('user_type', array(USER_NORMAL, USER_FOUNDER));
			$result = $db->sql_query($sql);
			$ret['active'] = $db->sql_fetchfield('active_users');
			$ret['inactive'] = $config['num_users'] - $ret['active'];
			$db->sql_freeresult($result);
			
			$sql = 'SELECT user_id AS user_id, user_lastvisit AS visited
					FROM ' . USERS_TABLE . '
					WHERE user_type = ' . USER_IGNORE . '
					AND user_id <> ' . ANONYMOUS;
			$result = $db->sql_query($sql);
			while ($row = $db->sql_fetchrow($result))
			{
				if ($row['visited'] != false)
				{
					++$ret['visited_bots'];
					++$ret['registered_bots'];
				}
				else
				{
					++$ret['registered_bots'];
				}
			}
			$db->sql_freeresult($result);
			
			$cache->put('stats_user_accounts_data', $ret, $this->cache_time);
		}
		
		if (!empty($type) && isset($ret[$type]))
		{
			return $ret[$type];
		}
		else
		{
			return $ret;
		}
	}





}
