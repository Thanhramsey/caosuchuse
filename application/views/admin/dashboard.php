<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="page-header d-print-none">
	<div class="container-xl">
		<div class="page-pretitle">Tổng quan</div>
		<h2 class="page-title">Xin chào, <?php echo html_escape($currentUser['full_name']); ?></h2>
		<div class="text-secondary mt-1">Tình hình nội dung website Cao su Chư Sê.</div>
	</div>
</div>
<div class="page-body">
	<div class="container-xl">
		<div class="row row-cards">
			<?php foreach ($stats as $stat): ?>
				<div class="col-sm-6 col-lg-4 col-xl-2">
					<a class="card card-sm card-link admin-stat" href="<?php echo site_url($stat['url']); ?>">
						<div class="card-body">
							<div class="d-flex align-items-center gap-3">
								<span class="avatar bg-<?php echo $stat['color']; ?>-lt"><?php echo admin_icon($stat['icon']); ?></span>
								<div>
									<div class="h2 m-0"><?php echo (int) $stat['count']; ?></div>
									<div class="text-secondary"><?php echo html_escape($stat['label']); ?></div>
								</div>
							</div>
						</div>
					</a>
				</div>
			<?php endforeach; ?>
		</div>

		<?php if (admin_can('news.view')): ?>
			<div class="card mt-4">
				<div class="card-header">
					<h3 class="card-title">Tin tức cập nhật gần đây</h3>
					<div class="card-actions"><a href="<?php echo site_url('admin/noi-dung/news'); ?>" class="btn btn-sm">Xem tất cả</a></div>
				</div>
				<?php if (empty($latestNews)): ?>
					<div class="empty admin-empty">
						<div class="empty-icon"><?php echo admin_icon('news', 'icon icon-lg'); ?></div>
						<p class="empty-title">Chưa có tin tức nào</p>
						<p class="empty-subtitle text-secondary">Các bài viết mới sẽ hiển thị tại đây.</p>
					</div>
				<?php else: ?>
					<div class="table-responsive">
						<table class="table table-vcenter card-table">
							<tbody>
								<?php foreach ($latestNews as $item): ?>
									<tr>
										<td>
											<div class="fw-medium"><?php echo html_escape($item['title']); ?></div>
											<div class="text-secondary small"><?php echo $item['category_name'] !== NULL ? html_escape($item['category_name']) : 'Chưa phân loại'; ?></div>
										</td>
										<td class="w-1"><span class="badge <?php echo $item['status'] === 'published' ? 'bg-green-lt' : 'bg-yellow-lt'; ?>"><?php echo $item['status'] === 'published' ? 'Đã xuất bản' : 'Bản nháp'; ?></span></td>
										<td class="w-1 text-secondary text-nowrap"><?php echo admin_date($item['updated_at']); ?></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</div>
