<?php defined('BASEPATH') OR exit('No direct script access allowed');
$companyName = isset($settings['company_name']) && $settings['company_name'] !== '' ? $settings['company_name'] : 'Công ty TNHH MTV Cao su Chư Sê';
$groupName = isset($settings['group_name']) && $settings['group_name'] !== '' ? $settings['group_name'] : 'Tập đoàn Công nghiệp Cao su Việt Nam';
$slogan = isset($settings['slogan']) ? (string) $settings['slogan'] : '';
if ($siteLang === 'en')
{
	$companyName = ! empty($settings['company_name_en']) ? $settings['company_name_en'] : $companyName;
	$groupName = ! empty($settings['group_name_en']) ? $settings['group_name_en'] : $groupName;
	$slogan = ! empty($settings['slogan_en']) ? $settings['slogan_en'] : $slogan;
}
$logoUrl = public_media_url(isset($settings['logo_path']) ? $settings['logo_path'] : '', base_url('assets/images/logo.png'));
$bannerUrl = public_media_url(isset($settings['header_banner']) ? $settings['header_banner'] : '', '');
$metaImage = isset($meta_image) && $meta_image !== '' ? $meta_image : $logoUrl;
$englishSiteUrl = isset($settings['english_site_url']) ? public_safe_url($settings['english_site_url']) : '#';
$showLanguageSwitch = $englishSiteUrl !== '#';
?>
<!DOCTYPE html>
<html lang="<?php echo $siteLang === 'en' ? 'en' : 'vi'; ?>">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo html_escape($page_title); ?></title>
	<meta name="description" content="<?php echo html_escape(public_excerpt($meta_description, 300)); ?>">
	<link rel="canonical" href="<?php echo html_escape(current_url()); ?>">
	<meta property="og:type" content="website">
	<meta property="og:site_name" content="<?php echo html_escape($companyName); ?>">
	<meta property="og:title" content="<?php echo html_escape($page_title); ?>">
	<meta property="og:description" content="<?php echo html_escape(public_excerpt($meta_description, 300)); ?>">
	<meta property="og:image" content="<?php echo html_escape($metaImage); ?>">
	<meta property="og:locale" content="<?php echo $siteLang === 'en' ? 'en_US' : 'vi_VN'; ?>">
	<link rel="icon" href="<?php echo html_escape($logoUrl); ?>">
	<link rel="stylesheet" href="<?php echo public_asset('assets/css/site.css'); ?>">
</head>
<body>
<a class="skip-link" href="#noi-dung-chinh"><?php echo lang('site_skip_to_content'); ?></a>

<header class="site-header">
	<div class="container">
		<div class="masthead">
			<?php if ($showLanguageSwitch): ?>
			<ul class="lang-switch" aria-label="<?php echo lang('site_language_switcher'); ?>">
				<li><a class="lang-vi<?php echo $siteLang === 'vi' ? ' is-current' : ''; ?>" href="<?php echo html_escape(public_lang_url('vi')); ?>" hreflang="vi"<?php echo $siteLang === 'vi' ? ' aria-current="true"' : ''; ?>><?php echo lang('site_language_vi'); ?></a></li>
				<li><a class="lang-en<?php echo $siteLang === 'en' ? ' is-current' : ''; ?>" href="<?php echo html_escape($englishSiteUrl); ?>" hreflang="en"<?php echo $siteLang === 'en' ? ' aria-current="true"' : ''; ?>><?php echo lang('site_language_en'); ?></a></li>
			</ul>
			<?php endif; ?>
			<a class="masthead-logo" href="<?php echo site_url(); ?>">
				<img src="<?php echo html_escape($logoUrl); ?>" alt="<?php echo lang('site_logo_of'); ?> <?php echo html_escape($companyName); ?>">
			</a>
			<div class="masthead-title">
				<p class="masthead-group"><?php echo html_escape($groupName); ?></p>
				<p class="masthead-company"><?php echo html_escape($companyName); ?></p>
				<?php if ($slogan !== ''): ?>
				<p class="masthead-slogan"><?php echo nl2br(html_escape($slogan)); ?></p>
				<?php endif; ?>
			</div>
			<div class="masthead-media">
				<?php if ($bannerUrl !== ''): ?>
				<img src="<?php echo html_escape($bannerUrl); ?>" alt="">
				<?php endif; ?>
			</div>
		</div>
		<div class="nav-bar">
			<div class="nav-inner">
				<button class="nav-toggle" type="button" data-nav-toggle aria-expanded="false" aria-controls="main-menu">
					<span aria-hidden="true">&#9776;</span> <?php echo lang('site_menu'); ?>
				</button>
				<nav aria-label="<?php echo lang('site_main_nav'); ?>">
					<ul class="main-menu" id="main-menu"><?php public_menu($mainMenu, $activePath, $siteLang); ?></ul>
				</nav>
				<div class="nav-search">
					<form action="<?php echo site_url('tim-kiem'); ?>" method="get" role="search">
						<label class="visually-hidden" for="site-search"><?php echo lang('site_search'); ?></label>
						<input id="site-search" type="search" name="q" value="<?php echo html_escape((string) $this->input->get('q')); ?>" placeholder="<?php echo lang('site_search_placeholder'); ?>">
						<button type="submit" aria-label="<?php echo lang('site_search'); ?>"><span aria-hidden="true">&#128269;</span></button>
					</form>
				</div>
			</div>
		</div>
	</div>
</header>

<?php if ($flashSuccess !== '' || $flashError !== ''): ?>
<div class="site-alert<?php echo $flashError !== '' ? ' site-alert-error' : ''; ?>" role="status">
	<?php echo html_escape($flashError !== '' ? $flashError : $flashSuccess); ?>
</div>
<?php endif; ?>

<main class="site-main" id="noi-dung-chinh">
	<div class="container">
