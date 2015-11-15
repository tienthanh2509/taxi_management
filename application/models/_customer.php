<?php

/*
 *  Chương trình quản lý Taxi
 *  Thiết kế bởi nhóm 1 lớp D13HT01
 *  Bao gồm các thành viên
 *  Hoàng Huy, Thái Sơn, Tiến Thành, Thanh Thúy, Thanh Vân
 */

/**
 * Description of _customer
 *
 * @author Phạm Tiến Thành <tienthanh.dqc@gmail.com>
 */
class _customer extends CI_Model {

	public function __construct()
	{
		$this->load->database();
	}

	public function get_all($limit = 30, $offset = 0)
	{
		$this->db->limit($limit, $offset);
		return $this->db->get('ci_customer')->result_array();
	}

	public function count_all()
	{
		return $this->db->count_all('ci_customer');
	}

	public function get_by_id($customer_id)
	{
		$this->db->where('customer_id', $customer_id);
		$data = $this->db->get('ci_customer')->result_array();

		if (!isset($data[0]))
			return NULL;
		else
			return $data[0];
	}

	public function get_by_phone($customer_phone)
	{
		$this->db->where('customer_phone', $customer_phone);
		$data = $this->db->get('ci_customer')->result_array();

		if (!isset($data[0]))
			return NULL;
		else
			return $data[0];
	}

	public function add($param)
	{
		if (empty($param))
		{
			return -1;
		}

		$this->db->trans_start();

		$this->db->insert('ci_customer', $param);

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

	public function update($customer_id, $param)
	{
		$this->db->trans_start();

		$this->db->where('customer_id', $customer_id);
		$this->db->update('ci_customer', $param);

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

	public function delete($customer_id)
	{
		$this->db->trans_start();
		$this->db->where('customer_id', $customer_id);
		$this->db->delete('ci_customer');
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
