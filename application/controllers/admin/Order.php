<?php

/*
 *  Chương trình quản lý Taxi
 *  Thiết kế bởi nhóm 1 lớp D13HT01
 *  Bao gồm các thành viên
 *  Hoàng Huy, Thái Sơn, Tiến Thành, Thanh Thúy, Thanh Vân
 */

/**
 * Description of Order
 *
 * @author Phạm Tiến Thành <tienthanh.dqc@gmail.com>
 * @property _order $_o
 */
class Order extends CI_D13HT01 {

	public function __construct()
	{
		parent::__construct();

		$this->load->model('_order', '_o');
	}

	public function index()
	{
		$page	 = (int) $this->input->get('page') ? $this->input->get('page') : 1;
		$page	 = $page < 1 ? 1 : $page;

		$this->load->library('pagination');

		$config['base_url']		 = $this->config->site_url('admin/order');
		$config['total_rows']	 = $this->_o->count_all();
		$config['per_page']		 = 10;

		$this->data['order_list'] = $this->_o->get_all($config['per_page'], ($page - 1) * $config['per_page']);

		$this->pagination->initialize($config);

		$this->data['pagination'] = $this->pagination->create_links();

		$this->render('admin/order/welcome');
	}

	public function delete($order_id = '')
	{
		$this->_b->delete($order_id);
		redirect('admin/order');
	}

}
