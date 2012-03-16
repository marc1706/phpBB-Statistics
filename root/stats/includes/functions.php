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

	/**
	* initialise variables for following actions
	*/
	public function __construct()
	{
		global $config;
		
		// cache time is in hours
		$this->cache_time = $config['stats_cache_time'] * 3600;
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
		
		$ret = $cache->get('stats_forum_types')
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
		
		switch($type)
		{
			case FORUM_CAT:
				return $ret[FORUM_CAT];
			break;

			case FORUM_POST:
				return $ret[FORUM_POST];
			break;

			case FORUM_LINK:
				return $ret[FORUM_LINK];
			break;
			
			default:
				return $ret;
		}
	}

	/**
	* grab all stats modules from the database
	*/
	public function obtain_modules()
	{
		global $db, $cache;
		
		$ret = $cache->get('stats_modules')
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
	* get the count of each topic type
	*
	* param $type (string):	choose wether you want the whole array or just one topic type
	* 						getting only one type will not decrease the server load
	*/
	public function topic_type_count($type = '')
	{
		global $db, $cache;
		
		$ret = $cache->get('stats_topic_types')
		if ($ret === false)
		{
			$ret = array(
				POST_NORMAL		=> 0,
				POST_STICKY		=> 0,
				POST_ANNOUNCE	=> 0,
				POST_GLOBAL		=> 0,
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
			}
			$db->sql_freeresult($result);
			
			$cache->put('stats_topic_types', $ret, $this->cache_time);
		}
		
		switch($type)
		{
			case POST_NORMAL:
				return $ret[POST_NORMAL];
			break;

			case POST_STICKY:
				return $ret[POST_STICKY];
			break;

			case POST_ANNOUNCE:
				return $ret[POST_ANNOUNCE];
			break;
			
			case POST_GLOBAL:
				return $ret[POST_GLOBAL];
			break;
			
			default:
				return $ret;
		}
	}

	/**
	* get the total number of attachments
	*/
	public function total_attachments()
	{
		global $db, $cache;
		
		$ret = $cache->get('total_attachments');
		if ($ret === false)
		{
			$sql = 'SELECT COUNT(attach_id) AS total_attachments
					FROM ' . ATTACHMENTS_TABLE;
			$result = $db->sql_query($sql);
			$ret = $db->sql_fetchfield('total_attachments');
			$db->sql_freeresult($result);
			
			$cache->put('total_attachments', $ret, $this->cache_time);
		}
		
		return $ret;
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
	* get the total number of unapproved topics
	*/
	public function unapproved_topics()
	{
		global $db, $cache;
		
		$ret = $cache->get('stats_unapproved_topics');
		if ($ret === false)
		{
			$sql = 'SELECT COUNT(DISTINCT(topic_id)) AS unapproved_topics
					FROM ' . TOPICS_TABLE . '
					WHERE topic_approved = 0';
			$result = $db->sql_query($sql);
			$ret = $db->sql_fetchfield('unapproved_topics');
			$db->sql_freeresult($result);
			
			$cache->put('stats_unapproved_topics', $ret, $this->cache_time);
		}
		
		return $ret;
	}






}
