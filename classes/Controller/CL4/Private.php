<?php defined('SYSPATH') or die('No direct script access.');

class Controller_CL4_Private extends Controller_Base {
	public $auth_required = TRUE;

	/**
	 * The current logged in user model.
	 * @var ORM Model
	 */
	protected $user;

	/**
	 * The id parameter in the route - if there is one, NULL otherwise.
	 * @var int
	 */
	protected $id;

	public function before() {
		$this->id = Kohana_Request::$current->param('id');

		parent::before();

		$this->user = Auth::instance()->get_user();
		$this->session['auth_user'] = $this->user;

		if ($this->auto_render) {
			$this->template_parameters['user'] = $this->user;
			$this->template_parameters['username'] = $this->session['auth_user']->username;

			// generate the avatar link for gravatar if the user has signed up with their email address (username)
			// todo: move this to login code? maybe
			if (HTTP_PROTOCOL == 'https') {
				$this->template->avatar = "https://secure.gravatar.com/avatar/" . md5(strtolower(trim($this->session['auth_user']->username))) . "?s=" . 41; // "?d=" . urlencode(URL_ROOT . '/images/loading.gif') .
			} else {
				$this->template->avatar = "http://www.gravatar.com/avatar/" . md5(strtolower(trim($this->session['auth_user']->username))) . "?s=" . 41; // "?d=" . urlencode(URL_ROOT . '/images/loading.gif') .
			}
		}
	}

	public function action_index() {
		$this->template->page_name = 'dashboard';
		$this->template->page_title = __('Dashboard');

		$this->template->body_html = CL4::get_view('dashboard/index', $this->template_parameters);
	}
}