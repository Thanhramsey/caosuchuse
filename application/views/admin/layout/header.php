<?php defined('BASEPATH') OR exit('No direct script access allowed');
$menuGroups = array(
	array('key' => 'overview', 'label' => 'Tổng quan', 'icon' => 'home', 'url' => 'admin', 'active' => 'dashboard'),
	array('key' => 'content', 'label' => 'Quản lý nội dung', 'icon' => 'news', 'items' => array(
		array('key' => 'news', 'label' => 'Tin tức', 'url' => 'admin/noi-dung/news', 'permission' => 'news.view'),
		array('key' => 'projects', 'label' => 'Dự án', 'url' => 'admin/noi-dung/projects', 'permission' => 'projects.manage'),
		array('key' => 'services', 'label' => 'Lĩnh vực / Dịch vụ', 'url' => 'admin/noi-dung/services', 'permission' => 'services.manage'),
		array('key' => 'products', 'label' => 'Sản phẩm', 'url' => 'admin/noi-dung/products', 'permission' => 'products.manage'),
		array('key' => 'pages', 'label' => 'Trang tĩnh', 'url' => 'admin/noi-dung/pages', 'permission' => 'pages.manage'),
		array('key' => 'internal', 'label' => 'Thông tin nội bộ', 'url' => 'admin/noi-dung/internal', 'permission' => 'internal.manage'),
		array('key' => 'disclosures', 'label' => 'Công bố thông tin', 'url' => 'admin/noi-dung/disclosures', 'permission' => 'disclosures.manage'),
		array('key' => 'achievements', 'label' => 'Thành tích', 'url' => 'admin/noi-dung/achievements', 'permission' => 'achievements.manage'),
		array('key' => 'faqs', 'label' => 'Hỏi đáp', 'url' => 'admin/noi-dung/faqs', 'permission' => 'faqs.manage'),
		array('key' => 'categories', 'label' => 'Danh mục nội dung', 'url' => 'admin/danh-muc', 'permission' => 'categories.manage')
	)),
	array('key' => 'library', 'label' => 'Thư viện', 'icon' => 'photo', 'items' => array(
		array('key' => 'media', 'label' => 'Media / Tệp', 'url' => 'admin/media', 'permission' => 'media.upload'),
		array('key' => 'albums', 'label' => 'Ngân hàng ảnh', 'url' => 'admin/noi-dung/albums', 'permission' => 'albums.manage'),
		array('key' => 'videos', 'label' => 'Video', 'url' => 'admin/noi-dung/videos', 'permission' => 'videos.manage')
	)),
	array('key' => 'appearance', 'label' => 'Giao diện website', 'icon' => 'device', 'items' => array(
		array('key' => 'home', 'label' => 'Nội dung trang chủ', 'url' => 'admin/trang-chu', 'permission' => 'home.manage'),
		array('key' => 'sliders', 'label' => 'Slider trang chủ', 'url' => 'admin/sliders', 'permission' => 'sliders.manage'),
		array('key' => 'menus', 'label' => 'Menu website', 'url' => 'admin/menu', 'permission' => 'menus.manage'),
		array('key' => 'partners', 'label' => 'Đối tác', 'url' => 'admin/partners', 'permission' => 'partners.manage'),
		array('key' => 'testimonials', 'label' => 'Ý kiến khách hàng', 'url' => 'admin/testimonials', 'permission' => 'testimonials.manage')
	)),
	array('key' => 'interaction', 'label' => 'Tương tác', 'icon' => 'users', 'items' => array(
		array('key' => 'contacts', 'label' => 'Liên hệ', 'url' => 'admin/contacts', 'permission' => 'contacts.manage'),
		array('key' => 'newsletter_subscribers', 'label' => 'Đăng ký nhận tin', 'url' => 'admin/newsletter_subscribers', 'permission' => 'newsletter.manage'),
		array('key' => 'comments', 'label' => 'Bình luận', 'url' => 'admin/comments', 'permission' => 'comments.moderate')
	)),
	array('key' => 'system', 'label' => 'Hệ thống', 'icon' => 'shield', 'items' => array(
		array('key' => 'settings', 'label' => 'Cấu hình website', 'url' => 'admin/cau-hinh', 'permission' => 'settings.manage'),
		array('key' => 'users', 'label' => 'Người dùng', 'url' => 'admin/nguoi-dung', 'permission' => 'users.view'),
		array('key' => 'permissions', 'label' => 'Vai trò & phân quyền', 'url' => 'admin/phan-quyen', 'permission' => 'users.assign_role')
	))
);
$visibleMenu = array();
foreach ($menuGroups as $group)
{
	if (! isset($group['items']))
	{
		$visibleMenu[] = $group;
		continue;
	}
	$items = array();
	$group['is_active'] = FALSE;
	foreach ($group['items'] as $item)
	{
		if ($item['permission'] !== NULL && ! admin_can($item['permission'])) continue;
		$items[] = $item;
		if ($activeMenu === $item['key']) $group['is_active'] = TRUE;
	}
	if (! empty($items))
	{
		$group['items'] = $items;
		$visibleMenu[] = $group;
	}
}
$activeGroupLabel = 'Tổng quan';
foreach ($visibleMenu as $group)
{
	if (! isset($group['items']) && $activeMenu === $group['active'])
	{
		$activeGroupLabel = $group['label'];
		break;
	}
	foreach (isset($group['items']) ? $group['items'] : array() as $item)
	{
		if ($activeMenu === $item['key'])
		{
			$activeGroupLabel = $group['label'];
			break 2;
		}
	}
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
	<link rel="stylesheet" href="<?php echo admin_asset('assets/css/admin.css'); ?>">
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
						<?php if (! isset($entry['items'])): ?>
						<li class="nav-item<?php echo $activeMenu === $entry['active'] ? ' active' : ''; ?>">
							<a class="nav-link" href="<?php echo site_url($entry['url']); ?>"<?php echo $activeMenu === $entry['active'] ? ' aria-current="page"' : ''; ?>>
								<span class="nav-link-icon"><?php echo admin_icon($entry['icon']); ?></span>
								<span class="nav-link-title"><?php echo html_escape($entry['label']); ?></span>
							</a>
						</li>
						<?php else: $collapseId = 'admin-nav-' . $entry['key']; ?>
						<li class="nav-item admin-nav-group<?php echo $entry['is_active'] ? ' active is-open' : ''; ?>">
							<button class="nav-link admin-nav-parent" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $collapseId; ?>" aria-expanded="<?php echo $entry['is_active'] ? 'true' : 'false'; ?>" aria-controls="<?php echo $collapseId; ?>">
								<span class="nav-link-icon"><?php echo admin_icon($entry['icon']); ?></span>
								<span class="nav-link-title"><?php echo html_escape($entry['label']); ?></span>
								<span class="admin-nav-caret" aria-hidden="true">›</span>
							</button>
							<div class="collapse admin-nav-children<?php echo $entry['is_active'] ? ' show' : ''; ?>" id="<?php echo $collapseId; ?>">
								<ul class="nav nav-pills flex-column">
									<?php foreach ($entry['items'] as $item): ?>
									<li class="nav-item<?php echo $activeMenu === $item['key'] ? ' active' : ''; ?>"><a class="nav-link admin-nav-child" href="<?php echo site_url($item['url']); ?>"<?php echo $activeMenu === $item['key'] ? ' aria-current="page"' : ''; ?>><span class="admin-nav-dot"></span><span class="nav-link-title"><?php echo html_escape($item['label']); ?></span></a></li>
									<?php endforeach; ?>
								</ul>
							</div>
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
				<div class="text-secondary small admin-breadcrumb">Quản trị <span aria-hidden="true">/</span> <?php echo html_escape($activeGroupLabel); ?><?php if ($pageTitle !== $activeGroupLabel): ?> <span aria-hidden="true">/</span> <strong><?php echo html_escape($pageTitle); ?></strong><?php endif; ?></div>
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
