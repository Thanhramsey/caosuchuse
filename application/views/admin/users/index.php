<?php defined('BASEPATH') OR exit('No direct script access allowed');
$hasFilter = $filters['q'] !== '' || $filters['status'] !== '' || $filters['role_id'] > 0;
?>
<div class="page-header d-print-none">
	<div class="container-xl">
		<div class="row g-2 align-items-center">
			<div class="col">
				<div class="page-pretitle">Người dùng & quyền</div>
				<h2 class="page-title">Người dùng</h2>
			</div>
			<?php if ($canManage): ?>
				<div class="col-auto ms-auto">
					<button type="button" class="btn btn-primary" data-entity-create data-title="Tạo tài khoản"><?php echo admin_icon('plus'); ?> Tạo tài khoản</button>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
<div class="page-body">
	<div class="container-xl">
		<div class="card">
			<div class="card-header admin-filter">
				<form class="row g-2 w-100 align-items-center" method="get" action="<?php echo site_url('admin/nguoi-dung'); ?>">
					<div class="col-12 col-md">
						<div class="input-icon">
							<span class="input-icon-addon"><?php echo admin_icon('search'); ?></span>
							<input type="search" class="form-control" name="q" value="<?php echo html_escape($filters['q']); ?>" placeholder="Tìm theo họ tên hoặc email…" aria-label="Từ khóa">
						</div>
					</div>
					<div class="col-6 col-md-3">
						<select class="form-select" name="role_id" aria-label="Vai trò" data-auto-submit>
							<option value="">Mọi vai trò</option>
							<?php foreach ($roles as $role): ?>
								<option value="<?php echo (int) $role['id']; ?>"<?php echo (int) $role['id'] === $filters['role_id'] ? ' selected' : ''; ?>><?php echo html_escape(admin_role_label($role['name'])); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="col-6 col-md-2">
						<select class="form-select" name="status" aria-label="Trạng thái" data-auto-submit>
							<option value="">Mọi trạng thái</option>
							<option value="1"<?php echo $filters['status'] === '1' ? ' selected' : ''; ?>>Đang hoạt động</option>
							<option value="0"<?php echo $filters['status'] === '0' ? ' selected' : ''; ?>>Đã khóa</option>
						</select>
					</div>
					<div class="col-auto">
						<button type="submit" class="btn btn-outline-primary"><?php echo admin_icon('filter'); ?> Lọc</button>
						<?php if ($hasFilter): ?><a class="btn btn-ghost-secondary" href="<?php echo site_url('admin/nguoi-dung'); ?>">Xóa lọc</a><?php endif; ?>
					</div>
				</form>
			</div>
			<?php if (empty($items)): ?>
				<div class="empty admin-empty">
					<div class="empty-icon"><?php echo admin_icon('users', 'icon icon-lg'); ?></div>
					<p class="empty-title">Không tìm thấy người dùng phù hợp</p>
					<p class="empty-subtitle text-secondary">Thử thay đổi từ khóa hoặc bộ lọc.</p>
				</div>
			<?php else: ?>
				<div class="table-responsive">
					<table class="table table-vcenter card-table table-hover">
						<thead>
							<tr>
								<th>Người dùng</th>
								<th>Vai trò</th>
								<th>Trạng thái</th>
								<th>Đăng nhập gần nhất</th>
								<th class="w-1"><span class="visually-hidden">Thao tác</span></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($items as $item):
								$isSelf = (int) $item['id'] === $currentUserId;
								$isActive = (int) $item['status'] === 1;
							?>
								<tr>
									<td>
										<div class="d-flex align-items-center gap-3">
											<span class="avatar admin-avatar"><?php echo html_escape(admin_initials($item['full_name'])); ?></span>
											<div class="admin-cell-title">
												<div class="fw-medium text-truncate"><?php echo html_escape($item['full_name']); ?><?php echo $isSelf ? ' <span class="badge bg-primary-lt ms-1">Bạn</span>' : ''; ?></div>
												<div class="text-secondary small text-truncate"><?php echo html_escape($item['email']); ?></div>
											</div>
										</div>
									</td>
									<td><?php echo $item['role_name'] !== NULL ? '<span class="badge ' . ($item['role_name'] === 'superadmin' ? 'bg-purple-lt' : 'bg-azure-lt') . '">' . html_escape(admin_role_label($item['role_name'])) . '</span>' : '<span class="text-secondary">—</span>'; ?></td>
									<td><?php echo $isActive ? '<span class="status status-green"><span class="status-dot status-dot-animated"></span>Hoạt động</span>' : '<span class="status status-red"><span class="status-dot"></span>Đã khóa</span>'; ?></td>
									<td class="text-secondary text-nowrap"><?php echo $item['last_login_at'] !== NULL ? admin_date($item['last_login_at']) : 'Chưa đăng nhập'; ?></td>
									<td>
										<?php if ($canManage): ?>
											<div class="btn-list flex-nowrap justify-content-end">
												<button type="button" class="btn btn-sm btn-icon btn-ghost-primary" title="Sửa" aria-label="Sửa <?php echo html_escape($item['email']); ?>" data-entity-edit="<?php echo site_url('admin/nguoi-dung/chi-tiet/' . $item['id']); ?>" data-title="Sửa tài khoản"><?php echo admin_icon('edit'); ?></button>
												<?php if (! $isSelf): ?>
													<button type="button" class="btn btn-sm btn-icon btn-ghost-<?php echo $isActive ? 'warning' : 'success'; ?>" title="<?php echo $isActive ? 'Khóa' : 'Mở khóa'; ?>" aria-label="<?php echo $isActive ? 'Khóa' : 'Mở khóa'; ?> <?php echo html_escape($item['email']); ?>" data-confirm-url="<?php echo site_url('admin/nguoi-dung/doi-trang-thai/' . $item['id']); ?>" data-confirm-title="<?php echo $isActive ? 'Khóa tài khoản' : 'Mở khóa tài khoản'; ?>" data-confirm-button="<?php echo $isActive ? 'Khóa' : 'Mở khóa'; ?>" data-confirm-variant="<?php echo $isActive ? 'warning' : 'success'; ?>" data-confirm-text="<?php echo $isActive ? 'Tài khoản ' . html_escape($item['email']) . ' sẽ không thể đăng nhập cho đến khi được mở khóa.' : 'Cho phép ' . html_escape($item['email']) . ' đăng nhập trở lại?'; ?>"><?php echo admin_icon($isActive ? 'lock' : 'unlock'); ?></button>
													<button type="button" class="btn btn-sm btn-icon btn-ghost-danger" title="Xóa" aria-label="Xóa <?php echo html_escape($item['email']); ?>" data-confirm-url="<?php echo site_url('admin/nguoi-dung/xoa/' . $item['id']); ?>" data-confirm-text="Xóa vĩnh viễn tài khoản <?php echo html_escape($item['email']); ?>?"><?php echo admin_icon('trash'); ?></button>
												<?php endif; ?>
											</div>
										<?php endif; ?>
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

