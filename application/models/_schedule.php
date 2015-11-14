<?php

/*
 *  Chương trình quản lý Taxi
 *  Thiết kế bởi nhóm 1 lớp D13HT01
 *  Bao gồm các thành viên
 *  Hoàng Huy, Thái Sơn, Tiến Thành, Thanh Thúy, Thanh Vân
 */

/**
 * Description of _schedule
 *
 * @author Phạm Tiến Thành <tienthanh.dqc@gmail.com>
 */
class _schedule extends CI_Model {

	public function __construct()
	{
		$this->load->database();
	}

	public function get_all($limit = 30, $offset = 0)
	{
		$this->db->limit($limit, $offset);
		return $this->db->get('view_schedule')->result_array();
	}

	public function count_all()
	{
		return $this->db->count_all('view_schedule');
	}

	public function get_by_id($schedule_id)
	{
		$this->db->where('schedule_id', $schedule_id);
		$data = $this->db->get('view_schedule')->result_array();

		if (!isset($data[0]))
			return NULL;
		else
			return $data[0];
	}

	public function add($param)
	{
		// Nhân viên chỉ được phép làm 2 ca/ ngày
		$this->db->where('user_id', $param['user_id']);
		$this->db->where('schedule_date', $param['schedule_date']);
		$query = $this->db->get('ci_schedule');

		if ($query->num_rows() >= 2)
		{
			return -2;
		}

		$this->db->trans_start();
		$this->db->where('user_id', $param['user_id']);
		$this->db->where('schedule_date', $param['schedule_date']);
		$this->db->where('schedule_shift', $param['schedule_shift']);
		$this->db->limit(1);
		$query = $this->db->get('ci_schedule');

		if ($query->num_rows() > 0)
		{
			return -1;
		}

		$this->db->insert('ci_schedule', $param);

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE)
		{
			return 0;
		}
		else
		{
			return 1;
		}
	}

	public function update($schedule_id, $param)
	{
		$this->db->where('user_id', $param['user_id']);
		$this->db->where('schedule_date', $param['schedule_date']);
		$this->db->where('schedule_shift', $param['schedule_shift']);
		$query = $this->db->get('ci_schedule');

		$s1 = $this->get_by_id($schedule_id);

		foreach ($query->result_array() as $s2)
		{
			if (($s1['schedule_id'] != $s2['schedule_id']) && ($param['schedule_shift'] == $s2['schedule_shift']))
				return -1;
		}


		$this->db->trans_start();
		$this->db->where('schedule_id', $schedule_id);
		$this->db->update('ci_schedule', $param);
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE)
		{
			return 0;
		}
		else
		{
			return 1;
		}
	}

	public function delete($schedule_id)
	{
		$this->db->trans_start();
		$this->db->where('schedule_id', $schedule_id);
		$this->db->delete('ci_schedule');
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE)
		{
			return 0;
		}
		else
		{
			return 1;
		}
	}

}
