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

		$this->form_validation->set_rules('ci_form_booking_customer_phone', 'Số diện thoại', 'required|numeric|min_length[6]|max_length[32]');
		$this->form_validation->set_rules('ci_form_booking_customer_gender', 'Giới tính', 'required|integer|in_list[0,1,2]');
		$this->form_validation->set_rules('ci_form_booking_customer_name', 'Họ & tên', 'required');
		$this->form_validation->set_rules('ci_form_booking_pickup_place', 'Vị trí hiện tại', 'required');
		$this->form_validation->set_rules('ci_form_booking_drop_place', 'Vị trí cần đến', 'required');
		$this->form_validation->set_rules('ci_form_booking_note', 'Ghi chú', 'max_length[500]');

		if ($this->form_validation->run() === TRUE)
		{
			if ((time() - $this->session->userdata('booking.timestamp')) < 10)
			{
				$this->data['error_message'] = 'Bạn vừa gửi yêu cầu đặt xe, hãy gửi tiếp yêu cầu khác sau 10s';
			}
			else
			{
				$customer_data	 = [
					'booking_customer_phone'	 => $this->input->post('ci_form_booking_customer_phone'),
					'booking_customer_gender'	 => $this->input->post('ci_form_booking_customer_gender'),
					'booking_customer_name'		 => $this->input->post('ci_form_booking_customer_name'),
					'booking_pickup_place'		 => $this->input->post('ci_form_booking_pickup_place'),
					'booking_drop_place'		 => $this->input->post('ci_form_booking_drop_place'),
					'booking_date'				 => date('c'),
					'booking_note'				 => $this->input->post('ci_form_booking_note'),
					'booking_status'			 => $this->session->userdata('customer_id') ? 1 : 0,
					'booking_ip'				 => $this->input->ip_address()
				];
				$status			 = $this->_b->add($customer_data);

				if ($status === 1)
				{
					$this->data['message'] = 'Xong! Chúng tôi sẽ liên hệ với bạn nhanh nhất có thể!';
					$this->session->set_userdata('booking.timestamp', time());
				}
				else
				{
					$this->data['error_message'] = 'Có lỗi xảy ra!@#!@';
				}
			}
		}
		else
		{
			$this->data['error_message'] = validation_errors();
		}

		$this->render('booking');
	}

}
