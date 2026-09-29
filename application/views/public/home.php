<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="layout-home">
	<aside class="rail rail-left" aria-label="<?php echo lang('site_sidebar_left'); ?>">
		<?php $this->load->view('public/partials/rail', array('sections' => $rail, 'siteLang' => $siteLang)); ?>
	</aside>

	<div class="main-col">
		<?php if (! empty($slider)): ?>
		<section class="panel panel-hero" aria-label="<?php echo lang('site_featured_images'); ?>">
			<div class="slides" data-slides data-interval="6000">
				<?php foreach ($slider as $index => $slide): $link = public_safe_url($slide['link_url']); ?>
				<div class="slide<?php echo $index === 0 ? ' is-active' : ''; ?>">
					<?php if ($link !== '#'): ?><a href="<?php echo html_escape($link); ?>"><?php endif; ?>
					<img src="<?php echo html_escape(public_media_url($slide['image_path'])); ?>" alt="<?php echo html_escape(! empty($slide['alt_text']) ? $slide['alt_text'] : $slide['title']); ?>"<?php echo $index === 0 ? '' : ' loading="lazy"'; ?>>
					<?php if ($link !== '#'): ?></a><?php endif; ?>
					<?php if (! empty($slide['title'])): ?><p class="hero-caption"><?php echo html_escape($slide['title']); ?><?php if (! empty($slide['subtitle'])): ?> <span class="muted" style="font-weight:400">&mdash; <?php echo html_escape($slide['subtitle']); ?></span><?php endif; ?></p><?php endif; ?>
				</div>
				<?php endforeach; ?>
			</div>
		</section>
		<?php endif; ?>

		<?php foreach ($mainSections as $section): if ($section['section_type'] === 'slider') continue; ?>
		<section class="panel">
			<h2 class="panel-title"><?php echo html_escape(public_label($section, 'title', $siteLang)); ?></h2>

			<?php if ($section['section_type'] === 'html'): ?>
				<?php if (! empty($section['subtitle'])): ?><p class="lead-title"><?php echo html_escape($section['subtitle']); ?></p><?php endif; ?>
				<div class="rich-text"><?php echo $section['content_html']; ?></div>

			<?php elseif ($section['section_type'] === 'image' && ! empty($section['image_path'])): ?>
				<img src="<?php echo html_escape(public_media_url($section['image_path'])); ?>" alt="<?php echo html_escape($section['title']); ?>" loading="lazy">

			<?php elseif (empty($section['items'])): ?>
				<p class="empty-state"><?php echo lang('site_content_updating'); ?></p>

			<?php elseif ($section['source_resource'] === 'products'): ?>
				<div class="product-grid">
					<?php foreach ($section['items'] as $item): ?>
					<article class="product-card">
						<?php if (! empty($item['cover_path'])): ?>
						<img src="<?php echo html_escape(public_media_url($item['cover_path'])); ?>" alt="<?php echo html_escape($item['title']); ?>" loading="lazy">
						<?php endif; ?>
						<h3><a href="<?php echo html_escape(public_content_url('products', $item['slug'])); ?>"><?php echo html_escape($item['title']); ?></a></h3>
					</article>
					<?php endforeach; ?>
				</div>

			<?php else: ?>
				<div class="news-grid">
					<?php foreach ($section['items'] as $item): ?>
					<?php $this->load->view('public/partials/news_card', array('item' => $item, 'resource' => $section['source_resource'])); ?>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php if (! empty($section['link_url']) && ! empty($section['link_label'])): ?>
			<p class="more-link"><a href="<?php echo html_escape(public_safe_url($section['link_url'])); ?>"><?php echo html_escape($section['link_label']); ?></a></p>
			<?php endif; ?>
		</section>
		<?php endforeach; ?>
	</div>

	<aside class="rail rail-right" aria-label="<?php echo lang('site_sidebar_right'); ?>">
		<?php $this->load->view('public/partials/rail', array('sections' => $railRight, 'siteLang' => $siteLang)); ?>
	</aside>
</div>

<?php foreach ($fullSections as $section): ?>
	<?php if ($section['section_type'] === 'image' && ! empty($section['image_path'])): ?>
	<section class="band" style="margin-top:var(--gap)">
		<?php if (! empty($section['link_url'])): ?><a href="<?php echo html_escape(public_safe_url($section['link_url'])); ?>"><?php endif; ?>
		<img src="<?php echo html_escape(public_media_url($section['image_path'])); ?>" alt="<?php echo html_escape($section['title']); ?>" loading="lazy">
		<?php if (! empty($section['link_url'])): ?></a><?php endif; ?>
	</section>
	<?php else: ?>
	<section class="panel" style="margin-top:var(--gap)">
		<h2 class="panel-title panel-title--left"><?php echo html_escape(public_label($section, 'title', $siteLang)); ?></h2>
		<?php if ($section['section_type'] === 'html'): ?>
			<div class="rich-text"><?php echo $section['content_html']; ?></div>
		<?php elseif (empty($section['items'])): ?>
			<p class="empty-state"><?php echo lang('site_content_updating'); ?></p>
		<?php else: ?>
			<div class="news-grid">
				<?php foreach ($section['items'] as $item): ?>
				<?php $this->load->view('public/partials/news_card', array('item' => $item, 'resource' => $section['source_resource'])); ?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
		<?php if (! empty($section['link_url']) && ! empty($section['link_label'])): ?>
		<p class="more-link"><a href="<?php echo html_escape(public_safe_url($section['link_url'])); ?>"><?php echo html_escape($section['link_label']); ?></a></p>
		<?php endif; ?>
	</section>
	<?php endif; ?>
<?php endforeach; ?>
