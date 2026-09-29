<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home_section_model extends CI_Model
{
	const POSITIONS = array('left' => 'Cột trái', 'main' => 'Nội dung chính', 'right' => 'Cột phải', 'full' => 'Toàn chiều rộng', 'footer' => 'Chân trang');
	const TYPES = array('html' => 'Nội dung soạn thảo', 'content' => 'Danh sách nội dung', 'image' => 'Ảnh / banner', 'slider' => 'Slider trang chủ');

	public function grouped()
	{
		$rows = $this->db
			->select('home_sections.*, categories.name AS category_name')
			->join('categories', 'categories.id = home_sections.source_category_id', 'left')
			->order_by('home_sections.sort_order', 'ASC')
			->order_by('home_sections.id', 'ASC')
			->get('home_sections')->result_array();
		$groups = array();
		foreach (array_keys(self::POSITIONS) as $position)
		{
			$groups[$position] = array();
		}
		foreach ($rows as $row)
		{
			$groups[isset($groups[$row['position']]) ? $row['position'] : 'main'][] = $row;
		}
		return $groups;
	}

	public function groupedActive()
	{
		$this->db->where('home_sections.status', 1);
		$rows = $this->db
			->select('home_sections.*, categories.name AS category_name')
			->join('categories', 'categories.id = home_sections.source_category_id', 'left')
			->order_by('home_sections.position', 'ASC')
			->order_by('home_sections.sort_order', 'ASC')
			->order_by('home_sections.id', 'ASC')
			->get('home_sections')->result_array();
		$groups = array();
		foreach (array_keys(self::POSITIONS) as $position)
		{
			$groups[$position] = array();
		}
		foreach ($rows as $row)
		{
			$position = isset($groups[$row['position']]) ? $row['position'] : 'main';
			$groups[$position][] = $row;
		}
		return $groups;
	}

	public function find($id)
	{
		return $this->db->get_where('home_sections', array('id' => (int) $id))->row_array();
	}

	public function keyExists($key, $exceptId)
	{
		$this->db->where('section_key', $key);
		if ($exceptId > 0)
		{
			$this->db->where('id !=', (int) $exceptId);
		}
		return $this->db->count_all_results('home_sections') > 0;
	}

	public function nextSortOrder($position)
	{
		$row = $this->db->select_max('sort_order', 'max_order')->where('position', $position)->get('home_sections')->row_array();
		return (int) $row['max_order'] + 1;
	}

	public function save($data, $id)
	{
		if ($id > 0)
		{
			return $this->db->where('id', (int) $id)->update('home_sections', $data);
		}
		return $this->db->insert('home_sections', $data);
	}

	public function delete($id)
	{
		return $this->db->delete('home_sections', array('id' => (int) $id));
	}

	public function siblings($position)
	{
		return $this->db->select('id')->where('position', $position)->order_by('sort_order', 'ASC')->order_by('id', 'ASC')->get('home_sections')->result_array();
	}

	public function swapWithNeighbour($table, $siblings, $id, $direction)
	{
		$ids = array();
		foreach ($siblings as $sibling)
		{
			$ids[] = (int) $sibling['id'];
		}
		$index = array_search((int) $id, $ids, TRUE);
		$target = $direction === 'up' ? $index - 1 : $index + 1;
		if ($index === FALSE || ! isset($ids[$target]))
		{
			return FALSE;
		}
		$swap = $ids[$index];
		$ids[$index] = $ids[$target];
		$ids[$target] = $swap;
		$this->db->trans_start();
		foreach ($ids as $order => $siblingId)
		{
			$this->db->where('id', $siblingId)->update($table, array('sort_order' => $order + 1));
		}
		$this->db->trans_complete();
		return $this->db->trans_status();
	}
}
