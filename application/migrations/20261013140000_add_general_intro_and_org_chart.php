<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/** Publish the general introduction and organization chart pages from public company information. */
class Migration_Add_general_intro_and_org_chart extends CI_Migration
{
	public function up()
	{
		$now = date('Y-m-d H:i:s');
		$this->savePage('gioi-thieu-chung', array(
			'title' => 'Giới thiệu chung',
			'status' => 'published',
			'cover_path' => 'assets/images/hero-rubber-plantation.webp',
			'excerpt' => 'Công ty TNHH MTV Cao su Chư Sê (CRC) được thành lập năm 1984, hoạt động trong chuỗi trồng, khai thác, chế biến cao su thiên nhiên và phát triển các lĩnh vực liên quan.',
			'content_html' => $this->introHtml(),
			'updated_at' => $now
		), $now);

		$this->savePage('so-do-to-chuc', array(
			'title' => 'Sơ đồ tổ chức',
			'status' => 'published',
			'cover_path' => NULL,
			'excerpt' => 'Cơ cấu quản trị gồm Hội đồng thành viên, Ban Tổng Giám đốc, Ban Kiểm soát; cùng các phòng chuyên môn, đơn vị phụ thuộc, công ty liên kết và tổ chức đoàn thể.',
			'content_html' => $this->orgHtml(),
			'updated_at' => $now
		), $now);
	}

	public function down()
	{
		// These pages are editable CMS content; retain them if this migration is rolled back.
	}

	private function savePage($slug, $data, $now)
	{
		$page = $this->db->select('id')->where('slug', $slug)->get('pages')->row_array();
		if ($page)
		{
			$this->db->where('id', (int) $page['id'])->update('pages', $data);
			return;
		}
		$data['slug'] = $slug;
		$data['created_at'] = $now;
		$this->db->insert('pages', $data);
	}

	private function introHtml()
	{
		return '<section class="company-intro">'
			. '<p class="company-intro-lead">Công ty TNHH MTV Cao su Chư Sê (CRC) là doanh nghiệp trực thuộc Tập đoàn Công nghiệp Cao su Việt Nam (VRG), được thành lập theo Quyết định số 71/QĐ-TCCB ngày 17/8/1984 của Tổng cục Cao su Việt Nam. Trải qua hơn bốn thập kỷ, Công ty xây dựng chuỗi hoạt động từ phát triển vườn cây đến chế biến và kinh doanh sản phẩm cao su thiên nhiên, gắn hoạt động sản xuất với việc làm và đời sống tại địa phương.</p>'
			. '<h2>Lĩnh vực hoạt động</h2>'
			. '<div class="intro-activity-grid">'
			. '<div><h3>Cao su thiên nhiên</h3><p>Trồng, chăm sóc, khai thác, thu mua, chế biến và kinh doanh cao su thiên nhiên.</p></div>'
			. '<div><h3>Chế biến gỗ</h3><p>Tham gia chế biến gỗ trong chuỗi hoạt động sản xuất của doanh nghiệp.</p></div>'
			. '<div><h3>Hạ tầng công nghiệp</h3><p>Đầu tư, xây dựng, kinh doanh và hỗ trợ khai thác hạ tầng khu công nghiệp.</p></div>'
			. '<div><h3>Nông nghiệp và năng lượng</h3><p>Phát triển dự án nông nghiệp ứng dụng công nghệ cao, năng lượng xanh và năng lượng tái tạo.</p></div>'
			. '</div>'
			. '<h2>Quy mô hoạt động sau sáp nhập</h2>'
			. '<p>Theo thông tin trên trang giới thiệu của Công ty sau khi sáp nhập Cao su Mang Yang, CRC quản lý hơn 17.139 ha đất, trong đó hơn 16.896 ha là đất nông nghiệp. Diện tích vườn cây cao su hơn 14.043 ha; diện tích dành cho nông nghiệp ứng dụng công nghệ cao hơn 478 ha.</p>'
			. '<div class="intro-metric-grid">'
			. '<div><strong>17.139+ ha</strong><span>đất do Công ty quản lý</span></div>'
			. '<div><strong>14.043+ ha</strong><span>vườn cây cao su</span></div>'
			. '<div><strong>23.000 tấn/năm</strong><span>công suất của 2 nhà máy chế biến</span></div>'
			. '<div><strong>2.052 người</strong><span>lao động theo thông tin công bố</span></div>'
			. '</div>'
			. '<p>Hệ thống hoạt động được giới thiệu gồm 7 phòng chuyên môn nghiệp vụ, 13 đội sản xuất cao su, 2 nhà máy chế biến, 1 khu công nghiệp và 1 bệnh viện.</p>'
			. '<h2>Định hướng phát triển</h2>'
			. '<p>Cao su Chư Sê hướng tới phát triển sản xuất hiệu quả và bền vững; ứng dụng công nghệ trong quản trị, nâng cao chất lượng chế biến, bảo vệ môi trường, chăm lo người lao động và mở rộng các lĩnh vực nông nghiệp công nghệ cao, hạ tầng công nghiệp, năng lượng xanh.</p>'
			. '<h2>Thông tin liên hệ</h2>'
			. '<ul><li><strong>Địa chỉ:</strong> 420 Hùng Vương, xã Chư Sê, tỉnh Gia Lai</li><li><strong>Điện thoại:</strong> 0269 385 1159</li><li><strong>Email:</strong> <a href="mailto:chusecrc@gmail.com">chusecrc@gmail.com</a></li></ul>'
			. '<p class="source-note"><strong>Nguồn:</strong> <a href="https://chuserubber.vn/gioi-thieu/" target="_blank" rel="noopener noreferrer">Trang giới thiệu Cao su Chư Sê</a>. Các số liệu được trình bày theo nội dung nguồn công bố và có thể thay đổi theo thời gian.</p>'
			. '</section>';
	}

