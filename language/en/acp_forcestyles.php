<?php
/**
 * This file is a part of the Force Style Changes modification by
 * lax.slash for the phpBB 3.1 Forums Software.
 *
 * @copyright (c) lax.slash <https://www.github.com/LaxSlash>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
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
	'ACP_LAXSLASH_FORCESTYLES_MODULE_TITLE' => 'Force Styles',
	'ACP_LAXSLASH_FORCESTYLES_PANEL_TITLE' => 'Force User Style',
	'ACP_LAXSLASH_FORCESTYLES_NOT_ENOUGH_STYLES_TO_USE' => 'In order to use this extension, there must be at least two styles installed and activated.',
	'ACP_LAXSLASH_FORCESTYLES_UNACCEPTABLE_STYLE_SELECTED' => 'A deactivated or uninstalled style has been selected in the criteria list.',
	'ACP_LAXSLASH_FORCESTYLES_FOUNDER_GROUP_SELECTED_BY_NON_FOUNDER_USER' => 'You may not select the group %s%, because it is a founder managed group.',
	'ACP_LAXSLASH_FORCESTYLES_NON_EXISTANT_GROUP_OR_GROUPS_SELECTED' => 'One or more selected criteria groups do not exist.',
	'ACP_LAXSLASH_FORCESTYLES_NEW_STYLE_NOT_FOUND_OR_USABLE' => 'The target style selected is either deactivated or uninstalled.',
	'ACP_LAXSLASH_FORCESTYLES_FORCED_SUCCESSFULLY_S' => 'Style successfully changed for %d user.',
	'ACP_LAXSLASH_FORCESTYLES_FORCED_SUCCESSFULLY_P' => 'Style successfully changed for %d users.',
	'ACP_LAXSLASH_FORCESTYLES_NON_FOUNDER_FORCED_SUCCESSFULLY_S' => 'Style successfully changed for %d user. Note that Founder users were not altered.',
	'ACP_LAXSLASH_FORCESTYLES_NON_FOUNDER_FORCED_SUCCESSFULLY_P' => 'Style successfully changed for %d users. Note that Founder users were not altered.',
	'LAXSLASH_FORCESTYLES_ACP_PANEL_HEADER' => 'Force User Style',
	'LAXSLASH_FORCESTYLES_ACP_PANEL_DESC' => 'Use this panel to force a style change for users falling under the selected criteria. Note that selecting no criteria will cause all users to be changed. Changes by Non-founder users will not effect Founder users.',
	'LAXSLASH_FORCESTYLES_WARN_ABOUT_OVERRIDE_STYLES_ON' => 'The board setting "override user style" is enabled. You can still use this panel, but users will not see a theme change until that option is disabled.',
	'LAXSLASH_FORCESTYLES_CRITERIA_SECTION_TITLE' => 'Criteria Selection',
	'LAXSLASH_FORCESTYLES_USERNAME_SELECTION_CRITERIA' => 'Select Users by Name',
	'LAXSLASH_FORCESTYLES_USERNAME_SELECTION_CRITERIA_EXPLAIN' => 'Choose users to change the styles for. One username per line. Non-existant users will be ignored.',
	'LAXSLASH_FORCESTYLES_GROUPS_SELECTION_CRITERIA' => 'Select Users by Group',
	'LAXSLASH_FORCESTYLES_GROUPS_SELECTION_CRITERIA_EXPLAIN' => 'Choose users to change the styles for by group. If usernames are set above, users not mentioned will be excluded.',
	'LAXSLASH_FORCESTYLES_CURR_STYLE_SELECTION_CRITERIA' => 'Select Users by Current Style',
	'LAXSLASH_FORCESTYLES_CURR_STYLE_SELECTION_CRITERIA_EXPLAIN' => 'Choose which style a user must be using in order to be effected by the change of styles.',
	'LAXSLASH_FORCESTYLES_STYLE_SELECTION_SECTION_TITLE' => 'New Style Selection',
	'LAXSLASH_FORCESTYLES_SELECT_A_NEW_STYLE' => 'Select a Style',
	'LAXSLASH_FORCESTYLES_SELECT_A_NEW_STYLE_EXPLAIN' => 'Choose the new target style for users matching the criteria above.',
	'ACL_A_LAXSLASH_FORCESTYLES_FORCE_STYLES' => 'Can force styles',
	'LAXSLASH_FORCESTYLES_FORCED_STYLES_ACP_LOG_ENTRY' => 'Forced Styles for user(s)',
));