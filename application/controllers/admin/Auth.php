<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library('Admin_auth');
	}

	public function login()
	{
		if ($this->admin_auth->check())
		{
			redirect('admin');
		}

		$data = array('error' => '', 'email' => '');
		if ($this->input->method(TRUE) === 'POST')
		{
			$email = trim((string) $this->input->post('email'));
			$password = (string) $this->input->post('password');
			if ($this->admin_auth->attempt($email, $password))
			{
				redirect('admin');
			}
			$data['error'] = 'Email hoặc mật khẩu không đúng, hoặc tài khoản đang bị khóa.';
			$data['email'] = $email;
		}

		$this->load->view('admin/login', $data);
	}

	public function logout()
	{
		$this->admin_auth->logout();
		redirect('admin/dang-nhap');
	}
}