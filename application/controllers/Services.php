<?php

/*
 *  Chương trình quản lý Taxi
 *  Thiết kế bởi nhóm 1 lớp D13HT01
 *  Bao gồm các thành viên
 *  Hoàng Huy, Thái Sơn, Tiến Thành, Thanh Thúy, Thanh Vân
 */

/**
 * Bảng giá dịch vụ
 *
 * @author Phạm Tiến Thành <tienthanh.dqc@gmail.com>
 */
class Services extends CI_D13HT01 {

	public function index()
	{
		$this->render('services');
	}

}
