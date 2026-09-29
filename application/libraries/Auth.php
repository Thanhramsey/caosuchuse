<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth
{
	private $CI;

	public function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->model('User_model');
	}

	public function attempt($email, $password)
	{
		$user = $this->CI->User_model->findByEmail($email);
		if (empty($user) || ! password_verify((string) $password, $user['password_hash']))
		{
			return FALSE;
		}

		$this->CI->session->sess_regenerate(TRUE);
		$this->CI->session->set_userdata(array('admin_user_id' => (int) $user['id'], 'admin_email' => $user['email']));
		$this->CI->User_model->markLogin($user['id']);
		return TRUE;
	}

	public function check()
	{
		return (int) $this->CI->session->userdata('admin_user_id') > 0;
	}

	public function user()
	{
		$userId = (int) $this->CI->session->userdata('admin_user_id');
		return $userId > 0 ? $this->CI->User_model->findById($userId) : NULL;
	}

	public function can($permission)
	{
		$user = $this->user();
		if (empty($user))
		{
			return FALSE;
		}

		$permissions = $this->CI->User_model->permissionsForUser($user['id']);
		foreach ($permissions as $item)
		{
			if ($item['name'] === $permission)
			{
				return TRUE;
			}
		}

		return FALSE;
	}

	public function logout()
	{
		$this->CI->session->sess_destroy();
	}
}