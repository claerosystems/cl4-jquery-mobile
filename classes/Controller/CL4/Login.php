<?php defined('SYSPATH') or die('No direct script access.');

class Controller_CL4_Login extends Controller_Base {
	public $page = 'login';

	public $auth_required = FALSE;

	/**
	* Displays the login form and logs the user in or detects and invalid login (through Auth and Model_User)
	*
	* View: Login form.
	*/
	public function action_index() {
		$this->template->page_name = 'login';
		$this->template->page_title = 'Login';

		// check for API token login special case
		$token = CL4::get_param('token', NULL, 'string');
		if ( ! empty($token)) {

			$token = ORM::factory('Global_Token')->where('token', '=', $token)->find();
			if ($token->loaded()) {
				// set GET username to populate the username field if the auto-login fails (not 100% sure this will work)
				$_GET['username'] = $token->email;

				// check token expiry
				if (strtotime($token->expiry_time) < time()) {
					// token has expired
					Message::add('Your auto-login token has expired, please login manually.', Message::$error);
				} else {
					if ($this->company->id != $token->global_company_id) {
						// company mismatch
						Message::add('Your auto-login token is invalid, please check your URL.', Message::$error);
					} else {
						// all good, log in this user
						Message::add('Successful API login.', Message::$debug);

						// todo: fix this
						// manual login hack
						$user = ORM::factory('User')->where('username', '=', $token->email)->find();
						if ( ! $user->loaded()) {
							Message::add('Your auto-login failed because the user was not found: ' . $token->email, Message::$error);
						} else {
							$session =& Session::instance()->as_array();


							Session::instance()->set(Kohana::$config->load('auth.timestamp_key'), time());

							$user->complete_login();

							// Regenerate session_id
							Session::instance()->regenerate();

							// Store username in session
							//Session::instance()->delete(Kohana::$config->load('cl4login.session_key'));
							//Session::instance()->set(Kohana::$config->load('cl4login.session_key'), $user);
							Session::instance()->delete('auth_user');
							Session::instance()->set('auth_user', $user);

							//echo Debug::vars(Auth::instance()->get_user(), $session); exit;

							// add the auth log entry
							$user->add_auth_log(Model_Auth_Log::LOG_TYPE_LOGGED_IN, $token->email);

							// todo: add update profile code?

							//$this->login_success_redirect();
							header(URL_ROOT . '/' . Route::get('private')->uri(array('controller' => 'time', 'action' => 'index')));
						}
					}
				}
			}
		}

		// get default username if set somewhere
		$default_username = Cookie::get('username', NULL);

		// get some variables from the request
		$username = CL4::get_param('username', $default_username);
		$password = CL4::get_param('password');
		$timed_out = CL4::get_param('timed_out');
		$redirect = CL4::get_param('redirect'); // default to NULL when no redirect is received so it uses the default redirect

		// If user already signed-in
		if (Auth::instance()->logged_in() === TRUE) {
			// redirect to the default login location or the redirect location
			$this->login_success_redirect($redirect);
		}

		$login_config = Kohana::$config->load('cl4login');

		// Get number of login attempts this session
		$attempts = Arr::path($this->session, $login_config['session_key'] . '.attempts', 0);

		// Update number of login attempts
		++$attempts;
		$this->session[$login_config['session_key']]['attempts'] = $attempts;

		// verify that the company database and user table exists
		try {
			// $_POST is not empty
			if ( ! empty($_POST)) {
				if (($login_messages = Auth::instance()->login($username, $password, FALSE)) === TRUE) {
					$user = Auth::instance()->get_user();

					// user has to update their profile or password
					/* 20130430 CSN commenting out, this is not implemented yet
					if ($user->force_update_profile_flag || $user->force_update_password_flag) {
						// add a message for the user regarding updating their profile or password
						$message_path = $user->force_update_profile_flag ? 'update_profile' : 'update_password';
						Message::message('user', $message_path, array(), Message::$notice);

						// instead of redirecting them to the location they requested, redirect them to the profile page
						$redirect = Route::get('account')->uri(array('action' => 'profile'));
					}
					*/

					// check for redirect and do it
					if ( ! empty($redirect) && is_string($redirect)) {
						// Redirect after a successful login, but check permissions first
						$redirect_request = Request::factory($redirect);
						$redirect_controller = $redirect_request->controller();
						if ( ! empty($redirect_controller)) {
							$next_controller = 'Controller_' . $redirect_request->controller();
							$next_controller = new $next_controller($redirect_request, Response::factory());
							if (Auth::instance()->allowed($next_controller, $redirect_request->action())) {
								// they have permission to access the page, so redirect them there
								$this->login_success_redirect($redirect);
							} else {
								// they don't have permission to access the page, so just go to the default page
								$this->login_success_redirect();
							}
						} else {
							$this->login_success_redirect();
						}
					} else {
						// redirect to the default location
						$this->login_success_redirect();
					}

					// If login failed (captcha and/or wrong credentials)
				} else {
					if ( ! empty($login_messages)) {
						foreach ($login_messages as $message_data) {
							list($message, $values) = $message_data;
							Message::message('user', $message, $values, Message::$error);
						}
					}

				}
			}
		} catch (ORM_Validation_Exception $e) {
			Message::message('user', 'username.invalid');
		}

		if ( ! empty($timed_out)) {
			// they have come from the timeout page, so try to send them back to their original page
			$this->redirect(Route::get(Route::name(Request::current()->route()))->uri(array('action' => 'timedout')) . $this->get_redirect_query());
		}

		//$this->template->on_load_js .= file_get_contents(ABS_ROOT . '/html/js/onload_login.js') . EOL;
		$this->template->body_html = View::factory('cl4/cl4login/login')
			->set('redirect', $redirect)
			->set('username', $username)
			->set('password', $password);
	}

