<?php

/*
 *  Chương trình quản lý Taxi
 *  Thiết kế bởi nhóm 1 lớp D13HT01
 *  Bao gồm các thành viên
 *  Hoàng Huy, Thái Sơn, Tiến Thành, Thanh Thúy, Thanh Vân
 */

/**
 * Lớp xử lý dữ liệu nhân viên
 *
 * @author Phạm Tiến Thành <tienthanh.dqc@gmail.com>
 * @property _groups $_g Lớp thao tác dữ liệu
 * @property _groups_users $_g2 Lớp thao tác dữ liệu
 */
class _users extends CI_Model {

	public function __construct()
	{
		$this->load->database();

		$this->load->model('_groups', '_g');
		$this->load->model('_groups_users', '_g2');
	}

	public function get_all($limit = 30, $offset = 0)
	{
		$this->db->limit($limit, $offset);
		return $this->db->get('view_users')->result_array();
	}

	public function count_all()
	{
		return $this->db->count_all('view_users');
	}

	public function get_by_id($user_id)
	{
		$this->db->where('user_id', $user_id);
		$data = $this->db->get('view_users')->result_array();

		if (!isset($data[0]))
			return NULL;
		else
			return $data[0];
	}

	public function get_by_user_name($user_name)
	{
		$this->db->where('user_name', $user_name);
		$data = $this->db->get('view_users')->result_array();

		if (!isset($data[0]))
			return NULL;
		else
			return $data[0];
	}

	public function add($param, $group_list)
	{
		if (empty($param) || empty($group_list))
		{
			return 0;
		}

		$this->db->trans_start();
		$this->db->insert('ci_users', $param);
		$this->db->where('user_name', $param['user_name']);
		$new_user = $this->db->get('ci_users')->result_array();

		if (empty($new_user['0']))
			return -1;
		else
			$new_user = $new_user[0];

		$grant = [];
		foreach ($group_list as $group)
		{
			$grant[] = [
				'group_id'	 => $group,
				'user_id'	 => $new_user['user_id']
			];
		}
		$this->db->insert_batch('ci_groups_users', $grant);
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE)
		{
			return 0;
		}
		else
		{
			return 1;
		}
	}

	public function update($user_id, $param, $new_group)
	{
		$this->db->trans_start();

		$this->db->where('user_id', $user_id);
		$this->db->update('ci_users', $param);

		$grant		 = [];
		$revoke		 = [];
		$group_list	 = [];
		$old_group	 = [];

		foreach ($this->_g2->get_by_uid($user_id) as $value)
		{
			$old_group[] = $value['group_id'];
		}
		foreach ($this->_g->get_all() as $value)
		{
			$group_list[] = $value['group_id'];
		}
		foreach ($group_list as $g)
		{
			if (in_array($g, $old_group) && !in_array($g, $new_group))
			{
				$revoke[] = [
					'group_id'	 => $g,
					'user_id'	 => $user_id
				];
			}
			if (!in_array($g, $old_group) && in_array($g, $new_group))
			{
				$grant[] = [
					'group_id'	 => $g,
					'user_id'	 => $user_id
				];
			}
		}

		if (!empty($grant))
		{
			$this->db->insert_batch('ci_groups_users', $grant);
		}
		if (!empty($revoke))
		{
			foreach ($revoke as $value)
			{
				$this->db->delete('ci_groups_users', $value);
			}
		}

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE)
		{
			return 0;
		}
		else
		{
			return 1;
		}
	}

	public function delete($user_id)
	{
		$this->db->trans_start();
		$this->db->where('user_id', $user_id);
		$this->db->delete('ci_users');
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE)
		{
			return 0;
		}
		else
		{
			return 1;
		}
	}

}
