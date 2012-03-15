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










}
