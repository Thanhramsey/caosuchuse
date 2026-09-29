<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_Controller extends CI_Controller
{
	protected $currentUser = NULL;

	public function __construct()
	{
		parent::__construct();
		$this->load->library('Admin_auth');
		$this->load->helper(array('admin', 'text'));
		if (! $this->admin_auth->check())
		{
			if ($this->input->is_ajax_request())
			{
				$this->haltJson(array('success' => FALSE, 'message' => 'Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.'), 401);
			}
			redirect('admin/dang-nhap');
		}
		$this->currentUser = $this->admin_auth->user();
	}

	protected function requirePermission($permission)
	{
		if ($this->admin_auth->can($permission))
		{
			return;
		}
		if ($this->input->is_ajax_request())
		{
			$this->haltJson(array('success' => FALSE, 'message' => 'Bạn không có quyền thực hiện thao tác này.'), 403);
		}
		show_error('Bạn không có quyền thực hiện thao tác này.', 403, 'Không có quyền truy cập');
	}

	protected function render($view, $data = array())
	{
		$data['currentUser'] = $this->currentUser;
		$data['pageTitle'] = isset($data['pageTitle']) ? $data['pageTitle'] : 'Quản trị';
		$data['activeMenu'] = isset($data['activeMenu']) ? $data['activeMenu'] : '';
		$data['useEditor'] = ! empty($data['useEditor']);
		$data['flashSuccess'] = (string) $this->session->flashdata('admin_success');
		$data['flashError'] = (string) $this->session->flashdata('admin_error');
		$this->load->view('admin/layout/header', $data);
		$this->load->view($view, $data);
		$this->load->view('admin/layout/footer', $data);
	}

	protected function json($data, $status = 200)
	{
		$this->output->set_status_header($status)->set_content_type('application/json', 'utf-8')->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
	}

	protected function haltJson($data, $status)
	{
		$this->json($data, $status);
		$this->output->_display();
		exit;
	}

	protected function requirePost()
	{
		if ($this->input->method(TRUE) !== 'POST')
		{
			$this->haltJson(array('success' => FALSE, 'message' => 'Phương thức không được phép.'), 405);
		}
	}

	protected function validator()
	{
		$this->load->library('form_validation');
		$this->form_validation->reset_validation();
		$this->form_validation->set_message(array(
			'required' => '{field} là bắt buộc.',
			'max_length' => '{field} không được vượt quá {param} ký tự.',
			'min_length' => '{field} phải có ít nhất {param} ký tự.',
			'valid_email' => '{field} không đúng định dạng.',
			'integer' => '{field} phải là số nguyên.',
			'is_natural' => '{field} phải là số không âm.',
			'is_natural_no_zero' => '{field} không hợp lệ.',
			'in_list' => '{field} không hợp lệ.'
		));
		return $this->form_validation;
	}

	protected function currentPage()
	{
		$page = (int) $this->input->get('page');
		return $page > 0 ? $page : 1;
	}

	protected function paginationLinks($baseUrl, $total, $perPage)
	{
		$this->load->library('pagination');
		$this->pagination->initialize(array(
			'base_url' => $baseUrl,
			'total_rows' => (int) $total,
			'per_page' => (int) $perPage,
			'page_query_string' => TRUE,
			'query_string_segment' => 'page',
			'use_page_numbers' => TRUE,
			'reuse_query_string' => TRUE,
			'num_links' => 2,
			'full_tag_open' => '<ul class="pagination m-0 ms-auto">',
			'full_tag_close' => '</ul>',
			'attributes' => array('class' => 'page-link'),
			'first_link' => '&laquo;',
			'last_link' => '&raquo;',
			'next_link' => '&rsaquo;',
			'prev_link' => '&lsaquo;',
			'first_tag_open' => '<li class="page-item">',
			'first_tag_close' => '</li>',
			'last_tag_open' => '<li class="page-item">',
			'last_tag_close' => '</li>',
			'next_tag_open' => '<li class="page-item">',
			'next_tag_close' => '</li>',
			'prev_tag_open' => '<li class="page-item">',
			'prev_tag_close' => '</li>',
			'num_tag_open' => '<li class="page-item">',
			'num_tag_close' => '</li>',
			'cur_tag_open' => '<li class="page-item active"><span class="page-link">',
			'cur_tag_close' => '</span></li>'
		));
		return $this->pagination->create_links();
	}

	protected function slugify($text)
	{
		$text = str_replace(array('đ', 'Đ'), array('d', 'D'), (string) $text);
		return url_title(convert_accented_characters($text), '-', TRUE);
	}

	protected function isUploadedImagePath($path)
	{
		return (bool) preg_match('#^assets/uploads/[a-f0-9]{32}\.(jpg|jpeg|png|gif|webp)$#', (string) $path);
	}
}

