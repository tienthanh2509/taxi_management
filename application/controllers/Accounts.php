<?php

/*
 *  Chương trình quản lý Taxi
 *  Thiết kế bởi nhóm 1 lớp D13HT01
 *  Bao gồm các thành viên
 *  Hoàng Huy, Thái Sơn, Tiến Thành, Thanh Thúy, Thanh Vân
 */

/**
 * Controller xác thực người dùng
 *
 * @author Phạm Tiến Thành <tienthanh.dqc@gmail.com>
 * @property _accounts $_a Lớp thao tác dữ liệu
 */
class Accounts extends CI_D13HT01 {

	public function __construct()
	{
		parent::__construct();

		$this->load->model('_accounts', '_a');
		$this->data['continue'] = $this->input->get_post('continue') ? $this->input->get_post('continue') : $this->config->site_url();
	}

	public function index()
	{
		
	}

	public function sign_in()
	{
		$this->data['role'] = $this->input->get('role') ? $this->input->get('role') : 'guest';

		if (in_array($this->data['role'], ['admin', 'accountant', 'driver']))
			$this->render('accounts/login');
		else
			$this->render('accounts/customer_login');
	}

	public function ajax_login()
	{
		if (!$this->input->is_ajax_request())
		{
			show_error('Từ chối truy cập!', 403);
		}

		$login_attempts				 = (int) $this->session->userdata('accounts.login.attempts') ? $this->session->userdata('accounts.login.attempts') : 0;
		$login_attempts_timestamp	 = (int) $this->session->userdata('accounts.login.attempts.countdown') ? $this->session->userdata('accounts.login.attempts.countdown') : 0;
		$login_attempts_countdown	 = (30 * 60) - (time() - $login_attempts_timestamp);

		if ($login_attempts > 3 && $login_attempts_countdown > 0)
		{
			$response = [
				'status'	 => -1,
				'message'	 => 'Bạn đã đăng nhập sai quá nhiều lần!',
				'timeout'	 => $login_attempts_countdown
			];
		}
		else
		{
			$this->load->library('form_validation');
			$this->form_validation->set_error_delimiters('<span>', '</span>');

			$this->form_validation->set_rules('username', 'Tên tài khoản', 'required|min_length[6]|max_length[32]');
			$this->form_validation->set_rules('password', 'Mật khẩu', 'required|min_length[4]|max_length[32]');
			$this->form_validation->set_rules('role', 'Cấp quyền', 'required');

			if ($this->form_validation->run() === TRUE)
			{
				$username	 = $this->input->post('username');
				$password	 = $this->input->post('password');
				$role		 = $this->input->post('role');
				$status		 = $this->_a->login($username, $password, $role);

				if ($status === 0)
				{
					$response = [
						'status'	 => 0,
						'message'	 => 'Sai tài khoản hoặc mật khẩu?!'
					];
					$this->session->set_userdata('accounts.login.attempts', $login_attempts + 1);
					if ($login_attempts > 3)
					{
						$this->session->set_userdata('accounts.login.attempts.countdown', time());
					}
				}
				elseif (!empty($status['user_id']) && !empty($status['user_name']))
				{
					$this->session->set_userdata('user_id', $status['user_id']);
					$this->session->set_userdata('user_name', $status['user_name']);
					$this->session->set_userdata('user_role', $role);

					$response = [
						'status'	 => 1,
						'message'	 => 'Đăng nhập thành công!'
					];
				}
				elseif ($status === -1)
				{
					$response = [
						'status'	 => 0,
						'message'	 => 'Tài khoản không đủ quyền hạn để truy cập!'
					];
				}
				else
				{
					$response = [
						'status'	 => 0,
						'message'	 => 'Đã xảy ra lỗi!'
					];
				}
			}
			else
			{
				$response = [
					'status'	 => FALSE,
					'message'	 => validation_errors(),
				];
			}
		}
		$this->output->set_content_type('json');
		$this->output->append_output(json_encode($response));
	}

