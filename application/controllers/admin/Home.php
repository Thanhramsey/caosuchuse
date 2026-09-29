<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->requirePermission('home.manage');
		$this->load->model(array('Home_section_model', 'Category_model', 'Content_model'));
	}

	public function index()
	{
		$categoryOptions = array();
		foreach (Content_model::RESOURCES as $resource => $config)
		{
			foreach ($this->Category_model->options($resource) as $category)
			{
				$categoryOptions[] = array('id' => (int) $category['id'], 'label' => $config['label'] . ' / ' . $category['name']);
			}
		}
		$this->render('admin/home/index', array(
			'pageTitle' => 'Nội dung trang chủ',
			'activeMenu' => 'home',
			'useEditor' => TRUE,
			'groups' => $this->Home_section_model->grouped(),
			'positions' => Home_section_model::POSITIONS,
			'types' => Home_section_model::TYPES,
			'resources' => Content_model::RESOURCES,
			'categoryOptions' => $categoryOptions
		));
	}

	public function show($id = 0)
	{
		$item = $this->Home_section_model->find($id);
		if (empty($item))
		{
			return $this->json(array('success' => FALSE, 'message' => 'Khối nội dung không tồn tại.'), 404);
		}
		$item['id'] = (int) $item['id'];
		$item['source_category_id'] = $item['source_category_id'] === NULL ? '' : (int) $item['source_category_id'];
		$item['status'] = (int) $item['status'];
		$this->json(array('success' => TRUE, 'data' => $item));
	}

	public function save()
	{
		$this->requirePost();
		$id = (int) $this->input->post('id');
		$item = $id > 0 ? $this->Home_section_model->find($id) : NULL;
		if ($id > 0 && empty($item))
		{
			return $this->json(array('success' => FALSE, 'message' => 'Khối nội dung không tồn tại hoặc đã bị xóa.'), 404);
		}
		$validator = $this->validator();
		$validator->set_rules('section_key', 'Mã cấu hình', 'trim|required|max_length[50]');
		$validator->set_rules('title', 'Tiêu đề', 'trim|required|max_length[150]');
		$validator->set_rules('title_en', 'Tiêu đề tiếng Anh', 'trim|max_length[150]');
		$validator->set_rules('subtitle', 'Mô tả phụ', 'trim|max_length[255]');
		$validator->set_rules('position', 'Vị trí', 'trim|required|in_list[' . implode(',', array_keys(Home_section_model::POSITIONS)) . ']');
		$validator->set_rules('section_type', 'Loại khối', 'trim|required|in_list[' . implode(',', array_keys(Home_section_model::TYPES)) . ']');
		$validator->set_rules('item_limit', 'Số lượng hiển thị', 'trim|is_natural');
		$validator->set_rules('sort_order', 'Thứ tự', 'trim|integer');
		$errors = $validator->run() ? array() : $validator->error_array();
		$key = (string) $this->input->post('section_key');
		if (! isset($errors['section_key']) && ! preg_match('/^[a-z0-9_]+$/', $key))
		{
			$errors['section_key'] = 'Mã chỉ gồm chữ thường, số và dấu gạch dưới.';
		}
		if (! isset($errors['section_key']) && $this->Home_section_model->keyExists($key, $id))
		{
			$errors['section_key'] = 'Mã cấu hình đã tồn tại.';
		}
		$resource = (string) $this->input->post('source_resource');
		$categoryId = (int) $this->input->post('source_category_id');
		if ($resource !== '' && ! isset(Content_model::RESOURCES[$resource]))
		{
			$errors['source_resource'] = 'Nguồn nội dung không hợp lệ.';
		}
		if ($categoryId > 0 && (! isset(Content_model::RESOURCES[$resource]) || ! $this->Category_model->belongsTo($categoryId, $resource)))
		{
			$errors['source_category_id'] = 'Danh mục nguồn không hợp lệ.';
		}
		$imagePath = (string) $this->input->post('image_path');
		if ($imagePath !== '' && ! $this->isUploadedImagePath($imagePath))
		{
			$errors['image_path'] = 'Ảnh phải được tải lên từ hệ thống.';
		}
		if (! empty($errors))
		{
			return $this->json(array('success' => FALSE, 'message' => 'Vui lòng kiểm tra lại các trường được đánh dấu.', 'errors' => $errors), 422);
		}
		$this->load->library('Html_sanitizer');
		$position = (string) $this->input->post('position');
		$now = date('Y-m-d H:i:s');
		$data = array(
			'section_key' => $key,
			'title' => (string) $this->input->post('title'),
			'title_en' => (string) $this->input->post('title_en'),
			'subtitle' => (string) $this->input->post('subtitle'),
			'position' => $position,
			'section_type' => (string) $this->input->post('section_type'),
			'content_html' => $this->html_sanitizer->clean($this->input->post('content_html')),
			'image_path' => $imagePath === '' ? NULL : $imagePath,
			'link_label' => (string) $this->input->post('link_label'),
			'link_url' => (string) $this->input->post('link_url'),
			'source_resource' => $resource === '' ? NULL : $resource,
			'source_category_id' => $categoryId > 0 ? $categoryId : NULL,
			'item_limit' => max(1, min(99, (int) $this->input->post('item_limit'))),
			'sort_order' => (int) $this->input->post('sort_order') > 0 ? (int) $this->input->post('sort_order') : $this->Home_section_model->nextSortOrder($position),
			'status' => $this->input->post('status') === '1' ? 1 : 0,
			'updated_at' => $now
		);
		if ($item === NULL)
		{
			$data['created_at'] = $now;
		}
		$this->Home_section_model->save($data, $id);
		$this->json(array('success' => TRUE, 'message' => ($item === NULL ? 'Đã thêm' : 'Đã cập nhật') . ' khối nội dung trang chủ.'));
	}

	public function delete($id = 0)
	{
		$this->requirePost();
		$item = $this->Home_section_model->find($id);
		if (empty($item))
		{
			return $this->json(array('success' => FALSE, 'message' => 'Khối nội dung không tồn tại hoặc đã bị xóa.'), 404);
		}
		$this->Home_section_model->delete($id);
		$this->json(array('success' => TRUE, 'message' => 'Đã xóa khối nội dung.'));
	}

	public function move($id = 0, $direction = 'up')
	{
		$this->requirePost();
		$item = $this->Home_section_model->find($id);
		if (empty($item) || ! in_array($direction, array('up', 'down'), TRUE) || ! $this->Home_section_model->swapWithNeighbour('home_sections', $this->Home_section_model->siblings($item['position']), (int) $id, $direction))
		{
			return $this->json(array('success' => FALSE, 'message' => 'Không thể đổi thứ tự khối nội dung.'), 409);
		}
		$this->json(array('success' => TRUE, 'message' => 'Đã cập nhật thứ tự trang chủ.'));
	}
}
