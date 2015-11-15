<?php

/*
 *  Chương trình quản lý Taxi
 *  Thiết kế bởi nhóm 1 lớp D13HT01
 *  Bao gồm các thành viên
 *  Hoàng Huy, Thái Sơn, Tiến Thành, Thanh Thúy, Thanh Vân
 */

/**
 * Lớp xử lý dữ liệu nhóm người dùng
 *
 * @author Phạm Tiến Thành <tienthanh.dqc@gmail.com>
 */
class _groups extends CI_Model {

	public function __construct()
	{
		$this->load->database();
	}

	public function get_all($limit = 30, $offset = 0)
	{
		$this->db->limit($limit, $offset);
		return $this->db->get('ci_groups')->result_array();
	}

	public function count_all()
	{
		return $this->db->count_all('ci_groups');
	}

	public function get_by_id($group_id)
	{
		$this->db->where('group_id', $group_id);
		$data = $this->db->get('ci_groups')->result_array();

		if (!isset($data[0]))
			return NULL;
		else
			return $data[0];
	}

	public function add($param)
	{
		$this->db->trans_start();
		$this->db->insert('ci_groups', $param);
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

	public function update($group_id, $param)
	{
		// Bảo vệ danh sách nhóm
		if ($group_id < 6 && isset($param['group_id']) && $group_id != $param['group_id'])
		{
			return -1;
		}

		$this->db->trans_start();
		$this->db->where('group_id', $group_id);
		$this->db->update('ci_groups', $param);
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

	public function delete($group_id)
	{
		// Bảo vệ danh sách nhóm
		if ($group_id < 7)
		{
			return -1;
		}

		$this->db->trans_start();
		$this->db->where('group_id', $group_id);
		$this->db->delete('ci_groups');
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