	public function ajax_customer_login()
	{
		if (!$this->input->is_ajax_request())
		{
			show_error('Từ chối truy cập!', 403);
		}

		$login_attempts				 = (int) $this->session->userdata('accounts.login.attempts') ? $this->session->userdata('accounts.login.attempts') : 0;
		$login_attempts_timestamp	 = (int) $this->session->userdata('accounts.login.attempts.countdown') ? $this->session->userdata('accounts.login.attempts.countdown') : 0;
		$login_attempts_countdown	 = (1 * 60) - (time() - $login_attempts_timestamp);

		if ($this->session->userdata('customer_id'))
		{
			$response = [
				'status'	 => 1,
				'message'	 => 'Đăng nhập thành công!'
			];
		}
		elseif ($login_attempts > 3 && $login_attempts_countdown > 0)
		{
			$response = [
				'status'	 => -1,
				'message'	 => 'Bạn đã đăng nhập sai quá nhiều lần!',
				'timeout'	 => $login_attempts_countdown
			];
		}
		else
		{
			$this->load->library('form_validation');
			$this->form_validation->set_error_delimiters('<span>', '</span>');

			$this->form_validation->set_rules('ci_form_customer_phone', 'Số điện thoại', 'required|numeric|min_length[6]|max_length[32]');
			$this->form_validation->set_rules('ci_form_customer_password', 'Mật khẩu', 'required|min_length[4]|max_length[32]');

			if ($this->form_validation->run() === TRUE)
			{
				$phone_number	 = $this->input->post('ci_form_customer_phone');
				$password		 = $this->input->post('ci_form_customer_password');
				$status			 = $this->_a->customer_login($phone_number, $password);

				if ($status === 0)
				{
					$response = [
						'status'	 => 0,
						'message'	 => 'Sai tài khoản hoặc mật khẩu?!'
					];
					$this->session->set_userdata('accounts.login.attempts', $login_attempts + 1);
					if ($login_attempts > 3)
					{
						$this->session->set_userdata('accounts.login.attempts.countdown', time());
					}
				}
				elseif (!empty($status['customer_id']) && !empty($status['customer_name']))
				{
					$this->session->set_userdata('customer_id', $status['customer_id']);
					$this->session->set_userdata('customer_name', $status['customer_name']);
					$this->session->set_userdata('customer_phone', $status['customer_phone']);
					$this->session->set_userdata('customer_gender', $status['customer_gender']);

					$response = [
						'status'	 => 1,
						'message'	 => 'Đăng nhập thành công!'
					];
				}
				elseif ($status === -1)
				{
					$response = [
						'status'	 => 0,
						'message'	 => 'Tài khoản đang chờ xác thực!'
					];
				}
				else
				{
					$response = [
						'status'	 => 0,
						'message'	 => 'Đã xảy ra lỗi!'
					];
				}
			}
			else
			{
				$response = [
					'status'	 => FALSE,
					'message'	 => validation_errors(),
				];
			}
		}
		$this->output->set_content_type('json');
		$this->output->append_output(json_encode($response));
	}

	public function sign_out()
	{
		if (!$this->session->userdata('user_id') && !$this->session->userdata('customer_id'))
		{
			show_error('Từ chối truy cập!', 403);
		}

		$this->session->sess_destroy();
		$this->output->set_header('Location: ' . $this->config->site_url());
	}

	public function change_password()
	{
		if (!$this->session->userdata('user_name'))
		{
			show_error('Từ chối truy cập!', 403);
		}

		$this->render('accounts/change_password');
	}

	public function ajax_change_password()
	{
		if (!$this->input->is_ajax_request())
		{
			show_error('Từ chối truy cập!', 403);
		}

		if (!$this->session->userdata('user_name'))
		{
			show_error('Từ chối truy cập!', 403);
		}

		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<span>', '</span>');

		$this->form_validation->set_rules('password_old', 'Mật khẩu cũ', 'required|min_length[4]|max_length[32]');
		$this->form_validation->set_rules('password_new', 'Mật khẩu mới', 'required|min_length[4]|max_length[32]');
		$this->form_validation->set_rules('password_confirm', 'Nhập lại mật khẩu mới', 'required|min_length[4]|max_length[32]|matches[password_new]');

		if ($this->form_validation->run() === TRUE)
		{
			$username		 = $this->session->userdata('user_name');
			$password_old	 = $this->input->post('password_old');
			$password_new	 = $this->input->post('password_new');
			$status			 = $this->_a->change_password($username, $password_old, $password_new);
			if ($status === 0)
			{
				$response = [
					'status'	 => 0,
					'message'	 => 'Tài khoản không tồn tại!'
				];
			}
			elseif ($status === 1)
			{
				$response = [
					'status'	 => 1,
					'message'	 => 'Đổi mật khẩu thành công!'
				];
			}
			elseif ($status === -1)
			{
				$response = [
					'status'	 => 0,
					'message'	 => 'Dường như mật khẩu cũ giống y chang mật khẩu mới!'
				];
			}
			elseif ($status === -2)
			{
				$response = [
					'status'	 => 0,
					'message'	 => 'Mật khẩu cũ không đúng!'
				];
			}
			else
			{
				$response = [
					'status'	 => 0,
					'message'	 => 'Đã xảy ra lỗi!'
				];
			}
		}
		else
		{
			$response = [
				'status'	 => 0,
				'message'	 => validation_errors(),
			];
		}

		$this->output->set_content_type('json');
		$this->output->append_output(json_encode($response));
	}

}
