<?php defined('BASEPATH') OR exit('No direct script access allowed');
$companyName = isset($settings['company_name']) && $settings['company_name'] !== '' ? $settings['company_name'] : 'Công ty TNHH MTV Cao su Chư Sê';
$address = isset($settings['address']) ? (string) $settings['address'] : '';
if ($siteLang === 'en')
{
	$companyName = ! empty($settings['company_name_en']) ? $settings['company_name_en'] : $companyName;
	$address = ! empty($settings['address_en']) ? $settings['address_en'] : $address;
}
$mapUrl = isset($settings['map_embed_url']) ? trim((string) $settings['map_embed_url']) : '';
$mapUrl = preg_match('~^https://~i', $mapUrl) ? $mapUrl : '';
$old = function ($field) { return html_escape((string) set_value($field)); };
?>
<div class="layout-inner">
	<aside class="rail" aria-label="Chuyên mục">
		<?php $this->load->view('public/partials/rail', array('sections' => $rail, 'siteLang' => $siteLang)); ?>
	</aside>

	<div class="main-col">
		<section class="panel">
			<h1 class="page-title"><?php echo html_escape($listTitle); ?></h1>
			<div class="contact-grid">
				<div>
					<p class="lead-title" style="text-transform:uppercase"><?php echo html_escape($companyName); ?></p>
					<ul class="contact-info">
						<?php if ($address !== ''): ?><li><?php echo lang('site_contact_address'); ?>: <?php echo html_escape($address); ?></li><?php endif; ?>
						<?php if (! empty($settings['phone'])): ?><li><?php echo lang('site_contact_phone_label'); ?>: <a href="tel:<?php echo html_escape(preg_replace('/[^0-9+]/', '', $settings['phone'])); ?>"><?php echo html_escape($settings['phone']); ?></a></li><?php endif; ?>
						<?php if (! empty($settings['fax'])): ?><li><?php echo lang('site_contact_fax'); ?>: <?php echo html_escape($settings['fax']); ?></li><?php endif; ?>
						<?php if (! empty($settings['email'])): ?><li>Email: <a href="mailto:<?php echo html_escape($settings['email']); ?>"><?php echo html_escape($settings['email']); ?></a></li><?php endif; ?>
						<?php if (! empty($settings['working_hours'])): ?><li><?php echo lang('site_contact_hours'); ?>: <?php echo html_escape($settings['working_hours']); ?></li><?php endif; ?>
					</ul>
					<?php if ($mapUrl !== ''): ?>
					<div class="contact-map">
						<iframe src="<?php echo html_escape($mapUrl); ?>" title="<?php echo lang('site_contact_map'); ?> <?php echo html_escape($companyName); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
					</div>
					<?php endif; ?>
				</div>

				<div>
					<form method="post" action="<?php echo site_url('lien-he/gui'); ?>">
						<?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
						<div class="form-field">
							<label for="contact-name"><?php echo lang('site_contact_name'); ?></label>
							<input id="contact-name" name="name" maxlength="150" value="<?php echo $old('name'); ?>" placeholder="<?php echo lang('site_contact_name_ph'); ?>" required>
							<?php echo form_error('name', '<p class="form-error">', '</p>'); ?>
						</div>
						<div class="form-field">
							<label for="contact-phone"><?php echo lang('site_contact_phone'); ?></label>
							<input id="contact-phone" name="phone" maxlength="40" value="<?php echo $old('phone'); ?>" placeholder="<?php echo lang('site_contact_phone_ph'); ?>">
							<?php echo form_error('phone', '<p class="form-error">', '</p>'); ?>
						</div>
						<div class="form-field">
							<label for="contact-email"><?php echo lang('site_contact_email'); ?></label>
							<input id="contact-email" type="email" name="email" maxlength="191" value="<?php echo $old('email'); ?>" placeholder="<?php echo lang('site_contact_email_ph'); ?>" required>
							<?php echo form_error('email', '<p class="form-error">', '</p>'); ?>
						</div>
						<div class="form-field">
							<label for="contact-subject"><?php echo lang('site_contact_subject'); ?></label>
							<input id="contact-subject" name="subject" maxlength="191" value="<?php echo $old('subject'); ?>" placeholder="<?php echo lang('site_contact_subject_ph'); ?>" required>
							<?php echo form_error('subject', '<p class="form-error">', '</p>'); ?>
						</div>
						<div class="form-field">
							<label for="contact-message"><?php echo lang('site_contact_message'); ?></label>
							<textarea id="contact-message" name="message" maxlength="5000" placeholder="<?php echo lang('site_contact_message_ph'); ?>" required><?php echo html_escape((string) set_value('message')); ?></textarea>
							<?php echo form_error('message', '<p class="form-error">', '</p>'); ?>
						</div>
						<button class="btn btn-green btn-block" type="submit"><?php echo lang('site_contact_submit'); ?></button>
					</form>
				</div>
			</div>
		</section>
	</div>
</div>
