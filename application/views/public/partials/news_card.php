<?php defined('BASEPATH') OR exit('No direct script access allowed');
/** @var array $item Bản ghi nội dung. @var string $resource Loại nội dung. */
$url = public_content_url($resource, $item['slug']);
$date = public_date(isset($item['published_at']) ? $item['published_at'] : '');
$imageTitleOnly = ! empty($image_title_only);
$thumbnail = $resource === 'videos' ? public_video_thumbnail_url($item) : (! empty($item['cover_path']) ? public_media_url($item['cover_path']) : '');
?>
<article class="news-card<?php echo $imageTitleOnly ? ' news-card--image-title' : ''; ?>">
	<?php if ($thumbnail !== ''): ?>
	<a href="<?php echo html_escape($url); ?>" tabindex="-1" aria-hidden="true">
		<img src="<?php echo html_escape($thumbnail); ?>" alt="" loading="lazy">
	</a>
	<?php else: ?>
	<a class="news-card-placeholder" href="<?php echo html_escape($url); ?>" tabindex="-1" aria-hidden="true" data-resource="<?php echo html_escape($resource); ?>"><span>CS</span></a>
	<?php endif; ?>
	<div class="news-card-body">
		<h3><a href="<?php echo html_escape($url); ?>"><?php echo html_escape($item['title']); ?></a></h3>
		<?php if (! $imageTitleOnly && ! empty($item['excerpt'])): ?><p><?php echo html_escape(public_excerpt($item['excerpt'], 120)); ?></p><?php endif; ?>
	</div>
	<?php if (! $imageTitleOnly): ?>
	<div class="news-card-foot">
		<?php if ($date !== ''): ?><time datetime="<?php echo html_escape(substr((string) $item['published_at'], 0, 10)); ?>"><?php echo html_escape($date); ?></time><?php else: ?><span></span><?php endif; ?>
		<a class="btn btn-orange" href="<?php echo html_escape($url); ?>"><?php echo lang('site_read_more'); ?></a>
	</div>
	<?php endif; ?>
</article>
