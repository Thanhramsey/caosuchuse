<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
		<footer class="footer footer-transparent d-print-none">
			<div class="container-xl text-secondary small">© <?php echo date('Y'); ?> Công ty TNHH MTV Cao su Chư Sê · Khu vực quản trị</div>
		</footer>
	</div>
</div>

<div class="modal modal-blur fade" id="confirm-modal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-sm modal-dialog-centered" role="document">
		<div class="modal-content">
			<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
			<div class="modal-status bg-danger" data-confirm-status></div>
			<div class="modal-body text-center py-4">
				<div class="admin-confirm-icon text-danger mb-2" data-confirm-icon><?php echo admin_icon('trash', 'icon icon-lg'); ?></div>
				<h3 data-confirm-title>Xác nhận xóa</h3>
				<div class="text-secondary" data-confirm-message></div>
			</div>
			<div class="modal-footer">
				<div class="w-100">
					<div class="row g-2">
						<div class="col"><button type="button" class="btn w-100" data-bs-dismiss="modal">Hủy</button></div>
						<div class="col"><button type="button" class="btn btn-danger w-100" data-confirm-submit>Xóa</button></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="toast-container position-fixed top-0 end-0 p-3" id="admin-toasts" aria-live="polite"></div>

<script src="<?php echo base_url('assets/vendor/bootstrap/5.3.3/bootstrap.bundle.min.js'); ?>"></script>
<?php if ($useEditor): ?><script src="<?php echo base_url('assets/vendor/jodit/4.2.27/jodit.min.js'); ?>"></script><?php endif; ?>
<script src="<?php echo admin_asset('assets/js/admin.js'); ?>"></script>
</body>
</html>
