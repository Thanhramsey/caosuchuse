<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Media extends Admin_Controller
{
	const IMAGE_TYPES = 'jpg|jpeg|png|gif|webp';
	const FILE_TYPES = 'jpg|jpeg|png|gif|webp|pdf|doc|docx|xls|xlsx|ppt|pptx|zip|rar';

	public function index()
	{
		$this->requirePermission('media.upload');
		$files = array();
		$path = FCPATH . 'assets/uploads/';
		if (is_dir($path))
		{
			foreach (glob($path . '*') as $file)
			{
				if (is_file($file) && preg_match('/^[a-f0-9]{32}\.[a-z0-9]+$/i', basename($file))) $files[] = array('name' => basename($file), 'path' => 'assets/uploads/' . basename($file), 'size' => (int) filesize($file), 'is_image' => (bool) preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $file));
			}
		}
		usort($files, function ($left, $right) { return strcmp($right['name'], $left['name']); });
		$this->render('admin/media/index', array('pageTitle' => 'Media / Tệp', 'activeMenu' => 'media', 'files' => $files, 'canDelete' => $this->admin_auth->can('media.delete')));
	}

	public function upload()
	{
		$this->requirePermission('media.upload');
		$this->requirePost();
		$uploadPath = FCPATH . 'assets/uploads/';
		if (! is_dir($uploadPath))
		{
			if (! mkdir($uploadPath, 0755, TRUE) && ! is_dir($uploadPath))
			{
				return $this->jsonError('Không thể tạo thư mục lưu tệp trên máy chủ.');
			}
		}
		if (! is_writable($uploadPath))
		{
			return $this->jsonError('Thư mục lưu tệp không có quyền ghi.');
		}
		$fields = $this->normalizeFiles();
		if (empty($fields))
		{
			return $this->jsonError('Không tìm thấy tệp tải lên.');
		}
		foreach ($fields as $field)
		{
			if (! isset($_FILES[$field]['error']) || (int) $_FILES[$field]['error'] !== UPLOAD_ERR_OK)
			{
				return $this->jsonError($this->uploadError(isset($_FILES[$field]['error']) ? (int) $_FILES[$field]['error'] : UPLOAD_ERR_NO_FILE));
			}
		}
		$imagesOnly = $this->input->get('kind') === 'image';
		$this->load->library('upload', array(
			'upload_path' => $uploadPath,
			'allowed_types' => $imagesOnly ? self::IMAGE_TYPES : self::FILE_TYPES,
			'max_size' => $imagesOnly ? 5120 : 20480,
			'encrypt_name' => TRUE,
			'file_ext_tolower' => TRUE,
			'remove_spaces' => TRUE
		));

		$uploaded = array();
		foreach ($fields as $field)
		{
			$result = $this->storeOne($field);
			if (is_string($result))
			{
				foreach ($uploaded as $file)
				{
					$this->removeFile(FCPATH . $file['path']);
				}
				return $this->jsonError($result);
			}
			$uploaded[] = $result;
		}

		$names = array();
		$isImages = array();
		foreach ($uploaded as $file)
		{
			$names[] = basename($file['path']);
			$isImages[] = $file['is_image'];
		}
		$first = $uploaded[0];
		$this->json(array('success' => TRUE, 'message' => 'Đã tải lên ' . count($uploaded) . ' tệp.', 'data' => array(
			'files' => $names,
			'baseurl' => base_url('assets/uploads/'),
			'isImages' => $isImages,
			'items' => $uploaded,
			'path' => $first['path'],
			'url' => base_url($first['path']),
			'messages' => array()
		)));
	}

	public function delete($name = '')
	{
		$this->requirePermission('media.delete');
		$this->requirePost();
		$name = basename((string) $name);
		if (! preg_match('/^[a-f0-9]{32}\.(jpg|jpeg|png|gif|webp|pdf|doc|docx|xls|xlsx|ppt|pptx|zip|rar)$/i', $name)) return $this->json(array('success' => FALSE, 'message' => 'Tên tệp không hợp lệ.'), 422);
		$path = FCPATH . 'assets/uploads/' . $name;
		if (! is_file($path)) return $this->json(array('success' => FALSE, 'message' => 'Tệp không tồn tại.'), 404);
		unlink($path);
		$this->json(array('success' => TRUE, 'message' => 'Đã xóa tệp.'));
	}

	private function storeOne($field)
	{
		if (! $this->upload->do_upload($field))
		{
			return strip_tags($this->upload->display_errors('', ''));
		}
		$file = $this->upload->data();
		if (! $this->validMime($file['full_path'], $file['file_ext']))
		{
			$this->removeFile($file['full_path']);
			return 'Nội dung tệp "' . $file['client_name'] . '" không khớp với định dạng cho phép.';
		}
		if ($file['is_image'] && getimagesize($file['full_path']) === FALSE)
		{
			$this->removeFile($file['full_path']);
			return 'Ảnh "' . $file['client_name'] . '" bị lỗi hoặc không đọc được.';
		}
		return array(
			'path' => 'assets/uploads/' . $file['file_name'],
			'name' => $this->displayName($file['client_name']),
			'ext' => ltrim($file['file_ext'], '.'),
			'size' => (int) round($file['file_size'] * 1024),
			'is_image' => (bool) $file['is_image']
		);
	}

	// Jodit posts files[0], files[1]...; CI3 Upload only reads flat $_FILES entries.
	private function normalizeFiles()
	{
		foreach (array('files', 'file', 'image') as $name)
		{
			if (! isset($_FILES[$name]))
			{
				continue;
			}
			if (! is_array($_FILES[$name]['name']))
			{
				return array($name);
			}
			$fields = array();
			foreach (array_slice(array_keys($_FILES[$name]['name']), 0, 20) as $index)
			{
				$flat = 'media_upload_' . count($fields);
				$_FILES[$flat] = array();
				foreach (array('name', 'type', 'tmp_name', 'error', 'size') as $key)
				{
					$_FILES[$flat][$key] = $_FILES[$name][$key][$index];
				}
				$fields[] = $flat;
			}
			return $fields;
		}
		return array();
	}

	private function displayName($clientName)
	{
		$name = trim(preg_replace('/[\x00-\x1F\x7F<>"]+/u', '', (string) $clientName));
		return function_exists('mb_substr') ? mb_substr($name, 0, 180, 'UTF-8') : substr($name, 0, 180);
	}

	private function uploadError($code)
	{
		$messages = array(
			UPLOAD_ERR_INI_SIZE => 'Tệp vượt quá giới hạn tải lên của máy chủ.',
			UPLOAD_ERR_FORM_SIZE => 'Tệp vượt quá giới hạn cho phép.',
			UPLOAD_ERR_PARTIAL => 'Tệp chỉ được tải lên một phần. Vui lòng thử lại.',
			UPLOAD_ERR_NO_FILE => 'Không tìm thấy tệp tải lên.',
			UPLOAD_ERR_NO_TMP_DIR => 'Máy chủ thiếu thư mục tạm để nhận tệp.',
			UPLOAD_ERR_CANT_WRITE => 'Máy chủ không thể ghi tệp tải lên.',
			UPLOAD_ERR_EXTENSION => 'Một tiện ích mở rộng PHP đã chặn tệp tải lên.'
		);
		return isset($messages[$code]) ? $messages[$code] : 'Không thể tải tệp lên. Vui lòng thử lại.';
	}

	private function validMime($path, $extension)
	{
		$zip = array('application/zip', 'application/x-zip', 'application/x-zip-compressed');
		$ole = array('application/msword', 'application/vnd.ms-excel', 'application/vnd.ms-powerpoint', 'application/vnd.ms-office', 'application/CDFV2', 'application/x-ole-storage');
		$allowed = array(
			'.jpg' => array('image/jpeg'),
			'.jpeg' => array('image/jpeg'),
			'.png' => array('image/png'),
			'.gif' => array('image/gif'),
			'.webp' => array('image/webp'),
			'.pdf' => array('application/pdf'),
			'.doc' => $ole,
			'.xls' => $ole,
			'.ppt' => $ole,
			'.docx' => array_merge(array('application/vnd.openxmlformats-officedocument.wordprocessingml.document'), $zip),
			'.xlsx' => array_merge(array('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'), $zip),
			'.pptx' => array_merge(array('application/vnd.openxmlformats-officedocument.presentationml.presentation'), $zip),
			'.zip' => $zip,
			'.rar' => array('application/x-rar', 'application/x-rar-compressed', 'application/vnd.rar')
		);
		$extension = strtolower($extension);
		if (! isset($allowed[$extension]))
		{
			return FALSE;
		}
		if (! function_exists('finfo_open'))
		{
			return FALSE;
		}
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mime = finfo_file($finfo, $path);
		finfo_close($finfo);
		return in_array($mime, $allowed[$extension], TRUE);
	}

	private function removeFile($path)
	{
		if (is_file($path))
		{
			unlink($path);
		}
	}

	// Jodit treats non-2xx as a network error, so upload failures stay HTTP 200 with success=false.
	private function jsonError($message)
	{
		$this->json(array('success' => FALSE, 'message' => $message, 'error' => $message, 'data' => array('messages' => array($message))));
	}
}