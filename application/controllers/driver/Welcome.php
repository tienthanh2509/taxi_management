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
 * @property _driver $_d
 */
class Welcome extends CI_D13HT01 {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('_driver', '_d');
	}

	public function index()
	{
		$user_id		 = $this->session->userdata('user_id');
		$schedule_list	 = $this->_d->get_schedule($user_id);

		$this->data['schedule_list'] = [];

		// Gom nhóm các ca làm việc có chung này
		foreach ($schedule_list as $schedule)
		{
			for ($i = 1; $i < 5; $i++)
			{
				if (isset($this->data['schedule_list'][$schedule['schedule_date']][4]['flag']))
				{
					continue;
				}

				$this->data['schedule_list'][$schedule['schedule_date']][$i] = [
					'flag'	 => FALSE,
					'note'	 => NULL
				];
			}

			$this->data['schedule_list'][$schedule['schedule_date']][$schedule['schedule_shift']] = [
				'flag'	 => TRUE,
				'note'	 => !empty($schedule['schedule_note']) ? $schedule['schedule_note'] : NULL
			];
		}

		$this->data['current_schedule'] = $this->_d->get_current_schedule($user_id);

		$this->data['time_remaining'] = 0;

		if (!empty($this->data['current_schedule']))
		{
			$time_limit						 = strtotime(date('Y-m-d') . ' ' . ($this->data['current_schedule']['schedule_shift'] * 6) . ':00:00');
			$timestamp						 = time();
			$this->data['time_remaining']	 = $time_limit - $timestamp;
		}

		$this->render('driver/welcome');
	}

}
