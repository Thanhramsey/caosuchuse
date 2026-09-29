<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Permissions extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->requirePermission('users.assign_role');
		$this->load->model('Permission_model');
	}

	public function index($roleId = 0)
	{
		$roles = $this->Permission_model->roles();
		if ((int) $roleId === 0 && ! empty($roles))
		{
			$roleId = (int) $roles[0]['id'];
		}
		$role = $this->Permission_model->findRole($roleId);
		if (empty($role))
		{
			show_404();
		}
		$groups = array();
		foreach ($this->Permission_model->permissions() as $permission)
		{
			$groups[admin_permission_group($permission['name'])][] = $permission;
		}
		$this->render('admin/permissions/index', array(
			'pageTitle' => 'Phân quyền',
			'activeMenu' => 'permissions',
			'roles' => $roles,
			'role' => $role,
			'groups' => $groups,
			'assigned' => $this->Permission_model->assigned($roleId),
			'locked' => $role['name'] === 'superadmin'
		));
	}

	public function save($roleId = 0)
	{
		if ($this->input->method(TRUE) !== 'POST')
		{
			show_error('Phương thức không được phép.', 405);
		}
		$role = $this->Permission_model->findRole($roleId);
		if (empty($role))
		{
			show_404();
		}
		if ($role['name'] === 'superadmin')
		{
			$this->session->set_flashdata('admin_error', 'Vai trò quản trị toàn hệ thống luôn có đủ quyền và không thể thay đổi.');
			redirect('admin/phan-quyen/' . (int) $roleId);
		}
		$permissionIds = $this->Permission_model->existingIds($this->input->post('permissions'));
		$this->Permission_model->sync($roleId, $permissionIds);
		$this->session->set_flashdata('admin_success', 'Đã cập nhật quyền cho vai trò "' . admin_role_label($role['name']) . '".');
		redirect('admin/phan-quyen/' . (int) $roleId);
	}
}