class Public_Controller extends CI_Controller
{
	const PER_PAGE = 8;
	const LANGUAGES = array('vi' => 'vietnamese', 'en' => 'english');

	protected $settings = array();

	protected $siteLang = 'vi';

	/** Thông báo lỗi hiển thị ngay trong request hiện tại, không dùng flashdata. */
	protected $inlineError = '';

	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('url', 'form', 'language', 'public'));
		$this->load->model(array('Menu_model', 'Home_section_model', 'Content_model', 'Slider_model', 'Setting_model', 'Attachment_model', 'Category_model'));
		$this->settings = $this->Setting_model->all();
		$this->resolveLanguage();
	}

	private function resolveLanguage()
	{
		$requested = strtolower(trim((string) $this->input->get('lang')));
		if (isset(self::LANGUAGES[$requested]))
		{
			$this->session->set_userdata('site_lang', $requested);
			$query = $this->input->get();
			unset($query['lang']);
			redirect(site_url(uri_string()) . (empty($query) ? '' : '?' . http_build_query($query)));
		}
		$stored = (string) $this->session->userdata('site_lang');
		$this->siteLang = isset(self::LANGUAGES[$stored]) ? $stored : 'vi';
		$this->lang->load('site', self::LANGUAGES[$this->siteLang]);
	}

	/** Ưu tiên giá trị "<key>_en" khi đang xem bản tiếng Anh. */
	protected function setting($key, $default = '')
	{
		if ($this->siteLang === 'en' && ! empty($this->settings[$key . '_en']))
		{
			return $this->settings[$key . '_en'];
		}
		return isset($this->settings[$key]) && $this->settings[$key] !== '' ? $this->settings[$key] : $default;
	}

	/**
	 * Cột bên của trang trong: gộp widget cột trái rồi cột phải như bản thiết kế.
	 */
	protected function railSections($positions)
	{
		$groups = $this->Home_section_model->groupedActive();
		$sections = array();
		foreach ($positions as $position)
		{
			foreach (isset($groups[$position]) ? $groups[$position] : array() as $section)
			{
				if ($section['section_type'] === 'slider')
				{
					continue;
				}
				$section['items'] = $this->sectionItems($section);
				$sections[] = $section;
			}
		}
		return $sections;
	}

	protected function sectionItems($section)
	{
		if ($section['section_type'] !== 'content' || ! $this->Content_model->exists($section['source_resource']))
		{
			return array();
		}
		return $this->Content_model->search($section['source_resource'], array(
			'status' => 'published',
			'category_id' => (int) $section['source_category_id']
		), max(1, (int) $section['item_limit']), 0);
	}

	protected function render($view, $data)
	{
		$data = array_merge(array(
			'page_title' => $this->setting('meta_title', $this->setting('company_name', 'Công ty TNHH MTV Cao su Chư Sê')),
			'meta_description' => $this->setting('meta_description'),
			'meta_image' => '',
			'rail' => array(),
			'railRight' => array(),
			'activePath' => trim((string) uri_string(), '/')
		), $data);
		$data['settings'] = $this->settings;
		$data['siteLang'] = $this->siteLang;
		$data['mainMenu'] = $this->Menu_model->tree('main');
		$data['footerMenu'] = $this->Menu_model->tree('footer');
		$data['footerSections'] = $this->railSections(array('footer'));
		$data['partners'] = $this->db->where('status', 1)->order_by('sort_order', 'ASC')->order_by('id', 'ASC')->get('partners')->result_array();
		$data['testimonials'] = $this->db->where('status', 1)->order_by('sort_order', 'ASC')->order_by('id', 'ASC')->limit(6)->get('testimonials')->result_array();
		$data['flashSuccess'] = (string) $this->session->flashdata('public_success');
		$data['flashError'] = $this->inlineError !== '' ? $this->inlineError : (string) $this->session->flashdata('public_error');
		$this->load->view('public/layout/header', $data);
		$this->load->view($view, $data);
		$this->load->view('public/layout/footer', $data);
	}

	protected function currentPage()
	{
		$page = (int) $this->input->get('page');
		return $page > 0 ? $page : 1;
	}

	protected function validator()
	{
		$this->load->library('form_validation');
		$this->form_validation->reset_validation();
		$this->form_validation->set_message(array(
			'required' => '{field} là bắt buộc.',
			'max_length' => '{field} không được vượt quá {param} ký tự.',
			'valid_email' => '{field} không đúng định dạng.'
		));
		return $this->form_validation;
	}

	protected function requirePost()
	{
		if ($this->input->method(TRUE) !== 'POST')
		{
			show_error('Phương thức không được phép.', 405);
		}
	}
}