	/**
	* Redirects the user the first page they should see after login
	* $redirect contains the page they may have requested before logging in and they should be redirected there
	* If $redirect is is NULL then the default redirect from the config will be used
	*
	* @param  string  $redirect  The path to redirect to
	* @return  void  never returns
	*/
	protected function login_success_redirect($redirect = NULL) {
		if ($redirect !== NULL) {
			$this->redirect($redirect);
		} else {
			$auth_config = Kohana::$config->load('auth');
			$this->redirect(URL::site(Route::get($auth_config['default_login_redirect'])->uri($auth_config['default_login_redirect_params'])));
		}
	} // function login_success_redirect

	public function action_login() {
		$this->action_index();
	}

	/**
	* Log the user out and redirect to the login page.
	*/
	public function action_logout() {
		try {
			if (Auth::instance()->logout()) {
				Message::add(__(Kohana::message('user', 'username.logged_out')), Message::$notice);
			} // if
		} catch (Exception $e) {
			Kohana_Exception::handler_continue($e);
			Message::add(__(Kohana::message('user', 'username.not_logged_out')), Message::$error);

			// redirect them to the default page
			$this->login_success_redirect();
		} // try

		// redirect to the user account and then the signin page if logout worked as expected
		$this->redirect(URL_ROOT . '/' . Route::get(Route::name($this->request->route()))->uri() . '?' . $_SERVER["QUERY_STRING"]);
	} // function action_logout

	/**
	* Display a page that displays the username and asks the user to enter the password
	* This is for when their session has timed out, but we don't want to make the login fully again
	* If the user has fully timed out, they will be logged out and returned to the login page
	*/
	public function action_timedout() {
		$user = Auth::instance()->get_user();

		$max_lifetime = Kohana::$config->load('auth.timed_out_max_lifetime');

		if ( ! $user || ($max_lifetime > 0 && Auth::instance()->timed_out($max_lifetime))) {
			// user is not logged in at all or they have reached the maximum amount of time we allow sometime to stay logged in, so redirect them to the login page
			$this->redirect(Base::get_url('login', array('action' => 'logout')) . $this->get_redirect_query());
		}

		$timeout_post = Session::instance()->get(Kohana::$config->load('cl4login.timeout_post_session_key'));
		if (Kohana::$config->load('cl4login.enable_timeout_post') && ! empty($timeout_post)) {
			$redirect = Base::get_url('login', array('action' => 'timeoutpost'));
		} else {
			// need to decode the redirect as it will be encoded in the URL
			$redirect = CL4::get_param('redirect');
		}

		$this->template->page_name = 'timedout';
		$this->template->page_title = 'Timed Out';
		$this->template->body_html = View::factory('cl4/cl4login/timed_out')
			->set('redirect', $redirect)
			->set('username', $user->username);
	}

