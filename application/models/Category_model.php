<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category_model extends CI_Model
{
	public function search($filters, $limit, $offset)
	{
		$this->applyFilters($filters);
		$usage = '((SELECT COUNT(*) FROM news WHERE news.category_id = categories.id)'
			. ' + (SELECT COUNT(*) FROM projects WHERE projects.category_id = categories.id)'
			. ' + (SELECT COUNT(*) FROM services WHERE services.category_id = categories.id)) AS item_count';
		return $this->db
			->select('categories.*, ' . $usage, FALSE)
			->order_by('categories.type', 'ASC')
			->order_by('categories.sort_order', 'ASC')
			->order_by('categories.name', 'ASC')
			->limit((int) $limit, (int) $offset)
			->get('categories')->result_array();
	}

	public function countSearch($filters)
	{
		$this->applyFilters($filters);
		return (int) $this->db->count_all_results('categories');
	}

	public function countAll()
	{
		return (int) $this->db->count_all('categories');
	}

	public function options($type)
	{
		return $this->db->select('id, name, status')->where('type', $type)->order_by('sort_order', 'ASC')->order_by('name', 'ASC')->get('categories')->result_array();
	}

	public function find($id)
	{
		return $this->db->get_where('categories', array('id' => (int) $id))->row_array();
	}

	public function findBySlug($type, $slug)
	{
		return $this->db->get_where('categories', array('type' => $type, 'slug' => (string) $slug, 'status' => 1))->row_array();
	}

	public function active($type)
	{
		return $this->db->where(array('type' => $type, 'status' => 1))->order_by('sort_order', 'ASC')->order_by('name', 'ASC')->get('categories')->result_array();
	}

	public function belongsTo($id, $type)
	{
		return $this->db->where(array('id' => (int) $id, 'type' => $type))->count_all_results('categories') > 0;
	}

	public function slugExists($type, $slug, $exceptId)
	{
		$this->db->where(array('type' => $type, 'slug' => $slug));
		if ($exceptId > 0)
		{
			$this->db->where('id !=', (int) $exceptId);
		}
		return $this->db->count_all_results('categories') > 0;
	}

	public function usageCount($id)
	{
		$total = 0;
		foreach (array('news', 'projects', 'services') as $table)
		{
			$total += (int) $this->db->where('category_id', (int) $id)->count_all_results($table);
		}
		return $total;
	}

	public function save($data, $id)
	{
		if ($id > 0)
		{
			return $this->db->where('id', (int) $id)->update('categories', $data);
		}
		return $this->db->insert('categories', $data);
	}

	public function delete($id)
	{
		return $this->db->delete('categories', array('id' => (int) $id));
	}

	private function applyFilters($filters)
	{
		if (! empty($filters['type']))
		{
			$this->db->where('categories.type', $filters['type']);
		}
		if (! empty($filters['q']))
		{
			$this->db->group_start()->like('categories.name', $filters['q'])->or_like('categories.slug', $filters['q'])->group_end();
		}
		if (isset($filters['status']) && $filters['status'] !== '')
		{
			$this->db->where('categories.status', (int) $filters['status']);
		}
	}
}
