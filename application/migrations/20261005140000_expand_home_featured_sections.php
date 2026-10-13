<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Expand_home_featured_sections extends CI_Migration
{
	public function up()
	{
		$now = date('Y-m-d H:i:s');
		$this->db->where('section_key', 'business_fields')->update('home_sections', array(
			'position' => 'full', 'sort_order' => 1, 'updated_at' => $now
		));
		$this->db->where('section_key', 'featured_projects')->update('home_sections', array(
			'position' => 'full', 'sort_order' => 2, 'updated_at' => $now
		));
		$this->db->where('section_key', 'company_numbers')->update('home_sections', array('sort_order' => 3, 'updated_at' => $now));
		$this->db->where('section_key', 'orientation')->update('home_sections', array('sort_order' => 4, 'updated_at' => $now));
		$this->db->where('section_key', 'news')->update('home_sections', array('sort_order' => 5, 'updated_at' => $now));
	}

	public function down()
	{
		$now = date('Y-m-d H:i:s');
		$this->db->where('section_key', 'business_fields')->update('home_sections', array('position' => 'main', 'sort_order' => 4, 'updated_at' => $now));
		$this->db->where('section_key', 'featured_projects')->update('home_sections', array('position' => 'main', 'sort_order' => 5, 'updated_at' => $now));
	}
}
