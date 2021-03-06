<?php

/*
 *  Chương trình quản lý Taxi
 *  Thiết kế bởi nhóm 1 lớp D13HT01
 *  Bao gồm các thành viên
 *  Hoàng Huy, Thái Sơn, Tiến Thành, Thanh Thúy, Thanh Vân
 */

/**
 * Nhóm tài khoản trên hệ thống
 *
 * @author Phạm Tiến Thành <tienthanh.dqc@gmail.com>
 * @property _groups _g
 */
class Group extends CI_D13HT01 {

	public function __construct()
	{
		parent::__construct();

		$this->load->model('_groups', '_g');
	}

	public function index()
	{
		$page	 = (int) $this->input->get('page') ? $this->input->get('page') : 1;
		$page	 = $page < 1 ? 1 : $page;

		$this->load->library('pagination');

		$config['base_url']		 = $this->config->site_url('admin/group');
		$config['total_rows']	 = $this->_g->count_all();
		$config['per_page']		 = 5;

		$this->data['group_list'] = $this->_g->get_all($config['per_page'], ($page - 1) * $config['per_page']);

		$this->pagination->initialize($config);

		$this->data['pagination'] = $this->pagination->create_links();

		$this->render('admin/group/group');
	}

	public function add()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<span>', '</span><br>');

		$this->form_validation->set_rules('ci_form_group_name', 'Tên nhóm', 'required|is_unique[ci_groups.group_name]');
		$this->form_validation->set_rules('ci_form_group_description', 'Ghi chú', '');

		if ($this->form_validation->run() === TRUE)
		{
			$data	 = [
				'group_name'		 => $this->input->post('ci_form_group_name'),
				'group_description'	 => $this->input->post('ci_form_group_description'),
			];
			$status	 = $this->_g->add($data);

			if ($status === 1)
			{
				$this->data['message'] = 'Đã thêm nhóm mới thành công';
			}
			else
			{
				$this->data['error_message'] = 'Không thể thêm nhóm mới';
			}
		}
		else
		{
			$this->data['error_message'] = validation_errors();
		}

		$this->render('admin/group/group_add');
	}

	public function edit($group_id = '')
	{
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<span>', '</span><br>');
		$this->form_validation->set_rules('ci_form_group_name', 'Tên nhóm', 'required');
		$this->form_validation->set_rules('ci_form_group_description', 'Ghi chú', '');

		$this->data['group'] = $this->_g->get_by_id($group_id);

		if (empty($this->data['group']))
		{
			$this->data['error_message'] = 'Không tìm thấy nhóm bạn yêu cầu!';
		}
		else
		{
			if ($this->form_validation->run() === TRUE)
			{
				$data	 = [
					'group_name'		 => $this->input->post('ci_form_group_name'),
					'group_description'	 => $this->input->post('ci_form_group_description'),
				];
				$status	 = $this->_g->update($group_id, $data);

				if ($status === 1)
				{
					$this->data['message'] = 'Đã cập nhật thông tin nhóm thành công';
				}
				elseif ($status === -1)
				{
					$this->data['error_message'] = 'Nhóm được bảo vệ bởi hệ thống, không thể xóa hoặc thay đổi ID!';
				}
				else
				{
					$this->data['error_message'] = 'Không thể cập nhật thông tin nhóm';
				}
			}
			else
			{
				$this->data['error_message'] = validation_errors();
			}
		}

		$this->render('admin/group/group_edit');
	}

	public function delete($group_id = '')
	{
		if (!$group_id)
		{
			show_404();
		}

		$this->data['group'] = $this->_g->get_by_id($group_id);

		if (empty($this->data['group']))
		{
			$this->data['error_message'] = 'Không tìm thấy nhóm bạn yêu cầu!';
		}
		elseif ($this->input->post('confirm'))
		{
			$status = $this->_g->delete($group_id);
			if ($status == 1)
			{
				$this->data['message'] = 'Đã xóa nhóm "' . $this->data['group']['group_name'] . '" thành công!';
			}
			elseif ($status === -1)
			{
				$this->data['error_message'] = 'Nhóm được bảo vệ bởi hệ thống, không thể xóa hoặc thay đổi ID!';
			}
			elseif ($status == 0)
			{
				$this->data['error_message'] = 'Không thể xóa nhóm được yêu cầu!';
			}
			else
			{
				$this->data['error_message'] = 'Lỗi không xác định, mã lỗi ' . $status;
			}
		}

		$this->render('admin/group/group_delete');
	}

}
