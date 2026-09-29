<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Permission_model extends CI_Model
{
	public function roles()
	{
		return $this->db
			->select('roles.*, (SELECT COUNT(*) FROM user_roles WHERE user_roles.role_id = roles.id) AS user_count, (SELECT COUNT(*) FROM role_permissions WHERE role_permissions.role_id = roles.id) AS permission_count', FALSE)
			->order_by('roles.id', 'ASC')
			->get('roles')->result_array();
	}

	public function findRole($roleId)
	{
		return $this->db->get_where('roles', array('id' => (int) $roleId))->row_array();
	}

	public function permissions()
	{
		return $this->db->order_by('name', 'ASC')->get('permissions')->result_array();
	}

	public function existingIds($ids)
	{
		$ids = array_values(array_unique(array_filter(array_map('intval', (array) $ids))));
		if (empty($ids))
		{
			return array();
		}
		$rows = $this->db->select('id')->where_in('id', $ids)->get('permissions')->result_array();
		$result = array();
		foreach ($rows as $row)
		{
			$result[] = (int) $row['id'];
		}
		return $result;
	}

	public function assigned($roleId)
	{
		$rows = $this->db->select('permission_id')->get_where('role_permissions', array('role_id' => (int) $roleId))->result_array();
		$assigned = array();
		foreach ($rows as $row)
		{
			$assigned[] = (int) $row['permission_id'];
		}
		return $assigned;
	}

	public function sync($roleId, $permissionIds)
	{
		$this->db->trans_start();
		$this->db->delete('role_permissions', array('role_id' => (int) $roleId));
		foreach ($permissionIds as $permissionId)
		{
			$this->db->insert('role_permissions', array('role_id' => (int) $roleId, 'permission_id' => (int) $permissionId));
		}
		$this->db->trans_complete();
		return $this->db->trans_status();
	}
}