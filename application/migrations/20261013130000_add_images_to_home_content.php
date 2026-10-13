<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/** Add local, source-attributed cover images to the home page content cards. */
class Migration_Add_images_to_home_content extends CI_Migration
{
	public function up()
	{
		$images = array(
			'products' => array(
				'cao-su-to-xong-khoi-rss3' => 'assets/images/content/product-rss3.jpg',
				'cao-su-khoi-svr-3l' => 'assets/images/content/product-svr3l.jpg',
				'cao-su-thien-nhien-so-che' => 'assets/images/content/product-svr3l2.jpg'
			),
			'services' => array(
				'trong-cham-soc-va-khai-thac-cao-su' => 'assets/images/content/rubber-worker-story.jpg',
				'thu-mua-va-che-bien-mu-cao-su' => 'assets/images/content/rubber-workers.jpg',
				'ha-tang-khu-cong-nghiep-nam-pleiku' => 'assets/images/content/nam-pleiku-industrial-park.jpg',
				'nong-nghiep-cong-nghe-cao-va-nang-luong-xanh' => 'assets/images/hero-rubber-plantation.webp'
			),
			'projects' => array(
				'khu-cong-nghiep-nam-pleiku' => 'assets/images/content/nam-pleiku-construction.jpg',
				'vung-nong-nghiep-cong-nghe-cao' => 'assets/images/content/rubber-worker-story.jpg',
				'chuyen-doi-so-quan-tri-doanh-nghiep' => 'assets/images/content/digital-transformation.jpg'
			)
		);

		foreach ($images as $table => $items)
		{
			foreach ($items as $slug => $path)
			{
				$this->db->where('slug', $slug)->update($table, array('cover_path' => $path));
			}
		}
	}

	public function down()
	{
		// Preserve manually replaceable cover images on rollback.
	}
}
