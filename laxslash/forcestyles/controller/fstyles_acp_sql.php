<?php
/**
 * This file is a part of the Force Style Changes modification by
 * lax.slash for the phpBB 3.1 Forums Software.
 *
 * @copyright (c) lax.slash <https://www.github.com/LaxSlash>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace laxslash\forcestyles\controller;

use phpbb\db\driver\driver_interface;

class fstyles_acp_sql
{
	/* @var $db */
	protected $db;

	/**
	 *  Constructor
	 *
	 * @param driver_interface $db
	 */
	public function __construct(driver_interface $db)
	{
		$this->db = $db;
	}

	/**
	 * Fetch styles in the database, and return them all in a table.
	 *
	 * @return Array An array of all active and installed database styles
	 */
	public function get_styles()
	{
		$sql = 'SELECT *
				FROM ' . STYLES_TABLE .'
				WHERE style_active = 1';
		$result = $this->db->sql_query($sql);
		$rowset = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		return $rowset;
	}

	/**
	 * Fetch all groups from the database.
	 *
	 * @return Array An array of all of the allowed groups.
	 */
	public function get_groups()
	{
		$sql = 'SELECT group_id, group_name, group_founder_manage
				FROM ' . GROUPS_TABLE;
		$result = $this->db->sql_query($sql);
		$rowset = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		return $rowset;
	}
}
