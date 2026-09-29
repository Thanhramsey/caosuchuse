<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_cao_su_schema extends CI_Migration
{
	public function up()
	{
		$this->createUsers();
		$this->createRoles();
		$this->createPermissions();
		$this->createUserRoles();
		$this->createRolePermissions();
		$this->createNews();
		$this->createProjects();
		$this->createServices();
		$this->createSliders();
		$this->seedRolesAndPermissions();
	}

	public function down()
	{
		$tables = array('role_permissions', 'user_roles', 'permissions', 'roles', 'users', 'sliders', 'services', 'projects', 'news');
		foreach ($tables as $table)
		{
			$this->dbforge->drop_table($table, TRUE);
		}
	}

	private function createUsers()
	{
		$this->dbforge->add_field(array(
			'id' => array('type' => 'INT', 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'full_name' => array('type' => 'VARCHAR', 'constraint' => 150),
			'email' => array('type' => 'VARCHAR', 'constraint' => 191),
			'password_hash' => array('type' => 'VARCHAR', 'constraint' => 255),
			'status' => array('type' => 'TINYINT', 'constraint' => 1, 'default' => 1),
			'failed_login_count' => array('type' => 'SMALLINT', 'constraint' => 5, 'unsigned' => TRUE, 'default' => 0),
			'locked_until' => array('type' => 'DATETIME', 'null' => TRUE),
			'last_login_at' => array('type' => 'DATETIME', 'null' => TRUE),
			'created_at' => array('type' => 'DATETIME'),
			'updated_at' => array('type' => 'DATETIME')
		));
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('users', TRUE, array('ENGINE' => 'InnoDB', 'DEFAULT CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci'));
		$this->db->query('ALTER TABLE `users` ADD UNIQUE KEY `uq_users_email` (`email`)');
	}

	private function createRoles()
	{
		$this->dbforge->add_field(array('id' => array('type' => 'INT', 'unsigned' => TRUE, 'auto_increment' => TRUE), 'name' => array('type' => 'VARCHAR', 'constraint' => 80), 'label' => array('type' => 'VARCHAR', 'constraint' => 120), 'created_at' => array('type' => 'DATETIME')));
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('roles', TRUE, array('ENGINE' => 'InnoDB', 'DEFAULT CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci'));
		$this->db->query('ALTER TABLE `roles` ADD UNIQUE KEY `uq_roles_name` (`name`)');
	}

	private function createPermissions()
	{
		$this->dbforge->add_field(array('id' => array('type' => 'INT', 'unsigned' => TRUE, 'auto_increment' => TRUE), 'name' => array('type' => 'VARCHAR', 'constraint' => 100), 'label' => array('type' => 'VARCHAR', 'constraint' => 150), 'created_at' => array('type' => 'DATETIME')));
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('permissions', TRUE, array('ENGINE' => 'InnoDB', 'DEFAULT CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci'));
		$this->db->query('ALTER TABLE `permissions` ADD UNIQUE KEY `uq_permissions_name` (`name`)');
	}

	private function createUserRoles()
	{
		$this->dbforge->add_field(array('user_id' => array('type' => 'INT', 'unsigned' => TRUE), 'role_id' => array('type' => 'INT', 'unsigned' => TRUE)));
		$this->dbforge->add_key(array('user_id', 'role_id'), TRUE);
		$this->dbforge->add_key('role_id');
		$this->dbforge->create_table('user_roles', TRUE, array('ENGINE' => 'InnoDB', 'DEFAULT CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci'));
		$this->db->query('ALTER TABLE `user_roles` ADD CONSTRAINT `fk_user_roles_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE, ADD CONSTRAINT `fk_user_roles_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE');
	}

	private function createRolePermissions()
	{
		$this->dbforge->add_field(array('role_id' => array('type' => 'INT', 'unsigned' => TRUE), 'permission_id' => array('type' => 'INT', 'unsigned' => TRUE)));
		$this->dbforge->add_key(array('role_id', 'permission_id'), TRUE);
		$this->dbforge->add_key('permission_id');
		$this->dbforge->create_table('role_permissions', TRUE, array('ENGINE' => 'InnoDB', 'DEFAULT CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci'));
		$this->db->query('ALTER TABLE `role_permissions` ADD CONSTRAINT `fk_role_permissions_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE, ADD CONSTRAINT `fk_role_permissions_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE');
	}

	private function createNews()
	{
		$this->createContentTable('news', array('excerpt' => array('type' => 'TEXT'), 'content_html' => array('type' => 'MEDIUMTEXT')));
	}

	private function createProjects()
	{
		$this->createContentTable('projects', array('excerpt' => array('type' => 'TEXT'), 'content_html' => array('type' => 'MEDIUMTEXT')));
	}

	private function createServices()
	{
		$this->createContentTable('services', array('excerpt' => array('type' => 'TEXT'), 'content_html' => array('type' => 'MEDIUMTEXT')));
	}

	private function createContentTable($table, $extraFields)
	{
		$fields = array(
			'id' => array('type' => 'INT', 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'title' => array('type' => 'VARCHAR', 'constraint' => 191),
			'slug' => array('type' => 'VARCHAR', 'constraint' => 191),
			'status' => array('type' => 'VARCHAR', 'constraint' => 20, 'default' => 'draft'),
			'cover_path' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE),
			'published_at' => array('type' => 'DATETIME', 'null' => TRUE),
			'created_by' => array('type' => 'INT', 'unsigned' => TRUE, 'null' => TRUE),
			'updated_by' => array('type' => 'INT', 'unsigned' => TRUE, 'null' => TRUE),
			'created_at' => array('type' => 'DATETIME'),
			'updated_at' => array('type' => 'DATETIME')
		);
		$this->dbforge->add_field(array_merge($fields, $extraFields));
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key('status');
		$this->dbforge->create_table($table, TRUE, array('ENGINE' => 'InnoDB', 'DEFAULT CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci'));
		$this->db->query('ALTER TABLE `' . $table . '` ADD UNIQUE KEY `uq_' . $table . '_slug` (`slug`)');
	}

	private function createSliders()
	{
		$this->dbforge->add_field(array(
			'id' => array('type' => 'INT', 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'title' => array('type' => 'VARCHAR', 'constraint' => 191),
			'subtitle' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE),
			'image_path' => array('type' => 'VARCHAR', 'constraint' => 255),
			'link_url' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE),
			'alt_text' => array('type' => 'VARCHAR', 'constraint' => 191),
			'sort_order' => array('type' => 'SMALLINT', 'constraint' => 5, 'default' => 0),
			'status' => array('type' => 'TINYINT', 'constraint' => 1, 'default' => 0),
			'created_at' => array('type' => 'DATETIME'),
			'updated_at' => array('type' => 'DATETIME')
		));
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key(array('status', 'sort_order'));
		$this->dbforge->create_table('sliders', TRUE, array('ENGINE' => 'InnoDB', 'DEFAULT CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci'));
	}

	private function seedRolesAndPermissions()
	{
		$now = date('Y-m-d H:i:s');
		$roles = array(
			array('name' => 'superadmin', 'label' => 'Quản trị toàn hệ thống', 'created_at' => $now),
			array('name' => 'content_manager', 'label' => 'Quản lý nội dung', 'created_at' => $now),
			array('name' => 'editor', 'label' => 'Biên tập viên', 'created_at' => $now),
			array('name' => 'support', 'label' => 'Hỗ trợ', 'created_at' => $now)
		);
		$this->db->insert_batch('roles', $roles);

		$names = array('news.view', 'news.create', 'news.update_own', 'news.update_any', 'news.publish', 'news.delete', 'projects.manage', 'services.manage', 'sliders.manage', 'media.upload', 'media.delete', 'comments.moderate', 'contacts.view', 'contacts.manage', 'users.view', 'users.create', 'users.assign_role', 'settings.manage');
		$permissions = array();
		foreach ($names as $name)
		{
			$permissions[] = array('name' => $name, 'label' => $name, 'created_at' => $now);
		}
		$this->db->insert_batch('permissions', $permissions);
		$this->db->query("INSERT INTO role_permissions (role_id, permission_id) SELECT roles.id, permissions.id FROM roles CROSS JOIN permissions WHERE roles.name = 'superadmin'");
	}
}