<?php

/*
 *  Chương trình quản lý Taxi
 *  Thiết kế bởi nhóm 1 lớp D13HT01
 *  Bao gồm các thành viên
 *  Hoàng Huy, Thái Sơn, Tiến Thành, Thanh Thúy, Thanh Vân
 */

/**
 * Description of _accountant
 *
 * @author Phạm Tiến Thành <tienthanh.dqc@gmail.com>
 */
class _accountant extends CI_Model {

	public function __construct()
	{
		$this->load->database();
	}

	public function view_stat_groups_users()
	{
		return $this->db->get('view_stat_groups_users')->result_array();
	}

	public function view_stat_car()
	{
		return $this->db->get('view_stat_car')->result_array();
	}

	public function stat_order()
	{
		$this->db->select('
			YEAR(order_date) AS year,
			MONTH(order_date) AS month,
			SUM(order_price) AS total
		');
		$this->db->group_by('YEAR(order_date), MONTH(order_date)');
		return $this->db->get('ci_order')->result_array();
	}

	public function stat_booking()
	{
		$this->db->select('
			YEAR(booking_date) AS year,
			MONTH(booking_date) AS month,
			DAY(booking_date) AS day,
			COUNT(booking_id) AS total
		');
		$this->db->group_by('YEAR(booking_date), MONTH(booking_date), DAY(booking_date)');
		return $this->db->get('ci_booking')->result_array();
	}

}
