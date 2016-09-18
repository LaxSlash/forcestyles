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

class force_style_info
{
	public function module()
	{
		return array(
			'filename'		=>		'\laxslash\forcestyles\acp\force_style_module',
			'title'			=>		'ACP_LAXSLASH_FORCESTYLES_MODULE_TITLE',
			'version'		=>		'0.2-PL1 ALPHA',
			'modes'			=>		array(
				'force_styles'		=>		array(
					'title'			=>		'ACP_LAXSLASH_FORCESTYLES_MODULE_TITLE',
					'auth'			=>		'ext_laxslash/forcestyles && acl_a_laxslash_forcestyles_force_styles',
					'cat'			=>		array('ACP_STYLE_MANAGEMENT'),
				),
			),
		);
	}
}