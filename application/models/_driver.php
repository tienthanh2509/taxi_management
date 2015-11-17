<?php

/*
 *  Chương trình quản lý Taxi
 *  Thiết kế bởi nhóm 1 lớp D13HT01
 *  Bao gồm các thành viên
 *  Hoàng Huy, Thái Sơn, Tiến Thành, Thanh Thúy, Thanh Vân
 */

/**
 * Description of _driver
 *
 * @author Phạm Tiến Thành <tienthanh.dqc@gmail.com>
 */
class _driver extends CI_Model {

	public function __construct()
	{
		$this->load->database();
		$this->load->model('_users', '_u');
	}

	public function get_schedule($user_id, $datetime = NULL)
	{
		$datetime = $datetime ? $datetime : date('Y-m-d');
		$this->db->where('user_id', $user_id);
		$this->db->where('schedule_date >= ', $datetime);

		return $this->db->get('ci_schedule')->result_array();
	}

	public function get_current_schedule($user_id)
	{
		$shift	 = 0;
		$hour	 = date('H');

		if ($hour < 6)
			$shift	 = 1;
		elseif ($hour < 12)
			$shift	 = 2;
		elseif ($hour < 18)
			$shift	 = 3;
		else
			$shift	 = 4;

		$datetime = date('Y-m-d');
		$this->db->where('user_id', $user_id);
		$this->db->where('schedule_date', date('Y-m-d'));
		$this->db->where('schedule_shift', $shift);

		foreach ($this->db->get('ci_schedule')->result_array() as $row)
		{
			return $row;
		}
	}

}
