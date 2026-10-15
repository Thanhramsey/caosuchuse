<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_rubber_purchase_prices extends CI_Migration
{
	private $options = array('ENGINE' => 'InnoDB', 'DEFAULT CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci');

	public function up()
	{
		$this->dbforge->add_field(array(
			'id' => array('type' => 'INT', 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'effective_date' => array('type' => 'DATE'),
			'grade_1_price' => array('type' => 'DECIMAL', 'constraint' => '14,2', 'unsigned' => TRUE),
			'grade_2_price' => array('type' => 'DECIMAL', 'constraint' => '14,2', 'unsigned' => TRUE),
			'created_by' => array('type' => 'INT', 'unsigned' => TRUE, 'null' => TRUE),
			'created_at' => array('type' => 'DATETIME'),
			'updated_at' => array('type' => 'DATETIME')
		));
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key(array('effective_date', 'id'));
		$this->dbforge->create_table('rubber_prices', TRUE, $this->options);

		$now = date('Y-m-d H:i:s');
		$this->db->query('INSERT IGNORE INTO permissions (name, label, created_at) VALUES (?, ?, ?)', array('rubber_prices.manage', 'Quản lý giá thu mua mủ', $now));
		$this->db->query('INSERT IGNORE INTO role_permissions (role_id, permission_id) SELECT roles.id, permissions.id FROM roles CROSS JOIN permissions WHERE roles.name IN (?, ?) AND permissions.name = ?', array('superadmin', 'content_manager', 'rubber_prices.manage'));
	}

	public function down()
	{
		$this->dbforge->drop_table('rubber_prices', TRUE);
		$this->db->delete('permissions', array('name' => 'rubber_prices.manage'));
	}
}
