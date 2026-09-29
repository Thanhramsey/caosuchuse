<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="page-header d-print-none">
	<div class="container-xl">
		<div class="page-pretitle">Người dùng & quyền</div>
		<h2 class="page-title">Phân quyền theo vai trò</h2>
	</div>
</div>
<div class="page-body">
	<div class="container-xl">
		<div class="row g-4">
			<div class="col-lg-4">
				<div class="card">
					<div class="card-header"><h3 class="card-title">Vai trò</h3></div>
					<div class="list-group list-group-flush">
						<?php foreach ($roles as $item): ?>
							<a class="list-group-item list-group-item-action<?php echo (int) $item['id'] === (int) $role['id'] ? ' active' : ''; ?>" href="<?php echo site_url('admin/phan-quyen/' . $item['id']); ?>">
								<div class="d-flex align-items-center">
									<span class="avatar avatar-sm admin-avatar me-3"><?php echo admin_icon('shield'); ?></span>
									<div class="flex-fill">
										<div class="fw-medium"><?php echo html_escape(admin_role_label($item['name'])); ?></div>
										<div class="small text-secondary"><?php echo (int) $item['user_count']; ?> người dùng · <?php echo (int) $item['permission_count']; ?> quyền</div>
									</div>
								</div>
							</a>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
			<div class="col-lg-8">
				<?php echo form_open('admin/phan-quyen/' . (int) $role['id'] . '/luu', array('class' => 'card')); ?>
					<div class="card-header">
						<div>
							<h3 class="card-title"><?php echo html_escape(admin_role_label($role['name'])); ?></h3>
							<div class="card-subtitle">Chọn các chức năng vai trò này được phép sử dụng.</div>
						</div>
					</div>
					<div class="card-body">
						<?php if ($locked): ?>
							<div class="alert alert-info">Vai trò quản trị toàn hệ thống luôn có đủ quyền và không thể chỉnh sửa để tránh mất quyền quản trị.</div>
						<?php endif; ?>
						<?php foreach ($groups as $groupName => $permissions): ?>
							<div class="admin-permission-group">
								<div class="admin-permission-group-title"><?php echo html_escape($groupName); ?></div>
								<div class="row g-2">
									<?php foreach ($permissions as $permission): ?>
										<div class="col-md-6">
											<label class="form-check admin-permission-item">
												<input class="form-check-input" type="checkbox" name="permissions[]" value="<?php echo (int) $permission['id']; ?>"<?php echo in_array((int) $permission['id'], $assigned, TRUE) ? ' checked' : ''; ?><?php echo $locked ? ' disabled' : ''; ?>>
												<span class="form-check-label">
													<?php echo html_escape(admin_permission_label($permission['name'])); ?>
													<span class="d-block small text-secondary"><?php echo html_escape($permission['name']); ?></span>
												</span>
											</label>
										</div>
									<?php endforeach; ?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
					<?php if (! $locked): ?>
						<div class="card-footer text-end">
							<button type="submit" class="btn btn-primary">Lưu phân quyền</button>
						</div>
					<?php endif; ?>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>
