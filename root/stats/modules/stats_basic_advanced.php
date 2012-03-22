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

class stats_basic_advanced_module
{
	/**
	* Default modulename
	*/
	public $name = 'STATS_BASIC_ADVANCED';
	
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
		global $db, $config, $template, $stats, $user, $phpbb_root_path, $phpEx;
		
		/** 
		* Get total stats from config
		*/
		$board_startdate = $user->format_date($config['board_startdate']);
		$total_attachments_size = get_formatted_filesize($config['upload_dir_size']);
		
		// get board age -- always round down
		$board_age = $stats->get_time_string($config['board_startdate']);
		
		// decide what we show as board version and if we show it at all
		if (!$config['stats_advanced_security'] && $config['stats_advanced_pretend_version'])
		{
			// pretend to have newest version installed
			$errstr = '';
			$errno = 0;
			
			//get and display advanced statistics
			if(!function_exists('recalc_nested_sets'))
			{
				include ("{$phpbb_root_path}includes/functions_admin.$phpEx"); //for database size
			}

			$info = get_remote_file('www.phpbb.com', '/updatecheck', ((defined('PHPBB_QA')) ? '30x_qa.txt' : '30x.txt'), $errstr, $errno);
		
			// if there is an error, fall back to displaying 3.x.x
			if ($info != false)
			{
				$info = explode("\n", $info);
				$board_version = trim($info[0]);
			}
			else
			{
				$board_version = '3.x.x';
			}
		}
		elseif (!$config['stats_advanced_security'] && !$config['stats_advanced_pretend_version'])
		{
			// Displays the exact version number, database info and database size
			$board_version = $config['version'];
		}
		else
		{
			$board_version = '';
			$template->assign_vars(array(
				'S_HIDE_BOARD_VERSION'			=> true,
				'S_HIDE_DATABASE_INFO'			=> true,
			));
		}
		
		// get avatar dir size
		$avatar_dir_size = 0;
		$total_avatars = 0;

		if ($avatar_dir = @opendir($phpbb_root_path . $config['avatar_path']))
		{
			while (($file = readdir($avatar_dir)) !== false)
			{
				if ($file[0] != '.' && $file != 'CVS' && strpos($file, 'index.') === false)
				{
					$avatar_dir_size += filesize($phpbb_root_path . $config['avatar_path'] . '/' . $file);
					++$total_avatars;
				}
			}
			closedir($avatar_dir);

			$avatar_dir_size = get_formatted_filesize($avatar_dir_size);
		}
		else
		{
			// Couldn't open Avatar dir.
			$avatar_dir_size = $user->lang['NOT_AVAILABLE'];
		}
		
		// get cached files info
		$cache_dir_size = 0;
		$total_cached_files = 0;

		if ($cache_dir = @opendir($phpbb_root_path . 'cache'))
		{
			while (($file = readdir($cache_dir)) !== false)
			{
				if ($file[0] != '.' && $file != 'CVS' && strpos($file, 'index.') === false)
				{
					$cache_dir_size += filesize($phpbb_root_path . 'cache/' . $file);
					++$total_cached_files;
				}
			}
			closedir($cache_dir);

			$cache_dir_size = get_formatted_filesize($cache_dir_size);
		}
		else
		{
			// Couldn't open cache dir.
			$cache_dir_size = $user->lang['NOT_AVAILABLE'];
		}
		
		// get all styles from the database @todo: cache this
		$sql = 'SELECT style_name AS name, style_copyright AS copyright, style_id
				FROM ' . STYLES_TABLE . '
				ORDER BY style_id ASC';
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$template->assign_block_vars('styles_row', array(
				'NAME'		=> $row['name'],
				'COPYRIGHT'	=> $row['copyright'],
			));
		}
		$db->sql_freeresult($result);
		
		// get all imagesets from the database @todo: cache this
		$sql = 'SELECT imageset_name AS name, imageset_copyright AS copyright, imageset_id
				FROM ' . STYLES_IMAGESET_TABLE . '
				ORDER BY imageset_id ASC';
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$template->assign_block_vars('imagesets_row', array(
				'NAME'		=> $row['name'],
				'COPYRIGHT'	=> $row['copyright'],
			));
		}
		$db->sql_freeresult($result);
		
		$template->assign_vars(array(
			'BOARD_STARTDATE'			=> $board_startdate,
			'BOARD_AGE'					=> $board_age,
			'BOARD_VERSION'				=> $board_version,
			'DATABASE_INFO'				=> (!empty($board_version)) ? $db->sql_server_info() : '',
			'DATABASE_SIZE'				=> (!empty($board_version)) ? get_database_size() : '',
			'GZIP_COMPRESSION'			=> (!empty($config['gzip_compress'])) ? $user->lang['ON'] : $user->lang['OFF'],
			'TOTAL_ATTACHMENTS'			=> $config['num_files'],
			'TOTAL_ATTACHMENTS_SIZE'	=> $total_attachments_size,
			'TOTAL_AVATARS'				=> $total_avatars,
			'TOTAL_AVATARS_SIZE'		=> $avatar_dir_size,
			'TOTAL_CACHED_FILES'		=> $total_cached_files,
			'CACHED_FILES_SIZE'			=> $cache_dir_size,
			
			
		));
		
		return 'basic_advanced.html';
	}
}
