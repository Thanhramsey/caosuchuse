<?php defined('BASEPATH') OR exit('No direct script access allowed');
$shown = count($items);
?>
<div class="card-footer d-flex flex-wrap gap-2 align-items-center">
	<p class="m-0 text-secondary">
		<?php if ($total > 0): ?>
			Hiển thị <strong><?php echo $offset + 1; ?>–<?php echo $offset + $shown; ?></strong> trong <strong><?php echo (int) $total; ?></strong> mục
		<?php else: ?>
			Không có mục nào
		<?php endif; ?>
	</p>
	<?php echo $pagination; ?>
</div>
