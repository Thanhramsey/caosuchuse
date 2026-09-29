<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (! function_exists('public_label'))
{
	/** Trả nhãn tiếng Anh nếu có, ngược lại dùng nhãn gốc. */
	function public_label($row, $field, $lang)
	{
		if ($lang === 'en' && ! empty($row[$field . '_en']))
		{
			return (string) $row[$field . '_en'];
		}
		return isset($row[$field]) ? (string) $row[$field] : '';
	}
}

if (! function_exists('public_lang_url'))
{
	function public_lang_url($code)
	{
		$query = $_GET;
		$query['lang'] = $code;
		return site_url(uri_string()) . '?' . http_build_query($query);
	}
}

if (! function_exists('public_asset'))
{
	function public_asset($path)
	{
		$full = FCPATH . $path;
		return base_url($path) . '?v=' . (is_file($full) ? filemtime($full) : 0);
	}
}

if (! function_exists('public_safe_url'))
{
	/**
	 * Chỉ cho phép URL nội bộ hoặc http(s) để tránh javascript:/data: trong dữ liệu quản trị.
	 */
	function public_safe_url($url)
	{
		$url = trim((string) $url);
		if ($url === '')
		{
			return '#';
		}
		if ($url[0] === '/' || $url[0] === '#')
		{
			return $url[0] === '#' ? $url : site_url(ltrim($url, '/'));
		}
		return preg_match('~^https?://~i', $url) ? $url : '#';
	}
}

if (! function_exists('public_media_url'))
{
	function public_media_url($path, $fallback = '')
	{
		$path = trim((string) $path);
		if ($path === '')
		{
			return $fallback;
		}
		if (preg_match('~^https?://~i', $path))
		{
			return $path;
		}
		return base_url(ltrim($path, '/'));
	}
}

if (! function_exists('public_content_url'))
{
	function public_content_url($resource, $slug)
	{
		$paths = array(
			'news' => 'tin-tuc', 'internal' => 'thong-tin-noi-bo', 'disclosures' => 'cong-bo-thong-tin',
			'products' => 'san-pham', 'services' => 'linh-vuc', 'projects' => 'du-an', 'pages' => 'trang',
			'albums' => 'ngan-hang-anh', 'videos' => 'video', 'achievements' => 'thanh-tich', 'faqs' => 'hoi-dap'
		);
		$prefix = isset($paths[$resource]) ? $paths[$resource] : 'trang';
		return site_url($prefix . '/' . rawurlencode((string) $slug));
	}
}

if (! function_exists('public_archive_url'))
{
	function public_archive_url($resource)
	{
		return rtrim(public_content_url($resource, ''), '/');
	}
}

if (! function_exists('public_date'))
{
	function public_date($value)
	{
		$value = (string) $value;
		if ($value === '' || $value === '0000-00-00 00:00:00')
		{
			return '';
		}
		$time = strtotime($value);
		return $time === FALSE ? '' : date('d/m/Y', $time);
	}
}

if (! function_exists('public_excerpt'))
{
	function public_excerpt($text, $limit = 140)
	{
		$text = trim(preg_replace('/\s+/u', ' ', strip_tags((string) $text)));
		if ($text === '' || mb_strlen($text, 'UTF-8') <= $limit)
		{
			return $text;
		}
		return mb_substr($text, 0, $limit, 'UTF-8') . '…';
	}
}

if (! function_exists('public_embed_url'))
{
	/**
	 * Chuyển link YouTube/Vimeo thường sang dạng nhúng; trả về '' nếu không hỗ trợ.
	 */
	function public_embed_url($url)
	{
		$url = trim((string) $url);
		if ($url === '' || ! preg_match('~^https?://~i', $url))
		{
			return '';
		}
		if (preg_match('~youtube\.com/watch\?(?:.*&)?v=([A-Za-z0-9_-]{6,})~i', $url, $m))
		{
			return 'https://www.youtube.com/embed/' . $m[1];
		}
		if (preg_match('~youtu\.be/([A-Za-z0-9_-]{6,})~i', $url, $m))
		{
			return 'https://www.youtube.com/embed/' . $m[1];
		}
		if (preg_match('~youtube\.com/embed/[A-Za-z0-9_-]{6,}~i', $url))
		{
			return $url;
		}
		if (preg_match('~vimeo\.com/(\d+)~i', $url, $m))
		{
			return 'https://player.vimeo.com/video/' . $m[1];
		}
		return '';
	}
}

if (! function_exists('public_menu'))
{
	function public_menu($items, $activePath, $lang = 'vi', $depth = 0)
	{
		if (empty($items))
		{
			return;
		}
		echo $depth === 0 ? '' : '<ul>';
		foreach ($items as $item)
		{
			$children = isset($item['children']) ? $item['children'] : array();
			$url = public_safe_url($item['url']);
			$isActive = $depth === 0 && public_menu_is_active($item, $activePath);
			echo '<li class="' . ($children ? 'has-children ' : '') . ($isActive ? 'is-active' : '') . '">';
			echo '<a href="' . html_escape($url) . '"';
			echo $item['target'] === '_blank' ? ' target="_blank" rel="noopener"' : '';
			echo '>' . html_escape(public_label($item, 'title', $lang));
			echo $children ? ' <span class="caret" aria-hidden="true">&#9662;</span>' : '';
			echo '</a>';
			public_menu($children, $activePath, $lang, $depth + 1);
			echo '</li>';
		}
		echo $depth === 0 ? '' : '</ul>';
	}
}

if (! function_exists('public_menu_is_active'))
{
	function public_menu_is_active($item, $activePath)
	{
		$activePath = trim((string) $activePath, '/');
		$itemPath = trim((string) $item['url'], '/');
		if ($itemPath === '' || preg_match('~^https?://~i', $itemPath))
		{
			return $activePath === '' && $itemPath === '';
		}
		if ($activePath === $itemPath || strpos($activePath . '/', $itemPath . '/') === 0)
		{
			return TRUE;
		}
		foreach (isset($item['children']) ? $item['children'] : array() as $child)
		{
			if (public_menu_is_active($child, $activePath))
			{
				return TRUE;
			}
		}
		return FALSE;
	}
}