	/**
	* Creates a form with all the fields from the GET and POST and then submits the form
	* to the page they were originally submitted to.
	*
	* @return  void
	*
	* @uses  Form::array_to_fields()
	*/
	public function action_timeoutpost() {
		// we want to redirect the user to the previous form, first creating the form and then submitting it with JS
		$session_key = Kohana::$config->load('cl4login.timeout_post_session_key');

		$timeout_post = Session::instance()->get(Kohana::$config->load('cl4login.timeout_post_session_key'));
		if ( ! Kohana::$config->load('cl4login.enable_timeout_post') || empty($timeout_post)) {
			$this->login_success_redirect();
		}

		try {
			$form_html = Form::open(URL::site($timeout_post['post_to']), array('id' => 'timeout_post')) . EOL;
			if ( ! empty($timeout_post['get'])) {
				$form_html .= Form::array_to_fields($timeout_post['get']);
			}
			if ( ! empty($timeout_post['post'])) {
				$form_html .= Form::array_to_fields($timeout_post['post']);
			}
			$form_html .= Form::close();

			$this->template->body_html = $form_html;
			$this->add_on_load_js('$(\'#timeout_post\').submit();');

			Session::instance()->delete(Kohana::$config->load('cl4login.timeout_post_session_key'));
		} catch (Exception $e) {
			Kohana_Exception::handler_continue($e);
			$this->login_success_redirect();
		}
	} // function action_timeoutpost

	/**
	* View: Access not allowed.
	*/
	public function action_noaccess() {
		// set the template title (see Controller_App for implementation)
		$this->template->page_title = 'Access Not Allowed - ' . $this->page_title_append;
		$view = $this->template->body_html = View::factory('cl4/cl4login/no_access')
			->set('referrer', CL4::get_param('referrer'));
	} // function action_noaccess

	/**
	* Returns the redirect value as a query string ready to use in a direct
	* The ? is added at the beginning of the string
	* An empty string is returned if there is no redirect parameter
	*
	* @return	string
	*/
	protected function get_redirect_query() {
		$redirect = urldecode(CL4::get_param('redirect'));

		if ( ! empty($redirect)) {
			return URL::array_to_query(array('redirect' => $redirect), '&');
		} else {
			return '';
		}
	} // function get_redirect_query


	/**
	 * A basic implementation of the "Forgot password" functionality
	 */
	public function action_forgot() {
		$saved_username = Cookie::get('username', NULL);
		try {
			$default_options = Kohana::$config->load('cl4login');
			if (isset($_POST['reset_username'])) {
				$user = ORM::factory('User')->where('username', '=', $_POST['reset_username'])
					->where_active('user')
					->find();

				// Admin passwords cannot be reset by email
				if ($user->loaded() && ! in_array($user->username, $default_options['admin_accounts'])) {
					// send an email with the account reset token
					$user->set('reset_token', Text::random('alnum', 32))
						->is_valid()
						->save();

					try {
						// build a link with action reset including their username and the reset token
						$message_body = Timeportal::get_theme_view('email/forgot_link', array(
							'url' => URL_ROOT . '/reset?username=' . $user->username  . '&reset_token=' . $user->reset_token,
						));

						if (Timeportal::deliver_email($user->username, LONG_NAME . ' ' . __('Password Reset'), $message_body)) {
							//Message::add(__(Kohana::message('login', 'reset_link_sent')), Message::$notice);
							$this->ajax_result['html'] = Timeportal::get_theme_view('block/forgot_success');
							Timeportal::send_ajax($this->ajax_result);
						}
					} catch (Exception $e) {
						Message::add(__(Kohana::message('login', 'forgot_send_error')), Message::$error);
						throw $e;
					}
				} else if (in_array($user->username, $default_options['admin_accounts'])) {
					Message::add(__(Kohana::message('login', 'reset_admin_account')), Message::$warning);

				} else {
					Message::add(__(Kohana::message('login', 'reset_not_found')), Message::$warning);
				}
			} // if post

			$this->ajax_result['html'] = Base::get_theme_view('block/forgot', array(
					'company' => $this->company->name,
					'username' => $saved_username)
			);
		} catch (Exception $e) {
			Kohana_Exception::handler_continue($e);
			Message::add(__(Kohana::message('login', 'reset_error')), Message::$error);
		}

		Base::send_ajax($this->ajax_result);
	} // function

