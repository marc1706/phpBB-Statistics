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
$user->setup('mods/stats');

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
if (!$config['stats_enable'])
{
	redirect(append_sid($phpbb_root_path . 'index.' . $phpEx));
}

/**
* load $stats class and globalize it
*/
global $stats;
$stats = new phpbb_stats();

// setup initial vars
$p = request_var('p', 0);
$id = request_var('id', 0);

// get modules
$stats->obtain_modules();
	
/** 
* decide what module to display
*/
if ($p == 0 && $id == 0)
{
	// use the first category
	foreach ($stats->modules as $cur_module)
	{
		if ($cur_module['module_parent'] == 0)
		{
			$cur_parent = $cur_module['module_id'];
			break;
		}
	}
	
	// now grab the first module of the first category
	foreach ($stats->modules as $cur_module)
	{
		if ($cur_module['module_parent'] == $cur_parent && $cur_module['module_order'] == 0)
		{
			$module = $cur_module;
			break;
		}
	}
}
elseif ($p != 0 && $id == 0)
{
	// grab the first module of the selected parent
	foreach ($stats->modules as $cur_module)
	{
		if ($cur_module['module_parent'] == $p && $cur_module['module_order'] == 0)
		{
			$module = $cur_module;
			break;
		}
	}
}
else
{
	// grab the module with the selected module id
	foreach ($stats->modules as $cur_module)
	{
		if ($cur_module['module_id'] == $id)
		{
			$module = $cur_module;
			break;
		}
	}
}

$class_name = $module['module_classname'] . '_module'; // classes have '_module' appended

/**
* load all module specific files
*/
if (!class_exists($class_name))
{
	include($phpbb_stats_path . 'modules/' . $module['module_classname'] . '.' . $phpEx);
}

if (!class_exists($class_name))
{
	trigger_error(sprintf($user->lang['CLASS_NOT_FOUND'], $class_name, $module['module_classname']), E_USER_ERROR);
}

$stats_module = new $classname();

$stats_template = $stats_module->get_stats();

$template->assign_vars(array(
	'TEMPLATE_FILE'		=> ($stats_template != false) ? $stats_template : '',
));


// Output the page
page_header($page_title, false);

$template->set_filenames(array(
	'body' => 'stats')
);

page_footer();
