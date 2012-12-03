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

class phpbb_stats_dbal
{
	private $db;
	private $cache;
	private $u_action;
	
	public function __construct($u_action)
	{
		global $db, $cache, $phpEx, $phpbb_root_path;
		
		$this->db = $db;
		$this->cache = $cache;
		
		$this->u_action = $u_action;
	}
	
	/**
	* sql_split_query - split query into multiple calls that will be executed
	*	after a page reload.
	*
	* @param $sql: (string) sql query to be executed. This musn't contain "LIMIT"
	* @param $limit: (int) number of queries to be executed before reloading
	* @param $sort_value: (string) value to use for sorting the return row
	*/
	public function sql_split_query($sql, $limit = 5000, $sort_value)
	{
		$start = $affected_rows = 0;
		$data = $cached_data = array();

		if (!is_int($limit))
		{
			return;
		}
		
		if (!is_string($sql) || stripos($sql, 'LIMIT'))
		{
			return;
		}
		
		$start = request_var('start', 0);
		$result = $this->db->sql_query_limit($sql, $limit, $start);
		$affected_rows = $this->db->sql_affectedrows();

		// we'll have to continue
		while ($row = $this->db->sql_fetchrow($result))
		{
			$data[$row[$sort_value]] = $row;
		}
		$this->db->sql_freeresult($result);

		$cached_data = $this->cache->get('_sql_split_query_data');

		if ($cached_data !== false)
		{
			echo 'merge ....<br />';
			// merge the arrays
			foreach ($data as $sort_a => $value)
			{
				foreach ($value as $key => $value_value)
				{
					echo "Sort_a: $sort_a<br />Key: $key<br />value_value: $value_value<br />";
					// add int values, ignore everthing else ...
					if (is_int($value_value))
					{
						$cached_data[$sort_a][$key] = $cached_data[$sort_a][$key] + $value_value;
					}
				}
			}
		}
		else
		{
			$cached_data = $data;
		}

		echo 'affected rows: ' . $affected_rows . '<br />limit: ' . $limit . '<br/>data:';
		print_r($cached_data);
		echo '<br />';
		if ($affected_rows == $limit)
		{
			// cache data, trigger an error, and refresh in 3 seconds
			$this->cache->put('_sql_split_query_data', $cached_data);
			$url = $this->u_action . '&amp;start=' . ($start + $limit);
			meta_refresh(3, $url);
			trigger_error('aquiring data:<br /><br />' . $sql .'<br /><br />');
		}
		else
		{
			$this->cache->destroy('_sql_split_query_data');
			return $cached_data;
		}
		
	}

}
