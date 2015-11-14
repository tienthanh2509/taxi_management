<?php

/*
 *  Chương trình quản lý Taxi
 *  Thiết kế bởi nhóm 1 lớp D13HT01
 *  Bao gồm các thành viên
 *  Hoàng Huy, Thái Sơn, Tiến Thành, Thanh Thúy, Thanh Vân
 */

/**
 * Trang đặt xe
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
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<span>', '</span><br>');

		$this->form_validation->set_rules('ci_form_booking_customer_phone', 'Số diện thoại', 'required|numeric|min_length[6]|max_length[32]|is_unique[ci_customer.customer_phone]');
		$this->form_validation->set_rules('ci_form_booking_customer_gender', 'Giới tính', 'required|integer|in_list[0,1,2]');
		$this->form_validation->set_rules('ci_form_booking_customer_name', 'Họ & tên', 'required');
		$this->form_validation->set_rules('ci_form_booking_pickup_place', 'Vị trí hiện tại', 'required');
		$this->form_validation->set_rules('ci_form_booking_drop_place', 'Vị trí cần đến', 'required');

		if ($this->form_validation->run() === TRUE)
		{
			$customer_data	 = [
				'customer_phone'	 => $this->input->post('ci_form_customer_name'),
				'customer_password'	 => password_hash($this->input->post('ci_form_customer_password'), PASSWORD_DEFAULT),
				'customer_phone'	 => $this->input->post('ci_form_customer_phone'),
				'customer_gender'	 => $this->input->post('ci_form_customer_gender'),
				'customer_name'		 => $this->input->post('ci_form_customer_name'),
				'customer_bd'		 => $this->input->post('ci_form_customer_bd'),
				'customer_active'	 => $this->input->post('ci_form_customer_active'),
			];
			$status			 = $this->_b->add($customer_data);

			if ($status === 1)
			{
				$this->data['message'] = 'Đã thêm khách hàng mới thành công';
			}
			elseif ($status === 0)
			{
				$this->data['error_message'] = 'Không thể thêm khách hàng mới';
			}
			else
			{
				$this->data['error_message'] = 'Lỗi không rõ';
			}
		}
		else
		{
			$this->data['error_message'] = validation_errors();
		}

		$this->render('booking');
	}

}
