<?php defined('BASEPATH') OR exit('No direct script access allowed');
$date = public_date(isset($item['published_at']) ? $item['published_at'] : '');
$embed = isset($item['extra_url']) ? public_embed_url($item['extra_url']) : '';
$images = array();
$files = array();
foreach ($attachments as $file)
{
	if (in_array(strtolower((string) $file['file_ext']), array('jpg', 'jpeg', 'png', 'gif', 'webp'), TRUE)) { $images[] = $file; }
	else { $files[] = $file; }
}
?>
<div class="layout-inner">
	<aside class="rail" aria-label="<?php echo lang('site_sidebar'); ?>">
		<?php $this->load->view('public/partials/rail', array('sections' => $rail, 'siteLang' => $siteLang)); ?>
	</aside>

	<div class="main-col">
		<article class="panel">
			<?php if ($resource === 'pages'): ?>
			<h1 class="page-title"><?php echo html_escape($item['title']); ?></h1>
			<?php else: ?>
			<header class="article-head">
				<h1><?php echo html_escape($item['title']); ?></h1>
				<p class="article-meta">
					<?php if (! empty($item['category_name'])): ?><?php echo html_escape($item['category_name']); ?><?php endif; ?>
					<?php if ($date !== '' && ! empty($item['category_name'])): ?> &middot; <?php endif; ?>
					<?php if ($date !== ''): ?><time datetime="<?php echo html_escape(substr((string) $item['published_at'], 0, 10)); ?>"><?php echo html_escape($date); ?></time><?php endif; ?>
				</p>
			</header>
			<?php endif; ?>

			<?php if (! empty($item['excerpt'])): ?>
			<p class="article-excerpt"><?php echo html_escape($item['excerpt']); ?></p>
			<?php endif; ?>

			<?php if ($embed !== ''): ?>
			<div class="video-frame"><iframe src="<?php echo html_escape($embed); ?>" title="<?php echo html_escape($item['title']); ?>" allowfullscreen loading="lazy"></iframe></div>
			<?php elseif (! empty($item['cover_path']) && $resource !== 'albums'): ?>
			<img class="article-cover" src="<?php echo html_escape(public_media_url($item['cover_path'])); ?>" alt="<?php echo html_escape($item['title']); ?>">
			<?php endif; ?>

			<?php if (! empty($item['content_html'])): ?>
			<div class="rich-text"><?php echo $item['content_html']; ?></div>
			<?php endif; ?>

			<?php if (! empty($images)): ?>
			<h2 class="panel-title panel-title--left" style="margin-top:18px"><?php echo lang('site_images'); ?></h2>
			<div class="gallery-grid">
				<?php foreach ($images as $image): ?>
				<a href="<?php echo html_escape(public_media_url($image['file_path'])); ?>" target="_blank" rel="noopener">
					<img src="<?php echo html_escape(public_media_url($image['file_path'])); ?>" alt="<?php echo html_escape($image['title']); ?>" loading="lazy">
				</a>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>

			<?php if (! empty($files)): ?>
			<h2 class="panel-title panel-title--left" style="margin-top:18px"><?php echo lang('site_attachments'); ?></h2>
			<ul class="attachment-list">
				<?php foreach ($files as $file): ?>
				<li><a href="<?php echo html_escape(public_media_url($file['file_path'])); ?>" target="_blank" rel="noopener"><?php echo html_escape($file['title']); ?> (<?php echo html_escape(strtoupper((string) $file['file_ext'])); ?>)</a></li>
				<?php endforeach; ?>
			</ul>
			<?php endif; ?>
		</article>

		<?php if (! empty($related)): ?>
		<section class="panel">
			<h2 class="panel-title panel-title--left"><?php echo lang('site_related'); ?></h2>
			<div class="news-grid">
				<?php foreach ($related as $row): ?>
				<?php $this->load->view('public/partials/news_card', array('item' => $row, 'resource' => $resource)); ?>
				<?php endforeach; ?>
			</div>
		</section>
		<?php endif; ?>

		<?php if (! empty($commentsOpen)): ?>
		<section class="panel">
			<h2 class="panel-title panel-title--left"><?php echo lang('site_comments'); ?></h2>
			<?php if (empty($comments)): ?>
			<p class="muted"><?php echo lang('site_no_comments'); ?></p>
			<?php else: ?>
			<ul class="widget-list">
				<?php foreach ($comments as $comment): ?>
				<li>
					<strong><?php echo html_escape($comment['author_name']); ?></strong>
					<span class="muted" style="font-size:12px"> — <?php echo html_escape(public_date($comment['created_at'])); ?></span>
					<p style="margin:4px 0 0;font-size:13px"><?php echo nl2br(html_escape($comment['body'])); ?></p>
				</li>
				<?php endforeach; ?>
			</ul>
			<?php endif; ?>

			<form method="post" action="<?php echo site_url('binh-luan'); ?>" style="margin-top:16px">
				<?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
				<input type="hidden" name="resource" value="<?php echo html_escape($resource); ?>">
				<input type="hidden" name="item_id" value="<?php echo (int) $item['id']; ?>">
				<div class="contact-grid">
					<div class="form-field">
						<label for="comment-name"><?php echo lang('site_comment_name'); ?></label>
						<input id="comment-name" name="author_name" maxlength="150" required>
					</div>
					<div class="form-field">
						<label for="comment-email"><?php echo lang('site_comment_email'); ?></label>
						<input id="comment-email" type="email" name="email" maxlength="191" required>
					</div>
				</div>
				<div class="form-field">
					<label for="comment-body"><?php echo lang('site_comment_body'); ?></label>
					<textarea id="comment-body" name="body" maxlength="3000" required></textarea>
				</div>
				<button class="btn btn-green" type="submit"><?php echo lang('site_comment_submit'); ?></button>
			</form>
		</section>
		<?php endif; ?>
	</div>
</div>
