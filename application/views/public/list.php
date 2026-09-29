<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="layout-inner">
	<aside class="rail" aria-label="<?php echo lang('site_sidebar'); ?>">
		<?php $this->load->view('public/partials/rail', array('sections' => $rail, 'siteLang' => $siteLang)); ?>
	</aside>

	<div class="main-col">
		<section class="panel">
			<h1 class="page-title"><?php echo html_escape($listTitle); ?></h1>

			<?php if (! empty($categories)): ?>
			<ul class="widget-list" style="display:flex;flex-wrap:wrap;gap:0 18px;margin-bottom:14px">
				<?php foreach ($categories as $category): ?>
				<li style="border:0"><a href="<?php echo html_escape(public_archive_url($resource) . '/' . rawurlencode($category['slug'])); ?>"><?php echo html_escape($category['name']); ?></a></li>
				<?php endforeach; ?>
			</ul>
			<?php endif; ?>

			<?php if (empty($items)): ?>
				<p class="empty-state"><?php echo lang('site_no_published_content'); ?></p>

			<?php elseif ($resource === 'products'): ?>
				<div class="product-grid">
					<?php foreach ($items as $item): ?>
					<article class="product-card">
						<?php if (! empty($item['cover_path'])): ?>
						<img src="<?php echo html_escape(public_media_url($item['cover_path'])); ?>" alt="<?php echo html_escape($item['title']); ?>" loading="lazy">
						<?php endif; ?>
						<h3><a href="<?php echo html_escape(public_content_url($resource, $item['slug'])); ?>"><?php echo html_escape($item['title']); ?></a></h3>
					</article>
					<?php endforeach; ?>
				</div>

			<?php elseif ($resource === 'faqs' || $resource === 'disclosures'): ?>
				<ul class="widget-list">
					<?php foreach ($items as $item): ?>
					<li>
						<a href="<?php echo html_escape(public_content_url($resource, $item['slug'])); ?>"><?php echo html_escape($item['title']); ?></a>
						<?php $d = public_date($item['published_at']); if ($d !== ''): ?><span class="muted" style="font-size:12px"> — <?php echo html_escape($d); ?></span><?php endif; ?>
					</li>
					<?php endforeach; ?>
				</ul>

			<?php else: ?>
				<div class="article-list">
					<?php foreach ($items as $item): $url = public_content_url($resource, $item['slug']); ?>
					<article class="article-item">
						<?php if (! empty($item['cover_path'])): ?>
						<a href="<?php echo html_escape($url); ?>" tabindex="-1" aria-hidden="true"><img src="<?php echo html_escape(public_media_url($item['cover_path'])); ?>" alt="" loading="lazy"></a>
						<?php endif; ?>
						<h3><a href="<?php echo html_escape($url); ?>"><?php echo html_escape($item['title']); ?></a></h3>
						<?php if (! empty($item['excerpt'])): ?><p><?php echo html_escape(public_excerpt($item['excerpt'], 150)); ?></p><?php endif; ?>
						<?php $d = public_date($item['published_at']); if ($d !== ''): ?><p class="meta"><?php echo html_escape($d); ?></p><?php endif; ?>
					</article>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php $this->load->view('public/partials/pagination', array('page' => $page, 'pages' => $pages, 'baseUrl' => $baseUrl)); ?>
		</section>
	</div>
</div>
