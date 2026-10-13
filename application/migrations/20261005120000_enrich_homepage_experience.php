<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Enrich_homepage_experience extends CI_Migration
{
	public function up()
	{
		$now = date('Y-m-d H:i:s');
		$this->db->where('section_key', 'intro')->update('home_sections', array(
			'subtitle' => 'Hơn 40 năm vun bồi giá trị từ vùng đất Tây Nguyên',
			'content_html' => '<div class="home-intro"><p class="home-intro-lead">Thành lập ngày 17/8/1984, Công ty TNHH MTV Cao su Chư Sê phát triển chuỗi hoạt động từ trồng, chăm sóc, khai thác đến chế biến và kinh doanh cao su thiên nhiên; đồng thời mở rộng sang hạ tầng khu công nghiệp, nông nghiệp công nghệ cao và năng lượng xanh.</p><div class="home-highlights"><div class="home-highlight"><span>01</span><strong>Truyền thống</strong><p>Đơn vị Anh hùng Lao động thời kỳ đổi mới.</p></div><div class="home-highlight"><span>02</span><strong>Chất lượng</strong><p>Quản lý theo các tiêu chuẩn quốc tế.</p></div><div class="home-highlight"><span>03</span><strong>Bền vững</strong><p>Gắn sản xuất với môi trường và cộng đồng.</p></div></div></div>',
			'link_label' => 'Khám phá về chúng tôi',
			'link_url' => '/trang/lich-su-hinh-thanh-va-phat-trien',
			'updated_at' => $now
		));

		$this->db->where('section_key', 'kcn_nam_pleiku')->update('home_sections', array(
			'title' => 'Dự án tiêu biểu', 'title_en' => 'Featured projects', 'section_type' => 'content',
			'source_resource' => 'projects', 'source_category_id' => NULL, 'item_limit' => 3,
			'image_path' => NULL, 'content_html' => '', 'link_label' => 'Xem dự án', 'link_url' => '/du-an', 'updated_at' => $now
		));

		foreach (array('photo_bank', 'videos', 'youth') as $key)
		{
			$this->db->where('section_key', $key)->update('home_sections', array('status' => 0, 'updated_at' => $now));
		}

		$this->db->where('section_key', 'ideas')->update('home_sections', array(
			'title' => 'Lĩnh vực hoạt động', 'title_en' => 'Business fields', 'source_resource' => 'services',
			'source_category_id' => NULL, 'item_limit' => 4, 'link_label' => 'Xem toàn bộ', 'link_url' => '/linh-vuc', 'updated_at' => $now
		));

		$this->db->where('section_key', 'products')->update('home_sections', array('item_limit' => 6, 'updated_at' => $now));
		$this->db->where('section_key', 'news')->update('home_sections', array('item_limit' => 6, 'sort_order' => 3, 'updated_at' => $now));
		$this->db->where('section_key', 'achievements')->update('home_sections', array('item_limit' => 6, 'link_label' => 'Xem thành tích', 'link_url' => '/thanh-tich', 'updated_at' => $now));

		$this->db->where('section_key', 'orientation')->update('home_sections', array(
			'section_type' => 'html', 'image_path' => NULL, 'sort_order' => 2,
			'subtitle' => 'Hiệu quả kinh tế - Trách nhiệm xã hội - Bảo vệ môi trường',
			'content_html' => '<div class="home-orientation"><div><strong>Phát triển vùng nguyên liệu</strong><p>Ứng dụng kỹ thuật, bảo vệ vườn cây và nâng cao năng suất trên nền tảng quản trị minh bạch.</p></div><div><strong>Nâng cao giá trị sản phẩm</strong><p>Kiểm soát chất lượng, hiện đại hóa chế biến và đáp ứng nhu cầu của khách hàng.</p></div><div><strong>Đồng hành cùng cộng đồng</strong><p>Chăm lo người lao động, đóng góp an sinh và sử dụng hiệu quả tài nguyên.</p></div></div>',
			'link_label' => 'Tìm hiểu định hướng', 'link_url' => '/trang/chat-luong-va-moi-truong', 'updated_at' => $now
		));

		$this->db->where('section_key', 'certifications')->update('home_sections', array(
			'content_html' => '<div class="footer-cert-list"><span>ISO 9001:2015</span><span>ISO 14001:2015</span><span>IEC 17025</span></div>',
			'updated_at' => $now
		));

		$this->insertSection(array(
			'section_key' => 'company_numbers', 'title' => 'Cao su Chư Sê qua những con số', 'title_en' => 'Chu Se Rubber in numbers',
			'subtitle' => 'Năng lực sau sáp nhập theo thông tin giới thiệu doanh nghiệp', 'position' => 'full', 'section_type' => 'html',
			'content_html' => '<div class="home-stats"><div class="home-stat"><strong data-count="14043" data-suffix=" ha">14.043 ha</strong><span>Diện tích cao su</span></div><div class="home-stat"><strong data-count="23000" data-suffix=" tấn/năm">23.000 tấn/năm</strong><span>Công suất chế biến</span></div><div class="home-stat"><strong data-count="2052" data-suffix=" người">2.052 người</strong><span>Người lao động</span></div><div class="home-stat"><strong data-count="13" data-suffix=" đội">13 đội</strong><span>Đơn vị sản xuất cao su</span></div></div>',
			'image_path' => NULL, 'link_label' => 'Xem năng lực hoạt động', 'link_url' => '/trang/nang-luc-hoat-dong',
			'source_resource' => NULL, 'source_category_id' => NULL, 'item_limit' => 1, 'sort_order' => 1, 'status' => 1,
			'created_at' => $now, 'updated_at' => $now
		));

		$this->insertSection(array(
			'section_key' => 'business_fields', 'title' => 'Lĩnh vực hoạt động', 'title_en' => 'Business fields',
			'subtitle' => 'Năng lực cốt lõi và những hướng phát triển mới', 'position' => 'main', 'section_type' => 'content',
			'content_html' => '', 'image_path' => NULL, 'link_label' => 'Xem toàn bộ lĩnh vực', 'link_url' => '/linh-vuc',
			'source_resource' => 'services', 'source_category_id' => NULL, 'item_limit' => 4, 'sort_order' => 4, 'status' => 1,
			'created_at' => $now, 'updated_at' => $now
		));

		$this->insertSection(array(
			'section_key' => 'featured_projects', 'title' => 'Dự án và chuyển đổi', 'title_en' => 'Projects and transformation',
			'subtitle' => 'Hạ tầng, nông nghiệp công nghệ cao và quản trị số', 'position' => 'main', 'section_type' => 'content',
			'content_html' => '', 'image_path' => NULL, 'link_label' => 'Xem toàn bộ dự án', 'link_url' => '/du-an',
			'source_resource' => 'projects', 'source_category_id' => NULL, 'item_limit' => 3, 'sort_order' => 5, 'status' => 1,
			'created_at' => $now, 'updated_at' => $now
		));
	}

	public function down()
	{
		$this->db->where_in('section_key', array('company_numbers', 'business_fields', 'featured_projects'))->delete('home_sections');
	}

	private function insertSection($data)
	{
		if ($this->db->where('section_key', $data['section_key'])->count_all_results('home_sections') === 0)
		{
			$this->db->insert('home_sections', $data);
		}
	}
}
