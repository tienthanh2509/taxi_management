<?php

/*
 *  Chương trình quản lý Taxi
 *  Thiết kế bởi nhóm 1 lớp D13HT01
 *  Bao gồm các thành viên
 *  Hoàng Huy, Thái Sơn, Tiến Thành, Thanh Thúy, Thanh Vân
 */

/**
 * Description of Car
 *
 * @author Phạm Tiến Thành <tienthanh.dqc@gmail.com>
 * @property _cars_manufacturer $_cm Lớp thao tác dữ liệu
 * @property _cars_model $_cm2 Lớp thao tác dữ liệu
 * @property _cars $_c Lớp thao tác dữ liệu
 */
class Car extends CI_D13HT01 {

	public function __construct()
	{
		parent::__construct();

		$this->load->model('_cars_manufacturer', '_cm');
		$this->load->model('_cars_model', '_cm2');
		$this->load->model('_cars', '_c');
	}

	public function index()
	{
		$page	 = (int) $this->input->get('page') ? $this->input->get('page') : 1;
		$page	 = $page < 1 ? 1 : $page;

		$this->load->library('pagination');

		$config['base_url']		 = $this->config->site_url('admin/car');
		$config['total_rows']	 = $this->_c->count_all();
		$config['per_page']		 = 20;

		$this->data['car_list'] = $this->_c->get_all($config['per_page'], ($page - 1) * $config['per_page']);

		$this->pagination->initialize($config);

		$this->data['pagination'] = $this->pagination->create_links();

		$this->render('admin/car/welcome');
	}

	public function model()
	{
		$page	 = (int) $this->input->get('page') ? $this->input->get('page') : 1;
		$page	 = $page < 1 ? 1 : $page;

		$this->load->library('pagination');

		$config['base_url']		 = $this->config->site_url('admin/car/model');
		$config['total_rows']	 = $this->_cm2->count_all();
		$config['per_page']		 = 10;

		$this->data['model_list'] = $this->_cm2->get_all($config['per_page'], ($page - 1) * $config['per_page']);

		$this->pagination->initialize($config);

		$this->data['pagination'] = $this->pagination->create_links();

		$this->render('admin/car/welcome_model');
	}

	public function manufacturer()
	{
		$page	 = (int) $this->input->get('page') ? $this->input->get('page') : 1;
		$page	 = $page < 1 ? 1 : $page;

		$this->load->library('pagination');

		$config['base_url']		 = $this->config->site_url('admin/car/manufacturer');
		$config['total_rows']	 = $this->_cm->count_all();
		$config['per_page']		 = 10;

		$this->data['manufacturer_list'] = $this->_cm->get_all($config['per_page'], ($page - 1) * $config['per_page']);

		$this->pagination->initialize($config);

		$this->data['pagination'] = $this->pagination->create_links();

		$this->render('admin/car/welcome_manufacturer');
	}

	/**
	 * 
	 * @param string $action
	 */
	public function add($module = 'car')
	{
		if (!in_array($module, ['car', 'model', 'manufacturer']))
		{
			show_404();
		}

		// Nạp động
		$this->{'__add_' . $module}();
	}

	private function __add_car()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<span>', '</span><br>');

		$this->form_validation->set_rules('ci_form_car_lp', 'Biển số', 'required|is_unique[ci_cars.car_lp]');
		$this->form_validation->set_rules('ci_form_model_id', 'Model', 'required');

		if ($this->form_validation->run() === TRUE)
		{
			$data	 = [
				'car_lp'	 => $this->input->post('ci_form_car_lp'),
				'model_id'	 => $this->input->post('ci_form_model_id'),
			];
			$status	 = $this->_c->add($data);

			if ($status === 0)
			{
				$this->data['error_message'] = 'Không thể thêm xe mới';
			}
			else
			{
				$this->data['message'] = 'Đã thêm xe mới thành công';
			}
		}
		else
		{
			$this->data['error_message'] = validation_errors();
		}

