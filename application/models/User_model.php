<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model
{
	public function findByEmail($email)
	{
		return $this->db->get_where('users', array('email' => strtolower(trim((string) $email)), 'status' => 1))->row_array();
	}

	public function findById($id)
	{
		return $this->db->get_where('users', array('id' => (int) $id, 'status' => 1))->row_array();
	}

	public function permissionsForUser($userId)
	{
		return $this->db->select('permissions.name')->from('permissions')->join('role_permissions', 'role_permissions.permission_id = permissions.id')->join('user_roles', 'user_roles.role_id = role_permissions.role_id')->where('user_roles.user_id', (int) $userId)->get()->result_array();
	}

	public function search($filters, $limit, $offset)
	{
		$this->applyFilters($filters);
		return $this->db
			->select('users.id, users.full_name, users.email, users.status, users.locked_until, users.last_login_at, roles.id AS role_id, roles.name AS role_name, roles.label AS role_label')
			->from('users')
			->join('user_roles', 'user_roles.user_id = users.id', 'left')
			->join('roles', 'roles.id = user_roles.role_id', 'left')
			->order_by('users.id', 'DESC')
			->limit((int) $limit, (int) $offset)
			->get()->result_array();
	}

	public function countSearch($filters)
	{
		$this->applyFilters($filters);
		return (int) $this->db->from('users')->join('user_roles', 'user_roles.user_id = users.id', 'left')->count_all_results();
	}

	public function countAll()
	{
		return (int) $this->db->count_all('users');
	}

	public function findWithRole($id)
	{
		return $this->db
			->select('users.id, users.full_name, users.email, users.status, roles.id AS role_id, roles.name AS role_name')
			->from('users')
			->join('user_roles', 'user_roles.user_id = users.id', 'left')
			->join('roles', 'roles.id = user_roles.role_id', 'left')
			->where('users.id', (int) $id)
			->get()->row_array();
	}

	public function roles()
	{
		return $this->db->order_by('id', 'ASC')->get('roles')->result_array();
	}

	public function findRole($roleId)
	{
		return $this->db->get_where('roles', array('id' => (int) $roleId))->row_array();
	}

	public function emailExists($email, $exceptId = 0)
	{
		$this->db->where('email', strtolower(trim((string) $email)));
		if ($exceptId > 0)
		{
			$this->db->where('id !=', (int) $exceptId);
		}
		return $this->db->count_all_results('users') > 0;
	}

	public function countActiveSuperadmins($exceptUserId = 0)
	{
		$this->db->from('users')
			->join('user_roles', 'user_roles.user_id = users.id')
			->join('roles', 'roles.id = user_roles.role_id')
			->where('roles.name', 'superadmin')
			->where('users.status', 1);
		if ($exceptUserId > 0)
		{
			$this->db->where('users.id !=', (int) $exceptUserId);
		}
		return (int) $this->db->count_all_results();
	}

	public function create($data, $roleId)
	{
		$this->db->trans_start();
		$this->db->insert('users', $data);
		$userId = $this->db->insert_id();
		$this->db->insert('user_roles', array('user_id' => $userId, 'role_id' => (int) $roleId));
		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	public function update($userId, $data, $roleId)
	{
		$this->db->trans_start();
		$this->db->where('id', (int) $userId)->update('users', $data);
		if ($roleId > 0)
		{
			$this->db->delete('user_roles', array('user_id' => (int) $userId));
			$this->db->insert('user_roles', array('user_id' => (int) $userId, 'role_id' => (int) $roleId));
		}
		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	public function delete($userId)
	{
		return $this->db->delete('users', array('id' => (int) $userId));
	}

	public function toggleStatus($userId, $status)
	{
		return $this->db->where('id', (int) $userId)->update('users', array('status' => (int) $status, 'failed_login_count' => 0, 'locked_until' => NULL, 'updated_at' => date('Y-m-d H:i:s')));
	}

	private function applyFilters($filters)
	{
		if (! empty($filters['q']))
		{
			$this->db->group_start()->like('users.full_name', $filters['q'])->or_like('users.email', $filters['q'])->group_end();
		}
		if (isset($filters['status']) && $filters['status'] !== '')
		{
			$this->db->where('users.status', (int) $filters['status']);
		}
		if (! empty($filters['role_id']))
		{
			$this->db->where('user_roles.role_id', (int) $filters['role_id']);
		}
	}

	public function markLogin($userId)
	{
		$this->db->where('id', (int) $userId)->update('users', array('failed_login_count' => 0, 'locked_until' => NULL, 'last_login_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')));
	}

	public function recordFailedLogin($userId, $failedCount)
	{
		$values = array('failed_login_count' => (int) $failedCount, 'updated_at' => date('Y-m-d H:i:s'));
		if ($failedCount >= 5)
		{
			$values['locked_until'] = date('Y-m-d H:i:s', time() + 900);
		}
		$this->db->where('id', (int) $userId)->update('users', $values);
	}
}