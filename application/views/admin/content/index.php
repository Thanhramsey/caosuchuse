<?php defined('BASEPATH') OR exit('No direct script access allowed');
$hasFilter = $filters['q'] !== '' || $filters['status'] !== '' || $filters['category_id'] > 0;
$statusLabels = array('draft' => array('Bản nháp', 'bg-yellow-lt'), 'published' => array('Đã xuất bản', 'bg-green-lt'));
$lowerLabel = mb_strtolower($resourceLabel, 'UTF-8');
?>
<div class="page-header d-print-none">
	<div class="container-xl">
		<div class="row g-2 align-items-center">
			<div class="col">
				<div class="page-pretitle">Nội dung</div>
				<h2 class="page-title"><?php echo html_escape($resourceLabel); ?></h2>
			</div>
			<?php if ($canCreate): ?>
				<div class="col-auto ms-auto">
					<button type="button" class="btn btn-primary" data-entity-create data-title="Thêm <?php echo html_escape($lowerLabel); ?>">
						<?php echo admin_icon('plus'); ?> Thêm mới
					</button>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
<div class="page-body">
	<div class="container-xl">
		<div class="card">
			<div class="card-header admin-filter">
				<form class="row g-2 w-100 align-items-center" method="get" action="<?php echo site_url('admin/noi-dung/' . $resource); ?>">
					<div class="col-12 col-md">
						<div class="input-icon">
							<span class="input-icon-addon"><?php echo admin_icon('search'); ?></span>
							<input type="search" class="form-control" name="q" value="<?php echo html_escape($filters['q']); ?>" placeholder="Tìm theo tiêu đề hoặc đường dẫn…" aria-label="Từ khóa">
						</div>
					</div>
					<div class="col-6 col-md-3">
						<select class="form-select" name="category_id" aria-label="Danh mục" data-auto-submit>
							<option value="">Tất cả danh mục</option>
							<?php foreach ($categories as $category): ?>
								<option value="<?php echo (int) $category['id']; ?>"<?php echo (int) $category['id'] === $filters['category_id'] ? ' selected' : ''; ?>><?php echo html_escape($category['name']); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="col-6 col-md-2">
						<select class="form-select" name="status" aria-label="Trạng thái" data-auto-submit>
							<option value="">Mọi trạng thái</option>
							<?php foreach ($statusLabels as $value => $label): ?>
								<option value="<?php echo $value; ?>"<?php echo $filters['status'] === $value ? ' selected' : ''; ?>><?php echo $label[0]; ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="col-auto">
						<button type="submit" class="btn btn-outline-primary"><?php echo admin_icon('filter'); ?> Lọc</button>
						<?php if ($hasFilter): ?><a class="btn btn-ghost-secondary" href="<?php echo site_url('admin/noi-dung/' . $resource); ?>">Xóa lọc</a><?php endif; ?>
					</div>
				</form>
			</div>
			<?php if (empty($items)): ?>
				<div class="empty admin-empty">
					<div class="empty-icon"><?php echo admin_icon($resource, 'icon icon-lg'); ?></div>
					<p class="empty-title"><?php echo $hasFilter ? 'Không tìm thấy kết quả phù hợp' : 'Chưa có ' . html_escape($lowerLabel) . ' nào'; ?></p>
					<p class="empty-subtitle text-secondary"><?php echo $hasFilter ? 'Thử thay đổi từ khóa hoặc bộ lọc.' : 'Bắt đầu bằng cách thêm mục đầu tiên.'; ?></p>
					<?php if ($canCreate && ! $hasFilter): ?>
						<div class="empty-action"><button type="button" class="btn btn-primary" data-entity-create data-title="Thêm <?php echo html_escape($lowerLabel); ?>"><?php echo admin_icon('plus'); ?> Thêm mới</button></div>
					<?php endif; ?>
				</div>
			<?php else: ?>
				<div class="table-responsive">
					<table class="table table-vcenter card-table table-hover">
						<thead>
							<tr>
								<th>Tiêu đề</th>
								<th>Danh mục</th>
								<th>Trạng thái</th>
								<th>Cập nhật</th>
								<th class="w-1"><span class="visually-hidden">Thao tác</span></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($items as $item):
								$canEdit = $canUpdateAny || ($canUpdateOwn && (int) $item['created_by'] === $currentUserId);
								$status = isset($statusLabels[$item['status']]) ? $statusLabels[$item['status']] : array($item['status'], 'bg-secondary-lt');
							?>
								<tr>
									<td>
										<div class="d-flex align-items-center gap-3">
											<?php if (! empty($item['cover_path'])): ?>
												<img class="admin-thumb" src="<?php echo base_url($item['cover_path']); ?>" alt="" loading="lazy">
											<?php else: ?>
												<span class="admin-thumb admin-thumb-empty"><?php echo admin_icon($resource); ?></span>
											<?php endif; ?>
											<div class="admin-cell-title">
												<div class="fw-medium text-truncate"><?php echo html_escape($item['title']); ?></div>
												<div class="text-secondary small text-truncate">/<?php echo html_escape($item['slug']); ?></div>
											</div>
										</div>
									</td>
									<td><?php echo $item['category_name'] !== NULL ? '<span class="badge bg-azure-lt">' . html_escape($item['category_name']) . '</span>' : '<span class="text-secondary">—</span>'; ?></td>
									<td><span class="badge <?php echo $status[1]; ?>"><?php echo html_escape($status[0]); ?></span></td>
									<td class="text-secondary text-nowrap"><?php echo admin_date($item['updated_at']); ?></td>
									<td>
										<div class="btn-list flex-nowrap justify-content-end">
											<?php if ($canEdit): ?>
												<button type="button" class="btn btn-sm btn-icon btn-ghost-primary" title="Sửa" aria-label="Sửa <?php echo html_escape($item['title']); ?>" data-entity-edit="<?php echo site_url('admin/noi-dung/' . $resource . '/chi-tiet/' . $item['id']); ?>" data-title="Sửa <?php echo html_escape($lowerLabel); ?>"><?php echo admin_icon('edit'); ?></button>
											<?php endif; ?>
											<?php if ($canDelete): ?>
												<button type="button" class="btn btn-sm btn-icon btn-ghost-danger" title="Xóa" aria-label="Xóa <?php echo html_escape($item['title']); ?>" data-confirm-url="<?php echo site_url('admin/noi-dung/' . $resource . '/xoa/' . $item['id']); ?>" data-confirm-text="Bạn có chắc muốn xóa &quot;<?php echo html_escape($item['title']); ?>&quot;? Thao tác này không thể hoàn tác."><?php echo admin_icon('trash'); ?></button>
											<?php endif; ?>
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

