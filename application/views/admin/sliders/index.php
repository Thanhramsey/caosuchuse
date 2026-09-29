<?php defined('BASEPATH') OR exit('No direct script access allowed');
$hasFilter = $filters['q'] !== '' || $filters['status'] !== '';
?>
<div class="page-header d-print-none">
	<div class="container-xl">
		<div class="row g-2 align-items-center">
			<div class="col">
				<div class="page-pretitle">Giao diện</div>
				<h2 class="page-title">Slider trang chủ</h2>
				<div class="text-secondary mt-1">Ảnh banner hiển thị theo thứ tự tăng dần.</div>
			</div>
			<div class="col-auto ms-auto">
				<button type="button" class="btn btn-primary" data-entity-create data-title="Thêm slider"><?php echo admin_icon('plus'); ?> Thêm slider</button>
			</div>
		</div>
	</div>
</div>
<div class="page-body">
	<div class="container-xl">
		<div class="card">
			<div class="card-header admin-filter">
				<form class="row g-2 w-100 align-items-center" method="get" action="<?php echo site_url('admin/sliders'); ?>">
					<div class="col-12 col-md">
						<div class="input-icon">
							<span class="input-icon-addon"><?php echo admin_icon('search'); ?></span>
							<input type="search" class="form-control" name="q" value="<?php echo html_escape($filters['q']); ?>" placeholder="Tìm theo tiêu đề hoặc mô tả…" aria-label="Từ khóa">
						</div>
					</div>
					<div class="col-6 col-md-3">
						<select class="form-select" name="status" aria-label="Trạng thái" data-auto-submit>
							<option value="">Mọi trạng thái</option>
							<option value="1"<?php echo $filters['status'] === '1' ? ' selected' : ''; ?>>Đang hiển thị</option>
							<option value="0"<?php echo $filters['status'] === '0' ? ' selected' : ''; ?>>Đang ẩn</option>
						</select>
					</div>
					<div class="col-auto">
						<button type="submit" class="btn btn-outline-primary"><?php echo admin_icon('filter'); ?> Lọc</button>
						<?php if ($hasFilter): ?><a class="btn btn-ghost-secondary" href="<?php echo site_url('admin/sliders'); ?>">Xóa lọc</a><?php endif; ?>
					</div>
				</form>
			</div>
			<?php if (empty($items)): ?>
				<div class="empty admin-empty">
					<div class="empty-icon"><?php echo admin_icon('photo', 'icon icon-lg'); ?></div>
					<p class="empty-title"><?php echo $hasFilter ? 'Không tìm thấy slider phù hợp' : 'Chưa có slider nào'; ?></p>
					<p class="empty-subtitle text-secondary"><?php echo $hasFilter ? 'Thử thay đổi từ khóa hoặc bộ lọc.' : 'Thêm ảnh banner cho trang chủ.'; ?></p>
					<?php if (! $hasFilter): ?><div class="empty-action"><button type="button" class="btn btn-primary" data-entity-create data-title="Thêm slider"><?php echo admin_icon('plus'); ?> Thêm slider</button></div><?php endif; ?>
				</div>
			<?php else: ?>
				<div class="table-responsive">
					<table class="table table-vcenter card-table table-hover">
						<thead>
							<tr>
								<th>Slider</th>
								<th>Liên kết</th>
								<th class="text-center">Thứ tự</th>
								<th>Trạng thái</th>
								<th class="w-1"><span class="visually-hidden">Thao tác</span></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($items as $item): ?>
								<tr>
									<td>
										<div class="d-flex align-items-center gap-3">
											<img class="admin-thumb admin-thumb-wide" src="<?php echo base_url($item['image_path']); ?>" alt="<?php echo html_escape($item['alt_text']); ?>" loading="lazy">
											<div class="admin-cell-title">
												<div class="fw-medium text-truncate"><?php echo html_escape($item['title']); ?></div>
												<div class="text-secondary small text-truncate"><?php echo $item['subtitle'] !== NULL && $item['subtitle'] !== '' ? html_escape($item['subtitle']) : '—'; ?></div>
											</div>
										</div>
									</td>
									<td class="text-secondary small text-truncate admin-cell-link"><?php echo $item['link_url'] !== NULL && $item['link_url'] !== '' ? html_escape($item['link_url']) : '—'; ?></td>
									<td class="text-center text-secondary"><?php echo (int) $item['sort_order']; ?></td>
									<td><?php echo (int) $item['status'] === 1 ? '<span class="status status-green"><span class="status-dot"></span>Hiển thị</span>' : '<span class="status status-secondary"><span class="status-dot"></span>Đang ẩn</span>'; ?></td>
									<td>
										<div class="btn-list flex-nowrap justify-content-end">
											<button type="button" class="btn btn-sm btn-icon btn-ghost-primary" title="Sửa" aria-label="Sửa <?php echo html_escape($item['title']); ?>" data-entity-edit="<?php echo site_url('admin/sliders/chi-tiet/' . $item['id']); ?>" data-title="Sửa slider"><?php echo admin_icon('edit'); ?></button>
											<button type="button" class="btn btn-sm btn-icon btn-ghost-danger" title="Xóa" aria-label="Xóa <?php echo html_escape($item['title']); ?>" data-confirm-url="<?php echo site_url('admin/sliders/xoa/' . $item['id']); ?>" data-confirm-text="Xóa slider &quot;<?php echo html_escape($item['title']); ?>&quot;?"><?php echo admin_icon('trash'); ?></button>
										</div>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			<?php endif; ?>
			<?php $this->load->view('admin/layout/table_footer'); ?>
		</div>
	</div>
