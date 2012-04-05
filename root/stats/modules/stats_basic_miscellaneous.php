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
		
		if (empty($top_smilies))
		{
			// Tell the user how far we have come
			$progress_info = sprintf($user->lang['RECOUNT_PROGRESS'], request_var('start', 0), $config['num_posts']);
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
				foreach ($top_smilies as $url => $current_smiley)
				{
					$template->assign_block_vars('top_smilies_row', array(
						'SMILEY'					=> '<img src="' . $phpbb_root_path. '/images/smilies/' . $url . '" alt="' . $current_smiley['emotion'] . '" title="' . $current_smiley['emotion'] . '" />',
						'COUNT'						=> $current_smiley['count'],
						'PCT'						=> number_format($current_smiley['count'] / $total_smiley_count * 100, 2),
						'BARWIDTH'					=> number_format($current_smiley['count'] / $max_count * 100, 1),
					));
				}
			}
		}
		
		
		
		$template->assign_vars(array(
			'TOTAL_SMILIES'			=> $smiley_count['total'],
			'SMILIES_DOP_COUNT'		=> (!empty($smiley_count['total'])) ? $smiley_count['dop'] . ' / ' . $smiley_count['total'] .  ' (' . number_format($smiley_count['dop'] / $smiley_count['total'] * 100, 2) . '%)' : ' 0 / 0',
			'SORT_BY_PROMPT'		=> sprintf($user->lang['LIMIT_PROMPT'], $user->lang['SMILEY'] . ', ' . $user->lang['BBCODE'] . ', ' . $user->lang['ICONS']),
			'TOP_SMILIES'			=> sprintf($user->lang['TOP_SMILIES_BY_URL'], $sort_by)
		));
		
		return 'basic_miscellaneous.html';
	}
}
