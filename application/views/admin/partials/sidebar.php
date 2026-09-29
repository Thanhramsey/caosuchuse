<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<aside class="admin-sidebar" aria-label="Điều hướng quản trị">
	<a class="admin-sidebar-brand" href="<?php echo site_url('admin'); ?>"><span class="admin-sidebar-mark">CS</span><span>Cao su Chư Sê<small>Quản trị hệ thống</small></span></a>
	<nav class="admin-sidebar-nav">
		<p>Tổng quan</p>
		<a href="<?php echo site_url('admin'); ?>">Dashboard</a>
		<p>Nội dung</p>
		<a href="<?php echo site_url('admin/noi-dung/news'); ?>">Tin tức</a>
		<a href="<?php echo site_url('admin/noi-dung/projects'); ?>">Dự án</a>
		<a href="<?php echo site_url('admin/noi-dung/services'); ?>">Dịch vụ</a>
		<p>Giao diện</p>
		<a href="<?php echo site_url('admin/sliders'); ?>">Slider trang chủ</a>
		<p>Người dùng & quyền</p>
		<a href="<?php echo site_url('admin/nguoi-dung'); ?>">Người dùng</a>
		<a href="<?php echo site_url('admin/phan-quyen'); ?>">Phân quyền</a>
	</nav>
	<a class="admin-sidebar-logout" href="<?php echo site_url('admin/dang-xuat'); ?>">Đăng xuất</a>
</aside>
<script>document.addEventListener('DOMContentLoaded', function () { document.body.classList.add('admin-shell-page'); });</script>
