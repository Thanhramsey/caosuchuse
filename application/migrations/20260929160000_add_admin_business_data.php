<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_admin_business_data extends CI_Migration
{
	private $options = array('ENGINE' => 'InnoDB', 'DEFAULT CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci');

	public function up()
	{
		$this->createContacts();
		$this->createNewsletter();
		$this->createComments();
		$this->createPartners();
		$this->createTestimonials();
		$permissions = array('contacts.manage', 'newsletter.manage', 'comments.moderate', 'partners.manage', 'testimonials.manage');
		$now = date('Y-m-d H:i:s');
		foreach ($permissions as $permission)
		{
			$this->db->query('INSERT IGNORE INTO permissions (name, label, created_at) VALUES (?, ?, ?)', array($permission, $permission, $now));
		}
		$grant = 'INSERT IGNORE INTO role_permissions (role_id, permission_id) SELECT roles.id, permissions.id FROM roles CROSS JOIN permissions WHERE roles.name IN (?, ?) AND permissions.name = ?';
		foreach ($permissions as $permission)
		{
			$this->db->query($grant, array('superadmin', 'content_manager', $permission));
		}
	}

	public function down()
	{
		foreach (array('comments', 'newsletter_subscribers', 'contacts', 'partners', 'testimonials') as $table)
		{
			$this->dbforge->drop_table($table, TRUE);
		}
		$this->db->query('DELETE FROM permissions WHERE name IN (?)', array(array('contacts.manage', 'newsletter.manage', 'comments.moderate', 'partners.manage', 'testimonials.manage')));
	}

	private function createContacts()
	{
		$this->dbforge->add_field(array('id' => array('type' => 'INT', 'unsigned' => TRUE, 'auto_increment' => TRUE), 'name' => array('type' => 'VARCHAR', 'constraint' => 150), 'email' => array('type' => 'VARCHAR', 'constraint' => 191), 'phone' => array('type' => 'VARCHAR', 'constraint' => 40, 'null' => TRUE), 'subject' => array('type' => 'VARCHAR', 'constraint' => 191), 'message' => array('type' => 'TEXT'), 'status' => array('type' => 'VARCHAR', 'constraint' => 20, 'default' => 'new'), 'notes' => array('type' => 'TEXT', 'null' => TRUE), 'created_at' => array('type' => 'DATETIME'), 'updated_at' => array('type' => 'DATETIME', 'null' => TRUE)));
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key('status');
		$this->dbforge->create_table('contacts', TRUE, $this->options);
	}

	private function createNewsletter()
	{
		$this->dbforge->add_field(array('id' => array('type' => 'INT', 'unsigned' => TRUE, 'auto_increment' => TRUE), 'email' => array('type' => 'VARCHAR', 'constraint' => 191), 'status' => array('type' => 'VARCHAR', 'constraint' => 20, 'default' => 'subscribed'), 'created_at' => array('type' => 'DATETIME'), 'unsubscribed_at' => array('type' => 'DATETIME', 'null' => TRUE)));
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key('email', FALSE, TRUE);
		$this->dbforge->create_table('newsletter_subscribers', TRUE, $this->options);
	}

	private function createComments()
	{
		$this->dbforge->add_field(array('id' => array('type' => 'INT', 'unsigned' => TRUE, 'auto_increment' => TRUE), 'resource' => array('type' => 'VARCHAR', 'constraint' => 30), 'item_id' => array('type' => 'INT', 'unsigned' => TRUE), 'author_name' => array('type' => 'VARCHAR', 'constraint' => 150), 'email' => array('type' => 'VARCHAR', 'constraint' => 191), 'body' => array('type' => 'TEXT'), 'status' => array('type' => 'VARCHAR', 'constraint' => 20, 'default' => 'pending'), 'created_at' => array('type' => 'DATETIME'), 'moderated_at' => array('type' => 'DATETIME', 'null' => TRUE)));
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key(array('resource', 'item_id'));
		$this->dbforge->create_table('comments', TRUE, $this->options);
	}

	private function createPartners()
	{
		$this->dbforge->add_field(array('id' => array('type' => 'INT', 'unsigned' => TRUE, 'auto_increment' => TRUE), 'name' => array('type' => 'VARCHAR', 'constraint' => 191), 'logo_path' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE), 'website' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE), 'sort_order' => array('type' => 'SMALLINT', 'default' => 0), 'status' => array('type' => 'TINYINT', 'constraint' => 1, 'default' => 1), 'created_at' => array('type' => 'DATETIME'), 'updated_at' => array('type' => 'DATETIME')));
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('partners', TRUE, $this->options);
	}

	private function createTestimonials()
	{
		$this->dbforge->add_field(array('id' => array('type' => 'INT', 'unsigned' => TRUE, 'auto_increment' => TRUE), 'author_name' => array('type' => 'VARCHAR', 'constraint' => 150), 'role' => array('type' => 'VARCHAR', 'constraint' => 150, 'null' => TRUE), 'company' => array('type' => 'VARCHAR', 'constraint' => 191, 'null' => TRUE), 'quote' => array('type' => 'TEXT'), 'image_path' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE), 'sort_order' => array('type' => 'SMALLINT', 'default' => 0), 'status' => array('type' => 'TINYINT', 'constraint' => 1, 'default' => 1), 'created_at' => array('type' => 'DATETIME'), 'updated_at' => array('type' => 'DATETIME')));
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('testimonials', TRUE, $this->options);
	}
}