</div>

<div class="modal modal-blur fade" id="entity-modal" tabindex="-1" aria-hidden="true" data-entity-modal data-bs-backdrop="static">
	<div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
		<?php echo form_open('admin/sliders/luu', array('class' => 'modal-content', 'novalidate' => 'novalidate')); ?>
			<input type="hidden" name="id" value="">
			<div class="modal-header">
				<h5 class="modal-title" data-modal-title>Thêm slider</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
			</div>
			<div class="modal-body">
				<div class="mb-3">
					<label class="form-label required">Ảnh slider</label>
					<div class="admin-image-field" data-image-field>
						<input type="hidden" name="image_path" data-image-input>
						<div class="admin-image-preview admin-image-preview-wide" data-image-preview></div>
						<div class="btn-list mt-2">
							<button type="button" class="btn btn-sm" data-upload-trigger><?php echo admin_icon('upload'); ?> Tải ảnh</button>
							<button type="button" class="btn btn-sm btn-ghost-danger" data-image-clear>Gỡ ảnh</button>
						</div>
						<input type="file" accept="image/jpeg,image/png,image/gif,image/webp" hidden data-upload-file>
						<div class="form-hint">Khuyến nghị ảnh ngang tỷ lệ 16:9, tối đa 5 MB.</div>
						<div class="invalid-feedback" data-error-for="image_path"></div>
					</div>
				</div>
				<div class="row g-3">
					<div class="col-md-8">
						<label class="form-label required" for="slider-title">Tiêu đề</label>
						<input type="text" class="form-control" id="slider-title" name="title" maxlength="191" required>
						<div class="invalid-feedback" data-error-for="title"></div>
					</div>
					<div class="col-md-4">
						<label class="form-label" for="slider-order">Thứ tự</label>
						<input type="number" class="form-control" id="slider-order" name="sort_order" value="0" step="1">
						<div class="invalid-feedback" data-error-for="sort_order"></div>
					</div>
					<div class="col-12">
						<label class="form-label" for="slider-subtitle">Mô tả ngắn</label>
						<input type="text" class="form-control" id="slider-subtitle" name="subtitle" maxlength="255">
						<div class="invalid-feedback" data-error-for="subtitle"></div>
					</div>
					<div class="col-md-6">
						<label class="form-label" for="slider-alt">Mô tả ảnh (alt)</label>
						<input type="text" class="form-control" id="slider-alt" name="alt_text" maxlength="191" placeholder="Mặc định dùng tiêu đề">
						<div class="invalid-feedback" data-error-for="alt_text"></div>
					</div>
					<div class="col-md-6">
						<label class="form-label" for="slider-link">Liên kết khi bấm</label>
						<input type="text" class="form-control" id="slider-link" name="link_url" maxlength="255" placeholder="https://… hoặc /du-an">
						<div class="invalid-feedback" data-error-for="link_url"></div>
					</div>
					<div class="col-12">
						<label class="form-check form-switch m-0">
							<input class="form-check-input" type="checkbox" name="status" value="1" checked>
							<span class="form-check-label">Hiển thị trên trang chủ</span>
						</label>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Hủy</button>
				<button type="submit" class="btn btn-primary ms-auto">Lưu</button>
			</div>
		<?php echo form_close(); ?>
	</div>
</div>
