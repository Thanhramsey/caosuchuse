<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Update_homepage_hero extends CI_Migration
{
	public function up()
	{
		$this->db->group_start()
			->where('title', 'banner')
			->or_where('image_path', 'assets/uploads/be7e0aa3cb05e8443f1082e662e67677.png')
			->group_end()
			->update('sliders', array(
				'title' => 'Giá trị bền vững từ đất bazan',
				'subtitle' => 'Kiến tạo tương lai xanh cùng người lao động và cộng đồng',
				'image_path' => 'assets/images/hero-rubber-plantation.webp',
				'alt_text' => 'Vườn cao su Chư Sê trong ánh nắng ban mai',
				'link_url' => '/trang/lich-su-hinh-thanh-va-phat-trien',
				'updated_at' => date('Y-m-d H:i:s')
			));
	}

	public function down()
	{
		// Giữ lại tài sản hero do nội dung có thể đã được biên tập tiếp trong quản trị.
	}
}
