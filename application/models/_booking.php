<?php

/*
 *  Chương trình quản lý Taxi
 *  Thiết kế bởi nhóm 1 lớp D13HT01
 *  Bao gồm các thành viên
 *  Hoàng Huy, Thái Sơn, Tiến Thành, Thanh Thúy, Thanh Vân
 */

/**
 * Thao tác CSDL bảng ci_booking
 *
 * @author Phạm Tiến Thành <tienthanh.dqc@gmail.com>
 */
class _booking extends CI_Model {

	public function __construct()
	{
		$this->load->database();
	}

	public function get_all($limit = 30, $offset = 0)
	{
		$this->db->limit($limit, $offset);
		return $this->db->get('ci_booking')->result_array();
	}

	public function get_all_pending($limit = 30, $offset = 0)
	{
		$this->db->where('booking_status < ', 2);
		$this->db->order_by('booking_date');
		$this->db->limit($limit, $offset);
		return $this->db->get('ci_booking')->result_array();
	}

	public function count_all_pending()
	{
		$this->db->select('COUNT(booking_id) AS total');
		$this->db->where('booking_status < ', 2);
		return $this->db->get('ci_booking')->first_row()->total;
	}

	public function count_all()
	{
		return $this->db->count_all('ci_booking');
	}

	public function get_by_id($booking_id)
	{
		$this->db->where('booking_id', $booking_id);
		$data = $this->db->get('ci_booking')->result_array();

		if (!isset($data[0]))
			return NULL;
		else
			return $data[0];
	}

	public function add($param)
	{
		$this->db->trans_start();
		$this->db->insert('ci_booking', $param);
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

	public function update($booking_id, $param)
	{
		$this->db->trans_start();
		$this->db->where('booking_id', $booking_id);
		$this->db->update('ci_booking', $param);
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

	public function delete($booking_id)
	{
		$this->db->trans_start();
		$this->db->where('booking_id', $booking_id);
		$this->db->delete('ci_booking');
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