<?php if ($canManage): ?>
<div class="modal modal-blur fade" id="entity-modal" tabindex="-1" aria-hidden="true" data-entity-modal data-bs-backdrop="static">
	<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<?php echo form_open('admin/nguoi-dung/luu', array('class' => 'modal-content', 'novalidate' => 'novalidate', 'autocomplete' => 'off')); ?>
			<input type="hidden" name="id" value="">
			<div class="modal-header">
				<h5 class="modal-title" data-modal-title>Tạo tài khoản</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
			</div>
			<div class="modal-body">
				<div class="row g-3">
					<div class="col-md-6">
						<label class="form-label required" for="user-name">Họ tên</label>
						<input type="text" class="form-control" id="user-name" name="full_name" maxlength="150" required>
						<div class="invalid-feedback" data-error-for="full_name"></div>
					</div>
					<div class="col-md-6">
						<label class="form-label required" for="user-email">Email</label>
						<input type="email" class="form-control" id="user-email" name="email" maxlength="191" required autocomplete="off">
						<div class="invalid-feedback" data-error-for="email"></div>
					</div>
					<div class="col-md-6">
						<label class="form-label required" for="user-role">Vai trò</label>
						<select class="form-select" id="user-role" name="role_id" required>
							<?php foreach ($roles as $role): ?>
								<option value="<?php echo (int) $role['id']; ?>"<?php echo $role['name'] === 'editor' ? ' selected' : ''; ?>><?php echo html_escape(admin_role_label($role['name'])); ?></option>
							<?php endforeach; ?>
						</select>
						<?php if (! $canAssignRole): ?><div class="form-hint">Bạn không có quyền thay đổi vai trò.</div><?php endif; ?>
						<div class="invalid-feedback" data-error-for="role_id"></div>
					</div>
					<div class="col-md-6">
						<label class="form-label" for="user-password" data-password-label>Mật khẩu</label>
						<input type="password" class="form-control" id="user-password" name="password" minlength="10" maxlength="72" autocomplete="new-password">
						<div class="form-hint" data-password-hint>Tối thiểu 10 ký tự.</div>
						<div class="invalid-feedback" data-error-for="password"></div>
					</div>
					<div class="col-12">
						<label class="form-check form-switch m-0">
							<input class="form-check-input" type="checkbox" name="status" value="1" checked>
							<span class="form-check-label">Cho phép đăng nhập</span>
						</label>
						<div class="invalid-feedback" data-error-for="status"></div>
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
<?php endif; ?>
