<?php

/*
 *  Chương trình quản lý Taxi
 *  Thiết kế bởi nhóm 1 lớp D13HT01
 *  Bao gồm các thành viên
 *  Hoàng Huy, Thái Sơn, Tiến Thành, Thanh Thúy, Thanh Vân
 */

/**
 * Description of Stat
 *
 * @author HuyDoan
 */
class Stat extends CI_D13HT01_Accountant{
	public function index(){
		$this->render('accountant/stat/welcome');
	}
}
