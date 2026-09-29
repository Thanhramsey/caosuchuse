<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (! function_exists('admin_icon'))
{
	function admin_icon($name, $class = 'icon')
	{
		static $icons = array(
			'home' => '<path d="M5 12l-2 0l9 -9l9 9l-2 0"/><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7"/><path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6"/>',
			'menu' => '<path d="M4 6l16 0"/><path d="M4 12l16 0"/><path d="M4 18l16 0"/>',
			'news' => '<path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/><path d="M9 9l1 0"/><path d="M9 13l6 0"/><path d="M9 17l6 0"/>',
			'projects' => '<path d="M3 9a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v9a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z"/><path d="M8 7v-2a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v2"/><path d="M12 12l0 .01"/><path d="M3 13a20 20 0 0 0 18 0"/>',
			'services' => '<path d="M5 21c.5 -4.5 2.5 -8 7 -10"/><path d="M9 18c6.218 0 10.5 -3.288 11 -12v-2h-4.014c-9 0 -11.986 4 -12 9c0 1 0 3 2 5h3z"/>',
			'folder' => '<path d="M5 4h4l3 3h7a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-11a2 2 0 0 1 2 -2"/>',
			'photo' => '<path d="M15 8h.01"/><path d="M3 6a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v12a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-12z"/><path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l5 5"/><path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l3 3"/>',
			'users' => '<path d="M5 7a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"/><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/><path d="M21 21v-2a4 4 0 0 0 -3 -3.85"/>',
			'shield' => '<path d="M12 3a12 12 0 0 0 8.5 3a12 12 0 0 1 -8.5 15a12 12 0 0 1 -8.5 -15a12 12 0 0 0 8.5 -3"/><path d="M9 12l2 2l4 -4"/>',
			'logout' => '<path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2"/><path d="M9 12h12l-3 -3"/><path d="M18 15l3 -3"/>',
			'plus' => '<path d="M12 5l0 14"/><path d="M5 12l14 0"/>',
			'edit' => '<path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4"/><path d="M13.5 6.5l4 4"/>',
			'trash' => '<path d="M4 7l16 0"/><path d="M10 11l0 6"/><path d="M14 11l0 6"/><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/>',
			'search' => '<path d="M3 10a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"/><path d="M21 21l-6 -6"/>',
			'lock' => '<path d="M5 13a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-6z"/><path d="M11 16a1 1 0 1 0 2 0a1 1 0 0 0 -2 0"/><path d="M8 11v-4a4 4 0 1 1 8 0v4"/>',
			'unlock' => '<path d="M5 13a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-6z"/><path d="M11 16a1 1 0 1 0 2 0a1 1 0 0 0 -2 0"/><path d="M8 11v-5a4 4 0 0 1 8 0"/>',
			'upload' => '<path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"/><path d="M7 9l5 -5l5 5"/><path d="M12 4l0 12"/>',
			'external' => '<path d="M12 6h-6a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-6"/><path d="M11 13l9 -9"/><path d="M15 4h5v5"/>',
			'x' => '<path d="M18 6l-12 12"/><path d="M6 6l12 12"/>',
			'filter' => '<path d="M4 4h16v2.172a2 2 0 0 1 -.586 1.414l-4.414 4.414v7l-6 2v-8.5l-4.48 -4.928a2 2 0 0 1 -.52 -1.345v-2.227z"/>',
			'device' => '<path d="M3 5a1 1 0 0 1 1 -1h16a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-16a1 1 0 0 1 -1 -1v-10z"/><path d="M7 20h10"/><path d="M9 16v4"/><path d="M15 16v4"/>'
		);
		$paths = isset($icons[$name]) ? $icons[$name] : '';
		return '<svg xmlns="http://www.w3.org/2000/svg" class="' . $class . '" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>' . $paths . '</svg>';
	}
}

if (! function_exists('admin_can'))
{
	function admin_can($permission)
	{
		$CI =& get_instance();
		return $CI->admin_auth->can($permission);
	}
}

if (! function_exists('admin_date'))
{
	function admin_date($value, $format = 'd/m/Y H:i')
	{
		$timestamp = strtotime((string) $value);
		return $timestamp ? date($format, $timestamp) : '—';
	}
}

if (! function_exists('admin_initials'))
{
	function admin_initials($name)
	{
		$parts = preg_split('/\s+/u', trim((string) $name));
		$last = end($parts);
		if ($last === FALSE || $last === '')
		{
			return '?';
		}
		return function_exists('mb_substr') ? mb_strtoupper(mb_substr($last, 0, 1, 'UTF-8'), 'UTF-8') : strtoupper(substr($last, 0, 1));
	}
}

if (! function_exists('admin_role_label'))
{
	function admin_role_label($name)
	{
		$labels = array('superadmin' => 'Quản trị toàn hệ thống', 'content_manager' => 'Quản lý nội dung', 'editor' => 'Biên tập viên', 'support' => 'Hỗ trợ');
		return isset($labels[$name]) ? $labels[$name] : (string) $name;
	}
}

if (! function_exists('admin_permission_label'))
{
	function admin_permission_label($name)
	{
		$labels = array(
			'news.view' => 'Xem danh sách tin tức',
			'news.create' => 'Tạo tin tức',
			'news.update_own' => 'Sửa tin do mình tạo',
			'news.update_any' => 'Sửa mọi tin tức',
			'news.publish' => 'Xuất bản tin tức',
			'news.delete' => 'Xóa tin tức',
			'projects.manage' => 'Quản lý dự án',
			'services.manage' => 'Quản lý dịch vụ',
			'categories.manage' => 'Quản lý danh mục',
			'sliders.manage' => 'Quản lý slider',
			'media.upload' => 'Tải lên ảnh/tệp',
			'media.delete' => 'Xóa ảnh/tệp',
			'comments.moderate' => 'Duyệt bình luận',
			'contacts.view' => 'Xem liên hệ',
			'contacts.manage' => 'Xử lý liên hệ',
			'users.view' => 'Xem người dùng',
			'users.create' => 'Tạo/sửa/khóa người dùng',
			'users.assign_role' => 'Gán vai trò và phân quyền',
			'settings.manage' => 'Quản lý cấu hình',
			'contacts.manage' => 'Quản lý liên hệ',
			'newsletter.manage' => 'Quản lý đăng ký nhận tin',
			'comments.moderate' => 'Duyệt bình luận',
			'partners.manage' => 'Quản lý đối tác',
			'testimonials.manage' => 'Quản lý ý kiến khách hàng'
			, 'menus.manage' => 'Quản lý menu'
			, 'home.manage' => 'Quản lý nội dung trang chủ'
		);
		return isset($labels[$name]) ? $labels[$name] : (string) $name;
	}
}

if (! function_exists('admin_permission_group'))
{
	function admin_permission_group($name)
	{
		$groups = array('news' => 'Tin tức', 'projects' => 'Dự án', 'services' => 'Dịch vụ', 'categories' => 'Danh mục', 'sliders' => 'Giao diện', 'media' => 'Media / Tệp', 'comments' => 'Tương tác', 'contacts' => 'Tương tác', 'newsletter' => 'Tương tác', 'users' => 'Người dùng & quyền', 'settings' => 'Cấu hình', 'menus' => 'Giao diện', 'home' => 'Giao diện', 'partners' => 'Giao diện', 'testimonials' => 'Giao diện');
		$prefix = strstr((string) $name, '.', TRUE);
		return isset($groups[$prefix]) ? $groups[$prefix] : 'Khác';
	}
}
