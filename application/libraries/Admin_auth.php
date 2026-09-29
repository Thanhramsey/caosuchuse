<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_auth
{
	private $CI;
	private $user = NULL;
	private $permissions = NULL;

	public function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->model('User_model');
	}

	public function attempt($email, $password)
	{
		$user = $this->CI->User_model->findByEmail($email);
		if (empty($user))
		{
			return FALSE;
		}
		if (! empty($user['locked_until']) && strtotime($user['locked_until']) > time())
		{
			return FALSE;
		}
		if (! password_verify((string) $password, $user['password_hash']))
		{
			$this->CI->User_model->recordFailedLogin($user['id'], ((int) $user['failed_login_count']) + 1);
			return FALSE;
		}

		$this->CI->session->sess_regenerate(TRUE);
		$this->CI->session->set_userdata(array('admin_user_id' => (int) $user['id'], 'admin_email' => $user['email']));
		$this->CI->User_model->markLogin($user['id']);
		return TRUE;
	}

	public function check()
	{
		return $this->user() !== NULL;
	}

	public function user()
	{
		if ($this->user === NULL)
		{
			$userId = (int) $this->CI->session->userdata('admin_user_id');
			$user = $userId > 0 ? $this->CI->User_model->findById($userId) : NULL;
			$this->user = empty($user) ? FALSE : $user;
		}
		return $this->user === FALSE ? NULL : $this->user;
	}

	public function permissions()
	{
		if ($this->permissions === NULL)
		{
			$this->permissions = array();
			$user = $this->user();
			if ($user !== NULL)
			{
				foreach ($this->CI->User_model->permissionsForUser($user['id']) as $item)
				{
					$this->permissions[] = $item['name'];
				}
			}
		}
		return $this->permissions;
	}

	public function can($permission)
	{
		return in_array((string) $permission, $this->permissions(), TRUE);
	}

	public function logout()
	{
		$this->CI->session->sess_destroy();
	}
}