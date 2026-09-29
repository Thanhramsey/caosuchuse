<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// View lỗi chạy ngoài Loader nên không chắc có url helper; lấy base_url từ config.
$base = rtrim((string) config_item('base_url'), '/') . '/';
$index = (string) config_item('index_page');
$home = $base . ($index !== '' ? $index : '');
$css = $base . 'assets/css/site.css';
?><!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex">
<title>Không tìm thấy trang</title>
<link rel="stylesheet" href="<?php echo html_escape($css); ?>">
<style>
	body { display: flex; align-items: center; justify-content: center; min-height: 100vh; background: #eee; margin: 0; font-family: Roboto, "Segoe UI", Arial, sans-serif; color: #333; }
	.error-box { max-width: 560px; margin: 24px; padding: 32px; background: #fff; border: 1px solid #dcdcdc; border-radius: 3px; text-align: center; }
	.error-box .code { font-size: 54px; font-weight: 700; color: #119a48; line-height: 1; }
	.error-box h1 { margin: 12px 0 8px; font-size: 20px; text-transform: uppercase; color: #119a48; }
	.error-box p { margin: 0 0 18px; font-size: 14px; color: #666; }
	.error-box a { display: inline-block; padding: 9px 18px; border-radius: 3px; background: #119a48; color: #fff; font-size: 13px; font-weight: 700; text-transform: uppercase; text-decoration: none; }
</style>
</head>
<body>
	<div class="error-box">
		<p class="code">404</p>
		<h1>Không tìm thấy trang</h1>
		<p>Đường dẫn không tồn tại hoặc nội dung đã được gỡ bỏ.</p>
		<a href="<?php echo html_escape($home); ?>">Về trang chủ</a>
	</div>
</body>
</html>
