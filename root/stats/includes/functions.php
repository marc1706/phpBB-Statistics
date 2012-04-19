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
	public $u_action;

	/**
	* initialise variables for following actions
	*/
	public function __construct()
	{
		global $config, $phpbb_root_path, $phpbb_stats_path, $phpEx;
		
		// include the constants file
		if (!defined('STATS_MODULES_TABLE'))
		{
			include($phpbb_root_path . 'stats/includes/constants.' . $phpEx);
		}
		
		// cache time is in hours
		$this->cache_time = $config['stats_cache_time'] * 3600;
		
		// append_sid will be run later
		$this->stats_link = $phpbb_root_path . 'stats.' . $phpEx;
		
		// this should be overwritten by stats.php and is just a fallback for other mods
		$this->u_action = append_sid($this->stats_link);
	}
	
	/**
	*
	* all functions follow below
	*
	*/
	
	/** create sort by drop-down box from specified options
	*
	* @param $options (array): holds the options data
	*	the options array needs to have the following structure:
	*	array => (
	*		'option1_value' => 'option1_name',
	*	)
	*
	* @param $tpl_row (string): the name of the template row this should be assigned to
	* @param $selected (string): the value of the currently selected option
	*
	*/
	public function create_sort_by($options = array(), $tpl_row = 'sort_by_row', $selected = '')
	{
		global $template;
		
		if (empty($options))
		{
			return;
		}
		
		foreach ($options as $value => $name)
		{
			$template->assign_block_vars($tpl_row, array(
				'S_IS_SELECTED'		=> ($value == $selected) ? true : false,
				'VALUE'				=> $value,
				'NAME'				=> $name,
			));
		}
	}
	
	/**
	* get the count of each forum type
	*
	* @param $type (string):	choose wether you want the whole array or just one forum type
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
	* move a module
	*
	* @param $type (string): the desired action type
	* @param $module (array): contains the module row of the specified module
	*/
	public function move_module($type = '', $module, $redirect = '')
	{
		global $db, $cache, $user;
		
		if (empty($type))
		{
			return;
		}
		
		if ($module['module_parent'] == 0)
		{
			$sql_where = ' AND module_parent = 0';
		}
		else
		{
			$sql_where = ' AND module_parent = ' . $module['module_parent'];
		}
		
		switch ($type)
		{
			case 'up':
				$sql = 'UPDATE ' . STATS_MODULES_TABLE . '
						SET module_order = module_order + 1
						WHERE module_order = ' . ($module['module_order'] - 1) . $sql_where;
				$result = $db->sql_query($sql);
				$db->sql_freeresult($result);
				
				$sql = 'UPDATE ' . STATS_MODULES_TABLE . '
						SET module_order = module_order - 1
						WHERE module_id = ' . $module['module_id'];
				$result = $db->sql_query($sql);
				$db->sql_freeresult($result);
			break;
			case 'down':
				$sql = 'UPDATE ' . STATS_MODULES_TABLE . '
						SET module_order = module_order - 1
						WHERE module_order = ' . ($module['module_order'] + 1) . $sql_where;
				$result = $db->sql_query($sql);
				$db->sql_freeresult($result);
				
				$sql = 'UPDATE ' . STATS_MODULES_TABLE . '
						SET module_order = module_order + 1
						WHERE module_id = ' . $module['module_id'];
				$result = $db->sql_query($sql);
				$db->sql_freeresult($result);
			break;
			default:
				return;
		}
		
		$cache->destroy('stats_modules');
		
		if (empty($redirect))
		{
			$redirect = $this->u_action;
		}
		
		meta_refresh(3, $redirect);
		trigger_error($user->lang['ACP_STATS_MODULE_MOVE_SUCCESS'] . adm_back_link($redirect));
		
		return true; // might be used later on, or we do error handling in here
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
	* @param $module_id (int): the currently active module
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
	* get the number of smilies installed on the board 
	*
	* @param $type (string): decide which data to return
	*/
	public function smiley_count($type = '')
	{
		global $db, $cache;
	
		$ret = $cache->get('stats_smiley_count');
		
		if ($ret === false)
		{
			$ret = array(
				'total' => 0,
				'dop'	=> 0, // display on posting
			);

			$sql = 'SELECT DISTINCT(smiley_url) AS count, display_on_posting AS dop
					FROM ' . SMILIES_TABLE;
			$result = $db->sql_query($sql);
			while($row = $db->sql_fetchrow($result))
			{
				++$ret['total'];
				if($row['dop'])
				{
					++$ret['dop'];
				}
			}
			$db->sql_freeresult($result);

			$cache->put('stats_smiley_count', $ret, $this->cache_time);
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
	* get the count of each topic type
	*
	* @param $type (string):	choose wether you want the whole array or just one topic type
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
	* get top bbcodes by count
	*
	* Copyright (c) 2009 - 2012 Marc Alexander(marc1706) www.m-a-styles.de
	*
	* @param $limit (int): the top xx bbcodes that should be returned
	*/
	public function top_bbcodes($type = '', $limit = 0)
	{
		global $db, $phpbb_root_path, $phpEx, $cache;
		
		$post_ary = $bbcode_ary = array();
		
		$update = request_var('update_bbcode', false);
		
		$ret = $cache->get('stats_top_bbcodes');
		
		if ($ret === false)
		{
			//	We need some BBCode information
			$bbcodes = array();
			$bbcode_ary[0] = array('bbcode' => '[/b:', 'count' => 0);
			$bbcode_ary[1] = array('bbcode' => '[/attachment:', 'count' => 0);
			$bbcode_ary[2] = array('bbcode' => '[/code:', 'count' => 0);
			//$bbcode_ary[3] = array('bbcode' => '[*', 'count' => 0);
			$bbcode_ary[4] = array('bbcode' => '[/size:', 'count' => 0);
			$bbcode_ary[5] = array('bbcode' => '[/i:', 'count' => 0);
			$bbcode_ary[6] = array('bbcode' => '[list:', 'count' => 0);
			$bbcode_ary[7] = array('bbcode' => '[/img:', 'count' => 0);
			$bbcode_ary[8] = array('bbcode' => '[list=', 'count' => 0);
			$bbcode_ary[9] = array('bbcode' => '[/quote:', 'count' => 0);
			$bbcode_ary[10] = array('bbcode' => '[/color:', 'count' => 0);
			$bbcode_ary[11] = array('bbcode' => '[/u:', 'count' => 0);
			$bbcode_ary[12] = array('bbcode' => '[/url:', 'count' => 0);
			$bbcode_ary[13] = array('bbcode' => '[/flash:', 'count' => 0);
				
			// now get the custom BBCodes
			$sql = 'SELECT bbcode_tag AS tag
					FROM ' . BBCODES_TABLE;
			$result = $db->sql_query($sql);
			while ($bbcode_row = $db->sql_fetchrow($result))
			{
				if(preg_match ('/[^a-z]/i', $bbcode_row['tag']))
				{
					$bbcode_row['tag'] = preg_replace('/[^a-zA-Z0-9\s]/', '', $bbcode_row['tag']);
				}
				
				//Make sure we don't get any duplicates
				if(!in_array(array('bbcode' => '[/' . strtolower($bbcode_row['tag']) . ':', 'count' => 0), $bbcode_ary))
				{
					$bbcode_ary[] = array('bbcode' => '[/' . strtolower($bbcode_row['tag']) . ':', 'count' => 0);
				}
			}	
			$db->sql_freeresult($result);
			
			$update = true;
		}
		else
		{
			$bbcode_ary = $ret;
		}
		
		if ($update)
		{
			$start = request_var('start_bbcode', 0);

			//	first we have to get all posts from the database
			$sql = 'SELECT post_text 
					FROM ' . POSTS_TABLE . '
					ORDER BY post_id ASC';
			$result = $db->sql_query_limit($sql, 5000, $start);
			$affected_rows = $db->sql_affectedrows();
			while ($row = $db->sql_fetchrow($result))
			{	
				$text = $row['post_text'];
				
				/**	strip the bbcodes
				*	we can't use preg_match_all here, since that will just parse everything that looks like a bbcode
				*/
				foreach($bbcode_ary as $key => $current_bbcode)
				{
					$bbcode_ary[$key]['count'] = $bbcode_ary[$key]['count'] + ((strlen($text) - strlen(str_replace($current_bbcode['bbcode'], '', $text))) / strlen($current_bbcode['bbcode']));
				}
			}			
			$db->sql_freeresult($result);
			
			// put the bbcode data into an array
			if (!empty($bbcode_ary))
			{
				$count_ary = array();
				foreach ($bbcode_ary as $key => $cur)
				{
					$count_ary[$key] = $cur['count'];
				}
				array_multisort($count_ary, SORT_DESC, $bbcode_ary);
			}
			$cache->put('stats_top_bbcodes', $bbcode_ary, $this->cache_time);
			
			if($affected_rows == 5000) // set this to the limit number of the post_text sql query
			{
				$url = $this->u_action . '&amp;update_bbcode=1&amp;start_bbcode=' . ($start + 5000); // add the limit number to $start
				meta_refresh(5, $url); // time is set to 5 seconds -- that should be enough for 5000 posts
				return false; // Tell script that we need a refresh
			}
			else
			{
				// clean the URL
				redirect($this->u_action);
			}
		}
		
		if (empty($type))
		{
			return array_slice($bbcode_ary, 0, $limit, true);
		}
		else // currently also return the total bbcode count
		{
			$total_count = 0;
			foreach ($bbcode_ary as $data)
			{
				$total_count = $total_count + $data['count'];
			}
			
			return $total_count;
		}
	}
	
	/**
	* get top icons by count
	*
	* @param $type (string): the return type ('' = all data, everything else = total count)
	* @param $limit (int): the top xx icons that should be returned
	*/
	public function top_icons($type= '', $limit = 0)
	{
		global $db, $cache;
		
		$ret = $cache->get('stats_top_icons');
		
		if ($ret === false)
		{
			$sql = 'SELECT COUNT(t.topic_id) AS count, t.icon_id AS icon_id, i.icons_url AS icon_url
					FROM ' . TOPICS_TABLE . ' t, ' . ICONS_TABLE . ' i
					WHERE icon_id = i.icons_id
						AND i.display_on_posting = 1
						AND t.topic_approved = 1
					GROUP BY icon_id
					ORDER BY count DESC';
			$result = $db->sql_query($sql);
			while ($row = $db->sql_fetchrow($result))
			{
				$ret[] = $row;
			}			
			$db->sql_freeresult($result);
			
			$cache->put('stats_top_icons', $ret, $this->cache_time);
		}
		
		if (empty($type))
		{
			return array_slice($ret, 0, $limit, true);
		}
		else // currently also return the total smiley count
		{
			$total_count = 0;
			foreach ($ret as $data)
			{
				if ($data['count'] == 0)
				{
					break; // stop if we reach an element with count 0
				}
				$total_count = $total_count + $data['count'];
			}
			
			return $total_count;
		}
	}
	
	/**
	* get top smilies by count
	*
	* Copyright (c) 2009 - 2012 Marc Alexander(marc1706) www.m-a-styles.de
	*
	* @param $type (string): the return type ('' = all data, everything else = total count)
	* @param $limit (int): the top xx smilies that should be returned
	*/
	public function top_smilies($type = '', $limit = 0)
	{
		global $db, $phpbb_root_path, $phpEx, $cache;
		
		$post_ary = $smilies = array();
		
		$update = request_var('update_smiley', false);
		
		$ret = $cache->get('stats_top_smilies');
		
		if ($ret === false)
		{
			// Now we also need some Smiley information
			$sql = 'SELECT DISTINCT(smiley_url) AS url, emotion AS emotion
					FROM ' . SMILIES_TABLE;
			$result = $db->sql_query($sql);
			while ($row = $db->sql_fetchrow($result))
			{
				if(!isset($smilies[$row['url']]))
				{
					$smilies[] = array(
						'url'		=> $row['url'],
						'count'		=> 0,
						'emotion'	=> $row['emotion'],
					);
				}
			}
			$db->sql_freeresult($result);
			
			$update = true;
		}
		else
		{
			$smilies = $ret;
		}
		
		if ($update)
		{
			$start = request_var('start_smiley', 0);

			//	first we have to get all posts from the database
			$sql = 'SELECT post_text 
					FROM ' . POSTS_TABLE . '
					ORDER BY post_id ASC';
			$result = $db->sql_query_limit($sql, 5000, $start);
			$affected_rows = $db->sql_affectedrows();
			while ($row = $db->sql_fetchrow($result))
			{	
				$text = $row['post_text'];
				
				/**
				* strip the smilies
				* unfortunately, we can't use preg_match_all anymore, since that stupid function also gets inline attachments
				*/
				foreach($smilies as $key => $data)
				{
					$smilies[$key]['count'] = $data['count'] + ((strlen($text) - strlen(str_replace($data['url'], '', $text))) / strlen($data['url']));	
				}
			}			
			$db->sql_freeresult($result);
			
			// put the smilies data into an array
			if (!empty($smilies))
			{
				$count_ary = $url_ary = $emotion_ary = array();
				foreach ($smilies as $key => $cur)
				{
					$count_ary[$key] = $cur['count'];
				}
				array_multisort($count_ary, SORT_DESC, $smilies);
			}
			$cache->put('stats_top_smilies', $smilies, $this->cache_time);
			
			if($affected_rows == 5000) // set this to the limit number of the post_text sql query
			{
				$url = $this->u_action . '&amp;update_smiley=1&amp;start_smiley=' . ($start + 5000); // add the limit number to $start
				meta_refresh(5, $url); // time is set to 5 seconds -- that should be enough for 5000 posts
				return false; // Tell script that we need a refresh
			}
			else
			{
				// clean the URL
				redirect($this->u_action);
			}
		}
		
		if (empty($type))
		{
			return array_slice($smilies, 0, $limit, true);
		}
		else // currently also return the total smiley count
		{
			$total_count = 0;
			foreach ($smilies as $data)
			{
				$total_count = $total_count + $data['count'];
			}
			
			return $total_count;
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
	* @param $type (string):	choose wether you want the whole array or just one type
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
