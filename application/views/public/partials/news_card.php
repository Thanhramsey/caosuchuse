<?php defined('BASEPATH') OR exit('No direct script access allowed');
/** @var array $item Bản ghi nội dung. @var string $resource Loại nội dung. */
$url = public_content_url($resource, $item['slug']);
$date = public_date(isset($item['published_at']) ? $item['published_at'] : '');
?>
<article class="news-card">
	<?php if (! empty($item['cover_path'])): ?>
	<a href="<?php echo html_escape($url); ?>" tabindex="-1" aria-hidden="true">
		<img src="<?php echo html_escape(public_media_url($item['cover_path'])); ?>" alt="" loading="lazy">
	</a>
	<?php endif; ?>
	<div class="news-card-body">
		<h3><a href="<?php echo html_escape($url); ?>"><?php echo html_escape($item['title']); ?></a></h3>
		<?php if (! empty($item['excerpt'])): ?><p><?php echo html_escape(public_excerpt($item['excerpt'], 120)); ?></p><?php endif; ?>
	</div>
	<div class="news-card-foot">
		<?php if ($date !== ''): ?><time datetime="<?php echo html_escape(substr((string) $item['published_at'], 0, 10)); ?>"><?php echo html_escape($date); ?></time><?php else: ?><span></span><?php endif; ?>
		<a class="btn btn-orange" href="<?php echo html_escape($url); ?>"><?php echo lang('site_read_more'); ?></a>
	</div>
</article>
