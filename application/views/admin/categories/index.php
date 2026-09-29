<?php defined('BASEPATH') OR exit('No direct script access allowed');
$hasFilter = $filters['q'] !== '' || $filters['type'] !== '' || $filters['status'] !== '';
$typeColors = array('news' => 'bg-green-lt', 'projects' => 'bg-teal-lt', 'services' => 'bg-lime-lt');
?>
<div class="page-header d-print-none">
	<div class="container-xl">
		<div class="row g-2 align-items-center">
			<div class="col">
				<div class="page-pretitle">Nội dung</div>
				<h2 class="page-title">Danh mục</h2>
				<div class="text-secondary mt-1">Nhóm tin tức, dự án và dịch vụ theo chủ đề.</div>
			</div>
			<div class="col-auto ms-auto">
				<button type="button" class="btn btn-primary" data-entity-create data-title="Thêm danh mục"><?php echo admin_icon('plus'); ?> Thêm danh mục</button>
			</div>
		</div>
	</div>
</div>
<div class="page-body">
	<div class="container-xl">
		<div class="card">
			<div class="card-header admin-filter">
				<form class="row g-2 w-100 align-items-center" method="get" action="<?php echo site_url('admin/danh-muc'); ?>">
					<div class="col-12 col-md">
						<div class="input-icon">
							<span class="input-icon-addon"><?php echo admin_icon('search'); ?></span>
							<input type="search" class="form-control" name="q" value="<?php echo html_escape($filters['q']); ?>" placeholder="Tìm theo tên hoặc đường dẫn…" aria-label="Từ khóa">
						</div>
					</div>
					<div class="col-6 col-md-3">
						<select class="form-select" name="type" aria-label="Loại nội dung" data-auto-submit>
							<option value="">Mọi loại nội dung</option>
							<?php foreach ($types as $value => $label): ?>
								<option value="<?php echo $value; ?>"<?php echo $filters['type'] === $value ? ' selected' : ''; ?>><?php echo html_escape($label); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="col-6 col-md-2">
						<select class="form-select" name="status" aria-label="Trạng thái" data-auto-submit>
							<option value="">Mọi trạng thái</option>
							<option value="1"<?php echo $filters['status'] === '1' ? ' selected' : ''; ?>>Đang hiển thị</option>
							<option value="0"<?php echo $filters['status'] === '0' ? ' selected' : ''; ?>>Đang ẩn</option>
						</select>
					</div>
					<div class="col-auto">
						<button type="submit" class="btn btn-outline-primary"><?php echo admin_icon('filter'); ?> Lọc</button>
						<?php if ($hasFilter): ?><a class="btn btn-ghost-secondary" href="<?php echo site_url('admin/danh-muc'); ?>">Xóa lọc</a><?php endif; ?>
					</div>
				</form>
			</div>
			<?php if (empty($items)): ?>
				<div class="empty admin-empty">
					<div class="empty-icon"><?php echo admin_icon('folder', 'icon icon-lg'); ?></div>
					<p class="empty-title"><?php echo $hasFilter ? 'Không tìm thấy danh mục phù hợp' : 'Chưa có danh mục nào'; ?></p>
					<p class="empty-subtitle text-secondary"><?php echo $hasFilter ? 'Thử thay đổi từ khóa hoặc bộ lọc.' : 'Tạo danh mục để phân loại tin tức, dự án và dịch vụ.'; ?></p>
					<?php if (! $hasFilter): ?><div class="empty-action"><button type="button" class="btn btn-primary" data-entity-create data-title="Thêm danh mục"><?php echo admin_icon('plus'); ?> Thêm danh mục</button></div><?php endif; ?>
				</div>
			<?php else: ?>
				<div class="table-responsive">
					<table class="table table-vcenter card-table table-hover">
						<thead>
							<tr>
								<th>Tên danh mục</th>
								<th>Loại nội dung</th>
								<th class="text-center">Thứ tự</th>
								<th class="text-center">Số mục</th>
								<th>Trạng thái</th>
								<th class="w-1"><span class="visually-hidden">Thao tác</span></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($items as $item): ?>
								<tr>
									<td>
										<div class="fw-medium"><?php echo html_escape($item['name']); ?></div>
										<div class="text-secondary small">/<?php echo html_escape($item['slug']); ?><?php echo $item['description'] !== NULL && $item['description'] !== '' ? ' · ' . html_escape($item['description']) : ''; ?></div>
									</td>
									<td><span class="badge <?php echo isset($typeColors[$item['type']]) ? $typeColors[$item['type']] : 'bg-secondary-lt'; ?>"><?php echo html_escape(isset($types[$item['type']]) ? $types[$item['type']] : $item['type']); ?></span></td>
									<td class="text-center text-secondary"><?php echo (int) $item['sort_order']; ?></td>
									<td class="text-center"><?php echo (int) $item['item_count']; ?></td>
									<td><?php echo (int) $item['status'] === 1 ? '<span class="status status-green"><span class="status-dot"></span>Hiển thị</span>' : '<span class="status status-secondary"><span class="status-dot"></span>Đang ẩn</span>'; ?></td>
									<td>
										<div class="btn-list flex-nowrap justify-content-end">
											<button type="button" class="btn btn-sm btn-icon btn-ghost-primary" title="Sửa" aria-label="Sửa <?php echo html_escape($item['name']); ?>" data-entity-edit="<?php echo site_url('admin/danh-muc/chi-tiet/' . $item['id']); ?>" data-title="Sửa danh mục"><?php echo admin_icon('edit'); ?></button>
											<button type="button" class="btn btn-sm btn-icon btn-ghost-danger" title="Xóa" aria-label="Xóa <?php echo html_escape($item['name']); ?>" data-confirm-url="<?php echo site_url('admin/danh-muc/xoa/' . $item['id']); ?>" data-confirm-text="Xóa danh mục &quot;<?php echo html_escape($item['name']); ?>&quot;?<?php echo (int) $item['item_count'] > 0 ? ' Danh mục đang có ' . (int) $item['item_count'] . ' nội dung nên sẽ không xóa được.' : ''; ?>"><?php echo admin_icon('trash'); ?></button>
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
	<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<?php echo form_open('admin/danh-muc/luu', array('class' => 'modal-content', 'novalidate' => 'novalidate')); ?>
			<input type="hidden" name="id" value="">
			<div class="modal-header">
				<h5 class="modal-title" data-modal-title>Thêm danh mục</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
			</div>
			<div class="modal-body">
				<div class="row g-3">
					<div class="col-md-6">
						<label class="form-label required" for="cat-type">Loại nội dung</label>
						<select class="form-select" id="cat-type" name="type" required>
							<?php foreach ($types as $value => $label): ?>
								<option value="<?php echo $value; ?>"<?php echo $filters['type'] === $value ? ' selected' : ''; ?>><?php echo html_escape($label); ?></option>
							<?php endforeach; ?>
						</select>
						<div class="invalid-feedback" data-error-for="type"></div>
					</div>
					<div class="col-md-6">
						<label class="form-label" for="cat-order">Thứ tự hiển thị</label>
						<input type="number" class="form-control" id="cat-order" name="sort_order" value="0" step="1">
						<div class="invalid-feedback" data-error-for="sort_order"></div>
					</div>
					<div class="col-md-6">
						<label class="form-label required" for="cat-name">Tên danh mục</label>
						<input type="text" class="form-control" id="cat-name" name="name" maxlength="150" required data-slug-source>
						<div class="invalid-feedback" data-error-for="name"></div>
					</div>
					<div class="col-md-6">
						<label class="form-label" for="cat-slug">Đường dẫn</label>
						<input type="text" class="form-control" id="cat-slug" name="slug" maxlength="150" placeholder="tự động tạo từ tên" data-slug-target>
						<div class="invalid-feedback" data-error-for="slug"></div>
					</div>
					<div class="col-12">
						<label class="form-label" for="cat-description">Mô tả</label>
						<input type="text" class="form-control" id="cat-description" name="description" maxlength="255">
						<div class="invalid-feedback" data-error-for="description"></div>
					</div>
					<div class="col-12">
						<label class="form-check form-switch m-0">
							<input class="form-check-input" type="checkbox" name="status" value="1" checked>
							<span class="form-check-label">Hiển thị danh mục</span>
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
