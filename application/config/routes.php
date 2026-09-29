<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'welcome';
$route['trang-chu'] = 'welcome';
$route['gioi-thieu'] = 'welcome/about';
$route['tim-kiem'] = 'welcome/search';
$route['lien-he'] = 'welcome/contact';
$route['lien-he/gui'] = 'welcome/submitContact';
$route['dang-ky-nhan-tin'] = 'welcome/subscribe';
$route['binh-luan'] = 'welcome/comment';
foreach (array(
	'tin-tuc' => 'news',
	'thong-tin-noi-bo' => 'internal',
	'cong-bo-thong-tin' => 'disclosures',
	'san-pham' => 'products',
	'linh-vuc' => 'services',
	'du-an' => 'projects',
	'trang' => 'pages',
	'ngan-hang-anh' => 'albums',
	'video' => 'videos',
	'thanh-tich' => 'achievements',
	'hoi-dap' => 'faqs'
) as $publicPath => $publicResource)
{
	$route[$publicPath] = 'welcome/archive/' . $publicResource;
	$route[$publicPath . '/(:any)'] = 'welcome/archive/' . $publicResource . '/$1';
}
$route['admin'] = 'admin/dashboard';
$route['admin/dang-nhap'] = 'admin/auth/login';
$route['admin/dang-xuat'] = 'admin/auth/logout';
$route['admin/tin-tuc'] = 'admin/content/index/news';
$route['admin/noi-dung/(news|internal|disclosures|products|pages|albums|videos|achievements|faqs|projects|services)'] = 'admin/content/index/$1';
$route['admin/noi-dung/(news|internal|disclosures|products|pages|albums|videos|achievements|faqs|projects|services)/chi-tiet/(:num)'] = 'admin/content/show/$1/$2';
$route['admin/noi-dung/(news|internal|disclosures|products|pages|albums|videos|achievements|faqs|projects|services)/luu'] = 'admin/content/save/$1';
$route['admin/noi-dung/(news|internal|disclosures|products|pages|albums|videos|achievements|faqs|projects|services)/xoa/(:num)'] = 'admin/content/delete/$1/$2';
$route['admin/danh-muc'] = 'admin/categories/index';
$route['admin/danh-muc/chi-tiet/(:num)'] = 'admin/categories/show/$1';
$route['admin/danh-muc/luu'] = 'admin/categories/save';
$route['admin/danh-muc/xoa/(:num)'] = 'admin/categories/delete/$1';
$route['admin/sliders'] = 'admin/sliders/index';
$route['admin/sliders/chi-tiet/(:num)'] = 'admin/sliders/show/$1';
$route['admin/sliders/luu'] = 'admin/sliders/save';
$route['admin/sliders/xoa/(:num)'] = 'admin/sliders/delete/$1';
$route['admin/nguoi-dung'] = 'admin/users/index';
$route['admin/nguoi-dung/chi-tiet/(:num)'] = 'admin/users/show/$1';
$route['admin/nguoi-dung/luu'] = 'admin/users/save';
$route['admin/nguoi-dung/doi-trang-thai/(:num)'] = 'admin/users/toggle/$1';
$route['admin/nguoi-dung/xoa/(:num)'] = 'admin/users/delete/$1';
$route['admin/phan-quyen'] = 'admin/permissions/index';
$route['admin/phan-quyen/(:num)'] = 'admin/permissions/index/$1';
$route['admin/phan-quyen/(:num)/luu'] = 'admin/permissions/save/$1';
$route['admin/media/upload'] = 'admin/media/upload';
$route['admin/media'] = 'admin/media/index';
$route['admin/media/xoa/(:any)'] = 'admin/media/delete/$1';
$route['admin/menu'] = 'admin/menus/index';
$route['admin/menu/chi-tiet/(:num)'] = 'admin/menus/show/$1';
$route['admin/menu/luu'] = 'admin/menus/save';
$route['admin/menu/xoa/(:num)'] = 'admin/menus/delete/$1';
$route['admin/menu/doi-thu-tu/(:num)/(up|down)'] = 'admin/menus/move/$1/$2';
$route['admin/trang-chu'] = 'admin/home/index';
$route['admin/trang-chu/chi-tiet/(:num)'] = 'admin/home/show/$1';
$route['admin/trang-chu/luu'] = 'admin/home/save';
$route['admin/trang-chu/xoa/(:num)'] = 'admin/home/delete/$1';
$route['admin/trang-chu/doi-thu-tu/(:num)/(up|down)'] = 'admin/home/move/$1/$2';
$route['admin/cau-hinh'] = 'admin/settings/index';
$route['admin/cau-hinh/luu'] = 'admin/settings/save';
foreach (array('contacts', 'newsletter_subscribers', 'comments', 'partners', 'testimonials') as $recordRoute)
{
	$route['admin/' . $recordRoute] = 'admin/records/index/' . $recordRoute;
	$route['admin/' . $recordRoute . '/chi-tiet/(:num)'] = 'admin/records/show/' . $recordRoute . '/$1';
	$route['admin/' . $recordRoute . '/luu'] = 'admin/records/save/' . $recordRoute;
	$route['admin/' . $recordRoute . '/xoa/(:num)'] = 'admin/records/delete/' . $recordRoute . '/$1';
}
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
