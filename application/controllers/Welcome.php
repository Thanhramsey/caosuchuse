<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends Public_Controller
{
	/** Loại nội dung mở bình luận công khai. */
	private $commentable = array('news', 'internal', 'projects', 'services');

	public function index()
	{
		$groups = $this->Home_section_model->groupedActive();
		$mainSections = array();
		$fullSections = array();
		foreach ($groups['main'] as $section)
		{
			$section['items'] = $this->sectionItems($section);
			$mainSections[] = $section;
		}
		foreach ($groups['full'] as $section)
		{
			$section['items'] = $this->sectionItems($section);
			$fullSections[] = $section;
		}

		$this->render('public/home', array(
			'page_title' => $this->setting('meta_title', $this->setting('company_name', 'Công ty TNHH MTV Cao su Chư Sê')),
			'meta_description' => $this->setting('meta_description', $this->setting('slogan')),
			'rail' => $this->railSections(array('left')),
			'railRight' => $this->railSections(array('right')),
			'mainSections' => $mainSections,
			'fullSections' => $fullSections,
			'slider' => $this->Slider_model->search(array('status' => '1'), 10, 0)
		));
	}

	/**
	 * Một URL phục vụ 3 trường hợp: danh sách, danh sách theo chuyên mục và chi tiết.
	 */
	public function archive($resource = '', $slug = '')
	{
		if (! $this->Content_model->exists($resource))
		{
			show_404();
		}
		$slug = trim((string) $slug);
		if ($slug === '')
		{
			$this->renderList($resource, NULL);
			return;
		}
		$category = $this->Category_model->findBySlug($resource, $slug);
		if (! empty($category))
		{
			$this->renderList($resource, $category);
			return;
		}
		$this->renderDetail($resource, $slug);
	}

	public function about()
	{
		$page = $this->db->get_where('pages', array('slug' => 'gioi-thieu-chung', 'status' => 'published'))->row_array();
		if (empty($page))
		{
			$this->renderList('pages', NULL);
			return;
		}
		$this->renderDetail('pages', 'gioi-thieu-chung');
	}

	public function search()
	{
		$keyword = trim((string) $this->input->get('q'));
		$keyword = mb_substr($keyword, 0, 100, 'UTF-8');
		$page = $this->currentPage();
		$items = array();
		$total = 0;
		if ($keyword !== '')
		{
			$filters = array('status' => 'published', 'q' => $keyword);
			$total = $this->Content_model->countSearch('news', $filters);
			$items = $this->Content_model->search('news', $filters, self::PER_PAGE, ($page - 1) * self::PER_PAGE);
		}

		$searchTitle = $keyword === '' ? lang('site_search') : sprintf(lang('site_search_results_for'), $keyword);
		$this->render('public/list', array(
			'page_title' => $searchTitle . ' - ' . $this->setting('company_name', 'Cao su Chư Sê'),
			'meta_description' => lang('site_search_description'),
			'rail' => $this->railSections(array('left', 'right')),
			'listTitle' => $searchTitle,
			'resource' => 'news',
			'categories' => array(),
			'items' => $items,
			'page' => $page,
			'pages' => (int) ceil($total / self::PER_PAGE),
			'baseUrl' => site_url('tim-kiem') . '?q=' . rawurlencode($keyword)
		));
	}

	public function contact()
	{
		$this->render('public/contact', array(
			'page_title' => lang('site_contact') . ' - ' . $this->setting('company_name', 'Cao su Chư Sê'),
			'meta_description' => sprintf(lang('site_contact_description'), $this->setting('company_name', 'Công ty TNHH MTV Cao su Chư Sê')),
			'rail' => $this->railSections(array('left', 'right')),
			'listTitle' => lang('site_contact')
		));
	}

	public function submitContact()
	{
		$this->requirePost();
		$validator = $this->validator();
		$validator->set_rules('name', lang('site_contact_name'), 'trim|required|max_length[150]');
		$validator->set_rules('email', 'Email', 'trim|required|valid_email|max_length[191]');
		$validator->set_rules('phone', lang('site_contact_phone'), 'trim|max_length[40]');
		$validator->set_rules('subject', lang('site_contact_subject'), 'trim|required|max_length[191]');
		$validator->set_rules('message', lang('site_contact_message'), 'trim|required|max_length[5000]');
		if (! $validator->run())
		{
			$this->inlineError = lang('site_flash_contact_error');
			$this->contact();
			return;
		}
		$this->db->insert('contacts', array(
			'name' => $this->input->post('name'),
			'email' => $this->input->post('email'),
			'phone' => $this->input->post('phone'),
			'subject' => $this->input->post('subject'),
			'message' => $this->input->post('message'),
			'status' => 'new',
			'created_at' => date('Y-m-d H:i:s')
		));
		$this->session->set_flashdata('public_success', lang('site_flash_contact_ok'));
		redirect('lien-he');
	}

	public function subscribe()
	{
		$this->requirePost();
		$email = strtolower(trim((string) $this->input->post('email')));
		if (! filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 191)
		{
			$this->session->set_flashdata('public_error', lang('site_flash_subscribe_error'));
			redirect();
		}
		$this->db->query(
			'INSERT INTO newsletter_subscribers (email, status, created_at) VALUES (?, ?, ?)'
			. ' ON DUPLICATE KEY UPDATE status = VALUES(status), unsubscribed_at = NULL',
			array($email, 'subscribed', date('Y-m-d H:i:s'))
		);
		$this->session->set_flashdata('public_success', lang('site_flash_subscribe_ok'));
		redirect();
	}

	public function comment()
	{
		$this->requirePost();
		$resource = trim((string) $this->input->post('resource'));
		$itemId = (int) $this->input->post('item_id');
		if (! in_array($resource, $this->commentable, TRUE) || $itemId < 1)
		{
			show_error('Nội dung bình luận không hợp lệ.', 422);
		}
		$item = $this->db->get_where($resource, array('id' => $itemId, 'status' => 'published'))->row_array();
		if (empty($item))
		{
			show_404();
		}
		$validator = $this->validator();
		$validator->set_rules('author_name', 'Họ tên', 'trim|required|max_length[150]');
		$validator->set_rules('email', 'Email', 'trim|required|valid_email|max_length[191]');
		$validator->set_rules('body', 'Bình luận', 'trim|required|max_length[3000]');
		if (! $validator->run())
		{
			$this->session->set_flashdata('public_error', lang('site_flash_comment_error'));
			redirect(public_content_url($resource, $item['slug']));
		}
		$this->db->insert('comments', array(
			'resource' => $resource,
			'item_id' => $itemId,
			'author_name' => $this->input->post('author_name'),
			'email' => $this->input->post('email'),
			'body' => $this->input->post('body'),
			'status' => 'pending',
			'created_at' => date('Y-m-d H:i:s')
		));
		$this->session->set_flashdata('public_success', lang('site_flash_comment_ok'));
		redirect(public_content_url($resource, $item['slug']));
	}

	private function renderList($resource, $category)
	{
		$config = $this->Content_model->config($resource);
		$page = $this->currentPage();
		$filters = array('status' => 'published');
		if (! empty($category))
		{
			$filters['category_id'] = (int) $category['id'];
		}
		$total = $this->Content_model->countSearch($resource, $filters);
		$items = $this->Content_model->search($resource, $filters, self::PER_PAGE, ($page - 1) * self::PER_PAGE);
		$title = empty($category) ? $config['label'] : $category['name'];
		$baseUrl = public_archive_url($resource) . (empty($category) ? '' : '/' . rawurlencode($category['slug']));

		$this->render('public/list', array(
			'page_title' => $title . ' - ' . $this->setting('company_name', 'Cao su Chư Sê'),
			'meta_description' => $title . ' - ' . $this->setting('company_name', 'Công ty TNHH MTV Cao su Chư Sê'),
			'rail' => $this->railSections(array('left', 'right')),
			'listTitle' => $title,
			'resource' => $resource,
			'categories' => empty($category) ? $this->Category_model->active($resource) : array(),
			'items' => $items,
			'page' => $page,
			'pages' => (int) ceil($total / self::PER_PAGE),
			'baseUrl' => $baseUrl
		));
	}

	private function renderDetail($resource, $slug)
	{
		$item = $this->db
			->select($resource . '.*, categories.name AS category_name')
			->from($resource)
			->join('categories', 'categories.id = ' . $resource . '.category_id', 'left')
			->where(array($resource . '.slug' => $slug, $resource . '.status' => 'published'))
			->get()->row_array();
		if (empty($item))
		{
			show_404();
		}

		$related = $this->Content_model->search($resource, array(
			'status' => 'published',
			'category_id' => (int) (isset($item['category_id']) ? $item['category_id'] : 0)
		), 4, 0);
		$related = array_values(array_filter($related, function ($row) use ($item) {
			return (int) $row['id'] !== (int) $item['id'];
		}));
		$commentsOpen = in_array($resource, $this->commentable, TRUE);

		$this->render('public/detail', array(
			'page_title' => $item['title'] . ' - ' . $this->setting('company_name', 'Cao su Chư Sê'),
			'meta_description' => $item['excerpt'] !== '' ? $item['excerpt'] : $item['content_html'],
			'meta_image' => public_media_url($item['cover_path'], ''),
			'rail' => $this->railSections(array('left', 'right')),
			'resource' => $resource,
			'item' => $item,
			'attachments' => $this->Attachment_model->forItem($resource, (int) $item['id']),
			'related' => array_slice($related, 0, 3),
			'commentsOpen' => $commentsOpen,
			'comments' => $commentsOpen ? $this->db->where(array('resource' => $resource, 'item_id' => (int) $item['id'], 'status' => 'approved'))->order_by('created_at', 'DESC')->limit(20)->get('comments')->result_array() : array()
		));
	}
}
