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

class stats_basic_miscellaneous_module
{
	/**
	* Default modulename
	*/
	public $name = 'STATS_BASIC_MISCELLANEOUS';
	
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
		global $db, $config, $template, $stats, $user, $phpbb_root_path;
		
		/** 
		* Get total stats from config
		*/
		$smiley_count = $stats->smiley_count();
		
		// create sort by drop-down list
		$sort_by = request_var('sort', 10);
		
		$options = array(
			1		=> 1,
			3		=> 3,
			5		=> 5,
			10		=> 10,
			15		=> 15,
		);
		
		$stats->create_sort_by($options, 'sort_by_row', $sort_by);
		
		// get top smilies
		$top_smilies = $stats->top_smilies('', $sort_by);
		
		if ($top_smilies === false)
		{
			// Tell the user how far we have come -- if we are here we always have another 5000 posts processed
			$progress_info = sprintf($user->lang['RECOUNT_PROGRESS'], $user->lang['SMILEY'], request_var('start', 0) + 5000, $config['num_posts']);
			trigger_error($progress_info);
		}
		else
		{
			// get total count
			$total_smiley_count = $stats->top_smilies('total');
			if(isset($top_smilies[0]['count']))
			{					
				$max_count = $top_smilies[0]['count'];
			}
			if ($total_smiley_count > 0 && $config['num_posts'] && isset($max_count))
			{
				foreach ($top_smilies as $current_smiley)
				{
					$template->assign_block_vars('top_smilies_row', array(
						'SMILEY'					=> '<img src="' . $phpbb_root_path. '/images/smilies/' . $current_smiley['url'] . '" alt="' . $current_smiley['emotion'] . '" title="' . $current_smiley['emotion'] . '" />',
						'COUNT'						=> $current_smiley['count'],
						'PCT'						=> number_format($current_smiley['count'] / $total_smiley_count * 100, 2),
						'BARWIDTH'					=> number_format($current_smiley['count'] / $max_count * 100, 1),
					));
				}
			}
		}
		
		// get top bbcodes
		$bbcode_ary = $stats->top_bbcodes('', $sort_by);
		
		if ($bbcode_ary === false)
		{
			// Tell the user how far we have come -- if we are here we always have another 5000 posts processed
			$progress_info = sprintf($user->lang['RECOUNT_PROGRESS'], $user->lang['BBCODE'], request_var('start', 0) + 5000, $config['num_posts']);
			trigger_error($progress_info);
		}
		else
		{
			//	Make the BBCodes look nicely
			foreach ($bbcode_ary as $temp_row)
			{
				$test_result = preg_replace('/[^a-zA-Z0-9\s]/', '', $temp_row['bbcode']);
				$test_result2 = strpos($temp_row['bbcode'], '/');
				$test_result3 = strpos($temp_row['bbcode'], ':', (strlen($temp_row['bbcode']) - 1));
				if($test_result2 == 1)
				{
					$temp_row['bbcode'] = '[' . (($test_result3 > 0) ? substr($temp_row['bbcode'], 2, -1) : substr($temp_row['bbcode'], 2)) . ']' . substr($temp_row['bbcode'], 0, -1) . ']';
				}
				elseif(!empty($test_result))
				{
					$temp_row['bbcode'] = '[' . (($test_result3 > 0) ? substr($temp_row['bbcode'], 1, -1) : substr($temp_row['bbcode'], 1)) . '][/' . preg_replace('/[^a-zA-Z0-9\s]/', '', $temp_row['bbcode']) . ']';
				}
				else
				{
					$temp_row['bbcode'] = '[' . (substr($temp_row['bbcode'], 1)) . '][/' . (substr($temp_row['bbcode'], 1)) . ']';
				}
				$top_bbcodes[] = $temp_row;
			}
			
			$total_bbcodes_count = $stats->top_bbcodes('total');
			if(isset($top_bbcodes[0]['count']))
			{					
				$max_count = $top_bbcodes[0]['count'];
			}
			if ($total_bbcodes_count > 0 && $config['num_posts'] && isset($max_count))
			{
				foreach ($top_bbcodes as $current_bbcode)
				{
					$template->assign_block_vars('top_bbcodes_row', array(
						'BBCODE'					=> $current_bbcode['bbcode'],
						'COUNT'						=> $current_bbcode['count'],
						'PCT'						=> number_format($current_bbcode['count'] / $total_bbcodes_count * 100, 2),
						'BARWIDTH'					=> number_format($current_bbcode['count'] / $max_count * 100, 1),
					));
				}
			}
			
		}
		