<div class="modal modal-blur fade" id="entity-modal" tabindex="-1" aria-hidden="true" data-entity-modal data-bs-backdrop="static" data-bs-focus="false">
	<div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
		<?php echo form_open('admin/noi-dung/' . $resource . '/luu', array('class' => 'modal-content', 'novalidate' => 'novalidate')); ?>
			<input type="hidden" name="id" value="">
			<div class="modal-header">
				<h5 class="modal-title" data-modal-title>Thêm mới</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
			</div>
			<div class="modal-body">
				<div class="row g-4">
					<div class="col-lg-8">
						<div class="mb-3">
							<label class="form-label required" for="field-title">Tiêu đề</label>
							<input type="text" class="form-control form-control-lg" id="field-title" name="title" maxlength="191" required data-slug-source>
							<div class="invalid-feedback" data-error-for="title"></div>
						</div>
						<div class="mb-3">
							<label class="form-label" for="field-slug">Đường dẫn</label>
							<div class="input-group">
								<span class="input-group-text">/</span>
								<input type="text" class="form-control" id="field-slug" name="slug" maxlength="191" placeholder="tự động tạo từ tiêu đề" data-slug-target>
							</div>
							<div class="invalid-feedback" data-error-for="slug"></div>
						</div>
						<div class="mb-3">
							<label class="form-label" for="field-excerpt">Tóm tắt</label>
							<textarea class="form-control" id="field-excerpt" name="excerpt" rows="3" maxlength="1000" placeholder="Mô tả ngắn hiển thị ở danh sách và thẻ SEO"></textarea>
							<div class="invalid-feedback" data-error-for="excerpt"></div>
						</div>
						<div>
							<label class="form-label" for="field-content">Nội dung</label>
							<textarea id="field-content" name="content_html" data-editor></textarea>
							<div class="invalid-feedback" data-error-for="content_html"></div>
						</div>
					</div>
					<div class="col-lg-4">
						<div class="admin-side-panel">
							<div class="mb-3">
								<label class="form-label required" for="field-status">Trạng thái</label>
								<select class="form-select" id="field-status" name="status">
									<option value="draft">Bản nháp</option>
									<option value="published"<?php echo $canPublish ? '' : ' disabled'; ?>>Xuất bản<?php echo $canPublish ? '' : ' (không có quyền)'; ?></option>
								</select>
								<div class="invalid-feedback" data-error-for="status"></div>
							</div>
							<div class="mb-3">
								<label class="form-label" for="field-category">Danh mục</label>
								<select class="form-select" id="field-category" name="category_id">
									<option value="">— Không chọn —</option>
									<?php foreach ($categories as $category): ?>
										<option value="<?php echo (int) $category['id']; ?>"><?php echo html_escape($category['name']); ?><?php echo (int) $category['status'] === 1 ? '' : ' (đang ẩn)'; ?></option>
									<?php endforeach; ?>
								</select>
								<div class="invalid-feedback" data-error-for="category_id"></div>
								<?php if (empty($categories) && admin_can('categories.manage')): ?>
									<div class="form-hint">Chưa có danh mục. <a href="<?php echo site_url('admin/danh-muc'); ?>">Tạo danh mục</a></div>
								<?php endif; ?>
							</div>
							<div>
								<label class="form-label">Ảnh đại diện</label>
								<div class="admin-image-field" data-image-field>
									<input type="hidden" name="cover_path" data-image-input>
									<div class="admin-image-preview" data-image-preview></div>
									<div class="btn-list mt-2">
										<button type="button" class="btn btn-sm" data-upload-trigger><?php echo admin_icon('upload'); ?> Tải ảnh</button>
										<button type="button" class="btn btn-sm btn-ghost-danger" data-image-clear>Gỡ ảnh</button>
									</div>
									<input type="file" accept="image/jpeg,image/png,image/gif,image/webp" hidden data-upload-file>
									<div class="form-hint">JPG, PNG, GIF hoặc WEBP, tối đa 5 MB.</div>
									<div class="invalid-feedback" data-error-for="cover_path"></div>
								</div>
							</div>
							<?php if (in_array('attachments', $fields, TRUE) || in_array('gallery', $fields, TRUE)): ?>
							<div class="mt-4" data-attachments-field>
								<label class="form-label"><?php echo in_array('gallery', $fields, TRUE) ? 'Ảnh thư viện' : 'Tệp đính kèm'; ?></label>
								<div class="list-group list-group-flush border rounded" data-attachments-list></div>
								<button type="button" class="btn btn-sm mt-2" data-attachments-trigger><?php echo admin_icon('upload'); ?> Chọn tệp</button>
								<input type="file" multiple hidden data-attachments-file accept="<?php echo in_array('gallery', $fields, TRUE) ? 'image/jpeg,image/png,image/gif,image/webp' : '.jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar'; ?>">
								<div class="form-hint"><?php echo in_array('gallery', $fields, TRUE) ? 'JPG, PNG, GIF hoặc WEBP, mỗi ảnh tối đa 5 MB.' : 'PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, ZIP hoặc RAR, mỗi tệp tối đa 20 MB.'; ?></div>
								<div class="invalid-feedback" data-error-for="attachments"></div>
							</div>
							<?php endif; ?>
						</div>
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
