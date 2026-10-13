<?php defined('BASEPATH') OR exit('No direct script access allowed');
$imageAttachments = array();
$fileAttachments = array();
foreach ($attachments as $attachment)
{
	if (in_array(strtolower($attachment['ext']), array('jpg', 'jpeg', 'png', 'gif', 'webp'), TRUE)) { $imageAttachments[] = $attachment; }
	else { $fileAttachments[] = $attachment; }
}
?>
<!doctype html>
<html lang="vi">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo html_escape($title); ?> · Xem trước</title>
	<link rel="stylesheet" href="<?php echo html_escape(base_url('assets/css/site.css')); ?>">
	<style>body{padding:28px 16px 48px;background:#f2f6f3}.preview-ribbon{max-width:1000px;margin:0 auto 16px;padding:10px 14px;border-radius:8px;background:#e6f4eb;color:#17683a;font:600 13px/1.4 system-ui,sans-serif}.preview-document{max-width:1000px;margin:0 auto}.preview-document .article-cover{max-width:100%;height:auto}.preview-document .gallery-grid img{max-width:100%}@media(max-width:600px){body{padding:16px 10px 32px}}</style>
</head>
<body>
	<div class="preview-ribbon">Bản xem trước · Nội dung chưa được lưu hoặc xuất bản</div>
	<main class="preview-document">
		<article class="panel">
			<header class="article-head"><h1><?php echo html_escape($title); ?></h1></header>
			<?php if (trim($excerpt) !== ''): ?><p class="article-excerpt"><?php echo nl2br(html_escape($excerpt)); ?></p><?php endif; ?>
			<?php if ($coverPath !== ''): ?><img class="article-cover" src="<?php echo html_escape(base_url($coverPath)); ?>" alt="<?php echo html_escape($title); ?>"><?php endif; ?>
			<?php if ($contentHtml !== ''): ?><div class="rich-text"><?php echo $contentHtml; ?></div><?php endif; ?>
			<?php if (! empty($imageAttachments)): ?>
			<h2 class="panel-title panel-title--left" style="margin-top:18px">Hình ảnh</h2>
			<div class="gallery-grid">
				<?php foreach ($imageAttachments as $image): ?><a href="<?php echo html_escape(base_url($image['path'])); ?>"><img src="<?php echo html_escape(base_url($image['path'])); ?>" alt="<?php echo html_escape($image['title']); ?>" loading="lazy"></a><?php endforeach; ?>
			</div>
			<?php endif; ?>
			<?php if (! empty($fileAttachments)): ?>
			<h2 class="panel-title panel-title--left" style="margin-top:18px">Tệp đính kèm</h2>
			<ul class="attachment-list"><?php foreach ($fileAttachments as $file): ?><li><a href="<?php echo html_escape(base_url($file['path'])); ?>" target="_blank" rel="noopener"><?php echo html_escape($file['title']); ?> (<?php echo html_escape(strtoupper($file['ext'])); ?>)</a></li><?php endforeach; ?></ul>
			<?php endif; ?>
		</article>
	</main>
</body>
</html>