		// get custom bbcodes count
		$total_bbcodes = sizeof($stats->top_bbcodes('', 9999));
		$total_custom_bbcodes = $total_bbcodes - 13; // standard amount of bbcodes is 13
		
		// get top icons
		$total_icons = 0;
		$total_icons = $stats->top_icons('total');
		if ($total_icons > 0)
		{
			$top_icons = $stats->top_icons('', $sort_by);
			$max_count = $top_icons[0]['count'];
			foreach ($top_icons as $current_icon)
			{					
				$template->assign_block_vars('top_icons_row', array(	
				'ICON'						=> '<img src="' . $phpbb_root_path . '/images/icons/' . $current_icon['icon_url'] . '" alt="' . $current_icon['icon_url'] . '" />',
				'COUNT'						=> $current_icon['count'],
				'PCT'						=> number_format($current_icon['count'] / $total_icons * 100, 2),
				'BARWIDTH'					=> number_format($current_icon['count'] / $max_count * 100, 1),
				));
			}
		}
		
		
		$template->assign_vars(array(
			'TOTAL_SMILIES'			=> $smiley_count['total'],
			'SMILIES_DOP_COUNT'		=> (!empty($smiley_count['total'])) ? $smiley_count['dop'] . ' / ' . $smiley_count['total'] .  ' (' . number_format($smiley_count['dop'] / $smiley_count['total'] * 100, 2) . '%)' : ' 0 / 0',
			'SORT_BY_PROMPT'		=> sprintf($user->lang['LIMIT_PROMPT'], $user->lang['SMILEY'] . ', ' . $user->lang['BBCODE'] . ', ' . $user->lang['ICONS']),
			'TOP_SMILIES'			=> sprintf($user->lang['TOP_SMILIES_BY_URL'], $sort_by),
			'TOP_BBCODES'			=> sprintf($user->lang['TOP_BBCODES'], $sort_by),
			'TOP_ICONS'				=> sprintf($user->lang['TOP_ICONS'], $sort_by),
			'TOTAL_BBCODES'			=> $total_bbcodes,
			'TOTAL_BBCODES_CUSTOM'	=> $total_custom_bbcodes,
		));
		
		return 'basic_miscellaneous.html';
	}

	/**
	* return acp settings
	*/
	public function load_acp()
	{
		return array(
			'title'	=> 'ACP_BASIC_MISCELLANEOUS_SETTINGS',
			'vars'	=> array(
				'legend3'							=> 'ACP_BASIC_MISCELLANEOUS_SETTINGS',
				'stats_basic_miscellaneous_hide_warnings'	=> array('lang' => 'ACP_BASIC_MISCELLANEOUS_WARNINGS'  , 'validate' => 'bool'  , 'type' => 'radio:yes_no'  , 'explain' => true),
			)
		);
	}
	
	/**
	* API Functions
	*/
	
	public function install()
	{
		set_config('stats_basic_miscellaneous_hide_warnings', 0);
		
		return true;
	}
	
	public function uninstall()
	{
		$sql = 'DELETE FROM ' . CONFIG_TABLE . "
				WHERE config_name = 'stats_basic_miscellaneous_hide_warnings'";
		$db->sql_query($sql);
		
		return true;
	}
	
}
