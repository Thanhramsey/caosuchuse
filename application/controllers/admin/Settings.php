<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends Admin_Controller
{
	private $fields = array(
		'company_name' => array('label' => 'Tên công ty', 'type' => 'text'), 'group_name' => array('label' => 'Tập đoàn/đơn vị chủ quản', 'type' => 'text'), 'slogan' => array('label' => 'Slogan', 'type' => 'text'), 'logo_path' => array('label' => 'Đường dẫn logo', 'type' => 'text'), 'header_banner' => array('label' => 'Ảnh banner đầu trang', 'type' => 'text'), 'business_registration' => array('label' => 'Mã số doanh nghiệp', 'type' => 'text'), 'address' => array('label' => 'Địa chỉ', 'type' => 'text'), 'phone' => array('label' => 'Điện thoại', 'type' => 'text'), 'hotline' => array('label' => 'Hotline', 'type' => 'text'), 'fax' => array('label' => 'Fax', 'type' => 'text'), 'email' => array('label' => 'Email', 'type' => 'email'), 'working_hours' => array('label' => 'Giờ làm việc', 'type' => 'text'), 'map_embed_url' => array('label' => 'Bản đồ nhúng', 'type' => 'url'), 'facebook_url' => array('label' => 'Facebook', 'type' => 'url'), 'youtube_url' => array('label' => 'YouTube', 'type' => 'url'), 'zalo_url' => array('label' => 'Zalo', 'type' => 'url'), 'english_site_url' => array('label' => 'Website tiếng Anh (hiện cờ EN)', 'type' => 'url'), 'meta_title' => array('label' => 'Meta title', 'type' => 'text'), 'meta_description' => array('label' => 'Meta description', 'type' => 'textarea'));

	public function __construct()
	{
		parent::__construct();
		$this->fields += array(
			'company_name_en' => array('label' => 'Tên công ty (tiếng Anh)', 'type' => 'text'),
			'group_name_en' => array('label' => 'Đơn vị chủ quản (tiếng Anh)', 'type' => 'text'),
			'slogan_en' => array('label' => 'Slogan (tiếng Anh)', 'type' => 'text'),
			'address_en' => array('label' => 'Địa chỉ (tiếng Anh)', 'type' => 'text'),
			'meta_title_en' => array('label' => 'Meta title (tiếng Anh)', 'type' => 'text'),
			'meta_description_en' => array('label' => 'Meta description (tiếng Anh)', 'type' => 'textarea')
		);
		$this->requirePermission('settings.manage');
		$this->load->model('Setting_model');
	}

	public function index()
	{
		$this->render('admin/settings/index', array('pageTitle' => 'Cấu hình website', 'activeMenu' => 'settings', 'fields' => $this->fields, 'settings' => $this->Setting_model->all()));
	}

	public function save()
	{
		$this->requirePost();
		$validator = $this->validator();
		$validator->set_rules('company_name', 'Tên công ty', 'trim|required|max_length[191]');
		$validator->set_rules('email', 'Email', 'trim|valid_email|max_length[191]');
		$errors = $validator->run() ? array() : $validator->error_array();
		if (! empty($errors))
		{
			$this->session->set_flashdata('admin_error', 'Vui lòng kiểm tra lại thông tin.');
			redirect('admin/cau-hinh');
		}
		$values = array();
		foreach ($this->fields as $key => $field) $values[$key] = trim((string) $this->input->post($key));
		$this->Setting_model->saveMany($values);
		$this->session->set_flashdata('admin_success', 'Đã lưu cấu hình website.');
		redirect('admin/cau-hinh');
	}
}
