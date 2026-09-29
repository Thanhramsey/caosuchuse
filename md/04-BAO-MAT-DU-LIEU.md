# Quy tắc bảo mật và dữ liệu

## Input, output và CSRF

- Mọi dữ liệu từ request phải validate ở server theo từng tác vụ; không tin validation JavaScript.
- Dùng Form Validation Library hoặc validation chuyên biệt ở server.
- Hiển thị dữ liệu động trong view phải escape đúng ngữ cảnh.
- Nội dung HTML từ editor phải lọc theo allowlist rõ ràng trước khi render.
- Bật CSRF trong `application/config/config.php` cho form ghi dữ liệu.
- AJAX phải gửi token CSRF hiện hành và cập nhật token nếu ứng dụng dùng chế độ regenerate.
- Thêm chống spam/throttle phù hợp cho liên hệ, bình luận, đăng ký bản tin và đăng nhập.

## Đăng nhập admin

CodeIgniter 3 không dùng CodeIgniter Shield. Dùng **một cơ chế xác thực admin duy nhất** được xây cho CI3:

- session server-side;
- password lưu bằng `password_hash()` và kiểm tra bằng `password_verify()`;
- regenerate session ID sau đăng nhập và khi thay đổi mức quyền quan trọng;
- logout phải hủy trạng thái xác thực;
- khóa/tạm khóa sau nhiều lần đăng nhập sai theo chính sách được duyệt;
- không lưu password plaintext hoặc tự viết thuật toán hash.

Nên gom kiểm tra đăng nhập/quyền vào `MY_Controller`, library `Auth` hoặc lớp dùng chung rõ ràng; không copy-paste logic ở từng controller.

## Session và cookie

- Production dùng HTTPS.
- Cookie session đặt `Secure` khi chạy HTTPS, `HttpOnly`, SameSite phù hợp.
- Không đưa dữ liệu nhạy cảm không cần thiết vào session.
- Session driver và nơi lưu phải phù hợp hosting; nếu dùng files, thư mục session không được public.
- Không phụ thuộc session path mặc định nếu hosting có khả năng dọn file bất thường.

## Phân quyền

- Phân quyền dựa trên vai trò/quyền được chủ dự án duyệt.
- Mặc định từ chối thao tác thiếu quyền.
- Kiểm tra quyền ở server cho từng hành động nhạy cảm; ẩn menu/nút chỉ là giao diện, không phải bảo mật.
- Quyền của site I không tự áp dụng sang site II.
- Không cho người dùng tự nâng quyền bằng dữ liệu form, hidden input hoặc request sửa trực tiếp.

## Upload

- Giới hạn dung lượng và extension cho phép.
- Xác thực MIME/type thực tế, không chỉ tin tên file do browser gửi.
- Sinh tên file server-side; không dùng nguyên tên người dùng làm đường dẫn lưu.
- Chống path traversal, ghi đè ngoài ý muốn và thực thi file upload.
- Ảnh nên decode/kiểm tra lại bằng thư viện ảnh khi cần.
- File riêng tư phải nằm ngoài web root nếu có thể và được trả về qua controller kiểm tra quyền.
- Không lưu ảnh base64 khối lượng lớn trực tiếp trong nội dung bài nếu không có yêu cầu đặc biệt.

## Database

- Dùng Query Builder hoặc query bindings.
- Không nối trực tiếp input vào SQL.
- Dùng transaction cho thao tác nhiều bảng.
- Có unique/index phù hợp để bảo vệ dữ liệu ở tầng database, không chỉ kiểm tra ở PHP.
- Không tự xóa dữ liệu production bằng migration/seed/script khởi tạo.

## Secret và log

- Không hardcode DB, SMTP, tài khoản admin, secret hoặc thông tin doanh nghiệp chưa xác nhận.
- File cấu hình production chứa secret không đưa vào Git.
- Không ghi password, token, nội dung riêng tư, file nhạy cảm hoặc thông tin cá nhân vào log nếu không cần.
- Production không hiển thị stack trace hoặc lỗi database chi tiết cho người dùng cuối.

## Tương thích PHP 8.x và bảo mật

Không xử lý warning/deprecation bằng cách tắt toàn bộ error reporting để “cho chạy”. Trong môi trường test PHP 8.2/8.3 phải bật đầy đủ cảnh báo, sửa nguyên nhân rồi mới nâng production.
