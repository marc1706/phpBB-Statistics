<?php
/**
*
* @package phpBB Statistics
* @copyright (c) 2009 - 2010 Marc Alexander(marc1706) www.m-a-styles.de
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
* @based on: acp_stats.php included in the Board3 Portal package (www.board3.de)
*/

if (!defined('IN_PHPBB') || !defined('ADMIN_START'))
{
	exit;
}

class acp_stats
{
	var $u_action;
	var $new_config = array();

	function main($id, $mode)
	{
		global $db, $user, $template;
		global $config, $phpbb_root_path, $phpbb_admin_path, $phpEx;

		include($phpbb_root_path . 'stats/includes/functions.' . $phpEx);
		
		/**
		* load $stats class and globalize it
		*/
		global $stats;
		$stats = new phpbb_stats();
		
		// get modules
		$stats->obtain_modules();
		$stats->u_action = $this->u_action;

		$user->add_lang('mods/stats');

		$action = request_var('action', '');
		$submit = (isset($_POST['submit'])) ? true : false;

		// @todo: remove this --> not needed anymore
		if($mode == 'addons')
		{
			$addon_loaded = false;
		
			/**
			* load addons from stats_addons
			*/
			$sql = 'SELECT addon_classname, addon_enabled, addon_id
					FROM ' . STATS_ADDONS_TABLE . '
					GROUP BY addon_classname, addon_enabled, addon_id
					ORDER BY addon_id ASC';
			$result = $db->sql_query($sql);
			while ($row = $db->sql_fetchrow($result))
			{
				$classname = $row['addon_classname'];
				
				if (!class_exists($classname))
				{
					include($phpbb_root_path . '/statistics/addons/' . $row['addon_classname'] . '.' . $phpEx);
				}
				if (!class_exists($classname))
				{
					trigger_error(sprintf($user->lang['CLASS_NOT_FOUND'], $classname, $row['addon_classname']), E_USER_ERROR);
				}
				
				$addon = new $classname();
				
				
				/**
				* check if all needed vars exist
				* if not, output an error
				*/
				$error = '';
				if(!isset($addon->module_name))
				{
					$error = 'module_name';
				}
				
				if(!isset($addon->module_file))
				{
					$error = (strlen($error) > 0) ? $error . ', module_file' : 'module_file';
				}
				
				if(!isset($addon->template_file))
				{
					$error = (strlen($error) > 0) ? $error . ', template_file' : 'template_file';
				}
				
				if(strlen($error) > 0)
				{
					trigger_error(sprintf($user->lang['DAMAGED_ADDON'], $classname, $error));
				}
				
				/**
				* start loading the necessary data
				*/
				if(strlen($addon->template_file) > 0)
				{
					$user->add_lang('mods/stats_addons/' . $addon->template_file);
				}
				$addon_acp_settings = ($addon->load_acp_settings) ? true : false;
				$addons[$addon->module_file] = array('classname' => $row['addon_classname'], 'enabled' => $row['addon_enabled'], 'lang' => $addon->module_name, 'settings' => $addon_acp_settings, 'id' => $row['addon_id']);
				
				/**
				* execute the necessary actions
				*/
				if($row['addon_classname'] == $addon_name)
				{
					switch ($addon_action)
					{
						case 'remove':
							$addon->uninstall();
							add_log('admin', sprintf('LOG_STATS_ADDON_REMOVED', $user->lang[$addon->module_name]));
							trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
						break;
						
						case 'enable':
							set_stats_addon($row['addon_classname'], 1);
							add_log('admin', sprintf('LOG_STATS_ADDON_ENABLED', $user->lang[$addon->module_name]));
							trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
						break;
						
						case 'disable':
							set_stats_addon($row['addon_classname'], 0);
							add_log('admin', sprintf('LOG_STATS_ADDON_DISABLED', $user->lang[$addon->module_name]));
							trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
						break;
						
						case 'edit':
							if($addon->load_acp_settings)
							{
								$display_vars = $addon->load_acp();
							}
						break;
						
						default:
							
					}
					
					$addon_loaded = true;
				}
			}			
			$db->sql_freeresult($result);
			
			/**
			* install the add-on if selected
			*/
			if($addon_action == 'install_addon' && $addon_loaded == false)
			{
				$classname = $addon_name;
				if (!class_exists($classname))
				{
					include($phpbb_root_path . '/statistics/addons/' . $classname . '.' . $phpEx);
				}
				if (!class_exists($classname))
				{
					trigger_error(sprintf($user->lang['CLASS_NOT_FOUND'], $classname, $classname), E_USER_ERROR);
				}
				
				$addon = new $classname();
				
				$addon->install();
				
				redirect($this->u_action);
			}
			
			/**
			* move addons if selected
			*/
			if($addon_action == 'move_up' || $addon_action == 'move_down')
			{
				$size = sizeof($addons);
				
				move_addon($addon_action, $size, $addons[$addon_name]['id'], $addon_name);
				
				redirect($this->u_action);
			}
			
			/**
			* now look for add-ons that are present but not installed yet
			*/
			$uninstalled_addons = false;
			$dp = @opendir("{$phpbb_root_path}statistics/addons");
			
			while(($file = readdir($dp)) !== false)
			{	
				if ($file[0] != '.')
				{
					$classname = str_replace('.php', '', $file);
					if(!isset($addons[$classname]))
					{
						if (!class_exists($classname))
						{
							include($phpbb_root_path . '/statistics/addons/' . $classname . '.' . $phpEx);
						}
						if (!class_exists($classname))
						{
							trigger_error(sprintf($user->lang['CLASS_NOT_FOUND'], $classname, $classname), E_USER_ERROR);
						}
						
						$addon = new $classname();
						
						if(strlen($addon->template_file) > 0)
						{
							$user->add_lang('mods/stats_addons/' . $addon->template_file);
						}
						
						
						$template->assign_block_vars('addons_urow', array(
							'NAME'		=> $user->lang[$addon->module_name],
							'U_INSTALL'	=> '<a href="' . $this->u_action . '&amp;addon_name=' . $classname . '&amp;addon_action=install_addon">' . $user->lang['INSTALL'] . '</a>',
						));
						
						$uninstalled_addons = true;
					}
				}
			}
			
			
			/**
			* assign vars and block_vars
			*/
			$template->assign_vars(array(
				'L_TITLE'			=> $user->lang['STATS_ADDONS'],
				'L_EXPLAIN'			=> '',
				'U_ACTION'			=> $this->u_action,
				)
			);
			
			$count = 1;
			foreach($addons as $key => $current_addon)
			{
				$s_actions = '<a href="' . $this->u_action . '&amp;addon_name=' . $current_addon['classname'] . '&amp;addon_action=';
				$s_actions .= ($current_addon['enabled']) ? 'disable">' : 'enable">';
				$s_actions .= ($current_addon['enabled']) ? $user->lang['DEACTIVATE'] : $user->lang['ACTIVATE'];
				$s_actions .= '</a> | <a href="' . $this->u_action . '&amp;addon_name=' . $current_addon['classname'] . '&amp;addon_action=remove">' . $user->lang['DELETE'] . '</a>';
				
				$template->assign_block_vars('addons_row', array(
					'NAME'				=> $user->lang[$current_addon['lang']],
					'S_ACTIONS'			=> $s_actions,
					'U_EDIT'			=> $this->u_action . '&amp;addon_name=' . $current_addon['classname'] . '&amp;addon_action=edit',
					'S_EDIT'			=> ($current_addon['settings']) ? true : false,
					'S_FIRST_ROW'		=> ($count == 1) ? true : false,
					'S_LAST_ROW'		=> ($count == (sizeof($addons))) ? true : false,
					'U_MOVE_DOWN'		=> $this->u_action . '&amp;addon_name=' . $current_addon['classname'] . '&amp;addon_action=move_down',
					'U_MOVE_UP'			=> $this->u_action . '&amp;addon_name=' . $current_addon['classname'] . '&amp;addon_action=move_up',
				));
				
				++$count;
			}
		}
		
		$this->new_config = $config;

		/**
		*	Validation types are:
		*		string, int, bool,
		*		script_path (absolute path in url - beginning with / and no trailing slash),
		*		rpath (relative), rwpath (realtive, writeable), path (relative path, but able to escape the root), wpath (writeable)
		*/
		switch ($mode)
		{
			case 'settings':
				$display_vars = array(
					'title'	=> 'ACP_STATS_GENERAL_INFO',
					'vars'	=> array(
						'legend1'							=> 'ACP_STATS_GENERAL_SETTINGS',
						'stats_enable'						=> array('lang' => 'ACP_STATS_ENABLE'	, 'validate' => 'bool'	, 'type' => 'radio:yes_no'	, 'explain' => true),
						'stats_cache_time'					=> array('lang' => 'ACP_STATS_CACHE_TIME', 'validate' => 'int', 'type' => 'text:2:2', 'explain' => true),
						
						'legend2'							=> 'ACP_BASIC_ADVANCED_SETTINGS',
						'stats_advanced_security'			=> array('lang' => 'ACP_BASIC_ADVANCED_SECURITY'  , 'validate' => 'bool'  , 'type' => 'radio:yes_no'  , 'explain' => true),
						'stats_advanced_pretend_version'	=> array('lang' => 'ACP_BASIC_ADVANCED_PRETEND'  , 'validate' => 'bool'  , 'type' => 'radio:yes_no'  , 'explain' => true),
						
						'legend3'							=> 'ACP_BASIC_MISCELLANEOUS_SETTINGS',
						'basic_miscellaneous_hide_warnings'	=> array('lang' => 'ACP_BASIC_MISCELLANEOUS_WARNINGS'  , 'validate' => 'bool'  , 'type' => 'radio:yes_no'  , 'explain' => true),
						'resync_stats_bbcodes'				=> array('lang' => 'ACP_BASIC_MISCELLANEOUS_BBCODES'  , 'validate' => 'bool'  , 'type' => 'radio:yes_no'  , 'explain' => true),
						
						'legend4' 							=> 'ACP_ACTIVITY_USERS_SETTINGS',
						'activity_users_hide_anonymous'		=> array('lang' => 'ACP_ACTIVITY_USERS_HIDE_ANONYMOUS'  , 'validate' => 'bool'  , 'type' => 'radio:yes_no'  , 'explain' => true),
					)
				);
			break;
			
			case 'modules':
				$user->add_lang('acp/modules');
				$view = request_var('view', '');
				$parent = request_var('parent_id', 0);
				$id = request_var('id', 0);
				
				switch ($action)
				{
					case 'move_up':
						foreach ($stats->modules as $module)
						{
							if ($module['module_id'] == $id)
							{
								break; // just leave all data in $module
							}
						}
						
						if ($module['module_order'] <= 0)
						{
							break; // this shouldn't happen
						}
						
						$stats->move_module('up', $module, (!empty($module['module_parent'])) ? $this->u_action . '&amp;view=sub&amp;parent_id=' . $module['module_parent'] : '');
						
					break;
					
					case 'move_down':
						foreach ($stats->modules as $module)
						{
							if ($module['module_id'] == $id)
							{
								break; // just leave all data in $module
							}
						}
						
						if ($module['module_order'] < 0)
						{
							break; // this shouldn't happen
						}
						
						$stats->move_module('down', $module, (!empty($module['module_parent'])) ? $this->u_action . '&amp;view=sub&amp;parent_id=' . $module['module_parent'] : '');
					break;
				}
				
				switch ($view)
				{
					case 'sub':
						// redirect if we don't have a parent id
						if (empty($parent))
						{
							redirect($this->u_action);
						}

						$template->assign_vars(array(
							'S_IS_OVERVIEW' 	=> false,
							'L_TITLE'			=> $user->lang['ACP_STATS_MODULES_INFO'],
							'L_TITLE_EXPLAIN'	=> $user->lang['ACP_STATS_MODULES_INFO_EXPLAIN'],
						));
						$this->page_title = $user->lang['ACP_STATS_MODULES_INFO'];
						// display all submodules
						$modules_ary = $module_orders = array();
						foreach ($stats->modules as $cur_module)
						{
							if ($cur_module['module_parent'] == $parent)
							{
								$modules_ary[] = $cur_module;
								$module_orders[] = $cur_module['module_order'];
							}
						}
						array_multisort($module_orders, SORT_ASC, $modules_ary);
						
						foreach ($modules_ary as $cur_module)
						{
							$template->assign_block_vars('stats_row', array(
								'NAME'			=> (isset($user->lang[$cur_module['module_name']])) ? $user->lang[$cur_module['module_name']] : $cur_module['module_name'],
								'U_EDIT'		=> $this->u_action . '&amp;view=edit&amp;id=' . $cur_module['module_id'],
								'S_EDIT'		=> true, // @todo: we'll need to find out which module we can edit
								'U_DELETE'		=> $this->u_action . '&amp;action=delete&amp;id=' . $cur_module['module_id'],
								'U_MOVE_UP'		=> $this->u_action . '&amp;id=' . $cur_module['module_id'] . '&amp;action=move_up',
								'U_MOVE_DOWN'	=> $this->u_action . '&amp;id=' . $cur_module['module_id'] . '&amp;action=move_down',
							));
						}
					break;
					
					default:
						$template->assign_vars(array(
							'S_IS_OVERVIEW' 	=> true,
							'L_TITLE'			=> $user->lang['ACP_STATS_MODULES_INFO'],
							'L_TITLE_EXPLAIN'	=> $user->lang['ACP_STATS_MODULES_INFO_EXPLAIN'],
						));
						$this->page_title = $user->lang['ACP_STATS_MODULES_INFO'];
						// display all parent modules
						$modules_ary = $module_orders = array();
						foreach ($stats->modules as $cur_module)
						{
							if ($cur_module['module_parent'] == 0)
							{
								$modules_ary[] = $cur_module;
								$module_orders[] = $cur_module['module_order'];
							}
						}
						array_multisort($module_orders, SORT_ASC, $modules_ary);
						
						foreach ($modules_ary as $cur_module)
						{
							$template->assign_block_vars('stats_row', array(
								'NAME'			=> (isset($user->lang[$cur_module['module_name']])) ? $user->lang[$cur_module['module_name']] : $cur_module['module_name'],
								'U_EDIT'		=> $this->u_action . '&amp;view=edit&amp;id=' . $cur_module['module_id'],
								'S_EDIT'		=> true, // @todo: we'll need to find out which module we can edit
								'U_SUB'			=> $this->u_action . '&amp;view=sub&amp;parent_id=' . $cur_module['module_id'],
								'U_MOVE_UP'		=> $this->u_action . '&amp;id=' . $cur_module['module_id'] . '&amp;action=move_up',
								'U_MOVE_DOWN'	=> $this->u_action . '&amp;id=' . $cur_module['module_id'] . '&amp;action=move_down',
							));
						}
				}
				
			break;
			
			default:
				trigger_error('NO_MODE', E_USER_ERROR);
			break;
		}

		if (isset($display_vars['lang']))
		{
			$user->add_lang($display_vars['lang']);
		}

		$cfg_array = (isset($_REQUEST['config'])) ? utf8_normalize_nfc(request_var('config', array('' => ''), true)) : $this->new_config;
		$error = array();
		
		// We validate the complete config if whished
		if(isset($display_vars) && sizeof($display_vars) > 0)
		{
			validate_config_vars($display_vars['vars'], $cfg_array, $error);
			
			// Do not write values if there is an error
			if (sizeof($error))
			{
				$submit = false;
			}
			
			// We go through the display_vars to make sure no one is trying to set variables he/she is not allowed to...
			foreach ($display_vars['vars'] as $config_name => $null)
			{
				if (!isset($cfg_array[$config_name]) || strpos($config_name, 'legend') || ($mode == 'links' && strpos($config_name, 'portal_link_') ) !== false)
				{
					continue;
				}

				$this->new_config[$config_name] = $config_value = $cfg_array[$config_name];

				if ($submit)
				{
					set_config($config_name, $config_value);
				}
			}
		}



		if ($submit && (($mode == 'modules' && $view == 'edit') || $mode == 'settings'))
		{
			add_log('admin', 'LOG_STATS_CONFIG_' . strtoupper($mode));
			trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
		}
		elseif ($submit)
		{
			trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
		}

		if($mode != 'modules' || ($mode == 'modules' && $view == 'edit'))
		{
			$this->tpl_name = 'acp_board';
			$this->page_title = $display_vars['title'];

			$title_explain = $user->lang[$display_vars['title'] . '_EXPLAIN'];

			$title_explain .= ( $display_vars['title'] == 'ACP_STATS_GENERAL_INFO' ) ? '<br /><br />' . sprintf($user->lang['ACP_STATS_VERSION'], $config['phpbb_statistics_version']) : '';
			
			$template->assign_vars(array(
				'L_TITLE'			=> $user->lang[$display_vars['title']],
				'L_TITLE_EXPLAIN'	=> $title_explain,

				'S_ERROR'			=> (sizeof($error)) ? true : false,
				'ERROR_MSG'			=> implode('<br />', $error),

				'U_ACTION'			=> ($mode == 'addons' && $addon_action == 'edit') ? $this->u_action . "&amp;addon_name=$addon_name&amp;addon_action=$addon_action" : $this->u_action)
			);

			// Output relevant page
			foreach ($display_vars['vars'] as $config_key => $vars)
			{
				if (!is_array($vars) && strpos($config_key, 'legend') === false)
				{
					continue;
				}

				if (strpos($config_key, 'legend') !== false)
				{
					$template->assign_block_vars('options', array(
						'S_LEGEND'		=> true,
						'LEGEND'		=> (isset($user->lang[$vars])) ? $user->lang[$vars] : $vars)
					);

					continue;
				}

				$type = explode(':', $vars['type']);

				$l_explain = '';
				if ($vars['explain'] && isset($vars['lang_explain']))
				{
					$l_explain = (isset($user->lang[$vars['lang_explain']])) ? $user->lang[$vars['lang_explain']] : $vars['lang_explain'];
				}
				else if ($vars['explain'])
				{
					$l_explain = (isset($user->lang[$vars['lang'] . '_EXPLAIN'])) ? $user->lang[$vars['lang'] . '_EXPLAIN'] : '';
				}

				$template->assign_block_vars('options', array(
					'KEY'			=> $config_key,
					'TITLE'			=> (isset($user->lang[$vars['lang']])) ? $user->lang[$vars['lang']] : $vars['lang'],
					'S_EXPLAIN'		=> $vars['explain'],
					'TITLE_EXPLAIN'	=> $l_explain,
					'CONTENT'		=> build_cfg_template($type, $config_key, $this->new_config, $config_key, $vars),
					)
				);
				unset($display_vars['vars'][$config_key]);
			}
		}
		else
		{
			$this->tpl_name = 'acp_stats_modules';
			$this->page_title = $user->lang['ACP_STATS_MODULES_INFO'];
			
			//$template->assign_var('S_UNINSTALLED_ADDONS', $uninstalled_addons);
		}
	}
	
	
}
