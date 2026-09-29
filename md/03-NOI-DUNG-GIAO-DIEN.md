# Quy tắc nội dung và giao diện

## Giao diện
- bám sát theo thiết kế tại thư mục md\VRG Chư Sê
- Ưu tiên HTML có ngữ nghĩa, điều hướng bằng bàn phím, nhãn form rõ, alt cho ảnh có ý nghĩa, độ tương phản và trạng thái focus dễ thấy.
- Kiểm tra ít nhất các cỡ màn hình điện thoại, tablet, desktop; menu, slider, lưới nội dung và form không tràn ngang.
- Tài nguyên ngoài như ảnh Unsplash trong demo chỉ là placeholder; ảnh production phải được cung cấp hoặc có quyền sử dụng.

## Nội dung

- Nội dung hiển thị tiếng Việt có dấu; slug URL ổn định, duy nhất trong loại nội dung; không đổi URL đã xuất bản tùy tiện.
- Tin tức: tiêu đề, tóm tắt, ảnh đại diện nếu có, nội dung, ngày xuất bản, trạng thái, slug. Danh sách có phân trang; chi tiết có tin liên quan. Bình luận cần chính sách duyệt/ẩn/xóa trước khi mở công khai.
- Dự án và dịch vụ của site I: danh mục/danh sách, trang chi tiết, mô tả. Chỉ thêm trường dữ liệu khi có yêu cầu nội dung cụ thể.
- Trang tĩnh có thể phục vụ giới thiệu, điều khoản, chính sách; nội dung pháp lý và thông tin liên hệ phải do chủ dự án xác nhận.
- Tìm kiếm theo tin tức như báo giá; xử lý từ khóa trống, ký tự đặc biệt và kết quả rỗng, có phân trang nếu nhiều kết quả.
- Slider, đối tác, ý kiến khách hàng và tin nổi bật phải có trạng thái hiển thị/thứ tự nếu được quản trị. Không hardcode dữ liệu giả làm nội dung chính thức.

## SEO cơ bản

Theo yêu cầu SEO ở trang chi tiết tin: title và description phù hợp từng trang, heading đúng cấp, URL dễ đọc, canonical khi cần, Open Graph nếu có ảnh/nội dung. Escaping đầu ra phải giữ an toàn cả trong meta tag. Sitemap/robots/schema markup chỉ bổ sung khi được giao hoặc có tiêu chí nghiệm thu cụ thể; không hứa thứ hạng tìm kiếm.
