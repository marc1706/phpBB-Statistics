<?php
/**
*
* @package phpBB Statistics
* @copyright (c) 2009 - 2010 Marc Alexander(marc1706) www.m-a-styles.de, (c) TheUniqueTiger - Nayan Ghosh
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
* @based on: Forum Statistics by TheUniqueTiger - Nayan Ghosh
* @translator (c) ( Marc Alexander - http://www.m-a-styles.de ), TheUniqueTiger - Nayan Ghosh
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(	
	'STATS'								=> 'phpBB Statistics',	
	'STATS_EXPLAIN'						=> 'Open phpBB Statistics',
	'STATS_BASIC'						=> 'Basic statistics',
	'STATS_BASIC_BASIC'					=> 'Basic Forum Statistics',
	'STATS_BASIC_ADVANCED'				=> 'Advanced Forum Statistics',
	'STATS_BASIC_MISCELLANEOUS'			=> 'Miscellaneous Statistics',
	'STATS_ACTIVITY'					=> 'Forum Activity',
	'STATS_ACTIVITY_FORUMS'				=> 'Forums Statistics',
	'STATS_ACTIVITY_TOPICS'				=> 'Topics Statistics',
	'STATS_ACTIVITY_USERS'				=> 'Users Statistics',
	'STATS_CONTRIBUTIONS'				=> 'Forum Contributions',
	'STATS_CONTRIBUTIONS_ATTACHMENTS'	=> 'Attachments Statistics',
	'STATS_CONTRIBUTIONS_POLLS'			=> 'Polls Statistics',
	'STATS_PERIODIC'					=> 'Periodic Statistics',
	'STATS_PERIODIC_DAILY'				=> 'Daily Statistics',
	'STATS_PERIODIC_MONTHLY'			=> 'Monthly Statistics',
	'STATS_PERIODIC_HOURLY'				=> 'Hourly Statistics',
	'STATS_SETTINGS'					=> 'Settings Statistics',
	'STATS_SETTINGS_BOARD'				=> 'Board Settings Statistics',
	'STATS_SETTINGS_PROFILE'			=> 'Profile Settings Statistics',
	'STATS_ADDONS'						=> 'Add-Ons',
	'STATS_ADDONS_MISCELLANEOUS'		=> 'hide',
	'STATS_DISABLED'					=> ' are currently disabled',
	
	'TOTALS'							=> 'Totals',
	'OVERALL'							=> 'Overall',
	'NONE'								=> 'None',
	'LIMIT_PROMPT'						=> 'Number of top %s to be retrieved',
	'GB'								=> 'GB',
	'AS_ON'								=> 'As on %s',	
	'DAMAGED_ADDON'						=> 'The Add-On %1$s is damaged. The following variables do not exist: %2$s. Please contact the author of the Add-On.',
	'ADDON_DISABLED'					=> 'The add-on you selected is currently disabled.<br /><br />',
	'ADDON_DISABLED_TITLE'				=> 'Add-On disabled',
	'NO_ADDONS'							=> 'There are currently no add-ons installed.<br /><br />',
	'NO_ADDONS_TITLE'					=> 'No installed Add-Ons',
	
	//basic stats
	'TOTAL_TOPICS'						=> 'Total topics',
	'TOTAL_USERS'						=> 'Total users',
	'TOTAL_FORUM_CAT'					=> 'Total forum categories',
	'TOTAL_FORUM_POST'					=> 'Total posting forums',
	'TOTAL_FORUM_LINK'					=> 'Total link forums',
	'TOTAL_FORUMS'						=> 'Total forums',
	'TOTAL_ATTACHMENTS'					=> 'Total attachments',
	'TOTAL_POLLS'						=> 'Total polls',
	'TOTAL_VIEWS'						=> 'Total topic views',
	'TOPICS_GLOBAL'						=> 'Global topics',
	'TOPICS_ANNOUNCE'					=> 'Announcement topics',
	'TOPICS_STICKY'						=> 'Sticky topics',
	'TOPICS_NORMAL'						=> 'Normal topics',
	'TOPICS_UNAPPROVED'					=> 'Unapproved topics',
	'UNAPPROVED_POSTS'					=> 'Unapproved posts',
	'USERS_INACTIVE'					=> 'Inactive users',
	'USERS_INACTIVE_EXPLAIN'			=> 'Users who have not visited in the past %d days',
	'USERS_ACTIVE'						=> 'Active users',
	'USERS_ACTIVE_EXPLAIN'				=> 'Users who have visited at least once in the past %d days',
	'USERS_TOTAL_BOTS'					=> 'Registered bots',
	'USERS_VISITED_BOTS'				=> 'Visited bots',
	'AVG_FILES_DAY'						=> 'Average attachments per day',
	'AVERAGES'							=> 'Averages',
	//advanced stats
	'BOARD_BACKGROUND'					=> 'Board background',
	'START_DATE'						=> 'Board start date',
	'BOARD_AGE'							=> 'Board age',
	'SECOND'							=> 'second',
	'MINUTE'							=> 'minute',
	'HOUR'								=> 'hour',
	'MONTHS'							=> 'months',
	'YEARS'								=> 'years',
	'BOARD_VERSION'						=> 'Board version',
	'GZIP_COMPRESSION'					=> 'GZip Compression',
	'ON'								=> 'On',
	'OFF'								=> 'Off',
	'DATABASE'							=> 'Database',
	'DATABASE_SIZE'						=> 'Database size',
	'DATABASE_INFO'						=> 'Database info',
	'FILESYSTEM'						=> 'Filesystem',
//	'ATTACHMENTS_TOTAL'					=> 'Total attachments',
	'ATTACHMENTS_SIZE'					=> 'Total attachments size',
	'TOTAL_AVATARS'						=> 'Total avatars',
	'TOTAL_AVATARS_SIZE'				=> 'Total avatars size',
	'TOTAL_CACHED_FILES'				=> 'Total cached files',
	'CACHED_FILES_SIZE'					=> 'Total cache size',
	'INSTALLED_COMPONENTS'				=> 'Installed components',
	'STYLES'							=> 'Styles',
	'IMAGESETS'							=> 'Imagesets',
	'TEMPLATES'							=> 'Templates',
	'THEMES'							=> 'Themes',
	'LANG_PACKS'						=> 'Language packs',
	'BY'								=> 'by',
	'BOARD_VERSION_SECURE'				=> '3.x.x',
	'SORT_BY_PROMPT'					=> 'Sort installed components information by',
	
	// miscellaneous stats
	'SMILEY'							=> 'Smilies',
	'SMILEY_COUNT'						=> 'Number of smilies installed',
	'SMILEY_POST_COUNT'					=> 'Number of smilies displayed on posting',
	'TOP_SMILIES_BY_URL'				=> 'Top %d smilies',
	'TOP_BBCODES'						=> 'Top %d bbcodes',
	'TOP_ICONS'							=> 'Top %d icons',
	'WARNING_COUNT'						=> 'Number of warnings',
	'OWN_WARNINGS_COUNT'				=> 'Warnings received yourself',
	'WARNINGS_PER_USER'					=> 'Number of warnings per user',
	'WARNINGS_PER_DAY'					=> 'Number of warnings per day',
	'BBCODE'							=> 'BBCodes',
	'BBCODE_COUNT'						=> 'Number of BBCodes',
	'BBCODE_COUNT_CUSTOM'				=> 'Number of custom BBCodes',
	'ICONS'								=> 'Icons',
	'COMPONENTS_NAME'					=> 'Name',
	'COMPONENTS_ID'						=> 'ID',
	'COMPONENTS_AUTHOR'					=> 'Copyright/Author',
	'RECOUNT_PROGRESS'					=> 'Counting the number of %1$s.<br />%2$d of %3$d posts have been processed. Please wait until the script has finished.<br /><br />',
	
	//activity - forums
	'COUNT'								=> 'Count',
	'PERCENT'							=> 'Percent',
	'TOP_FORUMS_BY_TOPICS'				=> 'Top %d forums (by topics)',
	'TOP_FORUMS_BY_POSTS'				=> 'Top %d forums (by posts)',
	'TOP_FORUMS_BY_POLLS'				=> 'Top %d forums (by polls)',
	'TOP_FORUMS_BY_STICKY'				=> 'Top %d forums (by sticky topics)',
	'TOP_FORUMS_BY_VIEWS'				=> 'Top %d forums (by views)',
	'TOP_FORUMS_BY_PARTICIPATION'		=> 'Top %d forums (by user participation)',
	'TOP_FORUMS_BY_SUBSCRIPTIONS'		=> 'Top %d forums (by subscriptions)',
	//activity - topics
	'TOP_TOPICS_BY_POSTS'				=> 'Top %d topics (by posts)',
	'TOP_TOPICS_BY_POSTS_PCT_EXPLAIN'	=> 'The percentage shown is the percentage of posts in that topic to the total posts.',
	'TOP_TOPICS_BY_POSTS_BAR_EXPLAIN'	=> 'The bar shown is for comparison with the topic with highest number of posts.',
	'TOP_TOPICS_BY_VIEWS'				=> 'Top %d topics (by views)',
	'TOP_TOPICS_BY_VIEWS_PCT_EXPLAIN'	=> 'The percentage shown is the percentage of topic views compared to the total topic views.',
	'TOP_TOPICS_BY_PARTICIPATION'		=> 'Top %d topics (by user participation)',
	'TOP_TOPICS_BY_ATTACHMENTS'			=> 'Top %d topics (by attachments)',
	'TOP_TOPICS_BY_BOOKMARKS'			=> 'Top %d topics (by bookmarks)',
	'TOP_TOPICS_BY_SUBSCRIPTIONS'		=> 'Top %d topics (by subscriptions)',
	//activity - users
	'MEMBERS'							=> 'Members',
	'TOTAL_MEMBERS'						=> 'Total members',
	'TOTAL_REG_USERS'					=> 'Total registered users',
	'MOST_ONLINE'						=> 'Most users online',
	'INCLUDING_BOTS'					=> 'including Bots',
	'TOTAL_ONLINE'						=> 'Total users online',
	'ONLINE_ON'							=> 'on',
	'TOTAL_HIDDEN'						=> 'Total hidden users online',
	'TOTAL_MEMBERS_ONLINE'				=> 'Total members online',
	'TOP_USERS_BY_POSTS'				=> 'Top %d users (by posts)',
	'TOP_USERS_BY_TOPICS'				=> 'Top %d users (by topics)',
	'TOP_FRIENDS'						=> 'Top %d friends',
	'TOP_FOES'							=> 'Top %d ignored users (foes)',
	'TOP_USERS_BY_RECENT_POSTS'			=> 'Top %1$d users (by recent posts over last %2$d days)',
	'RECENT_POSTS_DAYS_LIMIT_PROMPT'	=> 'Number of days to be considered for recent posts',
	'WHO_IS_ONLINE_EXPLAIN'				=> 'based on users active over the past %d minutes',
	'RANKS_POSTS'						=> 'Ranks (Non-special ranks based on post count)',
	'RANKS'								=> 'Ranks',
	'RANK_MIN_POSTS'					=> 'Minimum post count',
	'MEMBER_COUNT'						=> 'Member count',
	'DELETED_USERS'						=> 'Deleted users',
	//contributions - attachments
	'ATTACHMENTS_ORPHAN'				=> 'Orphan attachments',
	'ATTACHMENTS_ORPHAN_SIZE'			=> 'Orphan attachments size',
	'ATTACHMENTS_OR_USERS'				=> 'Attachments/Users',
	'RECENT_ATTACHMENTS'				=> 'Recent %d attachments',
	'ATTACH_ON'							=> 'on',
	'ATTACH_DETAILS'					=> 'Details',	
	'TOP_ATTACHMENTS_BY_FILETYPE'		=> 'Top %d attachment filetypes (by file extension)',
	'ATTACHMENT_FILETYPES'				=> 'Attachment filetypes',
	'TOP_ATTACHMENTS_BY_FILESIZE'		=> 'Top %d attachments (by file size)',
	'TOP_ATTACHMENTS_BY_DOWNLOAD'		=> 'Top %d attachments (by download count)',
	'TOP_USERS_BY_ATTACHMENTS'			=> 'Top %d users (by attachments)',
	'TOTAL_DOWNLOADS'					=> 'Total file downloads',
	'TOTAL_DOWNLOADS_SIZE'				=> 'Total downloads size',
	//contributions - polls
	'TOTAL_OPEN_POLLS'					=> 'Total open polls',
	'TOTAL_POLL_VOTES'					=> 'Total poll votes',
	'RECENT_POLLS'						=> 'Recent %d polls',
	'POLLS'								=> 'Polls',	
	'TOP_POLLS_BY_VOTES'				=> 'Top %d polls (by votes)',
	'TOTAL_POLLS_VOTED'					=> 'Total polls in which you have voted',
	'TOTAL_ACCESSIBLE_POLLS'			=> 'Total accessible polls',
	//periodic - daily, monthly
	'PERIODIC_DAY'						=> 'Day',
	'PERIODIC_MONTH'					=> 'Month',
	'AVG_POSTS_DAY'						=> 'Average posts per day',	
	'AVG_TOPICS_DAY'					=> 'Average topics per day',
	'AVG_USER_REGS_DAY'					=> 'Average registrations per day',
	'AVG_POSTS_MONTH'					=> 'Average posts per month',	
	'AVG_TOPICS_MONTH'					=> 'Average topics per month',
	'AVG_USER_REGS_MONTH'				=> 'Average registrations per month',
	'TOTAL_USER_REGS'					=> 'Total user registrations',
	'STATS_MONTH_EXPLAIN'				=> 'The following statistics are shown for month of <strong>%s</strong>',
	'STATS_YEAR_EXPLAIN'				=> 'The following statistics are shown for the year <strong>%s</strong>',
	'USER_REGS'							=> 'User registrations',
	'SHOW_STATS_FOR_MONTH'				=> 'Show statistics for the selected month',
	'SHOW_STATS_FOR_YEAR'				=> 'Show statistics for the selected year',
	'ALL'								=> 'All',
	//periodic- hourly
	'SELECT_TIME_PERIOD'				=> 'Select the time period',
	'PERCENT_OF_TOTAL'					=> '%% of total forum %s',
	'PERIODIC_HOUR'						=> 'Hour',
	'HOURLY_STATS_EXPLAIN'				=> 'Showing statistics for <strong>%s</strong>',
	//settings - board
	'OVERRIDE_STYLE_EXPLAIN'			=> 'The board administrators have set the option to override the user’s style with the default style.',
	'DEFAULT_STYLE_EXPLAIN'				=> 'The default style is <strong>%1$s (%2$s)</strong> which is the overriden style for all users (including bots).',
	'STYLE'								=> 'Style',
	'USERS_INCL_BOTS'					=> 'Users using this style (including bots)',
	'LANGUAGES_BY_USERS'				=> 'Languages (by users who have set that language)',
	'LANGUAGE'							=> 'Language',
	'TIMEZONES_BY_USERS' 				=> 'Timezones (by users who have set that timezone)',
	'TIMEZONE'							=> 'Timezone',
	'LEGEND_BOLD_ITALIC'				=> 'The element shown in bold letters is the maximum for that group. The element shown in italics is the group that you belong to.',
	'SINGLE_LANG_EXPLAIN'				=> 'There is only a single language pack installed on the forum which is used by all the users.',
	'DEFAULT_LANG_EXPLAIN'				=> 'The default language pack is <strong>%1$s (%2$s)</strong>.',
	//settings - profile
	'AGE_RANGES'						=> 'Users count by age ranges',
	'AGE_RANGE'							=> 'Age range',
	'SEL_AGE_INTERVAL_PROMPT'			=> 'Select the range interval',
	'USERS_WITH_BIRTHDAY'				=> 'Users who have set their birthday', 
	'USERS_WITH_LOCATION'				=> 'Users who have set their location',
	'USER_LOCATIONS'					=> 'User Locations',
	'TOP_USER_LOCATIONS'				=> 'Top %d User Locations',
	'CUSTOM_PROFILE_FIELD'				=> 'Custom Profile Field',
	'CPF_TOP_X'							=> 'Top %1$d %2$s',
	'TOTAL_VALUES_SET_PROMPT'			=> 'Total users who have set %s',
	'DEFAULT'							=> 'default',
	
	// viewonline
	'VIEWING_STATS'						=> 'Viewing phpBB Statistics',
	
	// Error message
	'STATS_NOT_ENABLED'					=> 'phpBB Statistics is currently disabled.',
));
