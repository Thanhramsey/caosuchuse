<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_english_labels extends CI_Migration
{
	public function up()
	{
		$this->dbforge->add_column('menus', array(
			'title_en' => array('type' => 'VARCHAR', 'constraint' => 150, 'null' => TRUE, 'after' => 'title')
		));
		$this->dbforge->add_column('home_sections', array(
			'title_en' => array('type' => 'VARCHAR', 'constraint' => 150, 'null' => TRUE, 'after' => 'title')
		));
		$this->seedMenuLabels();
		$this->seedSectionLabels();
		$this->seedSettings();
	}

	public function down()
	{
		$this->dbforge->drop_column('menus', 'title_en');
		$this->dbforge->drop_column('home_sections', 'title_en');
		$this->db->where_in('setting_key', array_keys($this->settingDefaults()))->delete('settings');
	}

	private function seedMenuLabels()
	{
		$labels = array(
			'/' => 'Home',
			'/gioi-thieu' => 'About us',
			'/trang/gioi-thieu-chung' => 'General introduction',
			'/trang/so-do-to-chuc' => 'Organisation chart',
			'/linh-vuc' => 'Business lines',
			'/tin-tuc' => 'News & events',
			'/san-pham' => 'Products',
			'/cong-bo-thong-tin' => 'Information disclosure',
			'/thong-tin-noi-bo' => 'Internal information',
			'/thong-tin-noi-bo/hoat-dong-cong-doan' => 'Trade union activities',
			'/thong-tin-noi-bo/hoat-dong-doan-thanh-nien' => 'Youth union activities',
			'/thong-tin-noi-bo/guong-nguoi-tot-viec-tot' => 'Good people, good deeds',
			'/thong-tin-noi-bo/y-tuong-sang-kien' => 'Ideas & initiatives',
			'/lien-he' => 'Contact'
		);
		foreach ($labels as $url => $label)
		{
			$this->db->where('url', $url)->where('title_en IS NULL')->update('menus', array('title_en' => $label));
		}
	}

	private function seedSectionLabels()
	{
		$labels = array(
			'kcn_nam_pleiku' => 'Nam Pleiku Industrial Park',
			'photo_bank' => 'Photo gallery',
			'videos' => 'Videos',
			'union' => 'Trade union activities',
			'youth' => 'Youth union activities',
			'good_deeds' => 'Good people, good deeds',
			'slider' => 'Home slider',
			'intro' => 'Introduction',
			'products' => 'Products',
			'achievements' => 'Achievements',
			'faqs' => 'Employee Q&A',
			'ideas' => 'Ideas & initiatives',
			'sustainability' => 'Sustainable development',
			'orientation' => 'Development orientation',
			'news' => 'News',
			'certifications' => 'Certifications'
		);
		foreach ($labels as $key => $label)
		{
			$this->db->where('section_key', $key)->where('title_en IS NULL')->update('home_sections', array('title_en' => $label));
		}
	}

	private function settingDefaults()
	{
		return array(
			'company_name_en' => 'Chu Se Rubber One Member Co., Ltd',
			'group_name_en' => 'Vietnam Rubber Group',
			'slogan_en' => '',
			'address_en' => '',
			'meta_title_en' => '',
			'meta_description_en' => ''
		);
	}

	private function seedSettings()
	{
		$now = date('Y-m-d H:i:s');
		foreach ($this->settingDefaults() as $key => $value)
		{
			$this->db->query(
				'INSERT INTO settings (setting_key, setting_value, updated_at) VALUES (?, ?, ?)'
				. ' ON DUPLICATE KEY UPDATE setting_key = setting_key',
				array($key, $value, $now)
			);
		}
	}
}
