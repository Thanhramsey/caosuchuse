<?php defined('BASEPATH') OR exit('No direct script access allowed');
/** @var array $section Một bản ghi home_sections kèm khoá 'items'. */
$items = isset($section['items']) ? $section['items'] : array();
// Hỏi đáp và công bố thông tin luôn hiển thị dạng danh sách chữ như bản thiết kế.
$hasCover = ! in_array($section['source_resource'], array('faqs', 'disclosures'), TRUE);
if ($hasCover)
{
	$hasCover = FALSE;
	foreach ($items as $item)
	{
		if (! empty($item['cover_path'])) { $hasCover = TRUE; break; }
	}
}
$widgetId = 'widget-' . (int) $section['id'];
?>
<section class="widget" aria-labelledby="<?php echo $widgetId; ?>-title">
	<h2 class="widget-head" id="<?php echo $widgetId; ?>-title"><?php echo html_escape(public_label($section, 'title', $siteLang)); ?></h2>
	<div class="widget-body">
		<?php if ($section['section_type'] === 'html'): ?>
			<div class="rich-text"><?php echo $section['content_html']; ?></div>

		<?php elseif ($section['section_type'] === 'image' && ! empty($section['image_path'])): ?>
			<?php $img = '<img src="' . html_escape(public_media_url($section['image_path'])) . '" alt="' . html_escape($section['title']) . '" loading="lazy">'; ?>
			<?php if (! empty($section['link_url'])): ?>
				<a href="<?php echo html_escape(public_safe_url($section['link_url'])); ?>"><?php echo $img; ?></a>
			<?php else: ?>
				<?php echo $img; ?>
			<?php endif; ?>

		<?php elseif (empty($items)): ?>
			<p class="muted" style="margin:0;font-size:12.5px"><?php echo lang('site_updating'); ?></p>

		<?php elseif ($section['source_resource'] === 'faqs'): ?>
			<div class="faq-widget-list">
				<?php foreach ($items as $item): ?>
				<details class="faq-widget-item">
					<summary><?php echo html_escape($item['title']); ?></summary>
					<?php if (! empty($item['content_html'])): ?><div class="faq-widget-answer rich-text"><?php echo $item['content_html']; ?></div><?php endif; ?>
					<a class="faq-widget-link" href="<?php echo html_escape(public_content_url('faqs', $item['slug'])); ?>"><?php echo lang('site_view_item'); ?></a>
				</details>
				<?php endforeach; ?>
			</div>

		<?php elseif ($hasCover): ?>
			<div class="slides" data-slides>
				<?php foreach ($items as $index => $item): ?>
				<div class="slide<?php echo $index === 0 ? ' is-active' : ''; ?>">
					<a class="widget-media" href="<?php echo html_escape(public_content_url($section['source_resource'], $item['slug'])); ?>">
						<?php if (! empty($item['cover_path'])): ?>
						<img src="<?php echo html_escape(public_media_url($item['cover_path'])); ?>" alt="<?php echo html_escape($item['title']); ?>" loading="lazy">
						<?php endif; ?>
						<p><?php echo html_escape($item['title']); ?></p>
					</a>
				</div>
				<?php endforeach; ?>
			</div>

		<?php else: ?>
			<ul class="widget-list">
				<?php foreach ($items as $item): ?>
				<li><a href="<?php echo html_escape(public_content_url($section['source_resource'], $item['slug'])); ?>"><?php echo html_escape($item['title']); ?></a></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<?php if (! empty($section['link_url']) && ! empty($section['link_label']) && $section['section_type'] !== 'image'): ?>
		<p class="more-link"><a href="<?php echo html_escape(public_safe_url($section['link_url'])); ?>"><?php echo html_escape($section['link_label']); ?></a></p>
		<?php endif; ?>
	</div>
</section>
