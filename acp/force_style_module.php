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
		global $user, $template, $request, $config, $phpbb_container, $phpbb_root_path, $phpEx, $db;

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

		if ($request->is_set_post('submit'))
		{
			// Make an errors checking array here.
			$errors = array();

			if (!check_form_key('laxslash/forcestyles'))
			{
				// Invalid form key, error!
				$user->add_lang('acp/common');
				$errors[] = 'FORM_INVALID';
			}

			// Make an array of just style IDs.
			foreach ($styles_coll as $working_style)
			{
				$style_ids_coll[] = $working_style['style_id'];
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

			// Convert $users_text into an array of Usernames
			if ($users_text != '')
			{
				$usernames = explode("\n", $users_text);
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
			if (isset($styles_selected))
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

			if (isset($groups_selected))
			{
				$groups_coll = $fstyles_acp_sql->get_groups();
				foreach ($groups_coll as $working_group)
				{
					$group_chks_coll[$working_group['group_id']]['group_founder_manage'] = $working_group['group_founder_manage'];
					$group_chks_coll[$working_group['group_id']]['group_name'] = $working_group['group_name'];
					$group_ids_coll[] = $working_group['group_id'];
				}
				unset($groups_coll);

				foreach ($groups_selected as $working_group)
				{
					// Make sure that we're not trying to work with any founder managed groups here, if the user's not a founder.
					if ($user->data['user_type'] != USER_FOUNDER)
					{
						if ($group_chks_coll[$working_group['group_id']]['group_founder_manage'] == true)
						{
							// Sorry, but no.
							$errors[] = $user->lang('ACP_LAXSLASH_FORCESTYLES_FOUNDER_GROUP_SELECTED_BY_NON_FOUNDER_USER', $group_checks_coll[$working_group['group_id']][group_name]);
						}
					}

					// Do all of these groups actually exist?
					if (!in_array($working_group, $group_ids_coll))
					{
						// Hello, what's this? A non-existant group? Only put this error in for the first one not found.
						$i++;
						if ($i == 1)
						{
							$errors[] = 'ACP_LAXSLASH_FORCESTYLES_NON_EXISTANT_GROUP_OR_GROUPS_SELECTED';
						}
					}
				}
				unset($group_ids_coll);
				unset($group_chks_coll);
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
				unset($styles_coll);
				unset($errors);

				// Base SQL Statement starts here.
				$sql_arr = array(
					'user_style' => $new_style_id,
				);

				$sql = 'UPDATE ' . USERS_TABLE . '
						SET ' . $db->sql_build_array('UPDATE', $sql_arr);
				$sql .= ($user->data['user_type'] != USER_FOUNDER) ? ' WHERE user_type != ' . USER_FOUNDER . ' AND user_style != ' . $new_style_id : 'WHERE user_style != ' . $new_style_id;

				if (!isset($usernames) && !isset($groups_selected) && !isset($styles_selected))
				{
					// No criteria was set by the user. This makes our life easy, just go ahead and apply the changes to all users. Check for founders and founder status, too. (Done already)
					$db->sql_query($sql);

					// Unset things now.


					// Well done.
					$changed_users_count = $db->sql_affectedrows();
					if ($changed_users_count == 0 || $changed_users_count > 1)
					{
						if ($user->data['user_type'] == USER_FOUNDER)
						{
							trigger_error($user->lang('ACP_LAXSLASH_FORCESTYLES_FORCED_SUCCESSFULLY_P', $changed_users_count) . adm_back_link($this->u_action));
						} else {
							trigger_error($user->lang('ACP_LAXSLASH_FORCESTYLES_NON_FOUNDER_FORCED_SUCCESSFULLY_P', $changed_users_count) . adm_back_link($this->u_action));
						}
					} else {
						if ($user->data['user_type'] == USER_FOUNDER)
						{
							trigger_error($user->lang('ACP_LAXSLASH_FORCESTYLES_FORCED_SUCCESSFULLY_S', $changed_users_count) . adm_back_link($this->u_action));
						} else {
							trigger_error($user->lang('ACP_LAXSLASH_FORCESTYLES_NON_FOUNDER_FORCED_SUCCESSFULLY_S', $changed_users_count) . adm_back_link($this->u_action));
						}
					}
				}

				if (isset($usernames) || isset($groups_selected))
				{
					// Setup the new final array here.
					$usernames_final_selected = array();

					if (isset($usernames))
					{
						// Add the usernames to the selections criteria.
						$usernames_final_selected = $usernames;
					}

					if (isset($groups_selected))
					{
						// Add the user groups to the selection criteria.

						// Step 1: Get all users in the selected groups.
						if (!function_exists(group_memberships))
						{
							require($phpbb_root_path . 'includes/functions_user.' . $phpEx);
						}

						$group_selected_users_pre = group_memberships($groups_selected);
						foreach ($group_selected_users_pre as $working_user)
						{
							$group_selected_users[] = $working_user['username'];
						}

						// Step 2: If users within these groups are NOT in the $usernames array, subtract them out.
						if (isset($usernames))
						{
							// Overwrite the array?)
							$usernames_final_selected = array_intersect($usernames, $group_selected_users);
							unset($usernames);
						} else {
							$usernames_final_selected = $group_selected_users;
						}
						unset($group_selected_users);
					} else {
						unset($usernames);
					}

					// Add it to the query here.
					$sql .= ' AND ' . $db->sql_in_set('username', $usernames_final_selected);
				}

				if (isset($styles_selected))
				{
					// Add the current styles to the selections criteria.
					$sql .= ' AND ' . $db->sql_in_set('user_style', $styles_selected);
				}

				// Cleared for takeoff.
				$db->sql_query($sql);

				// Unset things now.


				// Well done.
				$changed_users_count = $db->sql_affectedrows();
				if ($changed_users_count == 0 || $changed_users_count > 1)
				{
					if ($user->data['user_type'] == USER_FOUNDER)
					{
						trigger_error($user->lang('ACP_LAXSLASH_FORCESTYLES_FORCED_SUCCESSFULLY_P', $changed_users_count) . adm_back_link($this->u_action));
					} else {
						trigger_error($user->lang('ACP_LAXSLASH_FORCESTYLES_NON_FOUNDER_FORCED_SUCCESSFULLY_P', $changed_users_count) . adm_back_link($this->u_action));
					}
				} else {
					if ($user->data['user_type'] == USER_FOUNDER)
					{
						trigger_error($user->lang('ACP_LAXSLASH_FORCESTYLES_FORCED_SUCCESSFULLY_S', $changed_users_count) . adm_back_link($this->u_action));
					} else {
						trigger_error($user->lang('ACP_LAXSLASH_FORCESTYLES_NON_FOUNDER_FORCED_SUCCESSFULLY_S', $changed_users_count) . adm_back_link($this->u_action));
					}
				}
			}
		}

		// Generate the page here.

		// The first section should contain the criteria of which users to apply the changes to.
		// Usernames, groups and current theme are the three possible criteria.
		// The second section should provide a listing of all the themes installed on the boards.
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
		));

		unset($errors);
	}
}
