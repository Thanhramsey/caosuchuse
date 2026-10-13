<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/** Run pending schema/content migrations from the command line only. */
class Migrate extends CI_Controller
{
	public function index()
	{
		if (! $this->input->is_cli_request())
		{
			show_404();
		}

		$this->load->library('migration');
		if ($this->migration->current() === FALSE)
		{
			fwrite(STDERR, $this->migration->error_string() . PHP_EOL);
			exit(1);
		}

		fwrite(STDOUT, 'Migrations are up to date.' . PHP_EOL);
	}
}
