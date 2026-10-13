<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Place_home_gallery_widget extends CI_Migration
{
	public function up()
	{
		$lastSection = $this->db
			->select_max('sort_order', 'max_order')
			->where('position', 'left')
			->get('home_sections')->row_array();
		$this->db->where('section_key', 'photo_bank')->update('home_sections', array(
			'sort_order' => (int) $lastSection['max_order'] + 1,
			'updated_at' => date('Y-m-d H:i:s')
		));
	}

	public function down()
	{
		$this->db->where('section_key', 'photo_bank')->update('home_sections', array('sort_order' => 2));
	}
}