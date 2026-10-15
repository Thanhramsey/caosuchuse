<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_rubber_prices_home_widget extends CI_Migration
{
	public function up()
	{
		if ($this->db->where('section_key', 'rubber_prices')->count_all_results('home_sections') > 0)
		{
			return;
		}

		$now = date('Y-m-d H:i:s');
		$this->db->insert('home_sections', array(
			'section_key' => 'rubber_prices',
			'title' => 'Giá thu mua mủ cao su',
			'subtitle' => NULL,
			'position' => 'right',
			'section_type' => 'content',
			'content_html' => '',
			'image_path' => NULL,
			'link_label' => NULL,
			'link_url' => NULL,
			'source_resource' => NULL,
			'source_category_id' => NULL,
			'item_limit' => 1,
			'sort_order' => 0,
			'status' => 1,
			'created_at' => $now,
			'updated_at' => $now
		));
	}

	public function down()
	{
		$this->db->delete('home_sections', array('section_key' => 'rubber_prices'));
	}
}
