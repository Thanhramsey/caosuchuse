<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Categories extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->requirePermission('categories.manage');
		$this->load->model(array('Category_model', 'Content_model'));
	}

	public function index()
	{
		$filters = array(
			'type' => (string) $this->input->get('type'),
			'q' => trim((string) $this->input->get('q')),
			'status' => (string) $this->input->get('status')
		);
		if (! $this->Content_model->exists($filters['type']))
		{
			$filters['type'] = '';
		}
		if (! in_array($filters['status'], array('', '0', '1'), TRUE))
		{
			$filters['status'] = '';
		}
		$perPage = 15;
		$offset = ($this->currentPage() - 1) * $perPage;
		$total = $this->Category_model->countSearch($filters);
		$this->render('admin/categories/index', array(
			'pageTitle' => 'Danh mục',
			'activeMenu' => 'categories',
			'types' => $this->Content_model->labels(),
			'items' => $this->Category_model->search($filters, $perPage, $offset),
			'filters' => $filters,
			'total' => $total,
			'offset' => $offset,
			'pagination' => $this->paginationLinks(site_url('admin/danh-muc'), $total, $perPage)
		));
	}

	public function show($id = 0)
	{
		$item = $this->Category_model->find($id);
		if (empty($item))
		{
			return $this->json(array('success' => FALSE, 'message' => 'Danh mục không tồn tại.'), 404);
		}
		$this->json(array('success' => TRUE, 'data' => array(
			'id' => (int) $item['id'],
			'type' => $item['type'],
			'name' => $item['name'],
			'slug' => $item['slug'],
			'description' => (string) $item['description'],
			'sort_order' => (int) $item['sort_order'],
			'status' => (int) $item['status']
		)));
	}

	public function save()
	{
		$this->requirePost();
		$id = (int) $this->input->post('id');
		$item = NULL;
		if ($id > 0)
		{
			$item = $this->Category_model->find($id);
			if (empty($item))
			{
				return $this->json(array('success' => FALSE, 'message' => 'Danh mục không tồn tại hoặc đã bị xóa.'), 404);
			}
		}

		$validator = $this->validator();
		$validator->set_rules('type', 'Loại nội dung', 'trim|required|in_list[' . implode(',', array_keys(Content_model::RESOURCES)) . ']');
		$validator->set_rules('name', 'Tên danh mục', 'trim|required|max_length[150]');
		$validator->set_rules('slug', 'Đường dẫn', 'trim|max_length[150]');
		$validator->set_rules('description', 'Mô tả', 'trim|max_length[255]');
		$validator->set_rules('sort_order', 'Thứ tự', 'trim|integer');
		$errors = $validator->run() ? array() : $validator->error_array();

		$type = (string) $this->input->post('type');
		$name = (string) $this->input->post('name');
		$rawSlug = (string) $this->input->post('slug');
		$slug = $this->slugify($rawSlug !== '' ? $rawSlug : $name);
		if (! isset($errors['type']) && ! isset($errors['name']) && ! isset($errors['slug']))
		{
			if ($slug === '')
			{
				$errors['slug'] = 'Đường dẫn không hợp lệ.';
			}
			elseif ($this->Category_model->slugExists($type, $slug, $id))
			{
				$errors['slug'] = 'Đường dẫn đã tồn tại trong loại nội dung này.';
			}
		}
		if ($item !== NULL && $item['type'] !== $type && $this->Category_model->usageCount($id) > 0)
		{
			$errors['type'] = 'Không thể đổi loại khi danh mục đang có nội dung.';
		}
		if (! empty($errors))
		{
			return $this->json(array('success' => FALSE, 'message' => 'Vui lòng kiểm tra lại các trường được đánh dấu.', 'errors' => $errors), 422);
		}

		$now = date('Y-m-d H:i:s');
		$data = array(
			'type' => $type,
			'name' => $name,
			'slug' => $slug,
			'description' => (string) $this->input->post('description'),
			'sort_order' => (int) $this->input->post('sort_order'),
			'status' => $this->input->post('status') === '1' ? 1 : 0,
			'updated_at' => $now
		);
		if ($item === NULL)
		{
			$data['created_at'] = $now;
		}
		$this->Category_model->save($data, $id);
		$this->json(array('success' => TRUE, 'message' => ($item === NULL ? 'Đã thêm' : 'Đã cập nhật') . ' danh mục "' . $name . '".'));
	}

	public function delete($id = 0)
	{
		$this->requirePost();
		$item = $this->Category_model->find($id);
		if (empty($item))
		{
			return $this->json(array('success' => FALSE, 'message' => 'Danh mục không tồn tại hoặc đã bị xóa.'), 404);
		}
		$usage = $this->Category_model->usageCount($id);
		if ($usage > 0)
		{
			return $this->json(array('success' => FALSE, 'message' => 'Danh mục đang được dùng bởi ' . $usage . ' nội dung. Hãy chuyển các nội dung sang danh mục khác trước khi xóa.'), 409);
		}
		$this->Category_model->delete($id);
		$this->json(array('success' => TRUE, 'message' => 'Đã xóa danh mục "' . $item['name'] . '".'));
	}
}
