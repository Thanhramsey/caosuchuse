<?php defined('BASEPATH') OR exit('No direct script access allowed');
/** @var array $sections Danh sách home_sections kèm 'items'. */
foreach ($sections as $section)
{
	$this->load->view('public/partials/widget', array('section' => $section, 'siteLang' => $siteLang));
}
