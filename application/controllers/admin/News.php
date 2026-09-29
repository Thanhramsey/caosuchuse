<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class News extends Admin_Controller
{
	public function index()
	{
		$this->requirePermission('news.view');
		$news = $this->db->select('id, title, slug, status, published_at, updated_at')->from('news')->order_by('updated_at', 'DESC')->get()->result_array();
		$this->load->view('admin/news/index', array('news' => $news, 'user' => $this->admin_auth->user()));
	}
}