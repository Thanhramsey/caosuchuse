(function () {
	'use strict';

	var body = document.body;
	var csrfName = metaContent('csrf-name');
	var csrfHash = metaContent('csrf-hash');
	var baseUrl = body.getAttribute('data-base-url') || '/';
	var uploadUrl = body.getAttribute('data-upload-url') || '';
	var editors = [];

	function metaContent(name) {
		var el = document.querySelector('meta[name="' + name + '"]');
		return el ? el.getAttribute('content') : '';
	}

	function toast(message, type) {
		var container = document.getElementById('admin-toasts');
		if (!container || !message) {
			return;
		}
		var el = document.createElement('div');
		el.className = 'toast align-items-center border-0 text-bg-' + (type === 'error' ? 'danger' : 'success');
		el.setAttribute('role', type === 'error' ? 'alert' : 'status');
		el.innerHTML = '<div class="d-flex"><div class="toast-body"></div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Đóng"></button></div>';
		el.querySelector('.toast-body').textContent = message;
		container.appendChild(el);
		el.addEventListener('hidden.bs.toast', function () { el.remove(); });
		new bootstrap.Toast(el, { delay: type === 'error' ? 6000 : 3500 }).show();
	}

	function parseResponse(response) {
		return response.text().then(function (text) {
			try {
				return JSON.parse(text);
			} catch (e) {
				throw new Error(response.status === 403
					? 'Phiên làm việc đã hết hạn hoặc bạn không có quyền. Vui lòng tải lại trang.'
					: 'Máy chủ trả về phản hồi không hợp lệ (mã ' + response.status + ').');
			}
		});
	}

	function postForm(url, formData) {
		if (csrfName && !formData.has(csrfName)) {
			formData.append(csrfName, csrfHash);
		}
		return fetch(url, {
			method: 'POST',
			body: formData,
			credentials: 'same-origin',
			headers: { 'X-Requested-With': 'XMLHttpRequest' }
		}).then(parseResponse);
	}

	function getJson(url) {
		return fetch(url, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } }).then(parseResponse);
	}

	function reloadWithMessage(message) {
		try {
			sessionStorage.setItem('admin_flash', message || 'Đã lưu thay đổi.');
		} catch (e) {}
		window.location.reload();
	}

	function setLoading(button, loading) {
		if (!button) {
			return;
		}
		button.disabled = loading;
		button.classList.toggle('btn-loading', loading);
	}

	function slugify(text) {
		return String(text || '')
			.normalize('NFD').replace(/[\u0300-\u036f]/g, '')
			.replace(/đ/g, 'd').replace(/Đ/g, 'D')
			.toLowerCase()
			.replace(/[^a-z0-9]+/g, '-')
			.replace(/^-+|-+$/g, '');
	}

	/* ---------- Flash messages after reload ---------- */
	(function () {
		var stored = null;
		try {
			stored = sessionStorage.getItem('admin_flash');
			sessionStorage.removeItem('admin_flash');
		} catch (e) {}
		toast(stored, 'success');
		toast(body.getAttribute('data-flash-success'), 'success');
		toast(body.getAttribute('data-flash-error'), 'error');
	})();

	/* ---------- Filters ---------- */
	document.querySelectorAll('[data-auto-submit]').forEach(function (select) {
		select.addEventListener('change', function () { select.form.submit(); });
	});

	/* ---------- Rich text editor ---------- */
	if (window.Jodit) {
		document.querySelectorAll('textarea[data-editor]').forEach(function (textarea) {
			var editor = Jodit.make(textarea, {
				height: 420,
				toolbarAdaptive: false,
				askBeforePasteHTML: false,
				askBeforePasteFromWord: false,
				defaultActionOnPaste: 'insert_clear_html',
				showCharsCounter: false,
				showXPathInStatusbar: false,
				buttons: 'bold,italic,underline,strikethrough,|,paragraph,ul,ol,|,align,|,link,image,table,hr,|,eraser,undo,redo,|,source,fullsize',
				uploader: {
					url: uploadUrl,
					insertImageAsBase64URI: false,
					prepareData: function (data) {
						data.append(csrfName, csrfHash);
						return data;
					}
				}
			});
			editors.push({ textarea: textarea, editor: editor });
		});
	}

	function editorFor(textarea) {
		for (var i = 0; i < editors.length; i++) {
			if (editors[i].textarea === textarea) {
				return editors[i].editor;
			}
		}
		return null;
	}

	/* ---------- Image upload fields ---------- */
	document.querySelectorAll('input[type="hidden"][name="image_path"]:not([data-image-input])').forEach(function (input) {
		var field = document.createElement('div');
		field.className = 'admin-image-field mt-2';
		field.setAttribute('data-image-field', '');
		field.innerHTML = '<div class="admin-image-preview" data-image-preview><span class="text-secondary small">Chưa có ảnh</span></div><div class="btn-list mt-2"><button type="button" class="btn btn-sm" data-upload-trigger>' + (window.adminUploadIcon || 'Tải ảnh') + '</button><button type="button" class="btn btn-sm btn-ghost-danger" data-image-clear>Gỡ ảnh</button></div><input type="file" accept="image/jpeg,image/png,image/gif,image/webp" hidden data-upload-file>';
		input.parentNode.insertBefore(field, input);
		field.appendChild(input);
		input.setAttribute('data-image-input', '');
	});

	function renderPreview(field) {
		var input = field.querySelector('[data-image-input]');
		var preview = field.querySelector('[data-image-preview]');
		var clear = field.querySelector('[data-image-clear]');
		preview.innerHTML = '';
		if (input.value) {
			var img = document.createElement('img');
			img.src = baseUrl + input.value;
			img.alt = '';
			preview.appendChild(img);
		} else {
			var empty = document.createElement('span');
			empty.className = 'text-secondary small';
			empty.textContent = 'Chưa có ảnh';
			preview.appendChild(empty);
		}
		if (clear) {
			clear.hidden = !input.value;
		}
	}

	document.querySelectorAll('[data-image-field]').forEach(function (field) {
		var input = field.querySelector('[data-image-input]');
		var fileInput = field.querySelector('[data-upload-file]');
		var trigger = field.querySelector('[data-upload-trigger]');
		var clear = field.querySelector('[data-image-clear]');

		trigger.addEventListener('click', function () { fileInput.click(); });
		clear.addEventListener('click', function () {
			input.value = '';
			renderPreview(field);
		});
		fileInput.addEventListener('change', function () {
			if (!fileInput.files.length) {
				return;
			}
			var data = new FormData();
			data.append('files', fileInput.files[0]);
			setLoading(trigger, true);
			postForm(uploadUrl + '?kind=image', data)
				.then(function (res) {
					if (!res.success) {
						throw new Error(res.message || 'Không tải được ảnh.');
					}
					input.value = res.data.path;
					renderPreview(field);
					clearFieldError(field.closest('form'), input.name);
				})
				.catch(function (err) { toast(err.message, 'error'); })
				.finally(function () {
					setLoading(trigger, false);
					fileInput.value = '';
				});
		});
		renderPreview(field);
	});

	/* ---------- Attachment upload fields ---------- */
	function renderAttachments(field, items) {
		var list = field.querySelector('[data-attachments-list]');
		list.innerHTML = '';
		(items || []).forEach(function (item, index) {
			var row = document.createElement('div');
			row.className = 'list-group-item px-2 py-2';
			row.innerHTML = '<div class="d-flex align-items-center gap-2"><span class="text-secondary small text-uppercase">' + String(item.ext || 'file') + '</span><input class="form-control form-control-sm" name="attachments[' + index + '][title]" value="" maxlength="191"><button type="button" class="btn btn-sm btn-icon btn-ghost-danger" data-attachment-remove aria-label="Gỡ tệp">' + (window.adminIconTrash || '×') + '</button></div><input type="hidden" name="attachments[' + index + '][path]">';
			row.querySelector('input[name$="[title]"]').value = item.title || item.name || item.path.split('/').pop();
			row.querySelector('input[name$="[path]"]').value = item.path;
			row.querySelector('[data-attachment-remove]').addEventListener('click', function () {
				row.remove();
				renumberAttachments(field);
			});
			list.appendChild(row);
		});
	}

	function renumberAttachments(field) {
		field.querySelectorAll('[data-attachments-list] > .list-group-item').forEach(function (row, index) {
			row.querySelector('input[name*="[title]"]').name = 'attachments[' + index + '][title]';
			row.querySelector('input[name*="[path]"]').name = 'attachments[' + index + '][path]';
		});
	}

	document.querySelectorAll('[data-attachments-field]').forEach(function (field) {
		var trigger = field.querySelector('[data-attachments-trigger]');
		var fileInput = field.querySelector('[data-attachments-file]');
		trigger.addEventListener('click', function () { fileInput.click(); });
		fileInput.addEventListener('change', function () {
			Array.prototype.forEach.call(fileInput.files, function (file) {
				var data = new FormData();
				data.append('files[]', file);
				setLoading(trigger, true);
				postForm(uploadUrl, data).then(function (res) {
					if (!res.success || !res.data || !res.data.items) {
						throw new Error(res.message || 'Không tải được tệp.');
					}
					var current = [];
					field.querySelectorAll('[data-attachments-list] > .list-group-item').forEach(function (row) {
						current.push({ path: row.querySelector('input[name*="[path]"]').value, title: row.querySelector('input[name*="[title]"]').value, ext: row.querySelector('input[name*="[path]"]').value.split('.').pop() });
					});
					renderAttachments(field, current.concat(res.data.items));
				}).catch(function (err) { toast(err.message, 'error'); }).finally(function () { setLoading(trigger, false); });
			});
			fileInput.value = '';
		});
		renderAttachments(field, []);
	});

	/* ---------- Validation errors ---------- */
	function clearErrors(form) {
		form.querySelectorAll('.is-invalid').forEach(function (el) { el.classList.remove('is-invalid'); });
		form.querySelectorAll('[data-error-for]').forEach(function (el) {
			el.textContent = '';
			el.classList.remove('d-block');
		});
	}

	function clearFieldError(form, name) {
		if (!form) {
			return;
		}
		var feedback = form.querySelector('[data-error-for="' + name + '"]');
		if (feedback) {
			feedback.textContent = '';
			feedback.classList.remove('d-block');
		}
		var field = form.elements[name];
		if (field && field.classList) {
			field.classList.remove('is-invalid');
		}
	}

	function showErrors(form, errors) {
		var first = null;
		Object.keys(errors).forEach(function (name) {
			var feedback = form.querySelector('[data-error-for="' + name + '"]');
			var field = form.elements[name];
			if (feedback) {
				feedback.textContent = errors[name];
				feedback.classList.add('d-block');
			}
			if (field && field.classList && field.type !== 'hidden') {
				field.classList.add('is-invalid');
				first = first || field;
			}
		});
		if (first) {
			first.focus();
		}
	}

	/* ---------- Create / edit modal ---------- */
	var modalEl = document.querySelector('[data-entity-modal]');
	if (modalEl) {
		var form = modalEl.querySelector('form');
		var modal = bootstrap.Modal.getOrCreateInstance(modalEl, { focus: false });
		var titleEl = modalEl.querySelector('[data-modal-title]');
		var submitButton = form.querySelector('[type="submit"]');
		var slugSource = form.querySelector('[data-slug-source]');
		var slugTarget = form.querySelector('[data-slug-target]');
		var passwordHint = form.querySelector('[data-password-hint]');
		var slugTouched = false;

		var resetForm = function () {
			form.reset();
			form.elements.id.value = '';
			clearErrors(form);
			editors.forEach(function (item) {
				if (form.contains(item.textarea)) {
					item.editor.value = '';
				}
			});
			form.querySelectorAll('[data-image-field]').forEach(renderPreview);
			form.querySelectorAll('[data-attachments-field]').forEach(function (field) { renderAttachments(field, []); });
			slugTouched = false;
		};

		var fillForm = function (data) {
			Object.keys(data).forEach(function (name) {
				var field = form.elements[name];
				if (!field) {
					return;
				}
				var value = data[name] === null || data[name] === undefined ? '' : String(data[name]);
				if (field.type === 'checkbox') {
					field.checked = value === '1';
					return;
				}
				field.value = value;
				var editor = field.tagName === 'TEXTAREA' ? editorFor(field) : null;
				if (editor) {
					editor.value = value;
				}
			});
			form.querySelectorAll('[data-image-field]').forEach(renderPreview);
			form.querySelectorAll('[data-attachments-field]').forEach(function (field) { renderAttachments(field, data.attachments || []); });
			slugTouched = !!(slugTarget && slugTarget.value);
		};

		var setMode = function (isEdit) {
			if (passwordHint) {
				passwordHint.textContent = isEdit ? 'Để trống nếu giữ nguyên mật khẩu hiện tại.' : 'Tối thiểu 10 ký tự.';
				form.querySelector('[data-password-label]').classList.toggle('required', !isEdit);
			}
		};

		document.querySelectorAll('[data-entity-create]').forEach(function (button) {
			button.addEventListener('click', function () {
				resetForm();
				setMode(false);
				titleEl.textContent = button.getAttribute('data-title') || 'Thêm mới';
				modal.show();
			});
		});

		document.querySelectorAll('[data-entity-edit]').forEach(function (button) {
			button.addEventListener('click', function () {
				resetForm();
				setMode(true);
				titleEl.textContent = button.getAttribute('data-title') || 'Chỉnh sửa';
				setLoading(button, true);
				getJson(button.getAttribute('data-entity-edit'))
					.then(function (res) {
						if (!res.success) {
							throw new Error(res.message || 'Không tải được dữ liệu.');
						}
						fillForm(res.data);
						modal.show();
					})
					.catch(function (err) { toast(err.message, 'error'); })
					.finally(function () { setLoading(button, false); });
			});
		});

		if (slugSource && slugTarget) {
			slugTarget.addEventListener('input', function () { slugTouched = slugTarget.value !== ''; });
			slugTarget.addEventListener('blur', function () { slugTarget.value = slugify(slugTarget.value); });
			slugSource.addEventListener('input', function () {
				if (!slugTouched) {
					slugTarget.value = slugify(slugSource.value);
				}
			});
		}

		form.addEventListener('input', function (event) {
			if (event.target.name) {
				clearFieldError(form, event.target.name);
			}
		});

		form.addEventListener('submit', function (event) {
			event.preventDefault();
			editors.forEach(function (item) {
				if (form.contains(item.textarea)) {
					item.textarea.value = item.editor.value;
				}
			});
			clearErrors(form);
			setLoading(submitButton, true);
			postForm(form.action, new FormData(form))
				.then(function (res) {
					if (res.success) {
						modal.hide();
						reloadWithMessage(res.message);
						return;
					}
					showErrors(form, res.errors || {});
					toast(res.message || 'Không thể lưu dữ liệu.', 'error');
				})
				.catch(function (err) { toast(err.message, 'error'); })
				.finally(function () { setLoading(submitButton, false); });
		});
	}

	/* ---------- Confirm dialog (delete, lock/unlock) ---------- */
	var confirmEl = document.getElementById('confirm-modal');
	if (confirmEl) {
		var confirmModal = bootstrap.Modal.getOrCreateInstance(confirmEl);
		var confirmButton = confirmEl.querySelector('[data-confirm-submit]');
		var confirmStatus = confirmEl.querySelector('[data-confirm-status]');
		var confirmIcon = confirmEl.querySelector('[data-confirm-icon]');
		var pendingUrl = null;

		document.querySelectorAll('[data-confirm-url]').forEach(function (button) {
			button.addEventListener('click', function () {
				var variant = button.getAttribute('data-confirm-variant') || 'danger';
				pendingUrl = button.getAttribute('data-confirm-url');
				confirmEl.querySelector('[data-confirm-title]').textContent = button.getAttribute('data-confirm-title') || 'Xác nhận xóa';
				confirmEl.querySelector('[data-confirm-message]').textContent = button.getAttribute('data-confirm-text') || '';
				confirmButton.textContent = button.getAttribute('data-confirm-button') || 'Xóa';
				confirmButton.className = 'btn w-100 btn-' + variant;
				confirmStatus.className = 'modal-status bg-' + variant;
				confirmIcon.className = 'admin-confirm-icon mb-2 text-' + variant;
				confirmModal.show();
			});
		});

		confirmButton.addEventListener('click', function () {
			if (!pendingUrl) {
				return;
			}
			setLoading(confirmButton, true);
			postForm(pendingUrl, new FormData())
				.then(function (res) {
					if (!res.success) {
						throw new Error(res.message || 'Không thể thực hiện thao tác.');
					}
					confirmModal.hide();
					reloadWithMessage(res.message);
				})
				.catch(function (err) {
					confirmModal.hide();
					toast(err.message, 'error');
				})
				.finally(function () { setLoading(confirmButton, false); });
		});
	}
})();
