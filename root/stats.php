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
**/
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpbb_stats_path = $phpbb_root_path . 'stats/';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_stats_path . 'includes/functions.' . $phpEx);

// Start session
$user->session_begin();
$auth->acl($user->data);
$user->setup();

/**
* check if user has auth to view the stats
*/
if (!$auth->acl_get('u_view_stats'))
{
	trigger_error('NOT_AUTHORISED');
}

/** 
* check if the stats are enabled
* redirect to index if not
*/
if (!$stats_config['stats_enable'])
{
	redirect(append_sid($phpbb_root_path . 'index.' . $phpEx));
}

/**
* load $stats class and globalize it
*/
global $stats;
$stats = new stats_functions();

// setup initial vars
$p = request_var('p', 0);
$id = request_var('id', 0);

// get modules
$stats->obtain_modules();
	
// decide what module to display
if ($p == 0 && $id == 0)
{

}
elseif ($p != 0 && $id == 0)
{

}
else
{

}





// Output the page
page_header($page_title, false);

$template->set_filenames(array(
	'body' => 'stats')
);

page_footer();
