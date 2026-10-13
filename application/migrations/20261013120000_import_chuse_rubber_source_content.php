<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Additive import of public content collected from chuserubber.vn with Firecrawl.
 * The source only exposed article summaries on its archive page, so each news
 * record keeps that summary and links readers to the original article.
 */
class Migration_Import_chuse_rubber_source_content extends CI_Migration
{
	private $source = 'https://chuserubber.vn';
	private $now;

	public function up()
	{
		$this->now = date('Y-m-d H:i:s');
		$this->importNews();
		$this->importIntroduction();
		$this->importAwardsGallery();
	}

	public function down()
	{
		// Imported entries may be edited after migration; preserve them on rollback.
	}

	private function importNews()
	{
		$items = array(
			array('Kiện toàn tổ chức Đảng', 'kien-toan-to-chuc-dang', 'Nhằm kiện toàn tổ chức cơ sở Đảng sau sáp nhập, chiều 6/8 Đảng ủy Công ty tổ chức hội nghị công bố và trao quyết định về công tác tổ chức, cán bộ.', 'https://chuserubber.vn/tndjizxrtc-content/uploads/2026/08/DTP_8095.jpg', 'https://chuserubber.vn/kien-toan-to-chuc-dang/'),
			array('Bài tham gia chuyên mục “Gương sáng cao su Việt Nam” Phạm Thị Thành', 'bai-tham-gia-guong-sang-cao-su-viet-nam-pham-thi-thanh', 'Bông hoa nở muộn giữa rừng cao su: câu chuyện về chị Phạm Thị Thành được giới thiệu trong chuyên mục Gương sáng cao su Việt Nam.', 'https://chuserubber.vn/tndjizxrtc-content/uploads/2026/07/DTP_4634-scaled.jpg', 'https://chuserubber.vn/bai-tham-gia-chuyen-muc-guong-sang-cao-su-viet-nam-pham-thi-thanh/'),
			array('Tổ chức công bố các quyết định về nhân sự sau sáp nhập', 'cong-bo-quyet-dinh-nhan-su-sau-sap-nhap', 'Sáng 1/7, Công ty TNHH MTV Cao su Chư Sê tổ chức công bố các quyết định về nhân sự sau sáp nhập.', 'https://chuserubber.vn/tndjizxrtc-content/uploads/2026/07/DTP_7758-scaled.jpg', 'https://chuserubber.vn/to-chuc-cong-bo-cac-quyet-dinh-ve-nhan-su-sau-sap-nhap/'),
			array('Trao tặng 63 suất quà cho công nhân khó khăn', 'trao-tang-63-suat-qua-cho-cong-nhan-kho-khan', 'Công ty trao 63 suất quà nhằm kịp thời động viên, chia sẻ khó khăn với người lao động.', 'https://chuserubber.vn/tndjizxrtc-content/uploads/2026/05/DTP_7248-scaled.jpg', 'https://chuserubber.vn/trao-tang-63-suat-qua-cho-cong-nhan-kho-khan/'),
			array('Trao Huy hiệu 30 năm tuổi Đảng cho Bí thư Chi bộ Đội sản xuất Ia Glai', 'trao-huy-hieu-30-nam-tuoi-dang-bi-thu-chi-bo-ia-glai', 'Chiều 19/5, Đảng ủy Công ty TNHH MTV Cao su Chư Sê trao Huy hiệu 30 năm tuổi Đảng cho đồng chí Bí thư Chi bộ Đội sản xuất Ia Glai.', 'https://chuserubber.vn/tndjizxrtc-content/uploads/2026/05/DTP_7167-scaled.jpg', 'https://chuserubber.vn/trao-huy-hieu-30-nam-tuoi-dang-cho-dong-chi-bi-thu-chi-bo-doi-san-xuat-ia-glai/'),
			array('Đơn vị đầu tiên thi công, xây dựng tại KCN Nam Pleiku', 'don-vi-dau-tien-thi-cong-xay-dung-tai-kcn-nam-pleiku', 'Khu công nghiệp Nam Pleiku đã cơ bản hoàn tất công tác chuẩn bị; một đơn vị trở thành đơn vị đầu tiên triển khai thi công, xây dựng tại khu công nghiệp.', 'https://chuserubber.vn/tndjizxrtc-content/uploads/2026/05/DTP_7069-scaled.jpg', 'https://chuserubber.vn/don-vi-dau-tien-thi-cong-xay-dung-tai-kcn-nam-pleiku/')
		);
		$category = $this->db->select('id')->where(array('type' => 'news', 'slug' => 'tin-tuc-su-kien'))->get('categories')->row_array();
		foreach ($items as $item)
		{
			if ($this->db->where('slug', $item[1])->count_all_results('news') > 0)
			{
				continue;
			}
			$this->db->insert('news', array(
				'title' => $item[0], 'slug' => $item[1], 'category_id' => $category ? (int) $category['id'] : NULL,
				'status' => 'published', 'cover_path' => $item[3], 'excerpt' => $item[2],
				'content_html' => $this->articleBody($item[2], $item[3], $item[0], $item[4]),
				'published_at' => NULL, 'created_by' => NULL, 'updated_by' => NULL,
				'created_at' => $this->now, 'updated_at' => $this->now
			));
		}
	}

