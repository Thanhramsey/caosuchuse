<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex, nofollow">
	<title>Đăng nhập quản trị · Cao su Chư Sê</title>
	<link rel="icon" href="<?php echo base_url('assets/images/logo.png'); ?>">
	<link rel="stylesheet" href="<?php echo base_url('assets/vendor/tabler/1.4.0/tabler.min.css'); ?>">
	<link rel="stylesheet" href="<?php echo base_url('assets/css/admin.css'); ?>">
</head>
<body class="admin-login-page d-flex flex-column">
	<div class="page page-center">
		<div class="container container-tight py-4">
			<div class="text-center mb-4">
				<img src="<?php echo base_url('assets/images/logo.png'); ?>" alt="Logo Công ty Cao su Chư Sê" width="64" height="74">
				<div class="admin-login-brand">Cao su Chư Sê</div>
			</div>
			<div class="card card-md">
				<div class="card-body">
					<h2 class="h2 text-center mb-4">Đăng nhập quản trị</h2>
					<?php if ($error !== ''): ?>
						<div class="alert alert-danger" role="alert"><?php echo html_escape($error); ?></div>
					<?php endif; ?>
					<?php echo form_open('admin/dang-nhap', array('autocomplete' => 'on', 'novalidate' => 'novalidate')); ?>
						<div class="mb-3">
							<label class="form-label" for="email">Email</label>
							<input type="email" class="form-control" id="email" name="email" value="<?php echo html_escape($email); ?>" placeholder="ten@congty.vn" required autocomplete="username" autofocus>
						</div>
						<div class="mb-3">
							<label class="form-label" for="password">Mật khẩu</label>
							<input type="password" class="form-control" id="password" name="password" required autocomplete="current-password">
						</div>
						<div class="form-footer">
							<button type="submit" class="btn btn-primary w-100">Đăng nhập</button>
						</div>
					<?php echo form_close(); ?>
				</div>
			</div>
			<div class="text-center text-secondary mt-3 small">Tài khoản bị khóa tạm 15 phút sau 5 lần nhập sai mật khẩu.</div>
		</div>
	</div>
</body>
</html>
