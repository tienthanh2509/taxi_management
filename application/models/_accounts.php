<?php

/*
 *  Chương trình quản lý Taxi
 *  Thiết kế bởi nhóm 1 lớp D13HT01
 *  Bao gồm các thành viên
 *  Hoàng Huy, Thái Sơn, Tiến Thành, Thanh Thúy, Thanh Vân
 */

/**
 * Thao tác với tài khoản người dùng
 *
 * @author Phạm Tiến Thành <tienthanh.dqc@gmail.com>
 * @property _users $_u Thao tác với lớp người dùng
 */
class _accounts extends CI_Model {

	public function __construct()
	{
		$this->load->database();
		$this->load->model('_users', '_u');
	}

	function is_admin($userid)
	{
		$this->db->where('group_id', 1);
		$this->db->where('user_id', $userid);
		$query = $this->db->get('ci_groups_users');
		if ($query->num_rows() < 1)
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	function login($username, $password)
	{
		$this->db->where('user_id', $username);
		$this->db->or_where('user_name', $username);
		$this->db->or_where('user_email', $username);
		$user = $this->db->get('ci_users')->result_array();

		if (empty($user[0]) || !password_verify($password, $user[0]['user_password']))
		{
			return 0;
		}
		else
		{
			return $this->is_admin($user[0]['user_id']) ? $user[0] : -1;
		}
	}

	function change_password($user_name, $password_old, $password_new)
	{
		$user = $this->_u->get_by_user_name($user_name);

		if (!$user)
		{
			return 0;
		}
		elseif ($password_old == $password_new)
		{
			return -1;
		}
		elseif (!password_verify($password_old, $user['user_password']))
		{
			return -2;
		}
		else
		{
			$data = [
				'user_password' => password_hash($password_new, PASSWORD_DEFAULT)
			];

			$this->db->where('user_name', $user_name);
			$this->db->update('ci_users', $data);
			$row = $this->db->affected_rows();

			if ($row < 1)
			{
				return -3;
			}
			else
			{
				return 1;
			}
		}
	}

}
