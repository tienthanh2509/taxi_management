<?php

/*
 *  Chương trình quản lý Taxi
 *  Thiết kế bởi nhóm 1 lớp D13HT01
 *  Bao gồm các thành viên
 *  Hoàng Huy, Thái Sơn, Tiến Thành, Thanh Thúy, Thanh Vân
 */

/**
 * Quản lý nhân viên
 *
 * @author Phạm Tiến Thành <tienthanh.dqc@gmail.com>
 * @property _groups $_g
 * @property _users $_u
 */
class User extends CI_D13HT01 {

	public function __construct()
	{
		parent::__construct();

		$this->load->model('_groups', '_g');
		$this->load->model('_users', '_u');
	}

	function index()
	{
		$page	 = (int) $this->input->get('page') ? $this->input->get('page') : 1;
		$page	 = $page < 1 ? 1 : $page;

		$this->load->library('pagination');

		$config['base_url']		 = $this->config->site_url('admin/user');
		$config['total_rows']	 = $this->_u->count_all();
		$config['per_page']		 = 20;

		$this->data['user_list'] = $this->_u->get_all($config['per_page'], ($page - 1) * $config['per_page']);

		$this->pagination->initialize($config);

		$this->data['pagination'] = $this->pagination->create_links();

		$this->render('admin/user/user');
	}

	public function add()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<span>', '</span><br>');

		$this->form_validation->set_rules('ci_form_user_name', 'Tên tài khoản', 'required|min_length[6]|max_length[32]|is_unique[ci_users.user_name]');
		$this->form_validation->set_rules('ci_form_user_password', 'Mật khẩu', 'required|min_length[4]|max_length[32]');
		$this->form_validation->set_rules('ci_form_user_password_confirm', 'Nhập lại MK', 'required|matches[ci_form_user_password]');
		$this->form_validation->set_rules('ci_form_user_email', 'Email', 'is_unique[ci_users.user_email]');
		$this->form_validation->set_rules('ci_form_user_phone', 'Điện thoại', 'numeric');
		$this->form_validation->set_rules('ci_form_user_gender', 'Giới tính', 'required|integer|in_list[0,1,2]');
		$this->form_validation->set_rules('ci_form_user_ln', 'Họ & tên đệm', '');
		$this->form_validation->set_rules('ci_form_user_fn', 'Tên', 'required');
		$this->form_validation->set_rules('ci_form_group[]', 'Nhóm', 'required');
		$this->form_validation->set_rules('ci_form_user_bd', 'Ngày sinh', 'required|regex_match[/([0-9]{4})-([0-9]{2})-([0-9]{2})/]');

