<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_site_structure extends CI_Migration
{
	private $newTypes = array('internal', 'disclosures', 'products', 'pages', 'albums', 'videos', 'achievements', 'faqs');
	private $tableOptions = array('ENGINE' => 'InnoDB', 'DEFAULT CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci');

	public function up()
	{
		foreach ($this->newTypes as $table)
		{
			$this->createContentTable($table);
		}
		$this->createAttachments();
		$this->createMenus();
		$this->createHomeSections();
		$this->createSettings();
		$this->seedPermissions();

		$now = date('Y-m-d H:i:s');
		$categories = $this->seedCategories($now);
		$this->seedPages($now);
		$this->seedMenus($now);
		$this->seedHomeSections($categories, $now);
		$this->seedSettings($now);
	}

	public function down()
	{
		foreach (array('home_sections', 'settings', 'attachments') as $table)
		{
			$this->dbforge->drop_table($table, TRUE);
		}
		$this->db->query('UPDATE `menus` SET `parent_id` = NULL');
		$this->dbforge->drop_table('menus', TRUE);
		foreach ($this->newTypes as $table)
		{
			$this->dbforge->drop_table($table, TRUE);
		}
		$this->db->query('DELETE FROM categories WHERE type IN ?', array(array('internal', 'disclosures', 'products', 'albums', 'videos', 'faqs')));
		$this->db->query("DELETE FROM categories WHERE type = 'news' AND slug IN ('tin-tuc-su-kien', 'phat-trien-ben-vung')");
		$this->db->query('DELETE FROM permissions WHERE name IN ?', array($this->newPermissions()));
	}

	private function newPermissions()
	{
		$names = array('menus.manage', 'home.manage');
		foreach ($this->newTypes as $type)
		{
			$names[] = $type . '.manage';
		}
		return $names;
	}

	private function createContentTable($table)
	{
		$this->dbforge->add_field(array(
			'id' => array('type' => 'INT', 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'title' => array('type' => 'VARCHAR', 'constraint' => 191),
			'slug' => array('type' => 'VARCHAR', 'constraint' => 191),
			'category_id' => array('type' => 'INT', 'unsigned' => TRUE, 'null' => TRUE),
			'status' => array('type' => 'VARCHAR', 'constraint' => 20, 'default' => 'draft'),
			'cover_path' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE),
			'excerpt' => array('type' => 'TEXT', 'null' => TRUE),
			'content_html' => array('type' => 'MEDIUMTEXT', 'null' => TRUE),
			'extra_url' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE),
			'published_at' => array('type' => 'DATETIME', 'null' => TRUE),
			'created_by' => array('type' => 'INT', 'unsigned' => TRUE, 'null' => TRUE),
			'updated_by' => array('type' => 'INT', 'unsigned' => TRUE, 'null' => TRUE),
			'created_at' => array('type' => 'DATETIME'),
			'updated_at' => array('type' => 'DATETIME')
		));
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key('status');
		$this->dbforge->create_table($table, TRUE, $this->tableOptions);
		$this->db->query('ALTER TABLE `' . $table . '` ADD UNIQUE KEY `uq_' . $table . '_slug` (`slug`), ADD KEY `idx_' . $table . '_category` (`category_id`)');
		$this->db->query('ALTER TABLE `' . $table . '` ADD CONSTRAINT `fk_' . $table . '_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL');
	}

	private function createAttachments()
	{
		$this->dbforge->add_field(array(
			'id' => array('type' => 'INT', 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'resource' => array('type' => 'VARCHAR', 'constraint' => 20),
			'item_id' => array('type' => 'INT', 'unsigned' => TRUE),
			'title' => array('type' => 'VARCHAR', 'constraint' => 191),
			'file_path' => array('type' => 'VARCHAR', 'constraint' => 255),
			'file_ext' => array('type' => 'VARCHAR', 'constraint' => 10),
			'file_size' => array('type' => 'INT', 'unsigned' => TRUE, 'default' => 0),
			'sort_order' => array('type' => 'SMALLINT', 'constraint' => 5, 'default' => 0),
			'created_at' => array('type' => 'DATETIME')
		));
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key(array('resource', 'item_id'));
		$this->dbforge->create_table('attachments', TRUE, $this->tableOptions);
	}

	private function createMenus()
	{
		$this->dbforge->add_field(array(
			'id' => array('type' => 'INT', 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'location' => array('type' => 'VARCHAR', 'constraint' => 20, 'default' => 'main'),
			'parent_id' => array('type' => 'INT', 'unsigned' => TRUE, 'null' => TRUE),
			'title' => array('type' => 'VARCHAR', 'constraint' => 150),
			'url' => array('type' => 'VARCHAR', 'constraint' => 255),
			'target' => array('type' => 'VARCHAR', 'constraint' => 10, 'default' => '_self'),
			'sort_order' => array('type' => 'SMALLINT', 'constraint' => 5, 'default' => 0),
			'status' => array('type' => 'TINYINT', 'constraint' => 1, 'default' => 1),
			'created_at' => array('type' => 'DATETIME'),
			'updated_at' => array('type' => 'DATETIME')
		));
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key(array('location', 'parent_id', 'sort_order'));
		$this->dbforge->create_table('menus', TRUE, $this->tableOptions);
		$this->db->query('ALTER TABLE `menus` ADD KEY `idx_menus_parent` (`parent_id`), ADD CONSTRAINT `fk_menus_parent` FOREIGN KEY (`parent_id`) REFERENCES `menus` (`id`)');
	}

	private function createHomeSections()
	{
		$this->dbforge->add_field(array(
			'id' => array('type' => 'INT', 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'section_key' => array('type' => 'VARCHAR', 'constraint' => 50),
			'title' => array('type' => 'VARCHAR', 'constraint' => 150),
			'subtitle' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE),
			'position' => array('type' => 'VARCHAR', 'constraint' => 10, 'default' => 'main'),
			'section_type' => array('type' => 'VARCHAR', 'constraint' => 20, 'default' => 'html'),
			'content_html' => array('type' => 'MEDIUMTEXT', 'null' => TRUE),
			'image_path' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE),
			'link_label' => array('type' => 'VARCHAR', 'constraint' => 100, 'null' => TRUE),
			'link_url' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE),
			'source_resource' => array('type' => 'VARCHAR', 'constraint' => 20, 'null' => TRUE),
			'source_category_id' => array('type' => 'INT', 'unsigned' => TRUE, 'null' => TRUE),
			'item_limit' => array('type' => 'TINYINT', 'constraint' => 3, 'unsigned' => TRUE, 'default' => 4),
			'sort_order' => array('type' => 'SMALLINT', 'constraint' => 5, 'default' => 0),
			'status' => array('type' => 'TINYINT', 'constraint' => 1, 'default' => 1),
			'created_at' => array('type' => 'DATETIME'),
			'updated_at' => array('type' => 'DATETIME')
		));
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key(array('position', 'sort_order'));
		$this->dbforge->create_table('home_sections', TRUE, $this->tableOptions);
		$this->db->query('ALTER TABLE `home_sections` ADD UNIQUE KEY `uq_home_sections_key` (`section_key`), ADD KEY `idx_home_sections_category` (`source_category_id`)');
		$this->db->query('ALTER TABLE `home_sections` ADD CONSTRAINT `fk_home_sections_category` FOREIGN KEY (`source_category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL');
	}

	private function createSettings()
	{
		$this->dbforge->add_field(array(
			'setting_key' => array('type' => 'VARCHAR', 'constraint' => 100),
			'setting_value' => array('type' => 'TEXT', 'null' => TRUE),
			'updated_at' => array('type' => 'DATETIME')
		));
		$this->dbforge->add_key('setting_key', TRUE);
		$this->dbforge->create_table('settings', TRUE, $this->tableOptions);
	}

	private function seedPermissions()
	{
		$now = date('Y-m-d H:i:s');
		foreach ($this->newPermissions() as $name)
		{
			$this->db->query('INSERT IGNORE INTO permissions (name, label, created_at) VALUES (?, ?, ?)', array($name, $name, $now));
		}
		$grant = 'INSERT IGNORE INTO role_permissions (role_id, permission_id) SELECT roles.id, permissions.id FROM roles CROSS JOIN permissions WHERE roles.name = ? AND permissions.name IN ?';
		$this->db->query($grant, array('superadmin', $this->newPermissions()));
		$this->db->query($grant, array('content_manager', $this->newPermissions()));
	}

	private function seedCategories($now)
	{
		$rows = array(
			array('news', 'Tin tức - sự kiện', 'tin-tuc-su-kien', 1),
			array('news', 'Phát triển bền vững', 'phat-trien-ben-vung', 2),
			array('internal', 'Hoạt động công đoàn', 'hoat-dong-cong-doan', 1),
			array('internal', 'Hoạt động đoàn thanh niên', 'hoat-dong-doan-thanh-nien', 2),
			array('internal', 'Gương người tốt việc tốt', 'guong-nguoi-tot-viec-tot', 3),
			array('internal', 'Ý tưởng - sáng kiến', 'y-tuong-sang-kien', 4)
		);
		$ids = array();
		foreach ($rows as $row)
		{
			$this->db->query('INSERT IGNORE INTO categories (type, name, slug, sort_order, status, created_at, updated_at) VALUES (?, ?, ?, ?, 1, ?, ?)', array($row[0], $row[1], $row[2], $row[3], $now, $now));
			$found = $this->db->get_where('categories', array('type' => $row[0], 'slug' => $row[2]))->row_array();
			$ids[$row[2]] = (int) $found['id'];
		}
		return $ids;
	}

	private function seedPages($now)
	{
		foreach (array('gioi-thieu-chung' => 'Giới thiệu chung', 'so-do-to-chuc' => 'Sơ đồ tổ chức') as $slug => $title)
		{
			$this->db->insert('pages', array('title' => $title, 'slug' => $slug, 'status' => 'draft', 'excerpt' => '', 'content_html' => '', 'created_at' => $now, 'updated_at' => $now));
		}
	}

	private function seedMenus($now)
	{
		$main = array(
			array('Trang chủ', '/', array()),
			array('Giới thiệu', '/gioi-thieu', array(
				array('Giới thiệu chung', '/trang/gioi-thieu-chung'),
				array('Sơ đồ tổ chức', '/trang/so-do-to-chuc')
			)),
			array('Lĩnh vực hoạt động', '/linh-vuc', array()),
			array('Tin tức sự kiện', '/tin-tuc', array()),
			array('Sản phẩm', '/san-pham', array()),
			array('Công bố thông tin', '/cong-bo-thong-tin', array()),
			array('Thông tin nội bộ', '/thong-tin-noi-bo', array(
				array('Hoạt động công đoàn', '/thong-tin-noi-bo/hoat-dong-cong-doan'),
				array('Hoạt động đoàn thanh niên', '/thong-tin-noi-bo/hoat-dong-doan-thanh-nien'),
				array('Gương người tốt việc tốt', '/thong-tin-noi-bo/guong-nguoi-tot-viec-tot'),
				array('Ý tưởng - sáng kiến', '/thong-tin-noi-bo/y-tuong-sang-kien')
			)),
			array('Liên hệ', '/lien-he', array())
		);
		$this->insertMenuTree('main', $main, $now);
		$this->insertMenuTree('footer', array(array('Giới thiệu', '/gioi-thieu', array()), array('Liên hệ', '/lien-he', array())), $now);
	}

	private function insertMenuTree($location, $items, $now)
	{
		foreach ($items as $index => $item)
		{
			$this->db->insert('menus', array('location' => $location, 'title' => $item[0], 'url' => $item[1], 'sort_order' => $index + 1, 'status' => 1, 'created_at' => $now, 'updated_at' => $now));
			$parentId = $this->db->insert_id();
			foreach ($item[2] as $childIndex => $child)
			{
				$this->db->insert('menus', array('location' => $location, 'parent_id' => $parentId, 'title' => $child[0], 'url' => $child[1], 'sort_order' => $childIndex + 1, 'status' => 1, 'created_at' => $now, 'updated_at' => $now));
			}
		}
	}

	private function seedHomeSections($categories, $now)
	{
		$sections = array(
			array('kcn_nam_pleiku', 'Khu công nghiệp Nam Pleiku', 'left', 'image', NULL, NULL, 1, NULL, NULL),
			array('photo_bank', 'Ngân hàng ảnh', 'left', 'content', 'albums', NULL, 4, NULL, '/ngan-hang-anh'),
			array('videos', 'Video', 'left', 'content', 'videos', NULL, 4, NULL, '/video'),
			array('union', 'Hoạt động công đoàn', 'left', 'content', 'internal', 'hoat-dong-cong-doan', 4, NULL, NULL),
			array('youth', 'Hoạt động đoàn thanh niên', 'left', 'content', 'internal', 'hoat-dong-doan-thanh-nien', 4, NULL, NULL),
			array('good_deeds', 'Gương người tốt việc tốt', 'left', 'content', 'internal', 'guong-nguoi-tot-viec-tot', 4, NULL, NULL),
			array('slider', 'Slider trang chủ', 'main', 'slider', NULL, NULL, 5, NULL, NULL),
			array('intro', 'Giới thiệu', 'main', 'html', NULL, NULL, 1, 'Xem chi tiết', '/gioi-thieu'),
			array('products', 'Sản phẩm', 'main', 'content', 'products', NULL, 5, 'Xem toàn bộ', '/san-pham'),
			array('achievements', 'Thành tích', 'right', 'content', 'achievements', NULL, 4, NULL, NULL),
			array('faqs', 'Hỏi đáp của người lao động', 'right', 'content', 'faqs', NULL, 5, NULL, '/hoi-dap'),
			array('ideas', 'Ý tưởng - sáng kiến', 'right', 'content', 'internal', 'y-tuong-sang-kien', 2, NULL, NULL),
			array('sustainability', 'Phát triển bền vững', 'right', 'content', 'news', 'phat-trien-ben-vung', 2, NULL, NULL),
			array('orientation', 'Định hướng phát triển', 'full', 'image', NULL, NULL, 1, NULL, NULL),
			array('news', 'Tin tức', 'full', 'content', 'news', NULL, 3, 'Xem toàn bộ', '/tin-tuc'),
			array('certifications', 'Chứng nhận', 'footer', 'html', NULL, NULL, 1, NULL, NULL)
		);
		$order = array();
		foreach ($sections as $section)
		{
			$position = $section[2];
			$order[$position] = isset($order[$position]) ? $order[$position] + 1 : 1;
			$this->db->insert('home_sections', array(
				'section_key' => $section[0],
				'title' => $section[1],
				'position' => $position,
				'section_type' => $section[3],
				'source_resource' => $section[4],
				'source_category_id' => $section[5] !== NULL ? $categories[$section[5]] : NULL,
				'item_limit' => $section[6],
				'link_label' => $section[7],
				'link_url' => $section[8],
				'content_html' => '',
				'sort_order' => $order[$position],
				'status' => 1,
				'created_at' => $now,
				'updated_at' => $now
			));
		}
	}

	private function seedSettings($now)
	{
		$defaults = array(
			'company_name' => 'Công ty TNHH MTV Cao su Chư Sê',
			'group_name' => 'Tập đoàn Công nghiệp Cao su Việt Nam'
		);
		foreach (array('company_name', 'group_name', 'slogan', 'logo_path', 'business_registration', 'address', 'phone', 'hotline', 'fax', 'email', 'working_hours', 'map_embed_url', 'facebook_url', 'youtube_url', 'zalo_url', 'meta_title', 'meta_description') as $key)
		{
			$this->db->insert('settings', array('setting_key' => $key, 'setting_value' => isset($defaults[$key]) ? $defaults[$key] : '', 'updated_at' => $now));
		}
	}
}
