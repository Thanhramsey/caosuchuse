<?php defined('BASEPATH') OR exit('No direct script access allowed');
/** @var int $page @var int $pages @var string $baseUrl */
if ($pages < 2) { return; }
$start = max(1, $page - 2);
$end = min($pages, $start + 4);
$start = max(1, $end - 4);
$link = function ($number) use ($baseUrl) {
	return $baseUrl . (strpos($baseUrl, '?') === FALSE ? '?' : '&') . 'page=' . (int) $number;
};
?>
<nav class="pagination" aria-label="<?php echo lang('site_pagination'); ?>">
	<?php if ($page > 1): ?><a href="<?php echo html_escape($link($page - 1)); ?>" rel="prev" aria-label="<?php echo lang('site_previous_page'); ?>">&lsaquo;</a><?php endif; ?>
	<?php if ($start > 1): ?><a href="<?php echo html_escape($link(1)); ?>">1</a><?php if ($start > 2): ?><span>…</span><?php endif; ?><?php endif; ?>
	<?php for ($i = $start; $i <= $end; $i++): ?>
		<?php if ($i === $page): ?>
		<span class="is-current" aria-current="page"><?php echo $i; ?></span>
		<?php else: ?>
		<a href="<?php echo html_escape($link($i)); ?>"><?php echo $i; ?></a>
		<?php endif; ?>
	<?php endfor; ?>
	<?php if ($end < $pages): ?><?php if ($end < $pages - 1): ?><span>…</span><?php endif; ?><a href="<?php echo html_escape($link($pages)); ?>"><?php echo $pages; ?></a><?php endif; ?>
	<?php if ($page < $pages): ?><a href="<?php echo html_escape($link($page + 1)); ?>" rel="next" aria-label="<?php echo lang('site_next_page'); ?>">&rsaquo;</a><?php endif; ?>
</nav>
