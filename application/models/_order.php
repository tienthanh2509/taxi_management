<?php

/*
 *  Chương trình quản lý Taxi
 *  Thiết kế bởi nhóm 1 lớp D13HT01
 *  Bao gồm các thành viên
 *  Hoàng Huy, Thái Sơn, Tiến Thành, Thanh Thúy, Thanh Vân
 */

/**
 * Description of _order
 *
 * @author Phạm Tiến Thành <tienthanh.dqc@gmail.com>
 */
class _order extends CI_Model {

	public function __construct()
	{
		$this->load->database();
	}

	public function get_all($limit = 30, $offset = 0)
	{
		$this->db->limit($limit, $offset);
		return $this->db->get('view_trips_order')->result_array();
	}

	public function count_all()
	{
		return $this->db->count_all('view_trips_order');
	}

	public function get_by_id($order_id)
	{
		$this->db->where('order_id', $order_id);
		$data = $this->db->get('view_trips_order')->result_array();

		if (!isset($data[0]))
			return NULL;
		else
			return $data[0];
	}

	public function add($param)
	{
		$this->db->trans_start();
		$this->db->insert('ci_order', $param);
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

	public function update($order_id, $param)
	{
		$this->db->trans_start();
		$this->db->where('order_id', $order_id);
		$this->db->update('ci_order', $param);
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

	public function delete($order_id)
	{
		$this->db->trans_start();
		$this->db->where('order_id', $order_id);
		$this->db->delete('ci_order');
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
