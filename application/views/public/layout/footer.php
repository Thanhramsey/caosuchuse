<?php defined('BASEPATH') OR exit('No direct script access allowed');
$companyName = isset($settings['company_name']) && $settings['company_name'] !== '' ? $settings['company_name'] : 'Công ty TNHH MTV Cao su Chư Sê';
$address = isset($settings['address']) ? (string) $settings['address'] : '';
if ($siteLang === 'en')
{
	$companyName = ! empty($settings['company_name_en']) ? $settings['company_name_en'] : $companyName;
	$address = ! empty($settings['address_en']) ? $settings['address_en'] : $address;
}
$phone = isset($settings['phone']) ? (string) $settings['phone'] : '';
$hotline = isset($settings['hotline']) ? (string) $settings['hotline'] : '';
$email = isset($settings['email']) ? (string) $settings['email'] : '';
$registration = isset($settings['business_registration']) ? (string) $settings['business_registration'] : '';
$mapUrl = isset($settings['map_embed_url']) ? trim((string) $settings['map_embed_url']) : '';
$mapUrl = preg_match('~^https://~i', $mapUrl) ? $mapUrl : '';
?>
	</div>
</main>

<footer class="site-footer">
	<div class="container">
		<div class="footer-main">
			<div class="footer-col">
				<h2><?php echo lang('site_footer_contact'); ?></h2>
				<p class="footer-company"><?php echo html_escape($companyName); ?></p>
				<ul class="footer-list">
					<?php if ($address !== ''): ?><li><?php echo lang('site_contact_address'); ?>: <?php echo html_escape($address); ?></li><?php endif; ?>
					<?php if ($phone !== ''): ?><li><?php echo lang('site_contact_phone_label'); ?>: <a href="tel:<?php echo html_escape(preg_replace('/[^0-9+]/', '', $phone)); ?>"><?php echo html_escape($phone); ?></a></li><?php endif; ?>
					<?php if ($hotline !== '' && $hotline !== $phone): ?><li><?php echo lang('site_contact_hotline'); ?>: <a href="tel:<?php echo html_escape(preg_replace('/[^0-9+]/', '', $hotline)); ?>"><?php echo html_escape($hotline); ?></a></li><?php endif; ?>
					<?php if ($email !== ''): ?><li>Email: <a href="mailto:<?php echo html_escape($email); ?>"><?php echo html_escape($email); ?></a></li><?php endif; ?>
				</ul>
				<?php if ($registration !== ''): ?><p class="footer-note"><?php echo html_escape($registration); ?></p><?php endif; ?>
			</div>

			<?php if (! empty($footerSections)): ?>
			<div class="footer-col">
				<?php foreach ($footerSections as $footerSection): ?>
					<h2><?php echo html_escape(public_label($footerSection, 'title', $siteLang)); ?></h2>
					<?php if ($footerSection['section_type'] === 'html' && ! empty($footerSection['content_html'])): ?>
					<div class="footer-text"><?php echo $footerSection['content_html']; ?></div>
					<?php elseif ($footerSection['section_type'] === 'image' && ! empty($footerSection['image_path'])): ?>
					<div class="footer-cert"><img src="<?php echo html_escape(public_media_url($footerSection['image_path'])); ?>" alt="<?php echo html_escape(public_label($footerSection, 'title', $siteLang)); ?>" loading="lazy"></div>
					<?php elseif (! empty($footerSection['items'])): ?>
					<ul class="footer-list">
						<?php foreach ($footerSection['items'] as $footerItem): ?>
						<li><a href="<?php echo html_escape(public_content_url($footerSection['source_resource'], $footerItem['slug'])); ?>"><?php echo html_escape($footerItem['title']); ?></a></li>
						<?php endforeach; ?>
					</ul>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>

			<?php if (! empty($partners)): ?>
			<div class="footer-col">
				<h2><?php echo lang('site_footer_partners'); ?></h2>
				<div class="footer-cert">
					<?php foreach ($partners as $partner): $logo = public_media_url($partner['logo_path'], ''); if ($logo === '') continue; ?>
					<?php if (! empty($partner['website'])): ?><a href="<?php echo html_escape(public_safe_url($partner['website'])); ?>" target="_blank" rel="noopener"><?php endif; ?>
					<img src="<?php echo html_escape($logo); ?>" alt="<?php echo html_escape($partner['name']); ?>" loading="lazy">
					<?php if (! empty($partner['website'])): ?></a><?php endif; ?>
					<?php endforeach; ?>
				</div>
			</div>
			<?php endif; ?>

			<?php if (! empty($testimonials)): ?>
			<div class="footer-col">
				<h2><?php echo lang('site_footer_testimonials'); ?></h2>
				<div class="footer-testimonials">
					<?php foreach ($testimonials as $testimonial): ?>
					<blockquote class="footer-testimonial">
						<p>&ldquo;<?php echo nl2br(html_escape($testimonial['quote'])); ?>&rdquo;</p>
						<footer><?php echo html_escape($testimonial['author_name']); ?><?php if (! empty($testimonial['role']) || ! empty($testimonial['company'])): ?> <span>&mdash; <?php echo html_escape(implode(', ', array_filter(array($testimonial['role'], $testimonial['company'])))); ?></span><?php endif; ?></footer>
					</blockquote>
					<?php endforeach; ?>
				</div>
			</div>
			<?php endif; ?>

			<div class="footer-col">
				<h2><?php echo lang('site_footer_connect'); ?></h2>
				<p class="footer-text"><?php echo html_escape(sprintf(lang('site_footer_newsletter_intro'), $companyName)); ?></p>
				<form class="footer-subscribe" method="post" action="<?php echo site_url('dang-ky-nhan-tin'); ?>">
					<?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
					<label class="visually-hidden" for="footer-newsletter"><?php echo lang('site_footer_newsletter_label'); ?></label>
					<input id="footer-newsletter" type="email" name="email" placeholder="<?php echo lang('site_footer_newsletter_ph'); ?>" required>
					<button class="btn btn-orange" type="submit"><?php echo lang('site_footer_newsletter_submit'); ?></button>
				</form>
				<?php if (! empty($footerMenu)): ?>
				<ul class="footer-list" style="margin-top:12px">
					<?php foreach ($footerMenu as $item): ?>
					<li><a href="<?php echo html_escape(public_safe_url($item['url'])); ?>"><?php echo html_escape(public_label($item, 'title', $siteLang)); ?></a></li>
					<?php endforeach; ?>
				</ul>
				<?php endif; ?>
			</div>

			<?php if ($mapUrl !== ''): ?>
			<div class="footer-col footer-map">
				<h2 class="visually-hidden"><?php echo lang('site_contact_map'); ?></h2>
				<iframe src="<?php echo html_escape($mapUrl); ?>" title="<?php echo lang('site_contact_map'); ?> <?php echo html_escape($companyName); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
			</div>
			<?php endif; ?>
		</div>
		<p class="footer-bottom">&copy; <?php echo date('Y'); ?> <?php echo html_escape($companyName); ?></p>
	</div>
</footer>

<script src="<?php echo public_asset('assets/js/site.js'); ?>" defer></script>
</body>
</html>
