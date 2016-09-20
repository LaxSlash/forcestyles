<?php
/**
 * This file is a part of the Force Style Changes modification by
 * lax.slash for the phpBB 3.1 Forums Software.
 *
 * @copyright (c) lax.slash <https://www.github.com/LaxSlash>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace laxslash\forcestyles\notification;

use \phpbb\user_loader;
use \phpbb\db\driver\driver_interface;
use \phpbb\user;
use \phpbb\auth\auth;
use \phpbb\config\config;

class change_style extends \phpbb\notification\type\base
{
	protected $user_loader;
	protected $db;
	protected $cache;
	protected $user;
	protected $auth;
	protected $config;
	protected $notification_types_table;
	protected $notifications_table;
	protected $user_notifications_table;
	protected $phpbb_root_path;
	protected $php_ext;

	public function __construct(user_loader $user_loader, driver_interface $db, \phpbb\cache\driver\driver_interface $cache, user $user, auth $auth, config $config, $notification_types_table, $notifications_table, $user_notifications_table, $phpbb_root_path, $php_ext)
	{
		$this->user_loader = $user_loader;
		$this->db = $db;
		$this->cache = $cache;
		$this->user = $user;
		$this->auth = $auth;
		$this->config = $config;
		$this->notification_types_table = $notification_types_table;
		$this->notifications_table = $notifications_table;
		$this->user_notifications_table = $user_notifications_table;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
	}

	public function get_type()
	{
		return 'laxslash.forcestyles.notification.type.change_style';
	}

	protected $language_key = 'LAXSLASH_FORCESTYLES_NOTIFICATION_TYPE_CHANGE_STYLE';

	public static $notification_option = array(
		'group' => 'NOTIFICATION_GROUP_MISCELLANEOUS',
		'lang' => 'LAXSLASH_FORCESTYLES_NOTIFICATION_TYPE_CHANGE_STYLE_OPTION',
	);

	public function is_available()
	{
		return true;
	}

	public static function get_item_id($data)
	{
		return (int) $data['laxslash_forcestyles_notification_id'];
	}

	public static function get_item_parent_id($data)
	{
		return 0;
	}

	public function find_users_for_notification($data, $options = array())
	{
		$options = array_merge(array(
			'ignore_users' => array(),
		), $options);

		$users = $data['notify_users_ary'];

		return $this->check_user_notification_options($users, $options);
	}

	public function get_title()
	{
		return $this->user->lang($this->language_key);
	}

	public function users_to_query()
	{
		return array();
	}

	public function get_url()
	{
		return append_sid($this->phpbb_root_path . 'index.' . $this->php_ext);
	}

	public function get_redirect_url()
	{
		return $this->get_url();
	}

	public function get_email_template()
	{
		return false;
	}

	public function get_reference()
	{
		// Get the style name here.
		$sql = 'SELECT style_name
				FROM ' . STYLES_TABLE . '
				WHERE style_id = ' . $this->get_data('new_style_id');
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$new_style_name = $row['style_name'];
		unset($row);
		$this->db->sql_freeresult($result);

		return $this->user->lang('LAXSLASH_FORCESTYLES_NOTIFICATION_TYPE_CHANGE_STYLE_TEXT', $new_style_name);
	}

	public function get_email_template_variables()
	{
		return array();
	}

	public function create_insert_array($data, $pre_create_data = array())
	{
		$this->set_data('new_style_id', $data['new_style_id']);

		return parent::create_insert_array($data, $pre_create_data);
	}
}
