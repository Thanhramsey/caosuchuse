<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_categories extends CI_Migration
{
	private $contentTables = array('news', 'projects', 'services');

	public function up()
	{
		$this->dbforge->add_field(array(
			'id' => array('type' => 'INT', 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'type' => array('type' => 'VARCHAR', 'constraint' => 20),
			'name' => array('type' => 'VARCHAR', 'constraint' => 150),
			'slug' => array('type' => 'VARCHAR', 'constraint' => 150),
			'description' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE),
			'sort_order' => array('type' => 'SMALLINT', 'constraint' => 5, 'default' => 0),
			'status' => array('type' => 'TINYINT', 'constraint' => 1, 'default' => 1),
			'created_at' => array('type' => 'DATETIME'),
			'updated_at' => array('type' => 'DATETIME')
		));
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('categories', TRUE, array('ENGINE' => 'InnoDB', 'DEFAULT CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci'));
		$this->db->query('ALTER TABLE `categories` ADD UNIQUE KEY `uq_categories_type_slug` (`type`, `slug`), ADD KEY `idx_categories_type_sort` (`type`, `sort_order`)');

		foreach ($this->contentTables as $table)
		{
			$this->db->query('ALTER TABLE `' . $table . '` ADD COLUMN `category_id` INT UNSIGNED NULL AFTER `slug`, ADD KEY `idx_' . $table . '_category` (`category_id`)');
			$this->db->query('ALTER TABLE `' . $table . '` ADD CONSTRAINT `fk_' . $table . '_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL');
		}

		$now = date('Y-m-d H:i:s');
		$this->db->query("INSERT IGNORE INTO permissions (name, label, created_at) VALUES ('categories.manage', 'categories.manage', ?), ('users.view', 'users.view', ?)", array($now, $now));

		$defaults = array(
			'superadmin' => NULL,
			'content_manager' => array('news.view', 'news.create', 'news.update_own', 'news.update_any', 'news.publish', 'news.delete', 'projects.manage', 'services.manage', 'sliders.manage', 'categories.manage', 'media.upload', 'media.delete', 'comments.moderate'),
			'editor' => array('news.view', 'news.create', 'news.update_own', 'media.upload'),
			'support' => array('contacts.view', 'contacts.manage', 'comments.moderate')
		);
		foreach ($defaults as $role => $permissions)
		{
			$sql = 'INSERT IGNORE INTO role_permissions (role_id, permission_id) SELECT roles.id, permissions.id FROM roles CROSS JOIN permissions WHERE roles.name = ?';
			$bindings = array($role);
			if ($permissions !== NULL)
			{
				$sql .= ' AND permissions.name IN ?';
				$bindings[] = $permissions;
			}
			$this->db->query($sql, $bindings);
		}
	}

	public function down()
	{
		foreach ($this->contentTables as $table)
		{
			$this->db->query('ALTER TABLE `' . $table . '` DROP FOREIGN KEY `fk_' . $table . '_category`');
			$this->db->query('ALTER TABLE `' . $table . '` DROP KEY `idx_' . $table . '_category`, DROP COLUMN `category_id`');
		}
		$this->dbforge->drop_table('categories', TRUE);
		$this->db->query("DELETE FROM permissions WHERE name = 'categories.manage'");
	}
}
