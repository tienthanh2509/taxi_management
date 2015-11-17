<?php

/*
 *  Chương trình quản lý Taxi
 *  Thiết kế bởi nhóm 1 lớp D13HT01
 *  Bao gồm các thành viên
 *  Hoàng Huy, Thái Sơn, Tiến Thành, Thanh Thúy, Thanh Vân
 */

/**
 * Description of Welcome
 *
 * @author HuyDoan
 * @property _accountant $_a
 */
class Welcome extends CI_D13HT01 {

	public function __construct()
	{
		parent::__construct();

		$this->load->model('_accountant', '_a');
	}

	public function index()
	{
		$this->data['view_stat_groups_users']	 = $this->_a->view_stat_groups_users();
		$this->data['view_stat_car']			 = $this->_a->view_stat_car();
		$this->data['stat_order']				 = $this->_a->stat_order();
		$this->data['stat_booking']				 = $this->_a->stat_booking();

		$this->render('accountant/welcome');
	}

}
