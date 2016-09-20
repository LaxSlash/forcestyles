<?php
/**
 * This file is a part of the Force Style Changes modification by
 * lax.slash for the phpBB 3.1 Forums Software.
 *
 * @copyright (c) lax.slash <https://www.github.com/LaxSlash>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace laxslash\forcestyles\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event listener
 */

class main_listener implements EventSubscriberInterface
{
	static public function getSubscribedEvents()
	{
		return array(
			'core.user_setup' => 'load_language_on_setup',
			'core.permissions' => 'add_permissions',
		);
	}

	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'laxslash/forcestyles',
			'lang_set' => 'acp_forcestyles',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}

	public function add_permissions($event)
	{
		$permissions = $event['permissions'];
		$permissions['a_laxslash_forcestyles_force_styles'] = array(
			'lang' => 'ACL_A_LAXSLASH_FORCESTYLES_FORCE_STYLES',
			'cat' => 'misc',
		);
		$event['permissions'] = $permissions;
		unset($permissions);
	}
}

