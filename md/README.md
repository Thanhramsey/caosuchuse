# Quy tắc AI code - Cao su Chư Sê

Thư mục này là bộ chỉ dẫn cho AI khi phát triển ứng dụng tại `caosuchuse/` bằng:

- **CodeIgniter 3.1.13**
- **PHP 7.3 trên hosting hiện tại**
- mục tiêu giữ code ứng dụng tương thích tốt để sau này nâng lên **PHP 8.2/8.3**

Đọc theo thứ tự:

1. [01-PHAM-VI-BAO-GIA.md](01-PHAM-VI-BAO-GIA.md) - phạm vi đã được báo giá và các điểm cần xác nhận.
2. [02-KIEN-TRUC-CI3.md](02-KIEN-TRUC-CI3.md) - cấu trúc CI3, coding standard và quy tắc tương thích PHP 7.3 -> 8.x.
3. [03-NOI-DUNG-GIAO-DIEN.md](03-NOI-DUNG-GIAO-DIEN.md) - nội dung, SEO, view và giao diện.
4. [04-BAO-MAT-DU-LIEU.md](04-BAO-MAT-DU-LIEU.md) - validation, CSRF, auth, quyền, upload và dữ liệu.
5. [05-QUY-TRINH-NGHIEM-THU.md](05-QUY-TRINH-NGHIEM-THU.md) - cách AI thực hiện, test PHP 7.3 và kiểm tra trước khi nâng PHP.
6. [06-ADMIN-EDITOR-TEP-EXCEL-PHAN-QUYEN.md](06-ADMIN-EDITOR-TEP-EXCEL-PHAN-QUYEN.md) - admin, editor, file, Excel và RBAC cho CI3.

## Thứ tự ưu tiên

Yêu cầu trực tiếp của chủ dự án > báo giá `D:/project/caosu/10.BG - Web.pdf` > quy tắc trong thư mục này > bản demo tĩnh ở `D:/project/caosu/` > mặc định framework.

Nếu hai nguồn mâu thuẫn, nêu rõ mâu thuẫn trước khi thay đổi hành vi sản phẩm.

## Chuẩn môi trường

### Production hiện tại

- CodeIgniter 3.1.13.
- PHP 7.3.
- Code production phải parse và chạy được trên PHP 7.3.
- Không dùng syntax PHP 7.4/8.x khi production vẫn còn 7.3.

### Mục tiêu tương lai

Code mới phải tránh từ đầu những pattern dễ vỡ trên PHP 8.2/8.3, đặc biệt:

- dynamic property;
- API PHP đã bị loại;
- `count()` trên dữ liệu không xác định kiểu;
- truyền `null` bừa vào hàm nội bộ;
- optional parameter đứng trước required parameter;
- so sánh lỏng mơ hồ;
- suppress warning/deprecation thay vì sửa lỗi.

Trước khi đổi version PHP hosting, bắt buộc test trên staging. Không được tuyên bố “CI3 chắc chắn chạy PHP 8.3” chỉ dựa vào việc application code không dùng syntax mới.

## Cấu trúc dự án định hướng

```text
caosuchuse/
├─ application/
│  ├─ config/
│  ├─ controllers/
│  │  └─ admin/
│  ├─ core/
│  ├─ helpers/
│  ├─ libraries/
│  ├─ migrations/
│  ├─ models/
│  └─ views/
│     └─ admin/
├─ assets/
│  └─ vendor/
├─ system/
├─ uploads/          # chỉ file public nếu chính sách cho phép
├─ index.php
├─ .htaccess
├─ composer.json     # nếu dùng Composer
└─ composer.lock     # bắt buộc commit nếu dùng Composer
```

Không sửa `system/` cho nghiệp vụ.

## Quy tắc cho AI

Khi nhận task code:

1. đọc `mdcode/README.md`;
2. đọc file rule liên quan;
3. giữ code tương thích PHP 7.3;
4. tránh pattern không tương thích PHP 8.x;
5. không tự thêm module ngoài phạm vi;
6. không tự cài dependency mới nếu chưa cần;
7. không dùng CodeIgniter 4 API, namespace, Shield, filters, `app/Config/*`, `writable/` hoặc cấu trúc CI4 trong project này;
8. với admin/auth, tuân theo `06-ADMIN-EDITOR-TEP-EXCEL-PHAN-QUYEN.md`;
9. báo rõ những gì thực sự đã test.

## Ghi chú quan trọng

CodeIgniter 3.1.13 là nhánh CI3 cũ. Việc viết application code theo chuẩn tương thích giúp giảm đáng kể công sức khi nâng PHP, nhưng khi chuyển từ PHP 7.3 lên 8.2/8.3 vẫn phải kiểm tra:

- CI3 core;
- session;
- database driver;
- Composer dependencies;
- editor/upload/Excel/mail;
- warning/deprecation trong PHP mới.

Không nâng PHP trực tiếp trên production mà chưa test staging và có rollback.
