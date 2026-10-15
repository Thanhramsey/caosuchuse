<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rubber_price_model extends CI_Model
{
	public function latest()
	{
		return $this->db->order_by('effective_date', 'DESC')->order_by('id', 'DESC')->limit(1)->get('rubber_prices')->row_array();
	}

	public function all($limit = 100)
	{
		return $this->db->order_by('effective_date', 'DESC')->order_by('id', 'DESC')->limit((int) $limit)->get('rubber_prices')->result_array();
	}

	public function find($id)
	{
		return $this->db->get_where('rubber_prices', array('id' => (int) $id))->row_array();
	}

	public function save($data, $id = 0)
	{
		if ((int) $id > 0)
		{
			return $this->db->where('id', (int) $id)->update('rubber_prices', $data);
		}
		return $this->db->insert('rubber_prices', $data);
	}

	public function delete($id)
	{
		return $this->db->delete('rubber_prices', array('id' => (int) $id));
	}
}
