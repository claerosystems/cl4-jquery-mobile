<?php defined('SYSPATH') or die ('No direct script access.');

/**
 * Public controller for public pages.
 */
class Controller_CL4_Public extends Controller_Base {
	public $auth_required = FALSE;

	public $page;

	public function before() {
		$this->page = Kohana_Request::$current->param('page');

		parent::before();
	}

	/**
	 * Serve up a page of content.
	 */
	public function action_view() {
		$this->template->page_name = $this->page;
		$this->template->page_title = ucwords($this->page);
		$this->template->body_html = CL4::get_view('public/' . $this->page, $this->template_parameters);
	}
}