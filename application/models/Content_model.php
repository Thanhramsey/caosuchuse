<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Content_model extends CI_Model
{
	// fields: category, cover, excerpt, content, answer, attachments, gallery, video. path: planned public URL prefix.
	const RESOURCES = array(
		'news' => array('label' => 'Tin tức sự kiện', 'icon' => 'news', 'path' => 'tin-tuc', 'fields' => array('category', 'cover', 'excerpt', 'content', 'attachments')),
		'internal' => array('label' => 'Thông tin nội bộ', 'icon' => 'building', 'path' => 'thong-tin-noi-bo', 'fields' => array('category', 'cover', 'excerpt', 'content', 'attachments')),
		'disclosures' => array('label' => 'Công bố thông tin', 'icon' => 'file', 'path' => 'cong-bo-thong-tin', 'fields' => array('category', 'excerpt', 'content', 'attachments')),
		'products' => array('label' => 'Sản phẩm', 'icon' => 'box', 'path' => 'san-pham', 'fields' => array('category', 'cover', 'excerpt', 'content', 'attachments')),
		'services' => array('label' => 'Lĩnh vực hoạt động', 'icon' => 'services', 'path' => 'linh-vuc', 'fields' => array('category', 'cover', 'excerpt', 'content')),
		'projects' => array('label' => 'Dự án', 'icon' => 'projects', 'path' => 'du-an', 'fields' => array('category', 'cover', 'excerpt', 'content')),
		'pages' => array('label' => 'Trang tĩnh', 'icon' => 'page', 'path' => 'trang', 'fields' => array('cover', 'excerpt', 'content', 'attachments')),
		'albums' => array('label' => 'Ngân hàng ảnh', 'icon' => 'photo', 'path' => 'ngan-hang-anh', 'fields' => array('category', 'cover', 'excerpt', 'gallery')),
		'videos' => array('label' => 'Video', 'icon' => 'video', 'path' => 'video', 'fields' => array('category', 'cover', 'excerpt', 'video')),
		'achievements' => array('label' => 'Thành tích', 'icon' => 'award', 'path' => 'thanh-tich', 'fields' => array('cover', 'excerpt', 'content')),
		'faqs' => array('label' => 'Hỏi đáp người lao động', 'icon' => 'help', 'path' => 'hoi-dap', 'fields' => array('category', 'answer'))
	);

	public function exists($resource)
	{
		return is_string($resource) && isset(self::RESOURCES[$resource]);
	}

	public function label($resource)
	{
		return $this->exists($resource) ? self::RESOURCES[$resource]['label'] : '';
	}

	public function config($resource)
	{
		return self::RESOURCES[$resource];
	}

	public function has($resource, $field)
	{
		return $this->exists($resource) && in_array($field, self::RESOURCES[$resource]['fields'], TRUE);
	}

	public function labels($requiredField = NULL)
	{
		$labels = array();
		foreach (self::RESOURCES as $key => $config)
		{
			if ($requiredField === NULL || in_array($requiredField, $config['fields'], TRUE))
			{
				$labels[$key] = $config['label'];
			}
		}
		return $labels;
	}

	public function search($resource, $filters, $limit, $offset)
	{
		$this->applyFilters($resource, $filters);
		return $this->db
			->select($resource . '.id, ' . $resource . '.title, ' . $resource . '.slug, ' . $resource . '.status, ' . $resource . '.cover_path, ' . $resource . '.excerpt, ' . $resource . '.content_html, ' . $resource . '.created_by, ' . $resource . '.published_at, ' . $resource . '.updated_at, categories.name AS category_name')
			->from($resource)
			->join('categories', 'categories.id = ' . $resource . '.category_id', 'left')
			->order_by($resource . '.updated_at', 'DESC')
			->limit((int) $limit, (int) $offset)
			->get()->result_array();
	}

	public function countSearch($resource, $filters)
	{
		$this->applyFilters($resource, $filters);
		return (int) $this->db->count_all_results($resource);
	}

	public function countAll($resource)
	{
		return (int) $this->db->count_all($resource);
	}

	public function latest($resource, $limit)
	{
		return $this->search($resource, array(), $limit, 0);
	}

	public function find($resource, $id)
	{
		return $this->db->get_where($resource, array('id' => (int) $id))->row_array();
	}

	public function slugExists($resource, $slug, $exceptId)
	{
		$this->db->where('slug', $slug);
		if ($exceptId > 0)
		{
			$this->db->where('id !=', (int) $exceptId);
		}
		return $this->db->count_all_results($resource) > 0;
	}

	public function save($resource, $data, $id)
	{
		if ($id > 0)
		{
			return $this->db->where('id', (int) $id)->update($resource, $data);
		}
		return $this->db->insert($resource, $data);
	}

	public function delete($resource, $id)
	{
		return $this->db->delete($resource, array('id' => (int) $id));
	}

	private function applyFilters($resource, $filters)
	{
		if (! empty($filters['q']))
		{
			$this->db->group_start()->like($resource . '.title', $filters['q'])->or_like($resource . '.slug', $filters['q'])->group_end();
		}
		if (! empty($filters['status']))
		{
			$this->db->where($resource . '.status', $filters['status']);
		}
		if (! empty($filters['category_id']))
		{
			$this->db->where($resource . '.category_id', (int) $filters['category_id']);
		}
	}
}
