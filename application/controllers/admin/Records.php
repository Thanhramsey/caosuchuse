<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Records extends Admin_Controller
{
	private $configs = array(
		'contacts' => array('label' => 'Liên hệ', 'permission' => 'contacts.manage', 'columns' => array('name', 'email', 'subject'), 'fields' => array('name' => array('label' => 'Họ tên', 'type' => 'text'), 'email' => array('label' => 'Email', 'type' => 'email'), 'phone' => array('label' => 'Điện thoại', 'type' => 'text'), 'subject' => array('label' => 'Chủ đề', 'type' => 'text'), 'message' => array('label' => 'Nội dung', 'type' => 'textarea'), 'status' => array('label' => 'Trạng thái', 'type' => 'status'), 'notes' => array('label' => 'Ghi chú xử lý', 'type' => 'textarea'))),
		'newsletter_subscribers' => array('label' => 'Đăng ký nhận tin', 'permission' => 'newsletter.manage', 'columns' => array('email'), 'fields' => array('email' => array('label' => 'Email', 'type' => 'email'), 'status' => array('label' => 'Trạng thái', 'type' => 'status'))),
		'comments' => array('label' => 'Bình luận', 'permission' => 'comments.moderate', 'columns' => array('author_name', 'email', 'body'), 'fields' => array('author_name' => array('label' => 'Người gửi', 'type' => 'text'), 'email' => array('label' => 'Email', 'type' => 'email'), 'resource' => array('label' => 'Loại nội dung', 'type' => 'text'), 'item_id' => array('label' => 'ID nội dung', 'type' => 'number'), 'body' => array('label' => 'Bình luận', 'type' => 'textarea'), 'status' => array('label' => 'Trạng thái', 'type' => 'status'))),
		'partners' => array('label' => 'Đối tác', 'permission' => 'partners.manage', 'columns' => array('name', 'website'), 'fields' => array('name' => array('label' => 'Tên đối tác', 'type' => 'text'), 'logo_path' => array('label' => 'Logo', 'type' => 'image'), 'website' => array('label' => 'Website', 'type' => 'url'), 'sort_order' => array('label' => 'Thứ tự', 'type' => 'number'), 'status' => array('label' => 'Trạng thái', 'type' => 'status'))),
		'testimonials' => array('label' => 'Ý kiến khách hàng', 'permission' => 'testimonials.manage', 'columns' => array('author_name', 'role', 'company'), 'fields' => array('author_name' => array('label' => 'Người nhận xét', 'type' => 'text'), 'role' => array('label' => 'Chức vụ', 'type' => 'text'), 'company' => array('label' => 'Đơn vị', 'type' => 'text'), 'quote' => array('label' => 'Nội dung', 'type' => 'textarea'), 'image_path' => array('label' => 'Ảnh đại diện', 'type' => 'image'), 'sort_order' => array('label' => 'Thứ tự', 'type' => 'number'), 'status' => array('label' => 'Trạng thái', 'type' => 'status')))
	);

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Admin_record_model');
	}

	public function index($table = '')
	{
		$config = $this->config($table);
		$filters = array('q' => trim((string) $this->input->get('q')), 'status' => (string) $this->input->get('status'), 'columns' => $config['columns']);
		$perPage = 20;
		$offset = ($this->currentPage() - 1) * $perPage;
		$total = $this->Admin_record_model->count($table, $filters);
		$this->render('admin/records/index', array('pageTitle' => $config['label'], 'activeMenu' => $table, 'recordTable' => $table, 'recordConfig' => $config, 'items' => $this->Admin_record_model->search($table, $filters, $perPage, $offset), 'filters' => $filters, 'total' => $total, 'offset' => $offset, 'pagination' => $this->paginationLinks(site_url('admin/' . $table), $total, $perPage), 'useEditor' => FALSE));
	}

	public function show($table = '', $id = 0)
	{
		$this->config($table);
		$item = $this->Admin_record_model->find($table, $id);
		if (empty($item)) return $this->json(array('success' => FALSE, 'message' => 'Dữ liệu không tồn tại.'), 404);
		$this->json(array('success' => TRUE, 'data' => $item));
	}

	public function save($table = '')
	{
		$config = $this->config($table);
		$this->requirePost();
		$id = (int) $this->input->post('id');
		$item = $id > 0 ? $this->Admin_record_model->find($table, $id) : NULL;
		if ($id > 0 && empty($item)) return $this->json(array('success' => FALSE, 'message' => 'Dữ liệu không tồn tại.'), 404);
		$validator = $this->validator();
		foreach ($config['fields'] as $name => $field)
		{
			if (in_array($field['type'], array('status', 'image'), TRUE)) continue;
			$rule = 'trim|max_length[255]';
			if (in_array($field['type'], array('text', 'email', 'url'), TRUE)) $rule = 'trim|required|max_length[255]';
			$validator->set_rules($name, $field['label'], $rule);
		}
		$errors = $validator->run() ? array() : $validator->error_array();
		if (! empty($errors)) return $this->json(array('success' => FALSE, 'message' => 'Vui lòng kiểm tra lại thông tin.', 'errors' => $errors), 422);
		$data = array();
		foreach ($config['fields'] as $name => $field)
		{
			$value = $this->input->post($name);
			if ($field['type'] === 'status' && in_array($table, array('partners', 'testimonials'), TRUE)) $value = $value === '1' ? 1 : 0;
			if ($field['type'] === 'status' && $table === 'contacts' && ! in_array($value, array('new', 'processing', 'done'), TRUE)) $value = 'new';
			if ($field['type'] === 'status' && $table === 'comments' && ! in_array($value, array('pending', 'approved', 'hidden'), TRUE)) $value = 'pending';
			if ($field['type'] === 'status' && $table === 'newsletter_subscribers' && ! in_array($value, array('subscribed', 'unsubscribed'), TRUE)) $value = 'subscribed';
			if ($field['type'] === 'number') $value = (int) $value;
			$data[$name] = $value === '' ? NULL : $value;
		}
		$now = date('Y-m-d H:i:s');
		if ($item === NULL) $data['created_at'] = $now;
		if (in_array($table, array('contacts', 'partners', 'testimonials'), TRUE)) $data['updated_at'] = $now;
		$this->Admin_record_model->save($table, $data, $id);
		$this->json(array('success' => TRUE, 'message' => ($item === NULL ? 'Đã thêm ' : 'Đã cập nhật ') . strtolower($config['label']) . '.'));
	}

	public function delete($table = '', $id = 0)
	{
		$config = $this->config($table);
		$this->requirePost();
		$item = $this->Admin_record_model->find($table, $id);
		if (empty($item)) return $this->json(array('success' => FALSE, 'message' => 'Dữ liệu không tồn tại.'), 404);
		$this->Admin_record_model->delete($table, $id);
		$this->json(array('success' => TRUE, 'message' => 'Đã xóa ' . strtolower($config['label']) . '.'));
	}

	private function config($table)
	{
		if (! isset($this->configs[$table])) show_404();
		$this->requirePermission($this->configs[$table]['permission']);
		return $this->configs[$table];
	}
}