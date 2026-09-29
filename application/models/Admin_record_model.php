<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_record_model extends CI_Model
{
	public function search($table, $filters, $limit = 30, $offset = 0)
	{
		if (! empty($filters['q']))
		{
			$columns = isset($filters['columns']) ? $filters['columns'] : array();
			$this->db->group_start();
			foreach ($columns as $index => $column)
			{
				$index === 0 ? $this->db->like($column, $filters['q']) : $this->db->or_like($column, $filters['q']);
			}
			$this->db->group_end();
		}
		if (! empty($filters['status']))
		{
			$this->db->where('status', $filters['status']);
		}
		return $this->db->order_by(isset($filters['order']) ? $filters['order'] : 'id', 'DESC')->limit((int) $limit, (int) $offset)->get($table)->result_array();
	}

	public function count($table, $filters)
	{
		if (! empty($filters['q']))
		{
			$columns = isset($filters['columns']) ? $filters['columns'] : array();
			$this->db->group_start();
			foreach ($columns as $index => $column)
			{
				$index === 0 ? $this->db->like($column, $filters['q']) : $this->db->or_like($column, $filters['q']);
			}
			$this->db->group_end();
		}
		if (! empty($filters['status']))
		{
			$this->db->where('status', $filters['status']);
		}
		return (int) $this->db->count_all_results($table);
	}

	public function find($table, $id)
	{
		return $this->db->get_where($table, array('id' => (int) $id))->row_array();
	}

	public function save($table, $data, $id)
	{
		if ($id > 0)
		{
			return $this->db->where('id', (int) $id)->update($table, $data);
		}
		return $this->db->insert($table, $data);
	}

	public function delete($table, $id)
	{
		return $this->db->delete($table, array('id' => (int) $id));
	}
}