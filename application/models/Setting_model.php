<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting_model extends CI_Model
{
	public function all()
	{
		$settings = array();
		foreach ($this->db->get('settings')->result_array() as $row)
		{
			$settings[$row['setting_key']] = (string) $row['setting_value'];
		}
		return $settings;
	}

	public function get($key, $default = '')
	{
		$row = $this->db->get_where('settings', array('setting_key' => $key))->row_array();
		return empty($row) ? $default : (string) $row['setting_value'];
	}

	public function saveMany($values)
	{
		$now = date('Y-m-d H:i:s');
		$this->db->trans_start();
		foreach ($values as $key => $value)
		{
			$this->db->query('INSERT INTO settings (setting_key, setting_value, updated_at) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = VALUES(updated_at)', array($key, $value, $now));
		}
		$this->db->trans_complete();
		return $this->db->trans_status();
	}
}
