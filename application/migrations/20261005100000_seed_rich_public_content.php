<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Seed_rich_public_content extends CI_Migration
{
	public function up()
	{
		$this->seedContent('news', $this->news());
		$this->seedContent('internal', $this->internal());
		$this->seedContent('products', $this->products());
		$this->seedContent('services', $this->services());
		$this->seedContent('projects', $this->projects());
		$this->seedContent('disclosures', $this->disclosures());
		$this->seedContent('pages', $this->pages());
		$this->seedContent('achievements', $this->achievements());
		$this->seedContent('faqs', $this->faqs());
		$this->seedSettings();
	}

	public function down()
	{
		// Nội dung biên tập có thể đã được chỉnh sửa sau khi nhập. Không tự động xóa khi rollback.
	}

	private function seedContent($table, $items)
	{
		$fields = $this->db->list_fields($table);
		foreach ($items as $item)
		{
			if ($this->db->where('slug', $item['slug'])->count_all_results($table) > 0)
			{
				continue;
			}

			$publishedAt = isset($item['published_at']) ? $item['published_at'] : date('Y-m-d H:i:s');
			$data = array(
				'title' => $item['title'],
				'slug' => $item['slug'],
				'category_id' => $this->categoryId($table, isset($item['category_slug']) ? $item['category_slug'] : NULL),
				'status' => 'published',
				'cover_path' => NULL,
				'excerpt' => $item['excerpt'],
				'content_html' => $this->body($item['paragraphs'], $item['source_url'], $item['source_label']),
				'published_at' => $publishedAt,
				'created_by' => 1,
				'updated_by' => 1,
				'created_at' => $publishedAt,
				'updated_at' => date('Y-m-d H:i:s')
			);
			if (in_array('extra_url', $fields, TRUE))
			{
				$data['extra_url'] = $item['source_url'];
			}
			$this->db->insert($table, $data);
		}
	}

	private function categoryId($type, $slug)
	{
		if (!$slug)
		{
			return NULL;
		}
		$row = $this->db->select('id')->where('type', $type)->where('slug', $slug)->get('categories')->row_array();
		return $row ? (int) $row['id'] : NULL;
	}

	private function body($paragraphs, $sourceUrl, $sourceLabel)
	{
		$html = '';
		foreach ($paragraphs as $paragraph)
		{
			$html .= '<p>' . $paragraph . '</p>';
		}
		return $html . '<p><strong>Nguồn tham khảo:</strong> <a href="' . htmlspecialchars($sourceUrl, ENT_QUOTES, 'UTF-8') . '" target="_blank" rel="noopener noreferrer">' . htmlspecialchars($sourceLabel, ENT_QUOTES, 'UTF-8') . '</a>.</p>';
	}

	private function seedSettings()
	{
		$values = array(
			'slogan' => 'Cao su - Dòng chảy cuộc sống',
			'meta_title' => 'Công ty TNHH MTV Cao su Chư Sê',
			'meta_description' => 'Thông tin hoạt động, sản phẩm cao su thiên nhiên, công bố doanh nghiệp và định hướng phát triển bền vững của Công ty TNHH MTV Cao su Chư Sê.'
		);
		foreach ($values as $key => $value)
		{
			$this->db->query(
				'UPDATE settings SET setting_value = ?, updated_at = ? WHERE setting_key = ? AND (setting_value IS NULL OR TRIM(setting_value) = ?)',
				array($value, date('Y-m-d H:i:s'), $key, '')
			);
		}
	}

	private function news()
	{
		return array(
			array(
				'title' => 'Cao su Chư Sê tập trung nguồn lực hoàn thành kế hoạch năm 2026',
				'slug' => 'cao-su-chu-se-tap-trung-nguon-luc-hoan-thanh-ke-hoach-2026',
				'category_slug' => 'tin-tuc-su-kien',
				'excerpt' => 'Các đơn vị tập trung sản xuất, thu mua, chế biến và tiêu thụ để hoàn thành cao nhất kế hoạch năm 2026.',
				'paragraphs' => array('Trong 8 tháng đầu năm 2026, Công ty khai thác 5.937 tấn cao su, đạt 52,87% kế hoạch; thu mua 1.742 tấn, đạt 124,43% kế hoạch.', 'Sản lượng chế biến đạt 7.453 tấn và tiêu thụ đạt 8.752 tấn. Kết quả là cơ sở để các đơn vị tiếp tục điều hành linh hoạt trong những tháng cuối năm.'),
				'source_url' => 'https://news.vrg.vn/hoat-dong-co-so/cao-su-chu-se-va-chu-prong-tap-trung-nguon-luc-quyet-liet-hoan-thanh-cao-nhat-ke-hoach-nam-2026/',
				'source_label' => 'Tin tức VRG',
				'published_at' => '2026-09-11 08:00:00'
			),
			array(
				'title' => 'Ra quân thu hoạch mủ, quyết tâm vượt kế hoạch năm 2026',
				'slug' => 'ra-quan-thu-hoach-mu-quyet-tam-vuot-ke-hoach-2026',
				'category_slug' => 'tin-tuc-su-kien',
				'excerpt' => 'Công ty phát động mùa thu hoạch mới với mục tiêu khai thác trên 5.100 tấn mủ quy khô.',
				'paragraphs' => array('Ngày 2/5/2026, Công ty tổ chức ra quân thu hoạch mủ và đặt mục tiêu khai thác trên 5.100 tấn mủ quy khô.', 'Kết quả năm 2025 vượt kế hoạch 8,1% và hoàn thành sớm 18 ngày tạo động lực để người lao động tiếp tục nâng cao năng suất, chất lượng vườn cây.'),
				'source_url' => 'https://www.chuserubber.vn/cao-su-chu-se-quyet-tam-khai-thac-vuot-5-100-tan-ke-hoach-tro-len/',
				'source_label' => 'Cao su Chư Sê',
				'published_at' => '2026-05-02 08:00:00'
			),
			array(
				'title' => 'Thu nhập người lao động đạt trên 10,6 triệu đồng mỗi tháng',
				'slug' => 'thu-nhap-nguoi-lao-dong-tren-10-6-trieu-dong-moi-thang',
				'category_slug' => 'tin-tuc-su-kien',
				'excerpt' => 'Thu nhập bình quân của người lao động năm 2025 đạt trên 10,6 triệu đồng/người/tháng.',
				'paragraphs' => array('Năm 2025, thu nhập bình quân của người lao động Công ty đạt trên 10,6 triệu đồng/người/tháng, bằng 134% kế hoạch.', 'Hoạt động đầu tư ngoài ngành mang về hơn 103,5 tỷ đồng cổ tức, góp phần củng cố nguồn lực tài chính và chăm lo đời sống cho 895 người lao động trước thời điểm sáp nhập.'),
				'source_url' => 'https://news.vrg.vn/su-kien-van-de/thu-nhap-cua-nguoi-lao-dong-cao-su-chu-se-dat-tren-106-trieu-dong-nguoi-thang/',
				'source_label' => 'Tin tức VRG',
				'published_at' => '2026-02-06 08:00:00'
			),
			array(
				'title' => '41 năm phát huy truyền thống Anh hùng Lao động',
				'slug' => '41-nam-phat-huy-truyen-thong-anh-hung-lao-dong',
				'category_slug' => 'tin-tuc-su-kien',
				'excerpt' => 'Dấu mốc 41 năm xây dựng và phát triển gắn với ba trụ cột kinh tế, an sinh xã hội và môi trường.',
				'paragraphs' => array('Thành lập ngày 17/8/1984, Cao su Chư Sê từng bước xây dựng vùng nguyên liệu, cơ sở chế biến và đội ngũ lao động tại Tây Nguyên.', 'Truyền thống Anh hùng Lao động là nền tảng để Công ty theo đuổi định hướng phát triển xanh, chăm lo cộng đồng và nâng cao hiệu quả sản xuất.'),
				'source_url' => 'https://news.vrg.vn/hoat-dong-co-so/41-nam-hinh-thanh-va-phat-trien-cao-su-chu-se-phat-huy-truyen-thong-anh-hung-xay-dung-cong-ty-phat-trien-xanh-va-ben-vung/',
				'source_label' => 'Tin tức VRG',
				'published_at' => '2025-08-19 08:00:00'
			),
			array(
				'title' => 'Chính thức vận hành nền tảng số Base Platform',
				'slug' => 'chinh-thuc-van-hanh-nen-tang-so-base-platform',
				'category_slug' => 'phat-trien-ben-vung',
				'excerpt' => 'Nền tảng quản trị số được đưa vào vận hành nhằm chuẩn hóa quy trình và tăng hiệu quả phối hợp.',
				'paragraphs' => array('Ngày 22/8/2025, Công ty chính thức vận hành Base Platform sau giai đoạn khảo sát, thiết lập, chạy thử và đào tạo người dùng.', 'Hệ thống hỗ trợ quản trị công việc tập trung, tăng khả năng theo dõi tiến độ và tạo nền tảng cho quá trình chuyển đổi số lâu dài.'),
				'source_url' => 'https://chuserubber.vn/cao-su-chu-se-chinh-thuc-van-hanh-nen-tang-so-base-platform/',
				'source_label' => 'Cao su Chư Sê',
				'published_at' => '2025-08-22 08:00:00'
			),
			array(
				'title' => 'Định hướng phát triển xanh và bền vững',
				'slug' => 'dinh-huong-phat-trien-xanh-va-ben-vung',
				'category_slug' => 'phat-trien-ben-vung',
				'excerpt' => 'Công ty gắn hiệu quả sản xuất với bảo vệ môi trường, an sinh xã hội và quản trị minh bạch.',
				'paragraphs' => array('Cao su Chư Sê xác định phát triển bền vững dựa trên sự cân bằng giữa hiệu quả kinh tế, trách nhiệm với người lao động và bảo vệ tài nguyên.', 'Các hệ thống quản lý chất lượng, môi trường và năng lực thử nghiệm là công cụ hỗ trợ kiểm soát hoạt động theo hướng chuyên nghiệp.'),
				'source_url' => 'https://www.chuserubber.vn/',
				'source_label' => 'Cao su Chư Sê',
				'published_at' => '2026-01-15 08:00:00'
			)
		);
	}

	private function internal()
	{
		return array(
			array(
				'title' => 'Công đoàn chăm lo người lao động sau sáp nhập',
				'slug' => 'cong-doan-cham-lo-nguoi-lao-dong-sau-sap-nhap',
				'category_slug' => 'hoat-dong-cong-doan',
				'excerpt' => 'Các chương trình nhà ở và hỗ trợ đời sống tiếp tục được triển khai cho gần 2.100 người lao động.',
				'paragraphs' => array('Trong 9 tháng năm 2026, Công đoàn Công ty dành hơn 415 triệu đồng hỗ trợ xây mới 5 căn nhà, sửa chữa 3 căn, xây 1 nhà tình nghĩa và khu nhà tập thể cho 18 gia đình.', 'Sau sáp nhập, tổ chức công đoàn tiếp tục ổn định bộ máy, đồng hành cùng gần 2.100 người lao động và vận động hoàn thành nhiệm vụ sản xuất cuối năm.'),
				'source_url' => 'https://news.vrg.vn/hoat-dong-cong-doan/cong-doan-cao-su-chu-se-on-dinh-to-chuc-sau-sap-nhap-cham-lo-tot-doi-song-nguoi-lao-dong/',
				'source_label' => 'Tin tức VRG',
				'published_at' => '2026-10-01 08:00:00'
			),
			array(
				'title' => 'Trao 63 suất quà cho công nhân có hoàn cảnh khó khăn',
				'slug' => 'trao-63-suat-qua-cho-cong-nhan-kho-khan',
				'category_slug' => 'hoat-dong-cong-doan',
				'excerpt' => 'Hoạt động thiết thực trong Tháng Công nhân và Tháng hành động về an toàn, vệ sinh lao động.',
				'paragraphs' => array('Ngày 20/5/2026, Công ty phối hợp với Vietcombank Gia Lai trao 63 suất quà cho công nhân có hoàn cảnh khó khăn.', 'Chương trình thể hiện sự quan tâm tới đời sống người lao động và tinh thần đồng hành của doanh nghiệp, tổ chức công đoàn cùng đơn vị đối tác.'),
				'source_url' => 'https://www.chuserubber.vn/trao-tang-63-suat-qua-cho-cong-nhan-kho-khan/',
				'source_label' => 'Cao su Chư Sê',
				'published_at' => '2026-05-20 08:00:00'
			),
			array(
				'title' => 'Hơn 95% học viên lớp khai thác mủ đạt loại khá, giỏi',
				'slug' => 'hon-95-phan-tram-hoc-vien-khai-thac-mu-dat-kha-gioi',
				'category_slug' => 'guong-nguoi-tot-viec-tot',
				'excerpt' => 'Lớp đào tạo 82 học viên góp phần chuẩn hóa kỹ thuật và bổ sung lực lượng khai thác.',
				'paragraphs' => array('Lớp đào tạo kỹ thuật khai thác mủ có 82 học viên tham gia và trên 95% đạt kết quả khá, giỏi.', 'Đào tạo thực hành giúp người lao động nắm vững quy trình, bảo vệ vườn cây và nâng cao chất lượng mủ nguyên liệu.'),
				'source_url' => 'https://news.vrg.vn/hoat-dong-co-so/cao-su-chu-se-be-mac-lop-khai-thac-mu-voi-tren-95-hoc-vien-dat-loai-kha-gioi/',
				'source_label' => 'Tin tức VRG',
				'published_at' => '2026-03-30 08:00:00'
			)
		);
	}

	private function products()
	{
		return array(
			$this->item('Cao su tờ xông khói RSS3', 'cao-su-to-xong-khoi-rss3', 'Sản phẩm cao su tờ xông khói phục vụ nhu cầu sản xuất công nghiệp.', array('RSS3 là một trong các dòng sản phẩm cao su thiên nhiên được Công ty giới thiệu tới khách hàng.', 'Sản phẩm được sản xuất trong hệ thống chế biến có quy trình kiểm soát chất lượng và môi trường.'), 'https://chuserubber.vn/san-pham/rss3_1/', 'Cao su Chư Sê', '2026-01-10 08:00:00'),
			$this->item('Cao su khối SVR 3L', 'cao-su-khoi-svr-3l', 'Dòng cao su khối sáng màu dùng làm nguyên liệu cho nhiều ngành công nghiệp.', array('SVR 3L thuộc danh mục sản phẩm cao su thiên nhiên của Công ty, hướng tới yêu cầu ổn định về chất lượng nguyên liệu.', 'Khách hàng có thể liên hệ bộ phận kinh doanh để nhận thông tin quy cách, chứng thư và kế hoạch cung ứng.'), 'https://chuserubber.vn/san-pham/', 'Cao su Chư Sê', '2026-01-09 08:00:00'),
			$this->item('Cao su thiên nhiên sơ chế', 'cao-su-thien-nhien-so-che', 'Nguồn nguyên liệu được khai thác, thu mua và chế biến trong chuỗi hoạt động của Công ty.', array('Công ty tổ chức khai thác, thu mua và chế biến mủ cao su thiên nhiên tại khu vực Tây Nguyên.', 'Hai nhà máy chế biến sau sáp nhập có tổng công suất thiết kế khoảng 23.000 tấn mỗi năm.'), 'https://www.chuserubber.vn/gioi-thieu/', 'Cao su Chư Sê', '2026-01-08 08:00:00')
		);
	}

	private function services()
	{
		return array(
			$this->item('Trồng, chăm sóc và khai thác cao su', 'trong-cham-soc-va-khai-thac-cao-su', 'Tổ chức sản xuất cao su từ kiến thiết cơ bản đến khai thác vườn cây.', array('Hoạt động cốt lõi gồm trồng mới, chăm sóc, bảo vệ và khai thác vườn cây cao su.', 'Quy trình sản xuất chú trọng kỹ thuật, năng suất, an toàn lao động và chất lượng mủ nguyên liệu.'), 'https://www.chuserubber.vn/gioi-thieu/', 'Cao su Chư Sê', '2026-01-07 08:00:00'),
			$this->item('Thu mua và chế biến mủ cao su', 'thu-mua-va-che-bien-mu-cao-su', 'Dịch vụ thu mua nguyên liệu và chế biến các dòng cao su thiên nhiên.', array('Công ty thu mua mủ cao su và tổ chức chế biến tại các nhà máy trực thuộc.', 'Năng lực chế biến được kết nối với hệ thống quản lý chất lượng, môi trường và thử nghiệm sản phẩm.'), 'https://www.chuserubber.vn/', 'Cao su Chư Sê', '2026-01-06 08:00:00'),
			$this->item('Hạ tầng Khu công nghiệp Nam Pleiku', 'ha-tang-khu-cong-nghiep-nam-pleiku', 'Đầu tư, kinh doanh và hỗ trợ khai thác hạ tầng khu công nghiệp.', array('Kinh doanh hạ tầng khu công nghiệp là một trong các lĩnh vực hoạt động mở rộng của Công ty.', 'Thông tin chi tiết về quỹ đất, hạ tầng và cơ hội hợp tác được cung cấp qua đầu mối liên hệ doanh nghiệp.'), 'https://www.chuserubber.vn/gioi-thieu/', 'Cao su Chư Sê', '2026-01-05 08:00:00'),
			$this->item('Nông nghiệp công nghệ cao và năng lượng xanh', 'nong-nghiep-cong-nghe-cao-va-nang-luong-xanh', 'Phát triển các mô hình nông nghiệp ứng dụng công nghệ và năng lượng tái tạo.', array('Bên cạnh cao su, Công ty định hướng đầu tư nông nghiệp công nghệ cao, năng lượng xanh và năng lượng tái tạo.', 'Các hoạt động mới được triển khai theo hướng khai thác hiệu quả quỹ đất và giảm tác động tới môi trường.'), 'https://www.chuserubber.vn/', 'Cao su Chư Sê', '2026-01-04 08:00:00')
		);
	}

	private function projects()
	{
		return array(
			$this->item('Khu công nghiệp Nam Pleiku', 'khu-cong-nghiep-nam-pleiku', 'Hạ tầng phục vụ thu hút đầu tư và phát triển công nghiệp tại khu vực Nam Pleiku.', array('Khu công nghiệp Nam Pleiku là một cấu phần trong hệ sinh thái hoạt động của Công ty sau quá trình phát triển và mở rộng.', 'Dự án hướng tới cung cấp hạ tầng đồng bộ, tạo điều kiện kết nối sản xuất và thu hút nhà đầu tư.'), 'https://www.chuserubber.vn/gioi-thieu/', 'Cao su Chư Sê', '2026-01-03 08:00:00'),
			$this->item('Vùng nông nghiệp công nghệ cao', 'vung-nong-nghiep-cong-nghe-cao', 'Quy mô trên 478 ha dành cho định hướng nông nghiệp ứng dụng công nghệ.', array('Theo thông tin giới thiệu doanh nghiệp, diện tích nông nghiệp công nghệ cao của Công ty đạt trên 478 ha.', 'Đây là nguồn lực để đa dạng hóa sản phẩm và thử nghiệm các mô hình canh tác hiệu quả, bền vững.'), 'https://www.chuserubber.vn/gioi-thieu/', 'Cao su Chư Sê', '2026-01-02 08:00:00'),
			$this->item('Chuyển đổi số quản trị doanh nghiệp', 'chuyen-doi-so-quan-tri-doanh-nghiep', 'Chuẩn hóa công việc và tăng khả năng phối hợp trên nền tảng số.', array('Dự án Base Platform được triển khai qua bốn giai đoạn: khảo sát và thiết lập, chạy thử, đào tạo, vận hành chính thức.', 'Việc số hóa hỗ trợ minh bạch tiến độ, giảm phân tán thông tin và từng bước hình thành môi trường làm việc hiện đại.'), 'https://chuserubber.vn/cao-su-chu-se-cong-bo-van-hanh-chinh-thuc-he-thong-base-platform/', 'Cao su Chư Sê', '2025-08-22 08:00:00')
		);
	}

	private function disclosures()
	{
		return array(
			$this->item('Kế hoạch sản xuất kinh doanh và đầu tư phát triển 5 năm 2017-2021', 'ke-hoach-san-xuat-kinh-doanh-va-dau-tu-2017-2021', 'Tài liệu công bố kế hoạch sản xuất kinh doanh và đầu tư phát triển giai đoạn 2017-2021.', array('Tài liệu được công bố dưới định dạng PDF để tổ chức, cá nhân thuận tiện tra cứu.', 'Nhấn vào liên kết nguồn để mở tài liệu gốc trên website doanh nghiệp.'), 'https://www.chuserubber.vn/images/K%E1%BA%BE_HO%E1%BA%A0CH_SXKD__%C4%90TPT_5__N%C4%82M_2017-2021.pdf', 'Tài liệu PDF chính thức', '2021-12-31 08:00:00'),
			$this->item('Thực trạng quản trị và cơ cấu tổ chức doanh nghiệp năm 2019', 'thuc-trang-quan-tri-va-co-cau-to-chuc-nam-2019', 'Báo cáo về quản trị và cơ cấu tổ chức doanh nghiệp năm 2019.', array('Nội dung cung cấp thông tin phục vụ minh bạch hoạt động quản trị và tổ chức doanh nghiệp.', 'Nhấn vào liên kết nguồn để xem bản PDF do Công ty công bố.'), 'https://chuserubber.vn/images/Th%E1%BB%B1c_tr%E1%BA%A1ng_v%C3%A0_c%C6%A1_c%E1%BA%A5u_t%E1%BB%95_ch%E1%BB%A9c_doanh_nghi%E1%BB%87p_n%C4%83m_2019.pdf', 'Tài liệu PDF chính thức', '2019-12-31 08:00:00'),
			$this->item('Thuyết minh báo cáo tài chính', 'thuyet-minh-bao-cao-tai-chinh', 'Tài liệu thuyết minh báo cáo tài chính được doanh nghiệp công bố.', array('Tài liệu bổ sung thông tin chi tiết cho các chỉ tiêu và chính sách trình bày trong báo cáo tài chính.', 'Nhấn vào liên kết nguồn để xem nội dung PDF trên website chính thức.'), 'https://www.chuserubber.vn/images/THUYET_MINH_BCTC.pdf', 'Tài liệu PDF chính thức', '2020-12-31 08:00:00')
		);
	}

	private function pages()
	{
		return array(
			$this->item('Lịch sử hình thành và phát triển', 'lich-su-hinh-thanh-va-phat-trien', 'Hơn bốn thập kỷ xây dựng vùng cao su và đồng hành cùng địa phương.', array('Công ty được thành lập ngày 17/8/1984 theo Quyết định số 71/QĐ-TCCB. Qua hơn bốn thập kỷ, doanh nghiệp từng bước xây dựng vùng nguyên liệu, nhà máy chế biến và hệ thống đơn vị sản xuất.', 'Năm 2000, tập thể Công ty được phong tặng danh hiệu Anh hùng Lao động trong thời kỳ đổi mới. Truyền thống này tiếp tục được phát huy trong giai đoạn phát triển xanh và bền vững.'), 'https://www.chuserubber.vn/gioi-thieu/', 'Cao su Chư Sê', '2026-01-01 08:00:00'),
			$this->item('Năng lực hoạt động', 'nang-luc-hoat-dong', 'Tổng quan quỹ đất, vườn cây, nhà máy và lực lượng lao động của Công ty.', array('Sau sáp nhập, Công ty quản lý trên 17.139 ha đất, trong đó đất nông nghiệp trên 16.896 ha và diện tích cao su trên 14.043 ha.', 'Doanh nghiệp có 7 phòng chuyên môn, 2 nhà máy chế biến với tổng công suất 23.000 tấn/năm, 1 khu công nghiệp, 1 bệnh viện, 13 đội sản xuất và 2.052 lao động theo số liệu giới thiệu chính thức.'), 'https://www.chuserubber.vn/gioi-thieu/', 'Cao su Chư Sê', '2026-01-01 08:00:00'),
			$this->item('Chất lượng và môi trường', 'chat-luong-va-moi-truong', 'Các hệ thống quản lý hỗ trợ kiểm soát chất lượng sản phẩm và tác động môi trường.', array('Công ty giới thiệu các chứng nhận ISO 9001:2015 về quản lý chất lượng, ISO 14001:2015 về quản lý môi trường và IEC 17025 về năng lực phòng thử nghiệm.', 'Việc duy trì hệ thống quản lý giúp chuẩn hóa quy trình, theo dõi chất lượng và cải tiến hoạt động một cách có hệ thống.'), 'https://www.chuserubber.vn/', 'Cao su Chư Sê', '2026-01-01 08:00:00'),
			$this->item('Thông tin liên hệ và hợp tác', 'thong-tin-lien-he-va-hop-tac', 'Đầu mối liên hệ chính thức của Công ty TNHH MTV Cao su Chư Sê.', array('Trụ sở: 420 Hùng Vương, xã Chư Sê, tỉnh Gia Lai. Điện thoại: 0269 385 1159. Email: chusecrc@gmail.com.', 'Khách hàng và đối tác có thể gửi yêu cầu qua trang Liên hệ để được chuyển tới bộ phận phụ trách.'), 'https://www.chuserubber.vn/lien-he/', 'Cao su Chư Sê', '2026-01-01 08:00:00')
		);
	}

	private function achievements()
	{
		return array(
			$this->item('Danh hiệu Anh hùng Lao động thời kỳ đổi mới', 'anh-hung-lao-dong-thoi-ky-doi-moi', 'Phần thưởng cao quý ghi nhận thành tích của tập thể Công ty trong lao động và xây dựng.', array('Năm 2000, Công ty được phong tặng danh hiệu Anh hùng Lao động trong thời kỳ đổi mới.', 'Danh hiệu là dấu mốc nổi bật trong lịch sử phát triển và là động lực để các thế hệ người lao động tiếp tục đổi mới.'), 'https://chuserubber.vn/thanh-tich/', 'Cao su Chư Sê', '2000-12-31 08:00:00'),
			$this->item('Huân chương và các phần thưởng tiêu biểu', 'huan-chuong-va-cac-phan-thuong-tieu-bieu', 'Các phần thưởng của Đảng, Nhà nước và tổ chức cấp trên ghi nhận đóng góp của Công ty.', array('Công ty được tặng Huân chương Độc lập hạng Ba năm 2008; các Huân chương Lao động vào các năm 1995, 1996 và 2004.', 'Nhiều cờ thi đua, bằng khen khác cũng ghi nhận thành tích sản xuất kinh doanh, chăm lo người lao động và đóng góp cho địa phương.'), 'https://chuserubber.vn/thanh-tich/', 'Cao su Chư Sê', '2008-12-31 08:00:00')
		);
	}

	private function faqs()
	{
		return array(
			$this->item('Công ty hoạt động trong những lĩnh vực nào?', 'cong-ty-hoat-dong-trong-nhung-linh-vuc-nao', 'Các nhóm hoạt động chính của Cao su Chư Sê.', array('Hoạt động chính gồm trồng, chăm sóc, khai thác, thu mua, chế biến và kinh doanh cao su thiên nhiên; chế biến gỗ; hạ tầng khu công nghiệp; nông nghiệp công nghệ cao và năng lượng xanh.'), 'https://www.chuserubber.vn/', 'Cao su Chư Sê', '2026-01-01 08:00:00'),
			$this->item('Công ty hiện có những sản phẩm cao su nào?', 'cong-ty-co-nhung-san-pham-cao-su-nao', 'Thông tin nhanh về danh mục sản phẩm.', array('Danh mục được giới thiệu trên website chính thức có các dòng RSS3 và SVR 3L. Quy cách và khả năng cung ứng cụ thể được xác nhận theo từng thời điểm với bộ phận kinh doanh.'), 'https://chuserubber.vn/san-pham/', 'Cao su Chư Sê', '2026-01-01 08:00:00'),
			$this->item('Làm thế nào để liên hệ với Công ty?', 'lam-the-nao-de-lien-he-voi-cong-ty', 'Địa chỉ, điện thoại, email và biểu mẫu liên hệ.', array('Có thể liên hệ tại 420 Hùng Vương, xã Chư Sê, tỉnh Gia Lai; điện thoại 0269 385 1159; email chusecrc@gmail.com hoặc gửi biểu mẫu trên trang Liên hệ.'), 'https://www.chuserubber.vn/lien-he/', 'Cao su Chư Sê', '2026-01-01 08:00:00'),
			$this->item('Tra cứu tài liệu công bố doanh nghiệp ở đâu?', 'tra-cuu-tai-lieu-cong-bo-doanh-nghiep-o-dau', 'Hướng dẫn tìm tài liệu đã công bố.', array('Các kế hoạch, báo cáo quản trị và tài liệu tài chính được tổng hợp tại mục Công bố thông tin. Mỗi nội dung có liên kết tới tài liệu nguồn để tải hoặc xem trực tuyến.'), 'https://www.chuserubber.vn/', 'Cao su Chư Sê', '2026-01-01 08:00:00'),
			$this->item('Công ty áp dụng những hệ thống quản lý nào?', 'cong-ty-ap-dung-nhung-he-thong-quan-ly-nao', 'Thông tin về quản lý chất lượng, môi trường và thử nghiệm.', array('Website chính thức giới thiệu các chứng nhận ISO 9001:2015, ISO 14001:2015 và IEC 17025. Phạm vi chứng nhận cụ thể cần được đối chiếu trên chứng thư còn hiệu lực.'), 'https://www.chuserubber.vn/', 'Cao su Chư Sê', '2026-01-01 08:00:00'),
			$this->item('Gửi câu hỏi hoặc góp ý bằng cách nào?', 'gui-cau-hoi-hoac-gop-y-bang-cach-nao', 'Kênh tiếp nhận phản hồi của người dùng.', array('Bạn có thể sử dụng biểu mẫu Liên hệ trên website, cung cấp họ tên, thông tin liên lạc và nội dung cần hỗ trợ. Bộ phận quản trị sẽ tiếp nhận và chuyển yêu cầu tới đơn vị phù hợp.'), 'https://www.chuserubber.vn/lien-he/', 'Cao su Chư Sê', '2026-01-01 08:00:00')
		);
	}

	private function item($title, $slug, $excerpt, $paragraphs, $sourceUrl, $sourceLabel, $publishedAt)
	{
		return array(
			'title' => $title,
			'slug' => $slug,
			'excerpt' => $excerpt,
			'paragraphs' => $paragraphs,
			'source_url' => $sourceUrl,
			'source_label' => $sourceLabel,
			'published_at' => $publishedAt
		);
	}
}
