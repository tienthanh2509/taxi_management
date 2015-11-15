<?php

/*
 *  Chương trình quản lý Taxi
 *  Thiết kế bởi nhóm 1 lớp D13HT01
 *  Bao gồm các thành viên
 *  Hoàng Huy, Thái Sơn, Tiến Thành, Thanh Thúy, Thanh Vân
 */

/**
 * Trang chủ mục quản lý
 *
 * @author Phạm Tiến Thành <tienthanh.dqc@gmail.com>
 */
class Welcome extends CI_D13HT01 {

	public function index()
	{
		$_log_path	 = (config_item('log_path') !== '') ? config_item('log_path') : APPPATH . 'logs/';
		$_file_ext	 = config_item('log_file_extension');
		$_file_ext	 = $_file_ext ? ltrim($_file_ext, '.') : 'php';

		$filepath = $_log_path . 'log-' . date('Y-m-d') . '.' . $_file_ext;

		if (is_file($filepath) && $logs = explode("\n", file_get_contents($filepath)))
		{
			$this->load->library('pagination');

			$config['base_url']		 = $this->config->site_url('admin/welcome');
			$config['total_rows']	 = count($logs);
			$config['per_page']		 = 20;
			$config['uri_segment']	 = 4;
			$page					 = $this->uri->segment($config['uri_segment'], 1);

			$this->pagination->initialize($config);

			$this->data['pagination'] = $this->pagination->create_links();

			$this->data['logs']	 = [];
			$c					 = ($page - 1) * $config['per_page'];
			$d					 = $page * $config['per_page'];

			for ($i = ($config['total_rows'] - 1); $i >= 0; $i--)
			{
				if ($i < $c || $i > $d)
					continue;

				preg_match('/^(.*?) - (.*?) --> (.*?)$/i', $logs[$i], $m);

				$this->data['logs'][] = $m;
			}
		}

		$this->render('admin/welcome');
	}

}
