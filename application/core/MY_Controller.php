<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
 *  Chương trình quản lý Taxi
 *  Thiết kế bởi nhóm 1 lớp D13HT01
 *  Bao gồm các thành viên
 *  Hoàng Huy, Thái Sơn, Tiến Thành, Thanh Thúy, Thanh Vân
 */

/**
 * Mở rộng lớp CI_Controller
 *
 * @author Phạm Tiến Thành <tienthanh.dqc@gmail.com>
 */
class CI_D13HT01 extends CI_Controller {

	public $twig; // Twig instance
	protected $data; // parameters for view components

	/**
	 * Constructor.
	 * Establish view parameters & set a couple up
	 */

	function __construct()
	{
		parent::__construct();

		$this->load->driver('session');

		$this->data = [];

		//
		$this->add_param();

		// Khởi động thư viện Twig
		$this->load->library('Twig');
		$this->twig = new Twig();

		$this->add_security_header();

		$this->init_role();
	}

	protected function add_param()
	{
		$this->data['head'] = [
			'title'	 => 'D13HT01 - Hệ Thống Thông Tin',
			'meta'	 => [
				'description'	 => 'Website lớp D13HT01 ngành hệ thống thông tin.',
				'keywords'		 => 'd13ht01, httt, tdmu, cntt',
				'author'		 => 'Tiến Thành',
				'developer'		 => 'Phạm Tiến Thành',
				'robots'		 => 'all',
			]
		];

		$this->data['error_message'] = '';
		$this->data['CI']			 = &get_instance();
	}

	/**
	 * Thêm một số HTTP Header để tăng cường bảo mật
	 */
	protected function add_security_header()
	{
		// Prevent some security threats, per Kevin
		// Turn on IE8-IE9 XSS prevention tools
		$this->output->set_header('X-XSS-Protection: 1; mode=block');
		// Don't allow any pages to be framed - Defends against CSRF
		$this->output->set_header('X-Frame-Options: DENY');
		// prevent mime based attacks
		$this->output->set_header('X-Content-Type-Options: nosniff');
	}

	/**
	 * Xử lý giao diện
	 * 
	 * @param string $t Tên file template
	 */
	function render($t)
	{
		$this->output->append_output($this->twig->render($t, $this->data));
	}

	private function init_role()
	{
		$segment = $this->uri->segment(1);

		if ($segment == 'admin')
		{
			if ($this->session->userdata('user_role') != 'admin')
			{
				$this->__redirect_login('admin');
			}
		}
		elseif ($segment == 'accountant')
		{
			if ($this->session->userdata('user_role') != 'accountant')
			{
				$this->__redirect_login('accountant');
			}
		}
		elseif ($segment == 'driver')
		{
			if ($this->session->userdata('user_role') != 'driver')
			{
				$this->__redirect_login('driver');
			}
		}
	}

	/**
	 * Chuyển hướng URL về trang đăng nhập
	 * 
	 * @param string $role
	 * @param string $continue
	 */
	private function __redirect_login($role = 'guest', $continue = NULL)
	{
		$continue = !empty($continue) ? $continue : $this->config->site_url($this->uri->uri_string());

		redirect('accounts/sign_in?role=' . $role . '&continue=' . urlencode($continue));
	}

}