		if ($this->form_validation->run() === TRUE)
		{
			$user_data	 = [
				'user_name'		 => $this->input->post('ci_form_user_name'),
				'user_password'	 => password_hash($this->input->post('ci_form_user_password'), PASSWORD_DEFAULT),
				'user_email'	 => $this->input->post('ci_form_user_email'),
				'user_phone'	 => $this->input->post('ci_form_user_phone'),
				'user_gender'	 => $this->input->post('ci_form_user_gender'),
				'user_ln'		 => $this->input->post('ci_form_user_ln'),
				'user_fn'		 => $this->input->post('ci_form_user_fn'),
				'user_bd'		 => $this->input->post('ci_form_user_bd'),
			];
			$status		 = $this->_u->add($user_data, $this->input->post('ci_form_group'));

			if ($status === 1)
			{
				$this->data['message'] = 'Đã thêm nhân viên mới thành công';
			}
			elseif ($status === 0)
			{
				$this->data['error_message'] = 'Không thể thêm nhân viên mới';
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

		$this->data['group_list'] = $this->_g->get_all();
		$this->render('admin/user/user_add');
	}

	public function edit($user_id = '')
	{
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<span>', '</span><br>');

//		$this->form_validation->set_rules('ci_form_user_name', 'Tên tài khoản', 'required|min_length[6]|max_length[32]|is_unique[ci_users.user_name]');
		$this->form_validation->set_rules('ci_form_user_password', 'Mật khẩu', 'min_length[4]|max_length[32]');
//		$this->form_validation->set_rules('ci_form_user_password_confirm', 'Nhập lại MK', 'matches[ci_form_user_password]');
//		$this->form_validation->set_rules('ci_form_user_email', 'Email', 'is_unique[ci_users.user_email]');
		$this->form_validation->set_rules('ci_form_user_phone', 'Điện thoại', 'numeric');
		$this->form_validation->set_rules('ci_form_user_gender', 'Giới tính', 'required|integer|in_list[0,1,2]');
		$this->form_validation->set_rules('ci_form_user_ln', 'Họ & tên đệm', '');
		$this->form_validation->set_rules('ci_form_user_fn', 'Tên', 'required');
		$this->form_validation->set_rules('ci_form_group[]', 'Nhóm', 'required');
		$this->form_validation->set_rules('ci_form_user_bd', 'Ngày sinh', 'required|regex_match[/([0-9]{4})-([0-9]{2})-([0-9]{2})/]');

		$this->data['user'] = $this->_u->get_by_id($user_id);

		if (!$this->data['user'])
		{
			$this->data['error_message'] = 'Không tìm thấy tài khoản bạn yêu cầu!';
		}
		elseif ($this->form_validation->run() === TRUE)
		{
			$user_data = [
				'user_name'		 => $this->input->post('ci_form_user_name'),
				'user_email'	 => $this->input->post('ci_form_user_email'),
				'user_phone'	 => $this->input->post('ci_form_user_phone'),
				'user_gender'	 => $this->input->post('ci_form_user_gender'),
				'user_ln'		 => $this->input->post('ci_form_user_ln'),
				'user_fn'		 => $this->input->post('ci_form_user_fn'),
				'user_bd'		 => $this->input->post('ci_form_user_bd'),
			];
			if ($this->input->post('ci_form_user_password'))
			{
				$user_data['user_password'] = password_hash($this->input->post('ci_form_user_password'), PASSWORD_DEFAULT);
			}
			$status = $this->_u->update($user_id, $user_data, $this->input->post('ci_form_group'));

			if ($status === 1)
			{
				$this->data['message'] = 'Đã cập nhật thông tin nhân viên thành công';
			}
			else
			{
				$this->data['error_message'] = 'Không thể cập nhật thông tin nhân viên';
			}
		}
		else
		{
			$this->data['error_message'] = validation_errors();
		}

		$this->data['group_list'] = $this->_g->get_all();

		if (empty($this->input->post('ci_form_group')))
		{
			$ci_form_user_groups				 = $this->_g2->get_by_uid($user_id);
			$this->data['ci_form_user_groups']	 = [];
			foreach ($this->data['group_list'] as $key => $group1)
			{
				$this->data['ci_form_user_groups'][$key] = $group1;

				foreach ($ci_form_user_groups as $group2)
					if ($group1['group_id'] == $group2['group_id'])
					{
						$this->data['ci_form_user_groups'][$key]['grant'] = TRUE;
					}
			}
		}
		else
		{
			$this->data['ci_form_user_groups'] = [];
			foreach ($this->data['group_list'] as $key => $group1)
			{
				$this->data['ci_form_user_groups'][$key]			 = $group1;
				$this->data['ci_form_user_groups'][$key]['grant']	 = in_array($group1['group_id'], $this->input->post('ci_form_group'));
			}
		}

		$this->render('admin/user/user_edit');
	}

	public function delete($user_id = '')
	{
		$this->data['user'] = $this->_u->get_by_id($user_id);

		if (!$this->data['user'])
		{
			$this->data['error_message'] = 'Không tìm thấy tài khoản bạn yêu cầu!';
		}
		elseif ($this->input->post('confirm'))
		{
			$status = $this->_u->delete($user_id);
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
		else
		{
			$this->data['group_list'] = $this->_g->get_all();

			if (empty($this->input->post('ci_form_group')))
			{
				$ci_form_user_groups				 = $this->_g2->get_by_uid($user_id);
				$this->data['ci_form_user_groups']	 = [];
				foreach ($this->data['group_list'] as $key => $group1)
				{
					$this->data['ci_form_user_groups'][$key] = $group1;

					foreach ($ci_form_user_groups as $group2)
						if ($group1['group_id'] == $group2['group_id'])
						{
							$this->data['ci_form_user_groups'][$key]['grant'] = TRUE;
						}
				}
			}
			else
			{
				$this->data['ci_form_user_groups'] = [];
				foreach ($this->data['group_list'] as $key => $group1)
				{
					$this->data['ci_form_user_groups'][$key]			 = $group1;
					$this->data['ci_form_user_groups'][$key]['grant']	 = in_array($group1['group_id'], $this->input->post('ci_form_group'));
				}
			}
		}

		$this->render('admin/user/user_delete');
	}

}
