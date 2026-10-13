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

	public function galleryImages($limit)
	{
		$limit = max(1, (int) $limit);
		$images = $this->db
			->select('albums.title AS album_title, albums.slug, attachments.title, attachments.file_path AS image_path')
			->from('attachments')
			->join('albums', 'albums.id = attachments.item_id')
			->where(array('attachments.resource' => 'albums', 'albums.status' => 'published'))
			->where_in('attachments.file_ext', array('jpg', 'jpeg', 'png', 'gif', 'webp'))
			->order_by('attachments.created_at', 'DESC')
			->order_by('attachments.sort_order', 'ASC')
			->limit($limit)
			->get()->result_array();

		$paths = array();
		foreach ($images as $image)
		{
			$paths[] = $image['image_path'];
		}
		if (count($images) < $limit)
		{
			$covers = $this->db
				->select('title AS album_title, slug, title, cover_path AS image_path')
				->where('status', 'published')
				->where('cover_path IS NOT NULL', NULL, FALSE)
				->where('cover_path !=', '')
				->order_by('updated_at', 'DESC')
				->limit($limit)
				->get('albums')->result_array();
			foreach ($covers as $cover)
			{
				if (in_array($cover['image_path'], $paths, TRUE)) continue;
				$images[] = $cover;
				$paths[] = $cover['image_path'];
				if (count($images) >= $limit) break;
			}
		}
		return $images;
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
