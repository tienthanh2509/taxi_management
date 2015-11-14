<?php

/*
 *  Chương trình quản lý Taxi
 *  Thiết kế bởi nhóm 1 lớp D13HT01
 *  Bao gồm các thành viên
 *  Hoàng Huy, Thái Sơn, Tiến Thành, Thanh Thúy, Thanh Vân
 */

/**
 * Description of Customer
 *
 * @author Phạm Tiến Thành <tienthanh.dqc@gmail.com>
 * @property _customer $_c
 */
class Customer extends CI_D13HT01 {

	public function __construct()
	{
		parent::__construct();

		$this->load->model('_customer', '_c');
	}

	function index()
	{
		$page	 = (int) $this->input->get('page') ? $this->input->get('page') : 1;
		$page	 = $page < 1 ? 1 : $page;

		$this->load->library('pagination');

		$config['base_url']		 = $this->config->site_url('admin/customer/index');
		$config['total_rows']	 = $this->_c->count_all();
		$config['per_page']		 = 20;
		$config['uri_segment']	 = 3;

		$this->data['customer_list'] = $this->_c->get_all($config['per_page'], ($page - 1) * $config['per_page']);

		$this->pagination->initialize($config);

		$this->data['pagination'] = $this->pagination->create_links();

		$this->render('admin/customer/customer');
	}

	public function add()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<span>', '</span><br>');

		$this->form_validation->set_rules('ci_form_customer_phone', 'Số diện thoại', 'required|numeric|min_length[6]|max_length[32]|is_unique[ci_customer.customer_phone]');
		$this->form_validation->set_rules('ci_form_customer_password', 'Mật khẩu', 'required|min_length[4]|max_length[32]');
		$this->form_validation->set_rules('ci_form_customer_password_confirm', 'Nhập lại MK', 'required|matches[ci_form_customer_password]');
		$this->form_validation->set_rules('ci_form_customer_gender', 'Giới tính', 'required|integer|in_list[0,1,2]');
		$this->form_validation->set_rules('ci_form_customer_name', 'Họ & tên', 'required');
		$this->form_validation->set_rules('ci_form_customer_bd', 'Ngày sinh', 'regex_match[/([0-9]{4})-([0-9]{2})-([0-9]{2})/]');
		$this->form_validation->set_rules('ci_form_customer_active', 'Trạng thái tài khoản', 'required|integer');

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
			$status			 = $this->_c->add($customer_data);

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

		$this->render('admin/customer/customer_add');
	}

	public function edit($customer_id = '')
	{
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<span>', '</span><br>');

		$this->form_validation->set_rules('ci_form_customer_phone', 'Số diện thoại', 'required|numeric|min_length[6]|max_length[32]');
		$this->form_validation->set_rules('ci_form_customer_password', 'Mật khẩu', 'min_length[4]|max_length[32]');
		$this->form_validation->set_rules('ci_form_customer_password_confirm', 'Nhập lại MK', 'matches[ci_form_customer_password]');
		$this->form_validation->set_rules('ci_form_customer_gender', 'Giới tính', 'required|integer|in_list[0,1,2]');
		$this->form_validation->set_rules('ci_form_customer_name', 'Họ & tên', 'required');
		$this->form_validation->set_rules('ci_form_customer_bd', 'Ngày sinh', 'regex_match[/([0-9]{4})-([0-9]{2})-([0-9]{2})/]');
		$this->form_validation->set_rules('ci_form_customer_active', 'Trạng thái tài khoản', 'required|integer');

		$this->data['customer'] = $this->_c->get_by_id($customer_id);

		if (!$this->data['customer'])
		{
			$this->data['error_message'] = 'Không tìm thấy thông tin khách hàng bạn yêu cầu, có thể tài khoản đã bị xóa hoặc không tồn tại!';
		}
		elseif ($this->form_validation->run() === TRUE)
		{
			$customer_data = [
				'customer_phone'	 => $this->input->post('ci_form_customer_name'),
				'customer_phone'	 => $this->input->post('ci_form_customer_phone'),
				'customer_gender'	 => $this->input->post('ci_form_customer_gender'),
				'customer_name'		 => $this->input->post('ci_form_customer_name'),
				'customer_bd'		 => $this->input->post('ci_form_customer_bd'),
				'customer_active'	 => $this->input->post('ci_form_customer_active'),
			];
			if ($this->input->post('ci_form_customer_password'))
			{
				$customer_data['customer_password'] = password_hash($this->input->post('ci_form_customer_password'), PASSWORD_DEFAULT);
			}
			$status = $this->_c->update($customer_id, $customer_data);

			if ($status === 1)
			{
				$this->data['message'] = 'Đã cập nhật thông tin khách hàng thành công';
			}
			else
			{
				$this->data['error_message'] = 'Không thể cập nhật thông tin khách hàng';
			}
		}
		else
		{
			$this->data['error_message'] = validation_errors();
		}

		$this->render('admin/customer/customer_edit');
	}

	public function delete($customer_id = '')
	{
		$this->data['customer'] = $this->_c->get_by_id($customer_id);

		if (!$this->data['customer'])
		{
			$this->data['error_message'] = 'Không tìm thấy thông tin khách hàng bạn yêu cầu, có thể tài khoản đã bị xóa hoặc không tồn tại!';
		}
		elseif ($this->input->post('confirm'))
		{
			$status = $this->_c->delete($customer_id);
			if ($status == 1)
			{
				$this->data['message'] = 'Đã xóa tài khoản thành công!';
			}
			elseif ($status == 0)
			{
				$this->data['error_message'] = 'Không thể xóa tài khoản được yêu cầu!';
			}
			else
			{
				$this->data['error_message'] = 'Lỗi không xác định, mã lỗi ' . $status;
			}
		}

		$this->render('admin/customer/customer_delete');
	}

}
