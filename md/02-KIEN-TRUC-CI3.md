# Quy tắc kiến trúc CodeIgniter 3.1.13

## Mục tiêu nền tảng

- Framework: **CodeIgniter 3.1.13**.
- Hosting hiện tại: **PHP 7.3**.
- Mã ứng dụng phải được viết theo cú pháp chạy được trên PHP 7.3 nhưng đồng thời tránh các cách viết đã bị loại bỏ, cảnh báo hoặc siết chặt trong PHP 8.x.
- Mục tiêu nâng cấp: khi chuyển hosting sang PHP 8.2/8.3, phần lớn code trong `application/` không phải viết lại. Trước khi đổi PHP production vẫn phải kiểm tra CI3 và toàn bộ dependency thực tế.

## Cấu trúc mã

- `application/controllers/`: controller nhận request, kiểm tra quyền/validation, gọi model/library/service và chọn view/redirect. Không nhồi SQL hoặc HTML dài.
- `application/models/`: truy cập dữ liệu và các thao tác liên quan database.
- `application/views/`: giao diện PHP, chia layout/partial cho header, footer, menu, breadcrumb, flash message và form dùng lại.
- `application/libraries/`: logic dùng chung có trạng thái hoặc nghiệp vụ độc lập, ví dụ Auth, Upload, Spreadsheet.
- `application/helpers/`: hàm nhỏ, không trạng thái, dùng lặp lại.
- `application/core/`: chỉ dùng khi thật sự cần mở rộng `CI_Controller`, `CI_Model` hoặc core theo chuẩn `MY_*`. Không sửa trực tiếp framework.
- `application/config/`: cấu hình ứng dụng, route, database, autoload, constants, hooks khi cần.
- `application/migrations/`: migration nếu dự án quyết định quản lý schema bằng Migration Library của CI3.
- `assets/`: CSS, JS, ảnh, font và thư viện frontend được phép public.
- `uploads/`: chỉ dành cho file công khai nếu chính sách cho phép. File riêng tư nên đặt ngoài web root và phục vụ qua controller kiểm tra quyền.
- `system/`: mã framework CodeIgniter 3.1.13. **Không sửa trực tiếp** nếu không có một bản vá tương thích được ghi chép rõ ràng.

Nếu hosting cho phép, ưu tiên đặt `application/`, `system/`, file cấu hình nhạy cảm và upload riêng tư **ngoài `public_html`**, chỉ để `index.php` và tài nguyên public trong web root. Nếu hosting không cho phép, phải chặn truy cập trực tiếp bằng cấu hình web server/`.htaccess` phù hợp.

## Routing

- Route khai báo tường minh trong `application/config/routes.php` cho URL quan trọng, URL SEO, admin và endpoint upload.
- Không phụ thuộc vào việc người dùng có thể gọi tùy ý mọi public method của controller.
- Với controller admin, dùng prefix/thư mục `application/controllers/admin/` và route rõ ràng.
- Controller/action nội bộ không muốn truy cập trực tiếp phải để `protected/private` nếu có thể hoặc thiết kế không thành endpoint.
- URL public dùng slug ổn định; URL admin không cần làm đẹp bằng mọi giá nhưng phải nhất quán.

## Quy tắc viết PHP 7.3 nhưng hướng tới PHP 8.2/8.3

### Được dùng

- Cú pháp tương thích PHP 7.3: scalar type hints, return types hỗ trợ ở 7.3, anonymous class, null coalescing `??`, spaceship `<=>`, `Throwable`, `password_hash()`, `password_verify()`.
- Query Builder, query bindings và thư viện chuẩn của CI3.
- Composer nếu package được khóa version và xác nhận hỗ trợ PHP 7.3.

### Không dùng cú pháp chỉ có từ PHP 7.4/8.x

Không viết trong code production khi hosting còn PHP 7.3:

- arrow function `fn () =>`;
- typed property;
- null-coalescing assignment `??=`;
- spread trong array theo cách yêu cầu PHP mới;
- union/intersection type;
- named argument;
- attribute `#[...]`;
- `match`;
- nullsafe operator `?->`;
- constructor property promotion;
- enum;
- `readonly`;
- first-class callable syntax mới của PHP 8.1.

### Tránh từ đầu để không vỡ khi lên PHP 8.x

