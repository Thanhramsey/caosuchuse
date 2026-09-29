<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Menus extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->requirePermission('menus.manage');
		$this->load->model('Menu_model');
	}

	public function index()
	{
		$location = (string) $this->input->get('location');
		if (! isset(Menu_model::LOCATIONS[$location]))
		{
			$location = 'main';
		}
		$this->render('admin/menus/index', array(
			'pageTitle' => 'Quản lý menu',
			'activeMenu' => 'menus',
			'locations' => Menu_model::LOCATIONS,
			'location' => $location,
			'items' => $this->Menu_model->flatTree($location),
			'parentOptions' => $this->parentOptions($location, 0)
		));
	}

	public function show($id = 0)
	{
		$item = $this->Menu_model->find($id);
		if (empty($item))
		{
			return $this->json(array('success' => FALSE, 'message' => 'Mục menu không tồn tại.'), 404);
		}
		$this->json(array('success' => TRUE, 'data' => array(
			'id' => (int) $item['id'],
			'location' => $item['location'],
			'parent_id' => $item['parent_id'] === NULL ? '' : (int) $item['parent_id'],
			'title' => $item['title'],
			'title_en' => isset($item['title_en']) ? $item['title_en'] : '',
			'url' => $item['url'],
			'target' => $item['target'],
			'sort_order' => (int) $item['sort_order'],
			'status' => (int) $item['status']
		)));
	}

	public function save()
	{
		$this->requirePost();
		$id = (int) $this->input->post('id');
		$item = $id > 0 ? $this->Menu_model->find($id) : NULL;
		if ($id > 0 && empty($item))
		{
			return $this->json(array('success' => FALSE, 'message' => 'Mục menu không tồn tại hoặc đã bị xóa.'), 404);
		}
		$validator = $this->validator();
		$validator->set_rules('location', 'Vị trí menu', 'trim|required|in_list[' . implode(',', array_keys(Menu_model::LOCATIONS)) . ']');
		$validator->set_rules('title', 'Tên menu', 'trim|required|max_length[150]');
		$validator->set_rules('title_en', 'Tên menu tiếng Anh', 'trim|max_length[150]');
		$validator->set_rules('url', 'Đường dẫn', 'trim|required|max_length[255]');
		$validator->set_rules('target', 'Cách mở', 'trim|required|in_list[_self,_blank]');
		$validator->set_rules('parent_id', 'Menu cha', 'trim|is_natural');
		$validator->set_rules('sort_order', 'Thứ tự', 'trim|integer');
		$errors = $validator->run() ? array() : $validator->error_array();
		$location = (string) $this->input->post('location');
		$parentId = (int) $this->input->post('parent_id');
		if ($parentId > 0)
		{
			$parent = $this->Menu_model->find($parentId);
			if (empty($parent) || $parent['location'] !== $location || ($id > 0 && ($parentId === $id || $this->Menu_model->isDescendant($parentId, $id))))
			{
				$errors['parent_id'] = 'Menu cha không hợp lệ.';
			}
			elseif ($this->parentDepth($parentId) + ($id > 0 ? $this->Menu_model->subtreeHeight($id) : 1) > Menu_model::MAX_DEPTH)
			{
				$errors['parent_id'] = 'Cây menu không được vượt quá ' . Menu_model::MAX_DEPTH . ' cấp.';
			}
		}
		if (! empty($errors))
		{
			return $this->json(array('success' => FALSE, 'message' => 'Vui lòng kiểm tra lại các trường được đánh dấu.', 'errors' => $errors), 422);
		}
		$now = date('Y-m-d H:i:s');
		$data = array(
			'location' => $location,
			'parent_id' => $parentId > 0 ? $parentId : NULL,
			'title' => (string) $this->input->post('title'),
			'title_en' => (string) $this->input->post('title_en'),
			'url' => (string) $this->input->post('url'),
			'target' => (string) $this->input->post('target'),
			'sort_order' => (int) $this->input->post('sort_order') > 0 ? (int) $this->input->post('sort_order') : $this->Menu_model->nextSortOrder($location, $parentId),
			'status' => $this->input->post('status') === '1' ? 1 : 0,
			'updated_at' => $now
		);
		if ($item === NULL)
		{
			$data['created_at'] = $now;
		}
		$this->Menu_model->save($data, $id);
		$this->json(array('success' => TRUE, 'message' => ($item === NULL ? 'Đã thêm' : 'Đã cập nhật') . ' mục menu.'));
	}

	public function delete($id = 0)
	{
		$this->requirePost();
		$item = $this->Menu_model->find($id);
		if (empty($item))
		{
			return $this->json(array('success' => FALSE, 'message' => 'Mục menu không tồn tại hoặc đã bị xóa.'), 404);
		}
		if ($this->Menu_model->childCount($id) > 0)
		{
			return $this->json(array('success' => FALSE, 'message' => 'Menu đang có menu con. Hãy xóa hoặc chuyển menu con trước.'), 409);
		}
		$this->Menu_model->delete($id);
		$this->json(array('success' => TRUE, 'message' => 'Đã xóa mục menu.'));
	}

	public function move($id = 0, $direction = 'up')
	{
		$this->requirePost();
		if (! in_array($direction, array('up', 'down'), TRUE) || ! $this->Menu_model->move($id, $direction))
		{
			return $this->json(array('success' => FALSE, 'message' => 'Không thể đổi thứ tự mục menu.'), 409);
		}
		$this->json(array('success' => TRUE, 'message' => 'Đã cập nhật thứ tự menu.'));
	}

	private function parentOptions($location, $exceptId)
	{
		$options = array();
		foreach ($this->Menu_model->flatTree($location) as $item)
		{
			if ((int) $item['id'] === (int) $exceptId || ($exceptId > 0 && $this->Menu_model->isDescendant($item['id'], $exceptId)))
			{
				continue;
			}
			$options[] = $item;
		}
		return $options;
	}

	private function parentDepth($id)
	{
		return $this->Menu_model->depth($id);
	}
}
