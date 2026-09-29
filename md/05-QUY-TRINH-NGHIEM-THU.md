# Quy trình làm việc cho AI

## Trước khi sửa code

1. Đọc yêu cầu hiện tại, `mdcode/README.md` và các rule liên quan; xác định site I hay II.
2. Xem file đang ảnh hưởng và trạng thái dự án. Giữ thay đổi của người khác, không ghi đè tùy tiện.
3. Ghi ngắn phạm vi tính năng, dữ liệu cần có, route/màn hình và điểm chưa rõ. Hỏi khi quyết định đó ảnh hưởng hành vi sản phẩm, quyền truy cập hoặc dữ liệu thật.

## Trong khi sửa

- Làm thay đổi nhỏ, đầy đủ từ route đến giao diện/validation/database theo nhu cầu thực tế.
- Không sửa framework `system/`, không thêm dependency hoặc dịch vụ ngoài nếu chưa cần; nếu thêm, giải thích lý do và cấu hình.
- Cập nhật rule/tài liệu khi quyết định kiến trúc hoặc phạm vi đã được duyệt thay đổi.

## Kiểm tra tối thiểu

- Với PHP vừa sửa: chạy `php -l` cho các file đó nếu PHP CLI có sẵn.
- Nếu dependency đã cài: chạy test liên quan qua `vendor/bin/phpunit` hoặc `composer test`; không nói test đạt khi môi trường không chạy được.
- Với migration: chạy trên database phát triển, kiểm tra `up` và khả năng rollback an toàn; không tự chạy trên production.
- Kiểm tra thủ công luồng thành công và lỗi: URL không tồn tại, input sai, dữ liệu rỗng, thiếu quyền, CSRF, pagination, màn hình mobile.
- Kiểm tra tìm kiếm, form liên hệ, bình luận, upload và SEO theo phần đã thực sự triển khai; không đánh dấu hoàn tất chỉ vì có giao diện.

## Báo cáo khi hoàn thành

Nêu file đã đổi, chức năng đã chạy, lệnh kiểm tra và kết quả thực tế, phần chưa được kiểm tra, cùng giả định còn chờ xác nhận. Phân biệt rõ tính năng thật với bản demo/placeholder.
