<?php defined('BASEPATH') OR exit('No direct script access allowed');
$menu = array(
	array('heading' => 'Tổng quan'),
	array('key' => 'dashboard', 'label' => 'Bảng điều khiển', 'icon' => 'home', 'url' => 'admin', 'permission' => NULL),
	array('heading' => 'Nội dung'),
	array('key' => 'news', 'label' => 'Tin tức', 'icon' => 'news', 'url' => 'admin/noi-dung/news', 'permission' => 'news.view'),
	array('key' => 'projects', 'label' => 'Dự án', 'icon' => 'projects', 'url' => 'admin/noi-dung/projects', 'permission' => 'projects.manage'),
	array('key' => 'services', 'label' => 'Dịch vụ', 'icon' => 'services', 'url' => 'admin/noi-dung/services', 'permission' => 'services.manage'),
	array('key' => 'internal', 'label' => 'Thông tin nội bộ', 'icon' => 'news', 'url' => 'admin/noi-dung/internal', 'permission' => 'internal.manage'),
	array('key' => 'disclosures', 'label' => 'Công bố thông tin', 'icon' => 'file', 'url' => 'admin/noi-dung/disclosures', 'permission' => 'disclosures.manage'),
	array('key' => 'products', 'label' => 'Sản phẩm', 'icon' => 'box', 'url' => 'admin/noi-dung/products', 'permission' => 'products.manage'),
	array('key' => 'pages', 'label' => 'Trang tĩnh', 'icon' => 'page', 'url' => 'admin/noi-dung/pages', 'permission' => 'pages.manage'),
	array('key' => 'albums', 'label' => 'Ngân hàng ảnh', 'icon' => 'photo', 'url' => 'admin/noi-dung/albums', 'permission' => 'albums.manage'),
	array('key' => 'videos', 'label' => 'Video', 'icon' => 'video', 'url' => 'admin/noi-dung/videos', 'permission' => 'videos.manage'),
	array('key' => 'achievements', 'label' => 'Thành tích', 'icon' => 'award', 'url' => 'admin/noi-dung/achievements', 'permission' => 'achievements.manage'),
	array('key' => 'faqs', 'label' => 'Hỏi đáp', 'icon' => 'help', 'url' => 'admin/noi-dung/faqs', 'permission' => 'faqs.manage'),
	array('key' => 'categories', 'label' => 'Danh mục', 'icon' => 'folder', 'url' => 'admin/danh-muc', 'permission' => 'categories.manage'),
	array('heading' => 'Giao diện'),
	array('key' => 'sliders', 'label' => 'Slider trang chủ', 'icon' => 'photo', 'url' => 'admin/sliders', 'permission' => 'sliders.manage'),
	array('key' => 'media', 'label' => 'Media / Tệp', 'icon' => 'photo', 'url' => 'admin/media', 'permission' => 'media.upload'),
	array('key' => 'home', 'label' => 'Nội dung trang chủ', 'icon' => 'home', 'url' => 'admin/trang-chu', 'permission' => 'home.manage'),
	array('key' => 'menus', 'label' => 'Quản lý menu', 'icon' => 'menu', 'url' => 'admin/menu', 'permission' => 'menus.manage'),
	array('key' => 'partners', 'label' => 'Đối tác', 'icon' => 'users', 'url' => 'admin/partners', 'permission' => 'partners.manage'),
	array('key' => 'testimonials', 'label' => 'Ý kiến khách hàng', 'icon' => 'news', 'url' => 'admin/testimonials', 'permission' => 'testimonials.manage'),
	array('key' => 'settings', 'label' => 'Cấu hình website', 'icon' => 'device', 'url' => 'admin/cau-hinh', 'permission' => 'settings.manage'),
	array('heading' => 'Tương tác'),
	array('key' => 'contacts', 'label' => 'Liên hệ', 'icon' => 'news', 'url' => 'admin/contacts', 'permission' => 'contacts.manage'),
	array('key' => 'newsletter_subscribers', 'label' => 'Đăng ký nhận tin', 'icon' => 'users', 'url' => 'admin/newsletter_subscribers', 'permission' => 'newsletter.manage'),
	array('key' => 'comments', 'label' => 'Bình luận', 'icon' => 'news', 'url' => 'admin/comments', 'permission' => 'comments.moderate'),
	array('heading' => 'Người dùng & quyền'),
	array('key' => 'users', 'label' => 'Người dùng', 'icon' => 'users', 'url' => 'admin/nguoi-dung', 'permission' => 'users.view'),
	array('key' => 'permissions', 'label' => 'Phân quyền', 'icon' => 'shield', 'url' => 'admin/phan-quyen', 'permission' => 'users.assign_role')
);
$visibleMenu = array();
$pendingHeading = NULL;
foreach ($menu as $entry)
{
	if (isset($entry['heading']))
	{
		$pendingHeading = $entry;
		continue;
	}
	if ($entry['permission'] !== NULL && ! admin_can($entry['permission']))
	{
		continue;
	}
	if ($pendingHeading !== NULL)
	{
		$visibleMenu[] = $pendingHeading;
		$pendingHeading = NULL;
	}
	$visibleMenu[] = $entry;
}
$userName = isset($currentUser['full_name']) ? $currentUser['full_name'] : '';
$userEmail = isset($currentUser['email']) ? $currentUser['email'] : '';
?><!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex, nofollow">
	<meta name="csrf-name" content="<?php echo html_escape($this->security->get_csrf_token_name()); ?>">
	<meta name="csrf-hash" content="<?php echo html_escape($this->security->get_csrf_hash()); ?>">
	<title><?php echo html_escape($pageTitle); ?> · Quản trị Cao su Chư Sê</title>
	<link rel="icon" href="<?php echo base_url('assets/images/logo.png'); ?>">
	<link rel="stylesheet" href="<?php echo base_url('assets/vendor/tabler/1.4.0/tabler.min.css'); ?>">
	<?php if ($useEditor): ?><link rel="stylesheet" href="<?php echo base_url('assets/vendor/jodit/4.2.27/jodit.min.css'); ?>"><?php endif; ?>
	<link rel="stylesheet" href="<?php echo base_url('assets/css/admin.css'); ?>">
