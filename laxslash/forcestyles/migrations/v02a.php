<?php
/**
 * This file is a part of the Force Style Changes modification by
 * lax.slash for the phpBB 3.1 Forums Software.
 *
 * @copyright (c) lax.slash <https://www.github.com/LaxSlash>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace laxslash\forcestyles\migrations;

use phpbb\db\migration\migration;

class v02a extends migration
{
	static public function depends_on()
	{
		return array('\laxslash\forcestyles\migrations\v01a1');
	}

	public function update_data()
	{
		return array(
			array('config.update', array('laxslash_forcestyles_version', '0.2 ALPHA')),
			array('permission.remove', array('a_laxslash_forcestyles_force_styles')),
			array('permission.add', array('a_laxslash_forcestyles_force_styles', true)),
			array('permission.permission_set', array('ROLE_ADMIN_FULL', 'a_laxslash_forcestyles_force_styles')),
			array('permission.permission_set', array('ROLE_ADMIN_STANDARD', 'a_laxslash_forcestyles_force_styles')),
			array('permission.permission_set', array('ROLE_ADMIN_USERGROUP', 'a_laxslash_forcestyles_force_styles')),
			array('module.remove', array(
				'acp',
				'ACP_STYLE_MANAGEMENT',
				array(
					'module_basename' => '\laxslash\forcestyles\acp\force_style_module',
				),
			)),
			array('module.add', array(
				'acp',
				'ACP_STYLE_MANAGEMENT',
				array(
					'module_basename' => '\laxslash\forcestyles\acp\force_style_module',
				),
			)),
		);
	}

	public function revert_data()
	{
		return array(
			array('permission.remove', array('a_laxslash_forcestyles_force_styles')),
			array('module.remove', array(
				'acp',
				'ACP_STYLE_MANAGEMENT',
				array(
					'module_basename' => '\laxslash\forcestyles\acp\force_style_module',
				),
			)),
		);
	}
}