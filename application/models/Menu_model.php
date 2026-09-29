<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Menu_model extends CI_Model
{
	const LOCATIONS = array('main' => 'Menu chính', 'footer' => 'Menu chân trang');
	const MAX_DEPTH = 3;

	public function flatTree($location, $onlyActive = FALSE)
	{
		$this->db->where('location', $location);
		if ($onlyActive)
		{
			$this->db->where('status', 1);
		}
		$rows = $this->db->order_by('sort_order', 'ASC')->order_by('id', 'ASC')->get('menus')->result_array();
		$children = array();
		foreach ($rows as $row)
		{
			$parent = $row['parent_id'] === NULL ? 0 : (int) $row['parent_id'];
			$children[$parent][] = $row;
		}
		$flat = array();
		$this->walk($children, 0, 0, $flat);
		return $flat;
	}

	public function tree($location)
	{
		$rows = $this->db->where(array('location' => $location, 'status' => 1))->order_by('sort_order', 'ASC')->order_by('id', 'ASC')->get('menus')->result_array();
		$children = array();
		foreach ($rows as $row)
		{
			$children[$row['parent_id'] === NULL ? 0 : (int) $row['parent_id']][] = $row;
		}
		return $this->branch($children, 0);
	}

	private function branch($children, $parentId)
	{
		$items = array();
		if (isset($children[$parentId]))
		{
			foreach ($children[$parentId] as $row)
			{
				$row['children'] = $this->branch($children, (int) $row['id']);
				$items[] = $row;
			}
		}
		return $items;
	}

	public function find($id)
	{
		return $this->db->get_where('menus', array('id' => (int) $id))->row_array();
	}

	public function depth($id)
	{
		$depth = 0;
		$current = $this->find($id);
		while (! empty($current) && $depth < 10)
		{
			$depth++;
			$current = $current['parent_id'] === NULL ? NULL : $this->find($current['parent_id']);
		}
		return $depth;
	}

	public function subtreeHeight($id)
	{
		$height = 1;
		foreach ($this->db->select('id')->get_where('menus', array('parent_id' => (int) $id))->result_array() as $child)
		{
			$height = max($height, 1 + $this->subtreeHeight($child['id']));
		}
		return $height;
	}

	public function isDescendant($candidateId, $ancestorId)
	{
		$current = $this->find($candidateId);
		$guard = 0;
		while (! empty($current) && $guard++ < 10)
		{
			if ((int) $current['id'] === (int) $ancestorId)
			{
				return TRUE;
			}
			$current = $current['parent_id'] === NULL ? NULL : $this->find($current['parent_id']);
		}
		return FALSE;
	}

	public function childCount($id)
	{
		return (int) $this->db->where('parent_id', (int) $id)->count_all_results('menus');
	}

	public function nextSortOrder($location, $parentId)
	{
		$this->db->select_max('sort_order', 'max_order')->where('location', $location);
		$parentId > 0 ? $this->db->where('parent_id', (int) $parentId) : $this->db->where('parent_id IS NULL', NULL, FALSE);
		$row = $this->db->get('menus')->row_array();
		return (int) $row['max_order'] + 1;
	}

	public function save($data, $id)
	{
		if ($id > 0)
		{
			return $this->db->where('id', (int) $id)->update('menus', $data);
		}
		return $this->db->insert('menus', $data);
	}

	public function delete($id)
	{
		return $this->db->delete('menus', array('id' => (int) $id));
	}

	public function move($id, $direction)
	{
		$item = $this->find($id);
		if (empty($item))
		{
			return FALSE;
		}
		$this->db->where('location', $item['location']);
		$item['parent_id'] === NULL ? $this->db->where('parent_id IS NULL', NULL, FALSE) : $this->db->where('parent_id', (int) $item['parent_id']);
		$siblings = $this->db->order_by('sort_order', 'ASC')->order_by('id', 'ASC')->get('menus')->result_array();
		return $this->swapWithNeighbour('menus', $siblings, (int) $id, $direction);
	}

	public function swapWithNeighbour($table, $siblings, $id, $direction)
	{
		$ids = array();
		foreach ($siblings as $sibling)
		{
			$ids[] = (int) $sibling['id'];
		}
		$index = array_search($id, $ids, TRUE);
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

	private function walk($children, $parentId, $depth, &$flat)
	{
		if (! isset($children[$parentId]))
		{
			return;
		}
		$count = count($children[$parentId]);
		foreach ($children[$parentId] as $index => $row)
		{
			$row['depth'] = $depth;
			$row['is_first'] = $index === 0;
			$row['is_last'] = $index === $count - 1;
			$row['child_count'] = isset($children[(int) $row['id']]) ? count($children[(int) $row['id']]) : 0;
			$flat[] = $row;
			$this->walk($children, (int) $row['id'], $depth + 1, $flat);
		}
	}
}
