<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Slider_model extends CI_Model
{
	public function search($filters, $limit, $offset)
	{
		$this->applyFilters($filters);
		return $this->db->order_by('sort_order', 'ASC')->order_by('id', 'DESC')->limit((int) $limit, (int) $offset)->get('sliders')->result_array();
	}

	public function countSearch($filters)
	{
		$this->applyFilters($filters);
		return (int) $this->db->count_all_results('sliders');
	}

	public function countAll()
	{
		return (int) $this->db->count_all('sliders');
	}

	public function find($id)
	{
		return $this->db->get_where('sliders', array('id' => (int) $id))->row_array();
	}

	public function save($data, $id)
	{
		if ($id > 0)
		{
			return $this->db->where('id', (int) $id)->update('sliders', $data);
		}
		return $this->db->insert('sliders', $data);
	}

	public function delete($id)
	{
		return $this->db->delete('sliders', array('id' => (int) $id));
	}

	private function applyFilters($filters)
	{
		if (! empty($filters['q']))
		{
			$this->db->group_start()->like('title', $filters['q'])->or_like('subtitle', $filters['q'])->group_end();
		}
		if (isset($filters['status']) && $filters['status'] !== '')
		{
			$this->db->where('status', (int) $filters['status']);
		}
	}
}