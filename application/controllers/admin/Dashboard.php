<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends Admin_Controller
{
	public function index()
	{
		$this->load->model(array('Content_model', 'Category_model', 'Slider_model', 'User_model'));
		$stats = array();
		$cards = array(
			array('key' => 'news', 'label' => 'Tin tức', 'icon' => 'news', 'url' => 'admin/noi-dung/news', 'permission' => 'news.view', 'color' => 'green'),
			array('key' => 'projects', 'label' => 'Dự án', 'icon' => 'projects', 'url' => 'admin/noi-dung/projects', 'permission' => 'projects.manage', 'color' => 'teal'),
			array('key' => 'services', 'label' => 'Dịch vụ', 'icon' => 'services', 'url' => 'admin/noi-dung/services', 'permission' => 'services.manage', 'color' => 'lime'),
			array('key' => 'categories', 'label' => 'Danh mục', 'icon' => 'folder', 'url' => 'admin/danh-muc', 'permission' => 'categories.manage', 'color' => 'yellow'),
			array('key' => 'sliders', 'label' => 'Slider', 'icon' => 'photo', 'url' => 'admin/sliders', 'permission' => 'sliders.manage', 'color' => 'orange'),
			array('key' => 'users', 'label' => 'Người dùng', 'icon' => 'users', 'url' => 'admin/nguoi-dung', 'permission' => 'users.view', 'color' => 'azure')
		);
		foreach ($cards as $card)
		{
			if (! $this->admin_auth->can($card['permission']))
			{
				continue;
			}
			if ($this->Content_model->exists($card['key']))
			{
				$card['count'] = $this->Content_model->countAll($card['key']);
			}
			elseif ($card['key'] === 'categories')
			{
				$card['count'] = $this->Category_model->countAll();
			}
			elseif ($card['key'] === 'sliders')
			{
				$card['count'] = $this->Slider_model->countAll();
			}
			else
			{
				$card['count'] = $this->User_model->countAll();
			}
			$stats[] = $card;
		}
		$this->render('admin/dashboard', array(
			'pageTitle' => 'Bảng điều khiển',
			'activeMenu' => 'dashboard',
			'stats' => $stats,
			'latestNews' => $this->admin_auth->can('news.view') ? $this->Content_model->latest('news', 6) : array()
		));
	}
}