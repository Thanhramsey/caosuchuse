<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Content extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('Content_model', 'Category_model', 'Attachment_model'));
	}

	public function index($resource = '')
	{
		$this->ensureResource($resource);
		$this->requirePermission($this->permission($resource, 'view'));
		$filters = array(
			'q' => trim((string) $this->input->get('q')),
			'status' => (string) $this->input->get('status'),
			'category_id' => (int) $this->input->get('category_id')
		);
		if (! in_array($filters['status'], array('', 'draft', 'published'), TRUE))
		{
			$filters['status'] = '';
		}
		$perPage = 15;
		$offset = ($this->currentPage() - 1) * $perPage;
		$total = $this->Content_model->countSearch($resource, $filters);
		$label = $this->Content_model->label($resource);
		$config = $this->Content_model->config($resource);
		$this->render('admin/content/index', array(
			'pageTitle' => $label,
			'activeMenu' => $resource,
			'useEditor' => TRUE,
			'resource' => $resource,
			'resourceLabel' => $label,
			'resourceIcon' => $config['icon'],
			'fields' => $config['fields'],
			'items' => $this->Content_model->search($resource, $filters, $perPage, $offset),
			'filters' => $filters,
			'total' => $total,
			'offset' => $offset,
			'categories' => in_array('category', $config['fields'], TRUE) ? $this->Category_model->options($resource) : array(),
			'pagination' => $this->paginationLinks(site_url('admin/noi-dung/' . $resource), $total, $perPage),
			'canCreate' => $this->admin_auth->can($this->permission($resource, 'create')),
			'canDelete' => $this->admin_auth->can($this->permission($resource, 'delete')),
			'canPublish' => $this->canPublish($resource),
			'canUpdateAny' => $resource !== 'news' || $this->admin_auth->can('news.update_any'),
			'canUpdateOwn' => $resource === 'news' && $this->admin_auth->can('news.update_own'),
			'currentUserId' => (int) $this->currentUser['id']
		));
	}

	public function show($resource = '', $id = 0)
	{
		$this->ensureResource($resource);
		$item = $this->Content_model->find($resource, $id);
		if (empty($item) || ! $this->canUpdate($resource, $item))
		{
			return $this->json(array('success' => FALSE, 'message' => 'Không tìm thấy nội dung hoặc bạn không có quyền sửa.'), 404);
		}
		$data = array(
			'id' => (int) $item['id'],
			'title' => $item['title'],
			'slug' => $item['slug'],
			'category_id' => $item['category_id'] === NULL ? '' : (int) $item['category_id'],
			'status' => $item['status'],
			'excerpt' => (string) $item['excerpt'],
			'cover_path' => (string) $item['cover_path'],
			'content_html' => (string) $item['content_html'],
			'extra_url' => isset($item['extra_url']) ? (string) $item['extra_url'] : ''
		);
		if ($this->attachmentMode($resource) !== NULL)
		{
			$data['attachments'] = array();
			foreach ($this->Attachment_model->forItem($resource, $item['id']) as $file)
			{
				$data['attachments'][] = array('path' => $file['file_path'], 'title' => $file['title'], 'ext' => $file['file_ext'], 'size' => (int) $file['file_size']);
			}
		}
		$this->json(array('success' => TRUE, 'data' => $data));
	}

	public function save($resource = '')
	{
		$this->ensureResource($resource);
		$this->requirePost();
		$id = (int) $this->input->post('id');
		$item = NULL;
		if ($id > 0)
		{
			$item = $this->Content_model->find($resource, $id);
			if (empty($item))
			{
				return $this->json(array('success' => FALSE, 'message' => 'Nội dung không tồn tại hoặc đã bị xóa.'), 404);
			}
			if (! $this->canUpdate($resource, $item))
			{
				return $this->json(array('success' => FALSE, 'message' => 'Bạn không có quyền sửa nội dung này.'), 403);
			}
		}
		else
		{
			$this->requirePermission($this->permission($resource, 'create'));
		}

		$validator = $this->validator();
		$validator->set_rules('title', 'Tiêu đề', 'trim|required|max_length[191]');
		$validator->set_rules('slug', 'Đường dẫn', 'trim|max_length[191]');
		$validator->set_rules('category_id', 'Danh mục', 'trim|is_natural');
		$validator->set_rules('status', 'Trạng thái', 'trim|required|in_list[draft,published]');
		$validator->set_rules('excerpt', 'Tóm tắt', 'trim|max_length[1000]');
		$validator->set_rules('cover_path', 'Ảnh đại diện', 'trim|max_length[255]');
		$errors = $validator->run() ? array() : $validator->error_array();

		$title = (string) $this->input->post('title');
		$rawSlug = (string) $this->input->post('slug');
		$slug = $this->slugify($rawSlug !== '' ? $rawSlug : $title);
		if (! isset($errors['slug']) && ! isset($errors['title']))
		{
			if ($slug === '')
			{
				$errors['slug'] = 'Đường dẫn không hợp lệ.';
			}
			elseif ($this->Content_model->slugExists($resource, $slug, $id))
			{
				$errors['slug'] = 'Đường dẫn này đã được sử dụng.';
			}
		}
		$categoryId = (int) $this->input->post('category_id');
		if ($categoryId > 0 && ! $this->Category_model->belongsTo($categoryId, $resource))
		{
			$errors['category_id'] = 'Danh mục không hợp lệ.';
		}
		$status = (string) $this->input->post('status');
		$alreadyPublished = $item !== NULL && $item['status'] === 'published';
		if ($status === 'published' && ! $alreadyPublished && ! $this->canPublish($resource))
		{
			$errors['status'] = 'Bạn không có quyền xuất bản nội dung.';
		}
		$coverPath = (string) $this->input->post('cover_path');
		if ($coverPath !== '' && ! $this->isUploadedImagePath($coverPath))
		{
			$errors['cover_path'] = 'Ảnh đại diện phải được tải lên từ hệ thống.';
		}
		$videoUrl = trim((string) $this->input->post('extra_url'));
		if ($this->Content_model->has($resource, 'video') && ! preg_match('#^https://(www\.)?(youtube\.com/watch\?v=|youtu\.be/|youtube\.com/embed/)[A-Za-z0-9_-]{6,}([&?][A-Za-z0-9_=&.-]*)?$#', $videoUrl))
		{
			$errors['extra_url'] = 'Vui lòng nhập link YouTube hợp lệ (https://www.youtube.com/watch?v=... hoặc https://youtu.be/...).';
		}
		$attachments = $this->collectAttachments($resource, $errors);
		if (! empty($errors))
		{
			return $this->json(array('success' => FALSE, 'message' => 'Vui lòng kiểm tra lại các trường được đánh dấu.', 'errors' => $errors), 422);
		}

		$this->load->library('Html_sanitizer');
		$hasBody = $this->Content_model->has($resource, 'content') || $this->Content_model->has($resource, 'answer');
		$now = date('Y-m-d H:i:s');
		$data = array(
			'title' => $title,
			'slug' => $slug,
			'category_id' => $categoryId > 0 && $this->Content_model->has($resource, 'category') ? $categoryId : NULL,
			'status' => $status,
			'excerpt' => $this->Content_model->has($resource, 'excerpt') ? (string) $this->input->post('excerpt') : '',
			'content_html' => $hasBody ? $this->html_sanitizer->clean($this->input->post('content_html')) : '',
			'cover_path' => $coverPath === '' || ! $this->Content_model->has($resource, 'cover') ? NULL : $coverPath,
			'updated_by' => (int) $this->currentUser['id'],
			'updated_at' => $now
		);
		if ($this->Content_model->has($resource, 'video'))
		{
			$data['extra_url'] = $videoUrl;
		}
		if ($status === 'published' && ($item === NULL || empty($item['published_at'])))
		{
			$data['published_at'] = $now;
		}
		if ($item === NULL)
		{
			$data['created_by'] = (int) $this->currentUser['id'];
			$data['created_at'] = $now;
		}
		$this->db->trans_start();
		$this->Content_model->save($resource, $data, $id);
		$itemId = $id > 0 ? $id : (int) $this->db->insert_id();
		if ($attachments !== NULL)
		{
			$this->Attachment_model->replace($resource, $itemId, $attachments);
		}
		$this->db->trans_complete();
		if (! $this->db->trans_status())
		{
			return $this->json(array('success' => FALSE, 'message' => 'Không thể lưu nội dung. Vui lòng thử lại.'), 500);
		}
		$label = $this->Content_model->label($resource);
		$this->json(array('success' => TRUE, 'message' => ($item === NULL ? 'Đã thêm ' : 'Đã cập nhật ') . mb_strtolower($label, 'UTF-8') . ' "' . $title . '".'));
	}

	public function delete($resource = '', $id = 0)
	{
		$this->ensureResource($resource);
		$this->requirePost();
		$this->requirePermission($this->permission($resource, 'delete'));
		$item = $this->Content_model->find($resource, $id);
		if (empty($item))
		{
			return $this->json(array('success' => FALSE, 'message' => 'Nội dung không tồn tại hoặc đã bị xóa.'), 404);
		}
		$this->db->trans_start();
		$this->Content_model->delete($resource, $id);
		$this->Attachment_model->deleteForItem($resource, $id);
		$this->db->trans_complete();
		$this->json(array('success' => TRUE, 'message' => 'Đã xóa "' . $item['title'] . '".'));
	}

	private function attachmentMode($resource)
	{
		if ($this->Content_model->has($resource, 'gallery'))
		{
			return 'gallery';
		}
		return $this->Content_model->has($resource, 'attachments') ? 'attachments' : NULL;
	}

	private function collectAttachments($resource, &$errors)
	{
		$mode = $this->attachmentMode($resource);
		if ($mode === NULL)
		{
			return NULL;
		}
		$posted = $this->input->post('attachments');
		if (! is_array($posted))
		{
			return array();
		}
		$extensions = $mode === 'gallery' ? 'jpg|jpeg|png|gif|webp' : 'jpg|jpeg|png|gif|webp|pdf|doc|docx|xls|xlsx|ppt|pptx|zip|rar';
		$files = array();
		foreach (array_slice(array_values($posted), 0, 100) as $entry)
		{
			$path = is_array($entry) && isset($entry['path']) ? (string) $entry['path'] : '';
			if (! preg_match('#^assets/uploads/[a-f0-9]{32}\.(' . $extensions . ')$#', $path, $match) || ! is_file(FCPATH . $path))
			{
				$errors['attachments'] = 'Có tệp không hợp lệ hoặc đã bị xóa. Vui lòng tải lại.';
				return array();
			}
			$title = isset($entry['title']) ? trim(preg_replace('/[\x00-\x1F\x7F]+/u', '', (string) $entry['title'])) : '';
			$title = mb_substr($title !== '' ? $title : basename($path), 0, 191, 'UTF-8');
			$files[] = array('path' => $path, 'title' => $title, 'ext' => $match[1], 'size' => (int) filesize(FCPATH . $path));
		}
		return $files;
	}

	private function ensureResource($resource)
	{
		if ($this->Content_model->exists($resource))
		{
			return;
		}
		if ($this->input->is_ajax_request())
		{
			$this->haltJson(array('success' => FALSE, 'message' => 'Loại nội dung không tồn tại.'), 404);
		}
		show_404();
	}

	private function permission($resource, $action)
	{
		if ($resource !== 'news')
		{
			return $resource . '.manage';
		}
		$map = array('view' => 'news.view', 'create' => 'news.create', 'delete' => 'news.delete');
		return $map[$action];
	}

	private function canPublish($resource)
	{
		return $this->admin_auth->can($resource === 'news' ? 'news.publish' : $resource . '.manage');
	}

	private function canUpdate($resource, $item)
	{
		if ($resource !== 'news')
		{
			return $this->admin_auth->can($resource . '.manage');
		}
		if ($this->admin_auth->can('news.update_any'))
		{
			return TRUE;
		}
		return $this->admin_auth->can('news.update_own') && (int) $item['created_by'] === (int) $this->currentUser['id'];
	}
}