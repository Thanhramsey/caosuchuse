<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Attachment_model extends CI_Model
{
	public function forItem($resource, $itemId)
	{
		return $this->db
			->select('id, title, file_path, file_ext, file_size')
			->where(array('resource' => $resource, 'item_id' => (int) $itemId))
			->order_by('sort_order', 'ASC')
			->order_by('id', 'ASC')
			->get('attachments')->result_array();
	}

	public function replace($resource, $itemId, $files)
	{
		$this->db->delete('attachments', array('resource' => $resource, 'item_id' => (int) $itemId));
		$now = date('Y-m-d H:i:s');
		foreach (array_values($files) as $index => $file)
		{
			$this->db->insert('attachments', array(
				'resource' => $resource,
				'item_id' => (int) $itemId,
				'title' => $file['title'],
				'file_path' => $file['path'],
				'file_ext' => $file['ext'],
				'file_size' => $file['size'],
				'sort_order' => $index,
				'created_at' => $now
			));
		}
	}

	public function deleteForItem($resource, $itemId)
	{
		return $this->db->delete('attachments', array('resource' => $resource, 'item_id' => (int) $itemId));
	}
}