	/**
	 * A basic version of "reset password" functionality.
	 */
	function action_reset() {
		//try {
		$default_options = Kohana::$config->load('cl4login');

		// set the template title (see Controller_Base for implementation)
		$this->template->page_title = 'Password Reset';

		$username = CL4::get_param('username');
		if ($username !== null) $username = trim($username);
		$reset_token = CL4::get_param('reset_token');

		// make sure that the reset_token has exactly 32 characters (not doing that would allow resets with token length 0)
		// also make sure we aren't trying to reset the password for an admin
		if ( ! empty($username) && ! empty($reset_token) && strlen($reset_token) == 32) {
			$user = ORM::factory('User')
				->where('username', '=', $_REQUEST['username'])
				->and_where('reset_token', '=', $_REQUEST['reset_token'])
				->where_active('user')
				->find();

			// admin passwords cannot be reset by email
			if (is_numeric($user->id) && ! in_array($user->username, $default_options['admin_accounts'])) {
				try {
					$password = cl4_Auth::generate_password();
					$user->values(array(
						'password' => $password,
						// reset the failed login count
						'failed_login_count' => 0,
						// send the user to the password update page
						'force_update_password_flag' => 1,
					))
						->is_valid()
						->save();
				} catch (Exception $e) {
					Message::add(__(Kohana::message('login', 'password_email_error')), Message::$error);
					throw $e;
				}

				try {
					// provide a link to the user including their username
					$url = URL::site(Route::get(Route::name(Request::current()->route()))->uri() . '?' . http_build_query(array('username' => $user->username)), Request::current()->protocol());

					$message = View::factory('cl4/cl4login/forgot_reset')
						->set('app_name', LONG_NAME)
						->set('username', $user->username)
						->set('password', $password)
						->set('url', $url)
						->set('admin_email', ADMIN_EMAIL)->render();

					if (Timeportal::deliver_email($user->username, LONG_NAME . ' ' . __('New Password'), $message)) {
						Message::add(__(Kohana::message('login', 'password_emailed')), Message::$notice);
					} else {
						Message::add(__(Kohana::message('login', 'password_email_error')), Message::$error);
					}
				} catch (Exception $e) {
					Message::add(__(Kohana::message('login', 'password_email_error')), Message::$error);
					throw $e;
				}

				$this->redirect(Route::get(Route::name(Request::current()->route()))->uri());

			} else {
				Message::add(__(Kohana::message('login', 'password_email_username_not_found')), Message::$error);
				$this->redirect(Route::get(Route::name(Request::current()->route()))->uri(array('action' => 'forgot')));
			}

		} else {
			Message::add(__(Kohana::message('login', 'password_email_partial')), Message::$error);
			$this->redirect(Route::get(Route::name(Request::current()->route()))->uri(array('action' => 'forgot')));
		}
		//} catch (Exception $e) {
		//	Message::add(__(Kohana::message('login', 'reset_error')), Message::$error);
		//}
	}
}