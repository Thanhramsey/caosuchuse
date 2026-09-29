# Phạm vi theo báo giá

Nguồn: `D:/project/caosu/10.BG - Web.pdf`, các mục I và II, trang 1-3. Mỗi mục báo giá là một website riêng; không mặc định chia sẻ cơ sở dữ liệu, quản trị hoặc tên miền. Dự án hiện tại `caosuchuse/` ưu tiên **website Công ty TNHH MTV Cao su Chư Sê** (mục I). Chỉ làm website Chi nhánh KCN VRG Gia Lai (mục II) khi được giao cụ thể.

| Chức năng | Cao su Chư Sê (I) | KCN VRG Gia Lai (II) |
| --- | --- | --- |
| Trang chủ | Slider, giới thiệu, ý kiến khách hàng, dự án nổi bật, tin mới, đối tác, footer liên kết nhanh và đăng ký bản tin | Cùng mô tả trong báo giá |
| Dự án | Danh sách dạng lưới hoặc dòng, danh mục, trang chi tiết | Không có hạng mục trang dự án riêng |
| Tin tức | Danh sách, chi tiết, tin liên quan, bình luận, SEO | Có |
| Liên hệ | Form, địa chỉ, số điện thoại hỗ trợ | Có |
| Trang tĩnh | Ví dụ giới thiệu, điều khoản | Có |
| Tìm kiếm | Tìm tin tức | Có |
| Slider | Trang chủ, trang dự án, chi tiết dự án | Báo giá vẫn ghi slider trang dự án/chi tiết dự án dù không liệt kê trang dự án; cần xác nhận trước khi xây cho site II |
| Nhân viên và phân quyền | Có tạo nhân viên và phân quyền theo chức năng | Không được nêu |
| Dịch vụ | Danh mục và chi tiết | Không được nêu |
| Responsive | Điện thoại, máy tính bảng, máy tính | Có |

Footer có liên kết chính sách bán hàng, giới thiệu, liên hệ và đăng ký bản tin; nội dung/trạng thái của trang chính sách và luồng xác nhận email cần được chủ dự án cung cấp. Bảo hành, hỗ trợ 24/7, giá, thuế và hiệu lực báo giá là điều khoản dịch vụ, **không** phải tính năng cần code.

## Không tự suy diễn

- Báo giá không định nghĩa cấu trúc database, hệ quản trị CSDL, URL, tên miền, logo chính thức, dữ liệu thật, vai trò/quyền chi tiết, trạng thái duyệt bình luận, phương thức nhận liên hệ/bản tin hay công cụ SEO. Đề xuất thiết kế tối thiểu khi được giao chức năng, rồi ghi rõ giả định để duyệt.
- Không tự thêm thương mại điện tử, thanh toán, đa ngôn ngữ, cổng khách hàng, API công khai, tích hợp mạng xã hội hay chức năng báo cáo.
- Demo tĩnh có các trang sản phẩm, công bố thông tin và nội dung khác; đó là tham chiếu giao diện, không tự biến thành phạm vi bắt buộc của báo giá.
- Không dùng nội dung, hình ảnh hoặc số liệu giả như thể đã được doanh nghiệp xác nhận. Placeholder phải được đánh dấu rõ trong môi trường phát triển.

## Nguyên tắc chia giai đoạn

Đi theo lát cắt có thể chạy và kiểm tra: trang công khai + bố cục chung; nội dung tin/trang tĩnh/dự án/dịch vụ; tìm kiếm và liên hệ; quản trị nội dung; tài khoản và phân quyền; kiểm tra cuối. Chỉ triển khai phần được giao, không tạo toàn bộ module dự phòng.
