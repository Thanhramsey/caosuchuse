<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="page-header d-print-none">
	<div class="container-xl">
		<div class="row g-2 align-items-center">
			<div class="col"><div class="page-pretitle">Quản trị nội dung</div><h2 class="page-title">Giá thu mua mủ cao su</h2></div>
		</div>
	</div>
</div>
<div class="page-body">
	<div class="container-xl">
		<div class="card mb-4">
			<form method="post" action="<?php echo site_url('admin/gia-thu-mua-mu/luu'); ?>">
				<?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
				<input type="hidden" name="id" value="<?php echo (int) $values['id']; ?>">
				<div class="card-header"><h3 class="card-title"><?php echo (int) $values['id'] > 0 ? 'Chỉnh sửa giá' : 'Nhập giá hôm nay'; ?></h3></div>
				<div class="card-body">
					<div class="row g-3">
						<div class="col-md-4">
							<label class="form-label">Ngày áp dụng</label>
							<div class="form-control-plaintext fw-semibold"><?php echo html_escape((int) $values['id'] > 0 ? date('d/m/Y', strtotime($values['effective_date'])) : $today); ?></div>
							<div class="form-hint">Ngày hiện tại được tự động gán khi tạo bảng giá mới.</div>
						</div>
						<div class="col-md-4">
							<label class="form-label required" for="grade-1-price">Mủ nước (loại 1)</label>
							<div class="input-group"><input class="form-control" id="grade-1-price" name="grade_1_price" type="number" min="0.01" step="0.01" value="<?php echo html_escape($values['grade_1_price']); ?>" required><span class="input-group-text">đồng/TSC</span></div>
						</div>
						<div class="col-md-4">
							<label class="form-label required" for="grade-2-price">Mủ nước (loại 2)</label>
							<div class="input-group"><input class="form-control" id="grade-2-price" name="grade_2_price" type="number" min="0.01" step="0.01" value="<?php echo html_escape($values['grade_2_price']); ?>" required><span class="input-group-text">đồng/TSC</span></div>
						</div>
					</div>
				</div>
				<div class="card-footer d-flex justify-content-end gap-2">
					<?php if ((int) $values['id'] > 0): ?><a class="btn btn-outline-secondary" href="<?php echo site_url('admin/gia-thu-mua-mu'); ?>">Hủy sửa</a><?php endif; ?>
					<button class="btn btn-primary" type="submit"><?php echo (int) $values['id'] > 0 ? 'Lưu thay đổi' : 'Lưu giá hôm nay'; ?></button>
				</div>
			</form>
		</div>

		<div class="card">
			<div class="card-header"><h3 class="card-title">Lịch sử giá thu mua</h3></div>
			<?php if (empty($items)): ?>
			<div class="empty"><p class="empty-title">Chưa có bảng giá nào</p><p class="empty-subtitle text-secondary">Nhập giá hôm nay để hiển thị bảng giá trên trang chủ.</p></div>
			<?php else: ?>
			<div class="table-responsive">
				<table class="table table-vcenter card-table table-hover">
					<thead><tr><th>Ngày áp dụng</th><th>Mủ nước (loại 1)</th><th>Mủ nước (loại 2)</th><th class="w-1"></th></tr></thead>
					<tbody>
					<?php foreach ($items as $index => $item): ?>
					<tr>
						<td><?php echo html_escape(date('d/m/Y', strtotime($item['effective_date']))); ?><?php if ($index === 0): ?> <span class="badge bg-green-lt ms-1">Đang hiển thị</span><?php endif; ?></td>
						<td><?php echo html_escape(rtrim(rtrim(number_format((float) $item['grade_1_price'], 2, ',', '.'), '0'), ',')); ?> đồng/TSC</td>
						<td><?php echo html_escape(rtrim(rtrim(number_format((float) $item['grade_2_price'], 2, ',', '.'), '0'), ',')); ?> đồng/TSC</td>
						<td><div class="btn-list flex-nowrap justify-content-end">
							<a class="btn btn-sm btn-icon btn-ghost-primary" href="<?php echo site_url('admin/gia-thu-mua-mu?id=' . (int) $item['id']); ?>" title="Sửa giá" aria-label="Sửa giá"><?php echo admin_icon('edit'); ?></a>
							<form method="post" action="<?php echo site_url('admin/gia-thu-mua-mu/xoa/' . (int) $item['id']); ?>" onsubmit="return confirm('Xóa bảng giá ngày <?php echo html_escape(date('d/m/Y', strtotime($item['effective_date']))); ?>?');">
								<?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
								<button class="btn btn-sm btn-icon btn-ghost-danger" type="submit" title="Xóa giá" aria-label="Xóa giá"><?php echo admin_icon('trash'); ?></button>
							</form>
						</div></td>
					</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</div>
			<?php endif; ?>
		</div>
	</div>
</div>
