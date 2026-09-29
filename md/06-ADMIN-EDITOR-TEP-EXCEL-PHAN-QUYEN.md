# Lựa chọn công nghệ cho admin và nội dung

Quyết định áp dụng cho website Cao su Chư Sê trong `caosuchuse/`, nền **CodeIgniter 3.1.13 + PHP 7.3**. Đây là quy tắc chọn thư viện và thiết kế triển khai; chưa có nghĩa mọi package đã được cài.

Mọi PHP package phải được khóa version bằng `composer.lock` và kiểm tra cả:
- chạy được trên PHP 7.3 hiện tại;
- có đường nâng cấp hợp lý lên PHP 8.2/8.3;
- không yêu cầu syntax PHP mới hơn trong bản đang cài.

Ưu tiên tự host CSS/JS trong `assets/vendor/`; không phụ thuộc CDN cho admin production nếu không có lý do rõ ràng.

| Nhu cầu | Chọn | Cách dùng |
| --- | --- | --- |
| Giao diện quản trị | **Tabler Community** | Template HTML/CSS/JS dựa trên Bootstrap 5. Dùng server-rendered view của CI3; sidebar theo nhóm chức năng. Không cần SPA. |
| Soạn bài | **Jodit bản miễn phí** | WYSIWYG tiếng Việt; upload ảnh qua endpoint CI3 riêng có session, CSRF và quyền. |
| Xem ảnh | **PhotoSwipe 5** | Lightbox cho gallery/ảnh đính kèm. |
| Xem PDF | **PDF.js** | Xem trước PDF được phép truy cập; file riêng tư phải qua controller kiểm tra quyền. |
| Excel | **PhpSpreadsheet, version tương thích PHP 7.3 được khóa cụ thể** | Chỉ cài sau khi Composer resolve thành công với platform PHP 7.3. Không dùng `*` hoặc tự động lấy bản mới nhất. |
| Tài khoản và phân quyền | **Auth/RBAC riêng cho CI3, tập trung trong library/base controller** | Session auth + bảng users/roles/permissions. Không dùng CodeIgniter Shield vì Shield thuộc CI4. |

## Admin dễ dùng

- Menu nhóm: Tổng quan; Nội dung; Media/Tệp; Tương tác; Giao diện; Người dùng & quyền; Cấu hình.
- Chỉ hiện mục đã triển khai và người dùng có quyền.
- Danh sách có tìm kiếm, lọc trạng thái, phân trang, nút tạo/sửa/xem rõ ràng.
- Xóa phải xác nhận và kiểm tra quyền server-side.
- Form báo lỗi cạnh trường; giữ dữ liệu nhập hợp lệ khi validation thất bại.
- Nếu nghiệp vụ có nháp/duyệt/xuất bản, quyền phải được kiểm tra ở server.
- Trang người dùng tối thiểu: danh sách/tìm kiếm, tạo tài khoản, khóa/mở khóa, đặt lại mật khẩu theo luồng an toàn, gán vai trò/quyền.
- Không cho xóa/khóa tài khoản superadmin cuối cùng.

## Cấu trúc auth đề xuất cho CI3

Không rải logic `if ($this->session->userdata(...))` khắp controller.

Nên có:

```text
application/
├─ core/
│  ├─ MY_Controller.php
│  └─ Admin_Controller.php
├─ libraries/
│  ├─ Auth.php
│  └─ Permission.php
├─ controllers/
│  └─ admin/
├─ models/
│  ├─ User_model.php
│  ├─ Role_model.php
│  └─ Permission_model.php
└─ views/
   └─ admin/
```

`Admin_Controller` kiểm tra session đăng nhập. Từng action nhạy cảm tiếp tục gọi permission cụ thể, ví dụ:

```php
if ( ! $this->permission->can('news.publish')) {
    show_error('Bạn không có quyền thực hiện thao tác này.', 403);
}
```

## Bảng quyền đề xuất

Tối thiểu có thể dùng:

- `users`
- `roles`
- `permissions`
- `user_roles`
- `role_permissions`

Không thêm mô hình quyền phức tạp hơn nếu chưa cần.

Nhóm mặc định đề xuất:

| Nhóm | Quyền mặc định đề xuất |
| --- | --- |
| `superadmin` | Toàn bộ quản trị, cấu hình và phân quyền |
| `content_manager` | Xem/tạo/sửa/xuất bản nội dung, media, duyệt bình luận |
| `editor` | Tạo/sửa bài theo phạm vi được giao, lưu nháp/gửi duyệt |
| `support` | Xem/xử lý liên hệ, bình luận, bản tin |

Tên permission dùng `resource.action`, ví dụ:

- `news.view`
- `news.create`
- `news.update_own`
- `news.update_any`
- `news.publish`
- `news.delete`
- `media.upload`
- `media.delete`
- `comments.moderate`
- `contacts.view`
- `contacts.manage`
- `users.create`
- `users.assign_role`
- `settings.manage`

## Soạn bài, ảnh và file đính kèm

- Jodit gửi HTML lên server; không tin HTML đó là an toàn.
- Lọc allowlist tag/attribute cần thiết trước khi lưu/render.
- Chặn `script`, event handler, URL nguy hiểm, iframe chưa được duyệt.
- Upload ảnh phải kiểm tra CSRF, session, permission, dung lượng, extension, MIME và nội dung file.
- Sinh tên file ngẫu nhiên/an toàn.
- Không lưu ảnh base64 trong nội dung bài.
- Alt text nên có trước khi xuất bản.
- File đính kèm lưu thành bản ghi riêng liên kết bài viết.
- File private nằm ngoài web root nếu hosting cho phép; tải/xem qua controller kiểm tra quyền.

## Excel với PHP 7.3

Không ghi `composer require phpoffice/phpspreadsheet` rồi chấp nhận bản mới nhất. Các bản PhpSpreadsheet hiện đại đã nâng yêu cầu PHP, vì vậy dự án PHP 7.3 phải khóa một bản tương thích sau khi Composer kiểm tra platform.

Quy trình:

1. khai báo Composer platform PHP 7.3;
2. thử resolve version PhpSpreadsheet tương thích;
3. commit `composer.json` và `composer.lock`;
4. chạy import/export mẫu thật;
5. ghi version đã chọn vào tài liệu dự án;
6. khi nâng PHP 8.2/8.3, nâng PhpSpreadsheet riêng trên staging và regression test.

Tạo `Spreadsheet_service` hoặc library riêng; controller không trực tiếp xử lý workbook.

Import phải:

- kiểm tra extension/MIME/dung lượng;
- giới hạn số dòng;
- đọc đúng sheet/cột;
- validate từng dòng;
- trả danh sách lỗi có số dòng;
- tránh ghi dở dang ngoài ý muốn; dùng transaction khi phù hợp.

Export phải:

- cột cố định theo mẫu;
- format ngày/số rõ ràng;
- chỉ xuất dữ liệu người dùng được quyền;
- với CSV, vô hiệu hóa dữ liệu bắt đầu bằng `=`, `+`, `-`, `@` khi có nguy cơ formula injection.

## JavaScript/CSS vendor

Tabler, Jodit, PhotoSwipe và PDF.js là frontend nên không phụ thuộc trực tiếp version PHP, nhưng vẫn phải:

- khóa version;
- lưu license/notice;
- test browser;
- không copy nguyên demo có tài khoản/login giả rồi xem như auth thật.

## Nguyên tắc nâng cấp

Không để package PHP hoặc JS tự cập nhật theo `latest` trên production. Mọi nâng version phải đi qua staging, đọc changelog và regression test.
