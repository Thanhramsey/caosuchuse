<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->requirePermission('users.view');
		$this->load->model('User_model');
	}

	public function index()
	{
		$filters = array(
			'q' => trim((string) $this->input->get('q')),
			'status' => (string) $this->input->get('status'),
			'role_id' => (int) $this->input->get('role_id')
		);
		if (! in_array($filters['status'], array('', '0', '1'), TRUE))
		{
			$filters['status'] = '';
		}
		$perPage = 15;
		$offset = ($this->currentPage() - 1) * $perPage;
		$total = $this->User_model->countSearch($filters);
		$this->render('admin/users/index', array(
			'pageTitle' => 'Người dùng',
			'activeMenu' => 'users',
			'items' => $this->User_model->search($filters, $perPage, $offset),
			'roles' => $this->User_model->roles(),
			'filters' => $filters,
			'total' => $total,
			'offset' => $offset,
			'pagination' => $this->paginationLinks(site_url('admin/nguoi-dung'), $total, $perPage),
			'canManage' => $this->admin_auth->can('users.create'),
			'canAssignRole' => $this->admin_auth->can('users.assign_role'),
			'currentUserId' => (int) $this->currentUser['id']
		));
	}

	public function show($id = 0)
	{
		$user = $this->User_model->findWithRole($id);
		if (empty($user))
		{
			return $this->json(array('success' => FALSE, 'message' => 'Người dùng không tồn tại.'), 404);
		}
		$this->json(array('success' => TRUE, 'data' => array(
			'id' => (int) $user['id'],
			'full_name' => $user['full_name'],
			'email' => $user['email'],
			'role_id' => (int) $user['role_id'],
			'status' => (int) $user['status'],
			'password' => ''
		)));
	}

	public function save()
	{
		$this->requirePost();
		$this->requirePermission('users.create');
		$id = (int) $this->input->post('id');
		$user = NULL;
		if ($id > 0)
		{
			$user = $this->User_model->findWithRole($id);
			if (empty($user))
			{
				return $this->json(array('success' => FALSE, 'message' => 'Người dùng không tồn tại hoặc đã bị xóa.'), 404);
			}
		}

		$validator = $this->validator();
		$validator->set_rules('full_name', 'Họ tên', 'trim|required|max_length[150]');
		$validator->set_rules('email', 'Email', 'trim|required|valid_email|max_length[191]');
		$validator->set_rules('role_id', 'Vai trò', 'trim|required|is_natural_no_zero');
		$validator->set_rules('password', 'Mật khẩu', ($user === NULL ? 'required|' : '') . 'min_length[10]|max_length[72]');
		$errors = $validator->run() ? array() : $validator->error_array();

		$email = strtolower((string) $this->input->post('email'));
		if (! isset($errors['email']) && $this->User_model->emailExists($email, $id))
		{
			$errors['email'] = 'Email này đã được sử dụng.';
		}
		$roleId = (int) $this->input->post('role_id');
		$role = $roleId > 0 ? $this->User_model->findRole($roleId) : NULL;
		if (! isset($errors['role_id']) && empty($role))
		{
			$errors['role_id'] = 'Vai trò không hợp lệ.';
		}
		$roleChanged = $user === NULL || (int) $user['role_id'] !== $roleId;
		if (! isset($errors['role_id']) && $roleChanged && ! $this->admin_auth->can('users.assign_role'))
		{
			$errors['role_id'] = 'Bạn không có quyền gán vai trò.';
		}
		$status = $this->input->post('status') === '1' ? 1 : 0;
		$isSelf = $user !== NULL && (int) $user['id'] === (int) $this->currentUser['id'];
		if ($isSelf && $status === 0)
		{
			$errors['status'] = 'Không thể tự khóa tài khoản đang đăng nhập.';
		}
		$losesSuperadmin = $user !== NULL && $user['role_name'] === 'superadmin' && ($status === 0 || (! empty($role) && $role['name'] !== 'superadmin'));
		if ($losesSuperadmin && $this->User_model->countActiveSuperadmins($id) === 0)
		{
			$errors['role_id'] = 'Phải giữ lại ít nhất một tài khoản quản trị toàn hệ thống đang hoạt động.';
		}
		if (! empty($errors))
		{
			return $this->json(array('success' => FALSE, 'message' => 'Vui lòng kiểm tra lại các trường được đánh dấu.', 'errors' => $errors), 422);
		}

		$now = date('Y-m-d H:i:s');
		$fullName = (string) $this->input->post('full_name');
		$data = array('full_name' => $fullName, 'email' => $email, 'status' => $status, 'updated_at' => $now);
		$password = (string) $this->input->post('password');
		if ($password !== '')
		{
			$data['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
			$data['failed_login_count'] = 0;
			$data['locked_until'] = NULL;
		}
		if ($user === NULL)
		{
			$data['created_at'] = $now;
			$saved = $this->User_model->create($data, $roleId);
		}
		else
		{
			$saved = $this->User_model->update($id, $data, $roleChanged ? $roleId : 0);
		}
		if (! $saved)
		{
			return $this->json(array('success' => FALSE, 'message' => 'Không thể lưu người dùng. Vui lòng thử lại.'), 500);
		}
		$this->json(array('success' => TRUE, 'message' => ($user === NULL ? 'Đã tạo tài khoản ' : 'Đã cập nhật tài khoản ') . $email . '.'));
	}

	public function toggle($id = 0)
	{
		$this->requirePost();
		$this->requirePermission('users.create');
		$user = $this->guardTarget($id, 'khóa');
		$newStatus = (int) $user['status'] === 1 ? 0 : 1;
		if ($newStatus === 0 && $user['role_name'] === 'superadmin' && $this->User_model->countActiveSuperadmins($id) === 0)
		{
			return $this->json(array('success' => FALSE, 'message' => 'Không thể khóa tài khoản quản trị toàn hệ thống cuối cùng.'), 409);
		}
		$this->User_model->toggleStatus($id, $newStatus);
		$this->json(array('success' => TRUE, 'message' => ($newStatus === 1 ? 'Đã mở khóa ' : 'Đã khóa ') . $user['email'] . '.'));
	}

	public function delete($id = 0)
	{
		$this->requirePost();
		$this->requirePermission('users.create');
		$user = $this->guardTarget($id, 'xóa');
		if ($user['role_name'] === 'superadmin' && $this->User_model->countActiveSuperadmins($id) === 0)
		{
			return $this->json(array('success' => FALSE, 'message' => 'Không thể xóa tài khoản quản trị toàn hệ thống cuối cùng.'), 409);
		}
		$this->User_model->delete($id);
		$this->json(array('success' => TRUE, 'message' => 'Đã xóa tài khoản ' . $user['email'] . '.'));
	}

	private function guardTarget($id, $action)
	{
		$user = $this->User_model->findWithRole($id);
		if (empty($user))
		{
			$this->haltJson(array('success' => FALSE, 'message' => 'Người dùng không tồn tại hoặc đã bị xóa.'), 404);
		}
		if ((int) $user['id'] === (int) $this->currentUser['id'])
		{
			$this->haltJson(array('success' => FALSE, 'message' => 'Không thể ' . $action . ' tài khoản đang đăng nhập.'), 409);
		}
		return $user;
	}
}