- Không tạo **dynamic property** tùy ý trên object. Khai báo property của class rõ ràng.
- Không dùng `each()`, `create_function()`, `money_format()`, `get_magic_quotes_gpc()` hoặc API đã bị loại khỏi PHP 8.
- Không dùng cú pháp truy cập offset chuỗi bằng `{}`; dùng `[]`.
- Không dùng `mysql_*`; dùng `mysqli` hoặc PDO thông qua Database Library của CI3.
- Không gọi `count()` trên giá trị có thể là `null`, string hoặc object không Countable.
- Không truyền `null` vào hàm nội bộ PHP nếu tham số thực tế yêu cầu string/int; chuẩn hóa dữ liệu trước.
- Không đặt tham số bắt buộc sau tham số tùy chọn.
- Không phụ thuộc vào so sánh lỏng mơ hồ giữa số và chuỗi. Chuẩn hóa kiểu dữ liệu trước khi so sánh.
- Không dùng `utf8_encode()`/`utf8_decode()` cho code mới.
- Không che warning/deprecation bằng `@` để “chạy được”; phải sửa nguyên nhân.
- Không dựa vào thứ tự tham số hoặc hành vi lỗi của hàm PHP đã thay đổi ở PHP 8.

## Quy ước class và tên file CI3

- Tên file controller/model/library tuân theo quy ước CI3, tên class bắt đầu chữ hoa.
- Controller extends `CI_Controller`; model extends `CI_Model`.
- Không áp dụng namespace `App\...` như CI4 cho controller/model CI3.
- Tên class PascalCase khi phù hợp; method/biến camelCase; bảng/cột database snake_case.
- Tránh đặt tên trùng class/core của CodeIgniter.

Ví dụ controller:

```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class News extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('News_model');
    }

    public function detail($slug = '')
    {
        $slug = trim((string) $slug);

        if ($slug === '') {
            show_404();
        }

        $news = $this->News_model->findBySlug($slug);

        if (empty($news)) {
            show_404();
        }

        $this->load->view('news/detail', array('news' => $news));
    }
}
```

## Database

Hosting của khách hàng chạy **MySQL 5.5**; môi trường dev (Laragon) dùng `mysql-5.5.41-winx64`. Schema và SQL phải chạy được trên 5.5:

- Charset `utf8mb4` / collation `utf8mb4_unicode_ci`; không dùng `utf8mb4_0900_ai_ci` (chỉ có từ 8.0).
- Index trên cột chuỗi `utf8mb4` tối đa 767 byte: `VARCHAR` được index dài tối đa 191 (ví dụ `slug VARCHAR(191)`).
- Không dùng kiểu `JSON`, cột generated, CTE (`WITH`), window function, `CHECK`.
- `DATETIME` không có `DEFAULT CURRENT_TIMESTAMP`; mỗi bảng tối đa một cột `TIMESTAMP` dùng `CURRENT_TIMESTAMP`. Ưu tiên gán `created_at`/`updated_at` từ PHP.
- FULLTEXT chỉ có với MyISAM ở 5.5; bảng InnoDB tìm kiếm bằng `LIKE` có bind và phân trang.
- Engine InnoDB cho bảng có transaction/foreign key.
- Dump/import giữa máy dev và hosting phải xuất tương thích 5.5, không mang collation của MySQL 8 sang.

- Ưu tiên Query Builder của CI3.
- SQL viết tay phải dùng query bindings, không nối input người dùng trực tiếp vào SQL.
- Cấu hình database production không commit password.
- Dùng transaction cho thao tác nhiều bảng.
- Unique/index/foreign key phải được thiết kế theo nhu cầu thật.
- Tránh `SELECT *` ở truy vấn danh sách lớn hoặc luồng quan trọng.
- Phân trang ở database, không tải toàn bộ dữ liệu rồi cắt bằng PHP.

## Cấu hình môi trường

CI3 không có `.env` native như CI4. Dùng một trong các cách đã thống nhất:

1. biến môi trường của hosting/server;
2. file cấu hình riêng không commit, ví dụ `application/config/config.production.php`;
3. constants/config đọc từ `getenv()` với fallback an toàn.

Không hardcode DB password, SMTP password, API key hoặc tài khoản admin trong repository.

## Composer và dependency

- Nếu dùng Composer, bật `$config['composer_autoload']` theo đường dẫn thực tế.
- `composer.json` phải đặt platform PHP là `7.3.x` khi resolve dependency cho hosting hiện tại để tránh cài nhầm package yêu cầu PHP mới.
- Luôn commit `composer.lock`.
- Không chạy `composer update` trực tiếp trên production.
- Khi nâng PHP, tạo branch nâng cấp, đổi platform version, chạy `composer update` có kiểm soát và test lại.

Ví dụ định hướng:

```json
{
  "config": {
    "platform": {
      "php": "7.3.33"
    },
    "sort-packages": true
  }
}
```

## Mỗi tính năng mới

Trước khi code phải xác định: route, controller, validation, model/schema, view, quyền truy cập, CSRF, xử lý lỗi, log cần thiết và kiểm tra tương ứng. Chỉ tạo bảng/cột khi chức năng thật sự cần lưu dữ liệu.
