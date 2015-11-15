<?php

/*
 *  Chương trình quản lý Taxi
 *  Thiết kế bởi nhóm 1 lớp D13HT01
 *  Bao gồm các thành viên
 *  Hoàng Huy, Thái Sơn, Tiến Thành, Thanh Thúy, Thanh Vân
 */

/**
 * Description of Booking
 *
 * @author Phạm Tiến Thành <tienthanh.dqc@gmail.com>
 * @property _booking $_b
 */
class Booking extends CI_D13HT01 {

	public function __construct()
	{
		parent::__construct();

		$this->load->model('_booking', '_b');
	}

	public function index()
	{
		$page	 = (int) $this->input->get('page') ? $this->input->get('page') : 1;
		$page	 = $page < 1 ? 1 : $page;
		$all	 = $this->input->get('all') == 'true' ? TRUE : FALSE;

		$this->load->library('pagination');

		$config['base_url']		 = $this->config->site_url('admin/booking') . ($all ? '?all=true' : '');
		$config['total_rows']	 = $all ? $this->_b->count_all() : $this->_b->count_all_pending();
		$config['per_page']		 = 10;

		$this->data['booking_pending_list'] = $all ? $this->_b->get_all($config['per_page'], ($page - 1) * $config['per_page']) : $this->_b->get_all_pending($config['per_page'], ($page - 1) * $config['per_page']);

		$this->pagination->initialize($config);

		$this->data['pagination'] = $this->pagination->create_links();

		$this->render('admin/booking/welcome');
	}

	public function delete($booking_id = '')
	{
		$this->_b->delete($booking_id);
		redirect('admin/booking?all=true');
	}

}