	private function orgHtml()
	{
		$teams = array('Ia Glai', 'Ia Hlop', 'Ia Tiêm', 'Ia Ko', 'Ia Lâu', 'Đội 01', 'Đội 03', 'Đội 05', 'Đội 06', 'Đội 08', 'Đội 09', 'Đội 12', 'K’Dang');
		$affiliates = array('Công ty CP Cao su Chư Sê Kampong Thom', 'Công ty CP VRG Bảo Lộc', 'Công ty CP Thương mại Dịch vụ Du lịch Cao su', 'Công ty CP Mangyang-Rattanakiri', 'Công ty CP VRG Phú Yên', 'Công ty CP Krông Buk-Rattanakiri', 'Công ty CP Cao su Sa Thầy');
		$html = '<div class="org-chart">'
			. '<div class="org-chart-root">CÔNG TY TNHH MTV CAO SU CHƯ SÊ</div>'
			. '<div class="org-chart-leadership">'
			. '<section class="org-chart-card"><h2>Hội đồng thành viên</h2><p><strong>Chủ tịch:</strong> Nguyễn Tiến Dũng</p><p><strong>Thành viên:</strong> Lê Trung Kiên, Phan Thanh Hà, Nguyễn Hữu Tú</p></section>'
			. '<section class="org-chart-card"><h2>Ban Tổng Giám đốc</h2><p><strong>Tổng Giám đốc:</strong> Lê Trung Kiên</p><p><strong>Phó Tổng Giám đốc:</strong> Nguyễn Hữu Tú, Mai Trung Hiếu, Phan Thanh Hà</p></section>'
			. '<section class="org-chart-card"><h2>Ban Kiểm soát</h2><p><strong>Trưởng ban:</strong> Lê Thị Kiều Nga</p><p><strong>Kiểm soát viên kiêm nhiệm:</strong> Mai Ngọc Lực, Nguyễn Thế Linh</p></section>'
			. '</div>'
			. '<section class="org-chart-section"><h2>Phòng chuyên môn</h2><ul class="org-chart-list">'
			. '<li>Văn phòng</li><li>Phòng Tổ chức – Lao động tiền lương</li><li>Phòng Tài chính – Kế toán</li><li>Phòng Quản lý đầu tư và xây dựng</li><li>Phòng Quản lý kỹ thuật</li><li>Phòng Quản lý chất lượng</li><li>Phòng Pháp chế – Thanh tra – Bảo vệ</li>'
			. '</ul></section>'
			. '<section class="org-chart-section"><h2>Đơn vị phụ thuộc</h2><div class="org-chart-units">'
			. '<div><h3>Đội sản xuất cao su</h3><ul class="org-chart-list">';
		foreach ($teams as $team)
		{
			$html .= '<li>' . htmlspecialchars($team, ENT_QUOTES, 'UTF-8') . '</li>';
		}
		$html .= '</ul></div><div><h3>Đơn vị trực thuộc</h3><ul class="org-chart-list"><li>Bệnh viện Cao su Chư Sê</li><li>Chi nhánh Khu công nghiệp Nam Pleiku</li><li>Nhà máy Chế biến mủ Cao su Chư Sê</li><li>Nhà máy Chế biến mủ Thành Công</li></ul></div></div></section>'
			. '<section class="org-chart-section"><h2>Công ty có vốn góp và công ty liên kết</h2><ul class="org-chart-list org-chart-list--wide">';
		foreach ($affiliates as $affiliate)
		{
			$html .= '<li>' . htmlspecialchars($affiliate, ENT_QUOTES, 'UTF-8') . '</li>';
		}
		$html .= '</ul></section>'
			. '<section class="org-chart-section"><h2>Đoàn thể và tổ chức chính trị – xã hội</h2><ul class="org-chart-list"><li><strong>Công đoàn:</strong> Chủ tịch Lê Huy Phu; Phó Chủ tịch Đặng Đức Tài</li><li><strong>Đoàn Thanh niên:</strong> Bí thư Đặng Đức Tài</li><li><strong>Hội Cựu chiến binh</strong></li></ul></section>'
			. '<p class="source-note"><strong>Nguồn:</strong> <a href="https://chuserubber.vn/so-do-to-chuc/" target="_blank" rel="noopener noreferrer">Sơ đồ tổ chức Cao su Chư Sê</a>. Tên chức danh và đơn vị được trình bày theo sơ đồ công khai của Công ty.</p>'
			. '</div>';
		return $html;
	}
}
