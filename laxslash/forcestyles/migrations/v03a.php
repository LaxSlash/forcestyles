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

class v03a extends migration
{
	static public function depends_on()
	{
		return array('\laxslash\forcestyles\migrations\v02pl1a');
	}

	public function update_data()
	{
		return array(
			array('config.update', array('laxslash_forcestyles_version', '0.3 ALPHA')),
			array('config.add', array('laxslash_forcestyles_notification_id', 0)),
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
}