	private function importIntroduction()
	{
		$slug = 'gioi-thieu-cong-ty-cao-su-chu-se';
		if ($this->db->where('slug', $slug)->count_all_results('pages') > 0)
		{
			return;
		}
		$paragraphs = array(
			'Công ty TNHH MTV Cao su Chư Sê (CRC) là doanh nghiệp trực thuộc Tập đoàn Công nghiệp Cao su Việt Nam (VRG), thành lập theo Quyết định số 71/QĐ-TCCB ngày 17 tháng 8 năm 1984 của Tổng cục Cao su Việt Nam.',
			'Lĩnh vực hoạt động chính gồm trồng, chăm sóc, khai thác, mua bán và chế biến cao su thiên nhiên; chế biến gỗ; đầu tư, xây dựng và kinh doanh hạ tầng khu công nghiệp; phát triển nông nghiệp ứng dụng công nghệ cao; năng lượng xanh và năng lượng tái tạo.',
			'Theo thông tin công bố trên trang giới thiệu, sau khi sáp nhập Công ty TNHH MTV Cao su Mang Yang, Công ty quản lý hơn 17.139 ha đất, có 2 nhà máy chế biến với công suất 23.000 tấn/năm, 1 khu công nghiệp, 1 bệnh viện và 13 đội sản xuất; giải quyết việc làm cho 2.052 lao động.',
			'Hơn 42 năm qua, CRC phát triển hoạt động sản xuất cao su thiên nhiên gắn với phát triển kinh tế địa phương và đời sống người lao động.'
		);
		$html = '<figure><img src="https://chuserubber.vn/tndjizxrtc-content/uploads/2025/10/img-about-min.jpg" alt="Công nhân cao su" loading="lazy"></figure>';
		foreach ($paragraphs as $paragraph)
		{
			$html .= '<p>' . htmlspecialchars($paragraph, ENT_QUOTES, 'UTF-8') . '</p>';
		}
		$html .= '<p><strong>Nguồn:</strong> <a href="' . $this->source . '/gioi-thieu/" target="_blank" rel="noopener noreferrer">Trang giới thiệu Cao su Chư Sê</a>.</p>';
		$this->db->insert('pages', array(
			'title' => 'Giới thiệu Công ty Cao su Chư Sê', 'slug' => $slug, 'category_id' => NULL,
			'status' => 'published', 'cover_path' => 'https://chuserubber.vn/tndjizxrtc-content/uploads/2025/10/img-about-min.jpg',
			'excerpt' => $paragraphs[0], 'content_html' => $html, 'published_at' => NULL,
			'created_by' => NULL, 'updated_by' => NULL, 'created_at' => $this->now, 'updated_at' => $this->now
		));
	}

	private function importAwardsGallery()
	{
		$slug = 'anh-thanh-tich-khen-thuong';
		if ($this->db->where('slug', $slug)->count_all_results('albums') > 0)
		{
			return;
		}
		$photos = array(
			'hinh_01.jpg' => 'Bằng khen và chứng nhận 1', 'hinh_02.jpg' => 'Bằng khen và chứng nhận 2',
			'hinh_03.jpg' => 'Bằng khen và chứng nhận 3', 'hinh_04.jpg' => 'Bằng khen và chứng nhận 4',
			'hinh_05.jpg' => 'Bằng khen và chứng nhận 5', 'cup_01.jpg' => 'Cúp 1', 'cup_02.jpg' => 'Cúp 2',
			'cup_03.jpg' => 'Cúp 3', 'cup_04.jpg' => 'Cúp 4', 'cup_05.jpg' => 'Cúp 5', 'cup_06.jpg' => 'Cúp 6',
			'01.jpg' => 'Huy chương 1', '02.jpg' => 'Huy chương 2', '03.jpg' => 'Huy chương 3',
			'04.jpg' => 'Huy chương 4', '05.jpg' => 'Huy chương 5'
		);
		$html = '<p>Ảnh bằng khen, cúp và huy chương được giới thiệu trong mục Thành tích của Cao su Chư Sê. Chọn ảnh để xem tại nguồn.</p><div class="gallery-grid">';
		foreach ($photos as $file => $alt)
		{
			$url = 'https://chuserubber.vn/tndjizxrtc-content/uploads/2025/07/' . $file;
			$html .= '<figure><a href="' . $url . '" target="_blank" rel="noopener noreferrer"><img src="' . $url . '" alt="' . htmlspecialchars($alt, ENT_QUOTES, 'UTF-8') . '" loading="lazy"></a><figcaption>' . htmlspecialchars($alt, ENT_QUOTES, 'UTF-8') . '</figcaption></figure>';
		}
		$html .= '</div><p><strong>Nguồn ảnh:</strong> <a href="' . $this->source . '/thanh-tich/" target="_blank" rel="noopener noreferrer">Thành tích Cao su Chư Sê</a>.</p>';
		$this->db->insert('albums', array(
			'title' => 'Ảnh thành tích khen thưởng', 'slug' => $slug, 'category_id' => NULL,
			'status' => 'published', 'cover_path' => 'https://chuserubber.vn/tndjizxrtc-content/uploads/2025/07/hinh_01.jpg',
			'excerpt' => 'Bộ sưu tập 16 ảnh bằng khen, cúp và huy chương được công bố trên website Cao su Chư Sê.',
			'content_html' => $html, 'published_at' => NULL, 'created_by' => NULL, 'updated_by' => NULL,
			'created_at' => $this->now, 'updated_at' => $this->now
		));
	}

	private function articleBody($excerpt, $image, $alt, $sourceUrl)
	{
		return '<figure><a href="' . $sourceUrl . '" target="_blank" rel="noopener noreferrer"><img src="' . $image . '" alt="' . htmlspecialchars($alt, ENT_QUOTES, 'UTF-8') . '" loading="lazy"></a></figure>'
			. '<p>' . htmlspecialchars($excerpt, ENT_QUOTES, 'UTF-8') . '</p>'
			. '<p><strong>Nội dung đầy đủ:</strong> <a href="' . $sourceUrl . '" target="_blank" rel="noopener noreferrer">Đọc bài viết trên website Cao su Chư Sê</a>.</p>';
	}
}
