<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rubber_prices extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->requirePermission('rubber_prices.manage');
		$this->load->model('Rubber_price_model');
	}

	public function index()
	{
		$id = (int) $this->input->get('id');
		$item = $id > 0 ? $this->Rubber_price_model->find($id) : NULL;
		if ($id > 0 && empty($item)) show_404();
		$today = new DateTimeImmutable('now', new DateTimeZone('Asia/Ho_Chi_Minh'));
		$values = $item ? $item : array('id' => 0, 'effective_date' => $today->format('Y-m-d'), 'grade_1_price' => '', 'grade_2_price' => '');
		$this->render('admin/rubber_prices/index', array(
			'pageTitle' => 'Giá thu mua mủ',
			'activeMenu' => 'rubber_prices',
			'values' => $values,
			'today' => $today->format('d/m/Y'),
			'items' => $this->Rubber_price_model->all()
		));
	}

	public function save()
	{
		$this->requirePost();
		$id = (int) $this->input->post('id');
		$item = $id > 0 ? $this->Rubber_price_model->find($id) : NULL;
		if ($id > 0 && empty($item)) show_404();
		$validator = $this->validator();
		$validator->set_rules('grade_1_price', 'Giá mủ nước loại 1', 'trim|required|numeric|greater_than[0]|less_than_equal_to[999999999999]');
		$validator->set_rules('grade_2_price', 'Giá mủ nước loại 2', 'trim|required|numeric|greater_than[0]|less_than_equal_to[999999999999]');
		if (! $validator->run())
		{
			$this->session->set_flashdata('admin_error', 'Vui lòng nhập giá hợp lệ cho cả hai loại mủ.');
			redirect($id > 0 ? 'admin/gia-thu-mua-mu?id=' . $id : 'admin/gia-thu-mua-mu');
		}

		$now = date('Y-m-d H:i:s');
		$data = array(
			'grade_1_price' => number_format((float) $this->input->post('grade_1_price'), 2, '.', ''),
			'grade_2_price' => number_format((float) $this->input->post('grade_2_price'), 2, '.', ''),
			'updated_at' => $now
		);
		if ($item === NULL)
		{
			$today = new DateTimeImmutable('now', new DateTimeZone('Asia/Ho_Chi_Minh'));
			$data['effective_date'] = $today->format('Y-m-d');
			$data['created_by'] = (int) $this->currentUser['id'];
			$data['created_at'] = $now;
		}
		$this->Rubber_price_model->save($data, $id);
		$this->session->set_flashdata('admin_success', $item === NULL ? 'Đã thêm bảng giá thu mua mủ.' : 'Đã cập nhật bảng giá thu mua mủ.');
		redirect('admin/gia-thu-mua-mu');
	}

	public function delete($id = 0)
	{
		$this->requirePost();
		$item = $this->Rubber_price_model->find($id);
		if (empty($item)) show_404();
		$this->Rubber_price_model->delete($id);
		$this->session->set_flashdata('admin_success', 'Đã xóa bảng giá thu mua mủ.');
		redirect('admin/gia-thu-mua-mu');
	}
}
