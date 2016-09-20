<?php
/**
 * This file is a part of the Force Style Changes modification by
 * lax.slash for the phpBB 3.1 Forums Software.
 *
 * @copyright (c) lax.slash <https://www.github.com/LaxSlash>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace laxslash\forcestyles\acp;

class force_style_module
{
	public $u_action;
	public $tpl_name;
	public $page_title;

	public function main($id, $mode)
	{
		global $user, $template, $request, $config, $phpbb_container, $phpbb_root_path, $phpEx, $db, $phpbb_log;

		$fstyles_acp_sql = $phpbb_container->get('laxslash.forcestyles.fstyles_acp_sql');

		$this->tpl_name = 'acp_fstyles_main';

		$this->page_title = $user->lang('ACP_LAXSLASH_FORCESTYLES_PANEL_TITLE');

		add_form_key('laxslash/forcestyles');

		// If only one theme is installed and/or available, we give an error saying that this module can not be accessed.
		// So, get the list of themes, here...
		$styles_coll = $fstyles_acp_sql->get_styles();

		if (count($styles_coll) <= 1)
		{
			// Say that there needs to be more than one style to ride this ride.
			unset($styles_coll);
			trigger_error('ACP_LAXSLASH_FORCESTYLES_NOT_ENOUGH_STYLES_TO_USE', E_USER_WARNING);
		}

		// Make an errors checking array here.
		$errors = array();

		if ($request->is_set_post('submit'))
		{
			if (!check_form_key('laxslash/forcestyles'))
			{
				// Invalid form key, error!
				$user->add_lang('acp/common');
				$errors[] = 'FORM_INVALID';
			}

			// Make an array of just style IDs, and an array containing their names with the ID as the key value.
			foreach ($styles_coll as $working_style)
			{
				$style_ids_coll[] = $working_style['style_id'];
				$style_names[$working_style['style_id']] = $working_style['style_name'];
			}
			unset($working_style);

			// Assume the form test passed, follow through with the changing process here.
			// Was the user's preferred theme requested? If so, we have to get it.
			// Does this theme exist and pass all of the checks required for it to take effect?

			// Perform checks to make sure that the theme exists, the user has the permissions to change it, if a founder manage group is selected that
			// the user has the proper founder authentications, and that the style is active.

			$users_text = $request->variable('laxslash_forcestyles_usernames', '', true);
			$groups_selected = $request->variable('selected_group_ids', array(0));
			$styles_selected = $request->variable('selected_style_ids', array(0));
			$new_style_id = $request->variable('laxslash_forcestyles_select_new_style_id', '');
			$send_notification = $request->variable('laxslash_forcestyles_send_notification', false);
			$select_anonymous = $request->variable('laxslash_forcestyles_select_anonymous_user_id', false);

			// Convert $users_text into an array of Usernames

			$usernames = array();
			$selected_usernames = array();

			// Add the Anonymous username to the text if selected.
			if ($select_anonymous)
			{
				$sql = 'SELECT username
						FROM ' . USERS_TABLE . '
						WHERE user_id = ' . ANONYMOUS;
				$result = $db->sql_query($sql);
				$row = $db->sql_fetchrow($result);
				$db->sql_freeresult($result);
				$users_text .= "\n" . $row['username'];
				unset($row);
			}

			if ($users_text != '')
			{
				$usernames = explode("\n", $users_text);
				$usernames = array_unique($usernames);
				$selected_usernames = $usernames; // For logging purposes later on.
			}
			unset($users_text);

			if (empty($groups_selected))
			{
				unset($groups_selected);
			}

			if (empty($styles_selected))
			{
				unset($styles_selected);
			}

			// Perform the checks here.
			// If a style is not in the styles_coll array, but it is in the criteria array, stop the process and warn the user.
			if (!empty($styles_selected))
			{
				foreach ($styles_selected as $working_style)
				{
					if (!in_array($working_style, $style_ids_coll))
					{
						// Nope!
						$errors[] = 'ACP_LAXSLASH_FORCESTYLES_UNACCEPTABLE_STYLE_SELECTED';
					}
				}
			}

			if (!empty($groups_selected))
			{
				$groups_coll = $fstyles_acp_sql->get_groups();
				$affected_groups = array();
				foreach ($groups_coll as $working_group)
				{
					$group_chks_coll[$working_group['group_id']]['group_founder_manage'] = $working_group['group_founder_manage'];
					$group_names_coll[$working_group['group_id']] = ($working_group['group_type'] == GROUP_SPECIAL) ? $user->lang('G_' . $working_group['group_name']) : $working_group['group_name'];
					$group_ids_coll[] = $working_group['group_id'];
				}
				unset($groups_coll);

				$i = (int) 0;
				foreach ($groups_selected as $working_group)
				{
					// Make sure that we're not trying to work with any founder managed groups here, if the user's not a founder.
					if ($user->data['user_type'] != USER_FOUNDER && $group_chks_coll[$working_group]['group_founder_manage'])
					{
						// Sorry, but no.
						$errors[] = $user->lang('ACP_LAXSLASH_FORCESTYLES_FOUNDER_GROUP_SELECTED_BY_NON_FOUNDER_USER', $group_checks_coll[$working_group][group_name]);
					} elseif (!in_array($working_group, $group_ids_coll)) {     // Do all of these groups actually exist?
						// Hello, what's this? A non-existant group? Only put this error in for the first one not found.
						$i++;
						if ($i == 1)
						{
							$errors[] = 'ACP_LAXSLASH_FORCESTYLES_NON_EXISTANT_GROUP_OR_GROUPS_SELECTED';
						}
					} else {
						// Add the selected group into the $affected_groups array().
						$affected_groups[] = $group_names_coll[$working_group];
					}
				}
				unset($group_ids_coll);
			}

			// Make sure that the NEW Style ID is in the style_ids_coll list.
			if (!in_array($new_style_id, $style_ids_coll))
			{
				// Not sure if Inspect Element, or sheer bad luck.....
				$errors[] = 'ACP_LAXSLASH_FORCESTYLES_NEW_STYLE_NOT_FOUND_OR_USABLE';
			}
			unset($style_ids_coll);

			if (!sizeof($errors))
			{
				// All checks passed.
				// Unset unneeded arrays here now.

				// And make the newly needed arrays here now.
				$affected_ids = array();
				$affected_usernames_pre = array();
				$affected_usernames = array();

				// Get all affected User IDs here.
				$sql_user_ids_where = 'user_style != ' . $new_style_id;

				// Check for founder status
				$sql_user_ids_where .= ($user->data['user_type'] != USER_FOUNDER) ? ' AND user_type != ' . USER_FOUNDER : '';

				if (!empty($groups_selected) || !empty($usernames) || !empty($styles_selected))
				{
					$sql_user_ids_where .= ' AND (';

					// Get users in each selected usergroup here.
					if (!empty($groups_selected))
					{
						if (!function_exists('group_memberships'))
						{
							require($phpbb_root_path . 'includes/functions_user.' . $phpEx);
						}

						$group_members = group_memberships($groups_selected);

						foreach ($group_members as $current_member)
						{
							$usernames[] = $current_member['username'];
						}

						unset($group_members);
						unset($groups_selected);
					}

					// Get usernames. Group members/users will already be included in the array.
					$sql_user_ids_where .= (!empty($usernames)) ? ' ' . $db->sql_in_set('username', $usernames) : '';

					// Get style users.
					$sql_user_ids_where .= (!empty($styles_selected) && !empty($usernames)) ? ' OR ' . $db->sql_in_set('user_style', $styles_selected) : (!empty($styles_selected)) ? ' ' . $db->sql_in_set('user_style', $styles_selected) : '';

					$sql_user_ids_where .= ')';

					// Set an array of the affected styles and style names.
					if (!empty($styles_selected))
					{
						foreach ($styles_selected as $current_style)
						{
							if (isset($style_names[$current_style]))
							{
								// Failsafe.
								$affected_styles[] = $style_names[$current_style];
							}

							unset($current_style);
						}
					}
				}

				$sql = 'SELECT user_id, username
						FROM ' . USERS_TABLE . '
						WHERE ' . $sql_user_ids_where . '
						ORDER BY username ASC';
				$result = $db->sql_query($sql);
				while ($row = $db->sql_fetchrow($result))
				{
					$affected_ids[] = (int) $row['user_id'];
					$affected_usernames_pre[] = $row['username'];

					unset($row);
				}
				$db->sql_freeresult($sql);

				// array_intersect here for the right/needed usernames:
				$affected_usernames = array_intersect($affected_usernames_pre, $selected_usernames);
				unset($affected_usernames_pre);

				// Now, update the table with the selected users?
				if (!empty($affected_ids))
				{
					// Make the affected_ids array to be unique
					$affecte_ids = array_unique($affected_ids);
					$sql = 'UPDATE ' . USERS_TABLE . '
							SET user_style = ' . $new_style_id . '
							WHERE ' . $db->sql_in_set('user_id', $affected_ids);
					$db->sql_query($sql);

					// Get effected users count here.
					$changed_users = $db->sql_affectedrows();

					// Send the notification here if we need to.
					if ($send_notification)
					{
						$notification_manager = $phpbb_container->get('notification_manager'); // Load the notifications manager here.

						$config->increment('laxslash_forcestyles_notification_id', 1);

						$notify_data = array(
							'new_style_id' => $new_style_id,
							'notify_users_ary' => $affected_ids,
							'laxslash_forcestyles_notification_id' => $config['laxslash_forcestyles_notification_id'],
						);

						$notification_manager->add_notifications(array(
							'laxslash.forcestyles.notification.type.change_style',
						), $notify_data);

						unset($notify_data);
					}
					// Unset things now.
					unset($affected_ids);
					unset($group_chks_coll);

					// Forced Styles Logging Feature
					$selection_criteria_used_for_log = '';
					$new_style_name = $style_names[$new_style_id];

					if (!empty($affected_usernames) || !empty($affected_groups) || !empty($affected_styles))
					{
						$selection_criteria_used_for_log .= $user->lang('LAXSLASH_FORCESTYLES_LOG_ENTRY_CRITERIA_SPECIFIED_PRE');
						$selection_criteria_used_for_log .= (!empty($affected_usernames)) ? $user->lang('LAXSLASH_FORCESTYLES_LOG_ENTRY_USERNAMES', implode(', ', $affected_usernames)) : '';
						$selection_criteria_used_for_log .= (!empty($affected_groups)) ? $user->lang('LAXSLASH_FORCESTYLES_LOG_ENTRY_GROUPS', implode(', ', $affected_groups)) : '';
						$selection_criteria_used_for_log .= (!empty($affected_styles)) ? $user->lang('LAXSLASH_FORCESTYLES_LOG_ENTRY_STYLES', implode(', ', $affected_styles)) : '';
					} else {
						$selection_criteria_used_for_log = $user->lang('LAXSLASH_FORCESTYLES_LOG_ENTRY_NO_CRITERIA_SPECIFIED');
					}
					$selection_criteria_used_for_log .= ($send_notification) ? $user->lang('LAXSLASH_FORCESTYLES_LOG_ENTRY_NOTIFICATION_SENT') : '';

					if ($user->data['user_type'] != USER_FOUNDER)
					{
						$phpbb_log->add('admin', $user->data['user_id'], $user->ip, 'LAXSLASH_FORCESTYLES_ACP_NON_FOUNDER_LOG_ENTRY', time(), array($new_style_name, $selection_criteria_used_for_log));
					} else {
						$phpbb_log->add('admin', $user->data['user_id'], $user->ip, 'LAXSLASH_FORCESTYLES_ACP_LOG_ENTRY', time(), array($new_style_name, $selection_criteria_used_for_log));
					}

					// Well done.
					if ($user->data['user_type'] == USER_FOUNDER)
					{
						trigger_error($user->lang('ACP_LAXSLASH_FORCESTYLES_FORCED_SUCCESSFULLY', $changed_users) . adm_back_link($this->u_action));
					} else {
						trigger_error($user->lang('ACP_LAXSLASH_FORCESTYLES_NON_FOUNDER_FORCED_SUCCESSFULLY', $changed_users) . adm_back_link($this->u_action));
					}
				}  else {
					$errors[] = $user->lang('ACP_LAXSLASH_FORCESTYLES_NO_APPLICABLE_USERS_FOUND');
				}
			}
		}

		// Generate the page here.

		// The first section should contain the criteria of which users to apply the changes to.
		// Usernames, groups and current theme are the three possible criteria.
		// The second section should provide a listing of all the themes installed on the boards.
		$criteria_styles = ''; // Define a string variable here to prevent a PHP Notice.
		foreach ($styles_coll as $current_style)
		{
			// We can only force active styles here.
			if ($current_style['style_active'])
			{
				$criteria_styles .= '<option value="' . $current_style['style_id'] . '">' . $current_style['style_name'] . '</option>';
			}
		}
		// Usnet the Styles Collection array.
		unset($styles_coll);

		// Get the groups for the criteria here.
		$show_group_types = ($user->data['user_type'] == USER_FOUNDER) ? false : 0;
		$selectable_groups = group_select_options(false, false, $show_group_types);

		// Display a warning if "Force User Theme" is enabled in Board Settings, but still allow the process to occur/take place.
		$template->assign_vars(array(
			'S_LAXSLASH_FORCESTYLES_WARN_OVERRIDE_STYLES_ENABLED' => $config['override_user_style'],
			'U_LAXSLASH_FORCESTYLES_FIND_USERNAME' => append_sid("{$phpbb_root_path}memberlist.$phpEx", '&mode=searchuser&amp;form=acp_laxslash_forcestyles_force_style&amp;field=laxslash_forcestyles_usernames'),
			'S_LAXSLASH_FORCESTYLES_GROUP_OPTIONS' => $selectable_groups,
			'S_LAXSLASH_FORCESTYLES_STYLE_CRITERIA_OPTIONS' => $criteria_styles,
			'U_ACTION' => $this->u_action,
			'S_ERRORS' => (sizeof($errors)) ? true : false,
			'ERRORS_OUTPUT' => (sizeof($errors)) ? implode('<br />', $errors) : '',
			'S_ERROR_BOX_NEEDED' => (sizeof($errors) || $config['override_user_style']) ? true : false,
			'ANONYMOUS_USER_ID' => ANONYMOUS,
		));

		unset($errors);
	}
}
