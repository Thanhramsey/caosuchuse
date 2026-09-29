<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sliders extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->requirePermission('sliders.manage');
		$this->load->model('Slider_model');
	}

	public function index()
	{
		$filters = array('q' => trim((string) $this->input->get('q')), 'status' => (string) $this->input->get('status'));
		if (! in_array($filters['status'], array('', '0', '1'), TRUE))
		{
			$filters['status'] = '';
		}
		$perPage = 12;
		$offset = ($this->currentPage() - 1) * $perPage;
		$total = $this->Slider_model->countSearch($filters);
		$this->render('admin/sliders/index', array(
			'pageTitle' => 'Slider trang chủ',
			'activeMenu' => 'sliders',
			'items' => $this->Slider_model->search($filters, $perPage, $offset),
			'filters' => $filters,
			'total' => $total,
			'offset' => $offset,
			'pagination' => $this->paginationLinks(site_url('admin/sliders'), $total, $perPage)
		));
	}

	public function show($id = 0)
	{
		$item = $this->Slider_model->find($id);
		if (empty($item))
		{
			return $this->json(array('success' => FALSE, 'message' => 'Slider không tồn tại.'), 404);
		}
		$this->json(array('success' => TRUE, 'data' => array(
			'id' => (int) $item['id'],
			'title' => $item['title'],
			'subtitle' => (string) $item['subtitle'],
			'image_path' => $item['image_path'],
			'alt_text' => $item['alt_text'],
			'link_url' => (string) $item['link_url'],
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
			$item = $this->Slider_model->find($id);
			if (empty($item))
			{
				return $this->json(array('success' => FALSE, 'message' => 'Slider không tồn tại hoặc đã bị xóa.'), 404);
			}
		}

		$validator = $this->validator();
		$validator->set_rules('title', 'Tiêu đề', 'trim|required|max_length[191]');
		$validator->set_rules('subtitle', 'Mô tả ngắn', 'trim|max_length[255]');
		$validator->set_rules('image_path', 'Ảnh slider', 'trim|required|max_length[255]');
		$validator->set_rules('alt_text', 'Mô tả ảnh (alt)', 'trim|max_length[191]');
		$validator->set_rules('link_url', 'Liên kết', 'trim|max_length[255]');
		$validator->set_rules('sort_order', 'Thứ tự', 'trim|integer');
		$errors = $validator->run() ? array() : $validator->error_array();

		$imagePath = (string) $this->input->post('image_path');
		if (! isset($errors['image_path']) && ! $this->isUploadedImagePath($imagePath))
		{
			$errors['image_path'] = 'Vui lòng tải ảnh lên từ hệ thống.';
		}
		$linkUrl = (string) $this->input->post('link_url');
		if ($linkUrl !== '' && ! isset($errors['link_url']) && ! preg_match('#^(https?://[^\s<>"]+|/[^\s<>"]*)$#i', $linkUrl))
		{
			$errors['link_url'] = 'Liên kết phải bắt đầu bằng http://, https:// hoặc /.';
		}
		if (! empty($errors))
		{
			return $this->json(array('success' => FALSE, 'message' => 'Vui lòng kiểm tra lại các trường được đánh dấu.', 'errors' => $errors), 422);
		}

		$title = (string) $this->input->post('title');
		$altText = (string) $this->input->post('alt_text');
		$now = date('Y-m-d H:i:s');
		$data = array(
			'title' => $title,
			'subtitle' => (string) $this->input->post('subtitle'),
			'image_path' => $imagePath,
			'alt_text' => $altText !== '' ? $altText : $title,
			'link_url' => $linkUrl === '' ? NULL : $linkUrl,
			'sort_order' => (int) $this->input->post('sort_order'),
			'status' => $this->input->post('status') === '1' ? 1 : 0,
			'updated_at' => $now
		);
		if ($item === NULL)
		{
			$data['created_at'] = $now;
		}
		$this->Slider_model->save($data, $id);
		$this->json(array('success' => TRUE, 'message' => ($item === NULL ? 'Đã thêm' : 'Đã cập nhật') . ' slider "' . $title . '".'));
	}

	public function delete($id = 0)
	{
		$this->requirePost();
		$item = $this->Slider_model->find($id);
		if (empty($item))
		{
			return $this->json(array('success' => FALSE, 'message' => 'Slider không tồn tại hoặc đã bị xóa.'), 404);
		}
		$this->Slider_model->delete($id);
		$this->json(array('success' => TRUE, 'message' => 'Đã xóa slider "' . $item['title'] . '".'));
	}
}