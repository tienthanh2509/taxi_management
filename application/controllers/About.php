<?php

/*
 *  Chương trình quản lý Taxi
 *  Thiết kế bởi nhóm 1 lớp D13HT01
 *  Bao gồm các thành viên
 *  Hoàng Huy, Thái Sơn, Tiến Thành, Thanh Thúy, Thanh Vân
 */

/**
 * Trang giới thiệu
 *
 * @author Phạm Tiến Thành <tienthanh.dqc@gmail.com>
 */
class About extends CI_D13HT01_Guest {

	public function index()
	{
		$this->render('about');
	}

}
