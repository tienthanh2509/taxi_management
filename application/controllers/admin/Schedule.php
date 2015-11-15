<?php

/*
 *  Chương trình quản lý Taxi
 *  Thiết kế bởi nhóm 1 lớp D13HT01
 *  Bao gồm các thành viên
 *  Hoàng Huy, Thái Sơn, Tiến Thành, Thanh Thúy, Thanh Vân
 */

/**
 * Description of Schedule
 *
 * @author Phạm Tiến Thành <tienthanh.dqc@gmail.com>
 * @property _cars $_c Lớp thao tác dữ liệu
 * @property _users $_u Lớp thao tác dữ liệu
 * @property _schedule $_s Lớp thao tác dữ liệu
 */
class Schedule extends CI_D13HT01 {

	public function __construct()
	{
		parent::__construct();

		$this->load->model('_cars', '_c');
		$this->load->model('_users', '_u');
		$this->load->model('_schedule', '_s');
	}

	public function index()
	{
		$page	 = (int) $this->input->get('page') ? $this->input->get('page') : 1;
		$page	 = $page < 1 ? 1 : $page;

		$this->load->library('pagination');

		$config['base_url']		 = $this->config->site_url('admin/schedule');
		$config['total_rows']	 = $this->_s->count_all();
		$config['per_page']		 = 20;

		$this->data['schedule_list'] = $this->_s->get_all($config['per_page'], ($page - 1) * $config['per_page']);

		$this->pagination->initialize($config);

		$this->data['pagination'] = $this->pagination->create_links();

		$this->render('admin/schedule/welcome');
	}

	public function add()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<span>', '</span><br>');

		$this->form_validation->set_rules('ci_form_user_id', 'Mã NV', 'required');
		$this->form_validation->set_rules('ci_form_car_id', 'Mã xe', 'required');
		$this->form_validation->set_rules('ci_form_schedule_date', 'Ngày', 'required');
		$this->form_validation->set_rules('ci_form_schedule_shift', 'Ca làm việc', 'required');

		if ($this->form_validation->run() === TRUE)
		{
			$data	 = [
				'user_id'		 => $this->input->post('ci_form_user_id'),
				'car_id'		 => $this->input->post('ci_form_car_id'),
				'schedule_date'	 => $this->input->post('ci_form_schedule_date'),
				'schedule_shift' => $this->input->post('ci_form_schedule_shift'),
			];
			$status	 = $this->_s->add($data);

			if ($status === 1)
			{
				$this->data['message'] = 'Đã thêm lịch mới thành công';
			}
			elseif ($status === -1)
			{
				$this->data['error_message'] = 'Nhân viên đã được phân công vào ca này rồi, hãy chọn ca hoặc ngày khác!';
			}
			elseif ($status === -2)
			{
				$this->data['error_message'] = 'Nhân viên đang chọn đã lên lịch 2 ca, nhân viên chỉ được phép làm 2 ca 1 ngày!';
			}
			else
			{
				$this->data['error_message'] = 'Không thể thêm lịch trình mới';
			}
		}
		else
		{
			$this->data['error_message'] = validation_errors();
		}

		$this->data['user_list'] = $this->_u->get_all(0);
		$this->data['car_list']	 = $this->_c->get_all(0);
		$this->render('admin/schedule/schedule_add');
	}

	public function edit($schedule_id = '')
	{
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<span>', '</span><br>');

		$this->form_validation->set_rules('ci_form_user_id', 'Mã NV', 'required');
		$this->form_validation->set_rules('ci_form_car_id', 'Mã xe', 'required');
		$this->form_validation->set_rules('ci_form_schedule_date', 'Ngày', 'required');
		$this->form_validation->set_rules('ci_form_schedule_shift', 'Ca làm việc', 'required');

		$this->data['schedule'] = $this->_s->get_by_id($schedule_id);

		if (empty($this->data['schedule']))
		{
			$this->data['error_message'] = 'Không tìm thấy lịch trình bạn yêu cầu!';
		}
		elseif ($this->form_validation->run() === TRUE)
		{
			$data	 = [
				'user_id'		 => $this->input->post('ci_form_user_id'),
				'car_id'		 => $this->input->post('ci_form_car_id'),
				'schedule_date'	 => $this->input->post('ci_form_schedule_date'),
				'schedule_shift' => $this->input->post('ci_form_schedule_shift'),
			];
			$status	 = $this->_s->update($schedule_id, $data);

			if ($status === 1)
			{
				$this->data['message'] = 'Đã cập nhật thông tin lịch trình thành công';
			}
			elseif ($status === -1)
			{
				$this->data['error_message'] = 'Nhân viên đã được phân công vào ca này rồi, hãy chọn ca hoặc ngày khác!';
			}
			else
			{
				$this->data['error_message'] = 'Không thể cập nhật thông tin lịch trình mới';
			}
		}
		else
		{
			$this->data['error_message'] = validation_errors();
		}

		$this->data['user_list'] = $this->_u->get_all(0);
		$this->data['car_list']	 = $this->_c->get_all(0);

		$this->render('admin/schedule/schedule_edit');
	}

	public function delete($schedule_id = '')
	{
		$this->data['schedule'] = $this->_s->get_by_id($schedule_id);

		if (empty($this->data['schedule']))
		{
			$this->data['error_message'] = 'Không tìm thấy lịch trình bạn yêu cầu!';
		}
		elseif ($this->input->post('confirm'))
		{
			$status = $this->_s->delete($schedule_id);
			if ($status == 1)
			{
				$this->data['message'] = 'Đã xóa lịch trình "' . $this->data['schedule']['schedule_id'] . '" thành công!';
			}
			elseif ($status == 0)
			{
				$this->data['error_message'] = 'Không thể xóa lịch trình bạn yêu cầu!';
			}
			else
			{
				$this->data['error_message'] = 'Lỗi không xác định, mã lỗi ' . $status;
			}
		}

		$this->render('admin/schedule/schedule_delete');
	}

	public function auto()
	{
		$this->output->enable_profiler();

		$step = (int) $this->input->get('step') ? $this->input->get('step') : 1;

		if ($step == 1)
		{
			$this->load->library('form_validation');
			$this->form_validation->set_error_delimiters('<span>', '</span><br>');

			$this->form_validation->set_rules('ci_form_schedule_shift[]', 'Ca làm việc', 'required');
			$this->form_validation->set_rules('ci_form_schedule_date', 'Ngày làm việc', 'required|regex_match[/([0-9]{4})-([0-9]{2})-([0-9]{2})/]');
			$this->form_validation->set_rules('ci_form_schedule_limit', 'Giới hạn', 'required|integer');

			if ($this->form_validation->run() === TRUE)
			{
				$data = [
					'schedule_shift' => $this->input->get('ci_form_schedule_shift'),
					'schedule_date'	 => $this->input->get('ci_form_schedule_date'),
					'schedule_limit' => $this->input->get('ci_form_schedule_limit')
				];

				if (count($data['schedule_shift']) < $data['schedule_limit'])
				{
					$this->data['error_message'] = 'Hãy chọn số lượng ca làm việc lơn hơn hoặc bằng số lượng giới hạn ca làm việc!';
				}
			}
			else
			{
				$this->data['error_message'] = validation_errors();
			}

			$this->render('admin/schedule/schedule_auto_step_1');
		}
		elseif($step == 2)
		{
			show_error('Tính năng đang được phát triển!');
		}
	}

}