</head>
<body class="admin-body" data-base-url="<?php echo html_escape(base_url()); ?>" data-upload-url="<?php echo html_escape(site_url('admin/media/upload')); ?>" data-flash-success="<?php echo html_escape($flashSuccess); ?>" data-flash-error="<?php echo html_escape($flashError); ?>">
<div class="page">
	<aside class="navbar navbar-vertical navbar-expand-lg admin-sidebar" data-bs-theme="dark">
		<div class="container-fluid">
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false" aria-label="Mở menu">
				<span class="navbar-toggler-icon"></span>
			</button>
			<a class="navbar-brand admin-brand" href="<?php echo site_url('admin'); ?>">
				<img src="<?php echo base_url('assets/images/logo.png'); ?>" alt="" width="34" height="39">
				<span>Cao su Chư Sê<small>Quản trị nội dung</small></span>
			</a>
			<div class="collapse navbar-collapse" id="sidebar-menu">
				<ul class="navbar-nav pt-lg-2">
					<?php foreach ($visibleMenu as $entry): ?>
						<?php if (isset($entry['heading'])): ?>
							<li class="nav-item admin-nav-heading"><?php echo html_escape($entry['heading']); ?></li>
						<?php else: ?>
							<li class="nav-item<?php echo $activeMenu === $entry['key'] ? ' active' : ''; ?>">
								<a class="nav-link" href="<?php echo site_url($entry['url']); ?>"<?php echo $activeMenu === $entry['key'] ? ' aria-current="page"' : ''; ?>>
									<span class="nav-link-icon"><?php echo admin_icon($entry['icon']); ?></span>
									<span class="nav-link-title"><?php echo html_escape($entry['label']); ?></span>
								</a>
							</li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
				<div class="admin-sidebar-footer">
					<a class="nav-link" href="<?php echo base_url(); ?>" target="_blank" rel="noopener"><span class="nav-link-icon"><?php echo admin_icon('external'); ?></span><span class="nav-link-title">Xem website</span></a>
					<a class="nav-link" href="<?php echo site_url('admin/dang-xuat'); ?>"><span class="nav-link-icon"><?php echo admin_icon('logout'); ?></span><span class="nav-link-title">Đăng xuất</span></a>
				</div>
			</div>
		</div>
	</aside>
	<div class="page-wrapper">
		<header class="navbar navbar-expand-md d-none d-lg-flex d-print-none admin-topbar">
			<div class="container-xl">
				<div class="text-secondary small admin-breadcrumb">Quản trị <span aria-hidden="true">/</span> <strong><?php echo html_escape($pageTitle); ?></strong></div>
				<div class="navbar-nav flex-row order-md-last ms-auto">
					<div class="nav-item dropdown">
						<a href="#" class="nav-link d-flex lh-1 p-0 px-2" data-bs-toggle="dropdown" aria-label="Mở menu tài khoản">
							<span class="avatar avatar-sm admin-avatar"><?php echo html_escape(admin_initials($userName)); ?></span>
							<div class="d-none d-xl-block ps-2">
								<div class="fw-medium"><?php echo html_escape($userName); ?></div>
								<div class="mt-1 small text-secondary"><?php echo html_escape($userEmail); ?></div>
							</div>
						</a>
						<div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
							<a href="<?php echo base_url(); ?>" class="dropdown-item" target="_blank" rel="noopener">Xem website</a>
							<div class="dropdown-divider"></div>
							<a href="<?php echo site_url('admin/dang-xuat'); ?>" class="dropdown-item text-danger">Đăng xuất</a>
						</div>
					</div>
				</div>
			</div>
		</header>