		$this->data['model_list'] = $this->_cm2->get_all();
		$this->render('admin/car/add_car');
	}

	public function __add_model()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<span>', '</span><br>');
		$this->form_validation->set_rules('ci_form_manufacturer_id', 'NSX', 'required');
		$this->form_validation->set_rules('ci_form_model_name', 'Tên mẫu xe', 'required|is_unique[ci_cars_model.model_name]');

		if ($this->form_validation->run() === TRUE)
		{
			$data	 = [
				'model_name'		 => $this->input->post('ci_form_model_name'),
				'manufacturer_id'	 => $this->input->post('ci_form_manufacturer_id'),
			];
			$status	 = $this->_cm2->add($data);

			if ($status === 1)
			{
				$this->data['message'] = 'Đã thêm mẫu xe mới thành công';
			}
			else
			{
				$this->data['error_message'] = 'Không thể thêm mẫu xe mới';
			}
		}
		else
		{
			$this->data['error_message'] = validation_errors();
		}

		$this->data['manufacturer_list'] = $this->_cm->get_all(0);
		$this->render('admin/car/add_model');
	}

	public function __add_manufacturer()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<span>', '</span><br>');
		$this->form_validation->set_rules('ci_form_manufacturer_name', 'Tên NSX', 'required|is_unique[ci_cars_manufacturer.manufacturer_name]');

		if ($this->form_validation->run() === TRUE)
		{
			$data	 = [
				'manufacturer_name' => $this->input->post('ci_form_manufacturer_name'),
			];
			$status	 = $this->_cm->add($data);

			if ($status === 1)
			{
				$this->data['message'] = 'Đã thêm hãng xe mới thành công';
			}
			else
			{
				$this->data['error_message'] = 'Không thể thêm hãng xe mới';
			}
		}
		else
		{
			$this->data['error_message'] = validation_errors();
		}

		$this->render('admin/car/add_manufacturer');
	}

	public function edit($module = 'car', $id = '')
	{
		if (!in_array($module, ['car', 'model', 'manufacturer']) || !$id)
		{
			show_404();
		}

		// Nạp động
		$this->{'__edit_' . $module}($id);
	}

	private function __edit_car($car_id = '')
	{
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<span>', '</span><br>');
		$this->form_validation->set_rules('ci_form_car_lp', 'Biển số', 'required');
		$this->form_validation->set_rules('ci_form_model_id', 'Model', 'required');

		$this->data['car'] = $this->_c->get_by_id($car_id);

		if (empty($this->data['car']))
		{
			$this->data['error_message'] = 'Không tìm thấy xe bạn yêu cầu';
		}
		else
		{
			if ($this->form_validation->run() === TRUE)
			{
				$data	 = [
					'car_lp'	 => $this->input->post('ci_form_car_lp'),
					'model_id'	 => $this->input->post('ci_form_model_id'),
				];
				$status	 = $this->_c->update($car_id, $data);

				if ($status === 1)
				{
					$this->data['message'] = 'Đã cập nhật thông tin thành công';
				}
				else
				{
					$this->data['error_message'] = 'Không thể cập nhật thông tin xe';
				}
			}
			else
			{
				$this->data['error_message'] = validation_errors();
			}
		}

		$this->data['model_list'] = $this->_cm2->get_all(0);
		$this->render('admin/car/edit_car');
	}

	public function __edit_model($model_id = '')
	{
		$this->data['model'] = $this->_cm2->get_by_id($model_id);

		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<span>', '</span><br>');
		$this->form_validation->set_rules('ci_form_manufacturer_id', 'Tên NSX', 'required');
		$this->form_validation->set_rules('ci_form_model_name', 'Tên mẫu xe', 'required');

		if (empty($this->data['model']))
		{
			$this->data['error_message'] = 'Không tìm thấy mẫu xe bạn yêu cầu';
		}
		elseif ($this->form_validation->run() === TRUE)
		{
			$data	 = [
				'model_name'		 => $this->input->post('ci_form_model_name'),
				'manufacturer_id'	 => $this->input->post('ci_form_manufacturer_id'),
			];
			$status	 = $this->_cm2->update($model_id, $data);

			if ($status === 1)
			{
				$this->data['message'] = 'Đã cập nhật thông tin mẫu xe thành công';
			}
			else
			{
				$this->data['error_message'] = 'Không thể cập nhật thông tin mẫu xe';
			}
		}
		else
		{
			$this->data['error_message'] = validation_errors();
		}

		$this->data['manufacturer_list'] = $this->_cm->get_all();
		$this->render('admin/car/edit_model');
	}

	public function __edit_manufacturer($manufacturer_id = '')
	{
		$this->data['manufacturer'] = $this->_cm->get_by_id($manufacturer_id);

		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<span>', '</span><br>');
		$this->form_validation->set_rules('ci_form_manufacturer_name', 'Tên NSX', 'required');

		if (empty($this->data['manufacturer']))
		{
			$this->data['error_message'] = 'Không tìm thấy hãng xe bạn yêu cầu';
		}
		elseif ($this->form_validation->run() === TRUE)
		{
			$data	 = [
				'manufacturer_name' => $this->input->post('ci_form_manufacturer_name'),
			];
			$status	 = $this->_cm->update($manufacturer_id, $data);

			if ($status === 0)
			{
				$this->data['error_message'] = 'Không thể cập nhật thông tin hãng xe';
			}
			else
			{
				$this->data['message'] = 'Đã cập nhật thông tin hãng xe thành công';
			}
		}
		else
		{
			$this->data['error_message'] = validation_errors();
		}
		$this->render('admin/car/edit_manufacturer');
	}

	public function delete($module = 'car', $id = '')
	{
		if (!in_array($module, ['car', 'model', 'manufacturer']) || !$id)
		{
			show_404();
		}

		// Nạp động
		$this->{'__delete_' . $module}($id);
	}

	private function __delete_car($car_id = '')
	{
		$this->data['car'] = $this->_c->get_by_id($car_id);

		if (empty($this->data['car']))
		{
			$this->data['error_message'] = 'Không tìm thấy xe bạn yêu cầu';
		}
		elseif ($this->input->post('confirm'))
		{
			$status = $this->_c->delete($car_id);
			if ($status == 1)
			{
				$this->data['message'] = 'Đã xóa xe "' . $this->data['car']['car_lp'] . '" thành công!';
			}
			elseif ($status == 0)
			{
				$this->data['error_message'] = 'Không thể xóa xe được yêu cầu!';
			}
			else
			{
				$this->data['error_message'] = 'Lỗi không xác định, mã lỗi ' . $status;
			}
		}

		$this->render('admin/car/delete_car');
	}

	public function __delete_model($model_id = '')
	{
		$this->data['model'] = $this->_cm2->get_by_id($model_id);

		if (empty($this->data['model']))
		{
			$this->data['error_message'] = 'Không tìm thấy mẫu xe bạn yêu cầu';
		}
		elseif ($this->input->post('confirm'))
		{
			$status = $this->_cm2->delete($model_id);
			if ($status == 1)
			{
				$this->data['message'] = 'Đã xóa mẫu xe "' . $this->data['model']['model_name'] . '" thành công!';
			}
			elseif ($status == 0)
			{
				$this->data['error_message'] = 'Không thể xóa mẫu xe được yêu cầu!';
			}
			else
			{
				$this->data['error_message'] = 'Lỗi không xác định, mã lỗi ' . $status;
			}
		}

		$this->render('admin/car/delete_model');
	}

	public function __delete_manufacturer($manufacturer_id = '')
	{
		$this->data['manufacturer'] = $this->_cm->get_by_id($manufacturer_id);

		if (empty($this->data['manufacturer']))
		{
			$this->data['error_message'] = 'Không tìm thấy mẫu xe bạn yêu cầu';
		}
		elseif ($this->input->post('confirm'))
		{
			$status = $this->_cm->delete($manufacturer_id);
			if ($status == 1)
			{
				$this->data['message'] = 'Đã xóa nhà sản xuất "' . $this->data['manufacturer']['manufacturer_name'] . '" thành công!';
			}
			elseif ($status == 0)
			{
				$this->data['error_message'] = 'Không thể xóa nsx xe được yêu cầu!';
			}
			else
			{
				$this->data['error_message'] = 'Lỗi không xác định, mã lỗi ' . $status;
			}
		}

		$this->render('admin/car/delete_manufacturer');
	}

}
