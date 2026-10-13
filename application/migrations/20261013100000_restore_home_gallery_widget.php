<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Restore_home_gallery_widget extends CI_Migration
{
	public function up()
	{
		$this->db->where('section_key', 'photo_bank')->update('home_sections', array(
			'title' => 'Thư viện',
			'title_en' => 'Gallery',
			'section_type' => 'content',
			'source_resource' => 'albums',
			'item_limit' => 4,
			'link_label' => 'Xem thư viện',
			'link_url' => '/ngan-hang-anh',
			'status' => 1,
			'updated_at' => date('Y-m-d H:i:s')
		));
	}

	public function down()
	{
		$this->db->where('section_key', 'photo_bank')->update('home_sections', array('status' => 0));
	}
}