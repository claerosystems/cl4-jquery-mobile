<?php defined('SYSPATH') or die ('No direct script access.');

class Controller_CL4_Login extends Controller_Base {
	public $page = 'login';
	public $default_view = 'cl4/cl4login/login';

	public $auth_required = FALSE;

	public function before() {
		$this->ajax_actions[] = 'forgot';
		$this->ajax_actions[] = 'check_openid';
		$this->ajax_actions[] = 'check_openid_response';

		parent::before();

		if ($this->auto_render) {
			//$this->add_style('global', 'css/admin.css');
			//$this->add_script('login', 'js/login.js');
		}
	}

	/**
	 * used to populate and save the bug report form, must be accessible even when not logged in
	 */
	public function action_bugreport() {
		$bug = ORM::factory('Global_Bug_Report');
		$bug->set_options(array('field_id_prefix' => 'b'));
		$bug->global_company_id = $this->company->id;
		$bug->username = ( ! empty($this->user->username)) ? $this->user->username : '';

		if ( ! empty($_POST)) {
			// save the bug report
			try {
				$bug->save_values();
				if ($bug->save()) {
					Timeportal::message('timeportal', 'bug_submit_success', NULL, Message::$notice);
					// send email
					$email_recipient = SEND_BUGS_TO;
					$email_subject = 'Bug Report submitted by ' . $bug->username;
					if (Timeportal::deliver_email($email_recipient, $email_subject, $bug->get_form(array('mode' => 'view', 'display_buttons' => FALSE)))) {
						Timeportal::message('timeportal', 'email_sent', array('%type' => 'bug report', '%email' => $email_recipient), Message::$debug);
					} else {
						Timeportal::message('timeportal', 'email_not_sent', array('%type' => 'bug report', '%email' => $email_recipient), Message::$debug);
					}
					echo json_encode(array('status' => 1, 'html' => Message::display()->render()));
				} else {
					echo json_encode(array('status' => 0, 'html' => 'Bug report not saved.'));
				}
			} catch (Exception $e) {
				echo json_encode(array('status' => 0, 'html' => 'Bug report not saved: ' . Kohana_Exception::text($e)));
			}
		} else {
			// display the bug report form
			$bug->set_field_attribute('description', 'placeholder', 'Please enter the details here and be as specific as possible.');
			$bug->browser = $_SERVER["HTTP_USER_AGENT"];
			$form_html = Timeportal::get_theme_view('bugreport_form', array('bug' => $bug))->render();
			echo json_encode(array('status' => 1, 'html' => $form_html));
		}
		exit;
	}

	public function action_login() {
		$this->action_index();
	}

	/**
	 * This script is called back from Google after action_check_openid() is called, it will confirm or deny the auth request.
	 * if the user is already logged in to their google account, this will happen immediately, if not, the user will be prompted to log in
	 * to their google account first.
	 */
	public function action_check_openid_response() {
		// make sure the request was sent for this client
		$saved_username = Cookie::get('username', NULL);
		$email_hash = $_GET['timeportal_string'];
		if (md5($saved_username) == "{$email_hash}") {
			require_once(APPPATH . '/vendor/lightopenid/openid.php');
			$openid = new LightOpenID(COMPANY_URL . '.' . ROOT_DOMAIN);

			//echo Debug::vars($openid);

			if ($openid->mode && $openid->validate()) {
				// good to go!  log this user in

				// get the user object
				$user = ORM::factory('User')->where('username', '=', $saved_username)->find();
				if ($user->loaded() && $user->active_flag == 1 && ! empty($user->openid_google)) {
					$login_config = Kohana::$config->load('cl4login');
					$auth_types = $login_config['auth_type'];

					// Finish the login
					Auth::instance()->openid_complete_login($user);

					// add the auth log entry
					$user->add_auth_log($auth_types['logged_in'], $user->username);

					$user = Auth::instance()->get_user();
					// user has to update their profile or password
					if ($user->force_update_profile_flag || $user->force_update_password_flag) {
						// add a message for the user regarding updating their profile or password
						$message_path = $user->force_update_profile_flag ? 'update_profile' : 'update_password';
						Message::message('user', $message_path, array(), Message::$notice);

						// instead of redirecting them to the location they requested, redirect them to the profile page
						$redirect = Route::get('account')->uri(array('action' => 'profile'));
					} // if

					if ( ! empty($redirect) && is_string($redirect)) {
						// Redirect after a successful login, but check permissions first
						$redirect_request = Request::factory($redirect);
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
						// redirect to the defualt location (by default this is user account)
						$this->login_success_redirect();
					}
				} else {
					// invalid user after all that!
					echo 'open id login attempt failed, auth succeeded but user could not be loaded: ' . $saved_username;
				}

			} else {
				// OpenId login failed
				echo 'open id login attempt failed for ' . $saved_username;
			}
		} else {
			// hack attempt?
			echo 'hack attempt? sent email hash did not match cookie saved username/email';
		}
		exit;
	}

	/**
	 * Check for openid auth, send the request to Google with the return url set to /check_openid_response
	 */
	public function action_check_openid() {
		// if we have a username saved, try to verify based on this email address
		$saved_username = Cookie::get('username', NULL);

		// make sure this is a real user
		$user = ORM::factory('User')->where('username', '=', $saved_username)->find();

		if ($user->loaded()) {
			if ($user->active_flag == 1) {
				if ( ! empty($user->openid_google)) {
					require_once(APPPATH . '/vendor/lightopenid/openid.php');
					// create the openid object and send the auth request
					$openid = new LightOpenID(COMPANY_URL . '.' . ROOT_DOMAIN);
					$openid->returnUrl = URL_ROOT . '/check_openid_response?timeportal_string=' . md5($saved_username);
					$openid->identity = $user->openid_google;

					//echo Debug::vars($openid, $openid->authUrl());exit;
					header('Location: ' . $openid->authUrl());
					exit;
				} else {
					die(Timeportal::get_message('timeportal', 'openid_not_set'));
				}
			} else {
				die(Timeportal::get_message('timeportal', 'openid_inactive_user'));
			}
		} else {
			die(Timeportal::get_message('timeportal', 'openid_no_user'));
		}
	}

	/**
	 * A basic implementation of the "Forgot password" functionality
	 */
	public function action_forgot() {
		$this->ajax_result = array(
			'status' => 0,
			'html' => ''
		);
		$saved_username = Cookie::get('username', NULL);
		//try {
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
					$email_status = Base::send_email($user->username,
						$user->name(),
						LONG_NAME . ' Password Reset',
						Base::get_view('email/forgot_link', array(
							'user' => $user,
							'url' => Base::get_url('login', array('action' => 'reset')) . '?username=' . $user->username  . '&reset_token=' . $user->reset_token,
						))
					);

					if ($email_status) {
						Base::message('login', 'reset_link_sent', array(), Message::$notice);
						$this->ajax_result['status'] = 1;
					}
				} catch (Exception $e) {
					Base::message('login', 'forgot_send_error', array(), Message::$error);
					throw $e;
				}
			} else if (in_array($user->username, $default_options['admin_accounts'])) {
				Base::message('login', 'reset_admin_account', array(), Message::$warning);

			} else {
				Base::message('login', 'reset_not_found', array(), Message::$warning);
			}
		} // if post
		//} catch (Exception $e) {
		//	Kohana_Exception::handler_continue($e);
		//	Base::message('login', 'reset_error', array(), Message::$error);
		//}

		Base::send_ajax($this->ajax_result);
	}

	/**
	 * Displays the login form and logs the user in or detects and invalid login (through Auth and Model_User)
	 * Customized from cl4_login
	 *
	 * View: Login form.
	 */
	public function action_index() {
		$this->template->page_name = 'login';
		$this->template->page_title = 'Login';

		// get some variables from the request
		$username = CL4::get_param('username', Cookie::get('username', NULL));
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
		$force_captcha = Arr::path($this->session, $login_config['session_key'] . '.force_captcha', FALSE);

		// If more than three login attempts, add a captcha to form
		$captcha_required = ($force_captcha || $attempts > $login_config['failed_login_captcha_display']);

		// Update number of login attempts
		++$attempts;
		$this->session[$login_config['session_key']]['attempts'] = $attempts;

		// load recaptcha
		// do this here because there are likely to be a lot of accesses to this action that will never make it to here
		// loading it here will save server time finding (searching) and loading recaptcha
		Kohana::load(Kohana::find_file('vendor/recaptcha', 'recaptchalib'));

		try {
			// $_POST is not empty
			if ( ! empty($_POST)) {
				$human_verified = FALSE;
				$captcha_received = FALSE;

				// If recaptcha was set and is required
				if ($captcha_required && isset($_POST['recaptcha_challenge_field']) && isset($_POST['recaptcha_response_field'])) {
					$captcha_received = TRUE;
					// Test if recaptcha is valid
					$resp = recaptcha_check_answer(RECAPTCHA_PRIVATE_KEY, $_SERVER['REMOTE_ADDR'], $_POST['recaptcha_challenge_field'], $_POST['recaptcha_response_field']);
					$human_verified = $resp->is_valid;
					Message::add('ReCAPTCHA valid: ' . ($human_verified ? 'Yes' : 'No'), Message::$debug);
				} // if

				// if the captcha is required but we have not verified the human
				if ($captcha_required && ! $human_verified) {
					// increment the failed login count on the user
					$user = ORM::factory('User');
					$user->add_login_where($username)
						->find();

					// increment the login count and record the login attempt
					if ($user->loaded()) {
						$user->increment_failed_login();
					}

					$user->add_auth_log(Kohana::$config->load('cl4login.auth_type.too_many_attempts'), $username);
					Message::message('user', 'recaptcha_not_valid');

					// Check Auth and log the user in if their username and password is valid
				} else if (($login_messages = Auth::instance()->login($username, $password, FALSE, $human_verified)) === TRUE) {
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

					$user->complete_login();

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
					// force captcha may have changed within Auth::login()
					$force_captcha = Arr::path($this->session, $login_config['session_key'] . '.force_captcha', FALSE);
					if ( ! $captcha_required && $force_captcha) {
						$captcha_required = TRUE;
					}

					if ( ! empty($login_messages)) {
						foreach ($login_messages as $message_data) {
							list($message, $values) = $message_data;
							Message::message('user', $message, $values, Message::$error);
						}
					}

					// determine if we should be displaying a recaptcha message
					if ( ! $human_verified && $captcha_received) {
						Message::message('user', 'recaptcha_not_valid', array(), Message::$error);
					} else if ($captcha_required && ! $captcha_received) {
						Message::message('user', 'enter_recaptcha', array(), Message::$error);
					}
				} // if
			} // if $_POST
		} catch (ORM_Validation_Exception $e) {
			Message::message('user', 'username.invalid');
		}

		if ( ! empty($timed_out)) {
			// they have come from the timeout page, so try to send them back to their original page
			$this->redirect(Route::get(Route::name(Request::current()->route()))->uri(array('action' => 'timedout')) . $this->get_redirect_query());
		}

		//$this->template->on_load_js .= file_get_contents(ABS_ROOT . '/html/js/onload_login.js') . EOL;
		$this->template->body_html = View::factory($this->default_view)
			->set('redirect', $redirect)
			->set('username', $username)
			->set('password', $password)
			->set('add_captcha', $captcha_required);
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
	 * View: Access not allowed.
	 */
	public function action_noaccess() {
		// set the template title (see Controller_App for implementation)
		$this->template->page_title = 'Access Not Allowed';
		$view = $this->template->body_html = Base::get_view('cl4/cl4login/no_access')
			->set('referrer', CL4::get_param('referrer'));
	}

	/**
	 * Displays the register new user form or processes the form submission (if POST is not empty).
	 */
	public function action_register() {
		$company = NULL;
		$company_id = NULL;

		$this->template->page_name = 'account_request';
		$this->template->page_title = 'Register for an Account';

		$account_request = ORM::factory('User_Request', NULL, array(
			'display_form_tag' => FALSE,
			'display_buttons' => FALSE,
			'mode' => 'add',
			//'field_name_prefix' => 'ur',
			'field_id_prefix' => 'ur'
		));

		if ( ! empty($_POST)) {
			// attempt to submit the request
			$company_id = CL4::get_param('company_id', NULL, 'int');
			$company = ORM::factory('Company', $company_id);

			if ( ! $company->loaded()) {
				Base::message('login', 'invalid_company', array(), Message::$error);
			} else {
				// validate the request

				// todo: hash the password

				// process the request
				$account_request->save_values();

				$account_request->company_id = $company_id;
				$account_request->source_ip = $_SERVER['REMOTE_ADDR'];
				$account_request->source_browser = $_SERVER['HTTP_USER_AGENT'];

				// make sure this email address is not already in use by a registered user
				$duplicate = ORM::factory('User')->where('username', '=', $account_request->email)->find();
				if ($duplicate->loaded()) {
					Base::message('login', 'email_in_use', array('%name%' => $duplicate->name()), Message::$error);
				} else {
					// todo: check to see if a request is already submitted for this email?

					// get the client admin
					$admins = $company->get_admins();

					// try to save the request
					try {
						if ($account_request->save()) {

							Base::message('login', 'registration_success', array(), Message::$notice);

							// now try to identify the client administrator
							if (sizeof($admins) > 0) {
								foreach($admins as $admin) {
									if (ABS_ROOT == '/home/secure/secure.tuckerenergy.com' || substr_count($admin['username'], 'claero') > 0) {
										Base::send_email($admin['username'],
											$admin['first_name'] . ' ' . $admin['last_name'],
											LONG_NAME . ' Account Request',
											Base::get_view('email/new_registration_notification', array(
												'account_request' => $account_request,
												'company' => $company,
												'admin' => $admin,
											))
										);
										//} else {
										//	Message::add('Would have (but didn\'t) sent a confirmation to <strong>' . $admin['first_name'] . ' ' . $admin['last_name'] . ' (' . $admin['username'] . ')</strong>.', Message::$error);
									}
								}
							} else {
								// todo: cannot find a client administrator, send to tucker admin
								//Message::add('No company administrator found, so we would have (but didn\'t) sent a confirmation to the Data Administrator(s) for ' . $account_request->country->name . '.', Message::$error);
								// 20140505 CSN just sending to Calgary PCS for now (in prod)
								Base::send_email(ADMIN_EMAIL,
									'Calgary PACS',
									LONG_NAME . ' Account Request',
									Base::get_view('email/new_registration_notification', array(
										'account_request' => $account_request,
										'company' => $company,
										'admins' => $admins,
									))
								);
							}

							// send a copy of the request to the requester
							Base::send_email($account_request->email,
								$account_request->first_name . ' ' . $account_request->last_name,
								LONG_NAME . ' Account Request Received',
								Base::get_view('email/new_registration', array(
									'account_request' => $account_request,
									'company' => $company,
									'admins' => $admins,
								))
							);

							$this->redirect(Base::get_url('login', array('action' => 'index')));
						} else {
							Base::message('login', 'registration_failure', array(), Message::$notice);
						}
					} catch (ORM_Validation_Exception $e) {
						Base::message('login', 'registration_failure', array(), Message::$notice);
						Message::message('cl4admin', 'values_not_valid', array(
							':validation_errors' => Message::add_validation_errors($e, i18n::lang() . '/validation')
						), Message::$error);
					}
				}
			}
		}

		// (re)display the request form
		$this->template->body_html = Base::get_view('login/register', array_merge($this->template_parameters, array(
			'user_request' => $account_request,
			'company' => $company,
			'company_id' => $company_id,
		)));
	}

	/**
	 * A basic version of "reset password" functionality.
	 */
	function action_reset() {
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

					$email_status = Base::send_email($user->username,
						$user->name(),
						LONG_NAME . ' New Password',
						Base::get_view('email/forgot_reset', array(
							'app_name' => LONG_NAME,
							'username' => $user->username,
							'password' => $password,
							'url' => Base::get_url('login') . '?username=' . $user->username, //URL::site(Route::get(Route::name(Request::current()->route()))->uri() . '?' . http_build_query(array('username' => $user->username)), Request::current()->protocol()),
							'admin_email' => ADMIN_EMAIL,
						))
					);

					if ($email_status) {
						Base::message('login', 'password_emailed', array(), Message::$notice);
					} else {
						Base::message('login', 'password_email_error', array(), Message::$error);
					}
				} catch (Exception $e) {
					Base::message('login', 'password_email_error', array(), Message::$error);
					throw $e;
				}
			} else {
				Base::message('login', 'password_email_username_not_found', array(), Message::$error);
			}

		} else {
			Base::message('login', 'password_email_partial', array(), Message::$error);
		}

		$this->redirect('/');
	} // function


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
	 * Redirects the user the first page they should see after login
	 * $redirect contains the page they may have requested before logging in and they should be redirected there
	 * If $redirect is is NULL then the default redirect from the config will be used
	 *
	 * @param  string  $redirect  The path to redirect to
	 * @return  void  never returns
	 */
	protected function login_success_redirect($redirect = NULL) {
		if ( ! empty($redirect)) {
			// todo: make sure redirect is valid
			$redirect_url = URL_ROOT . '/' . $redirect; //Base::get_url($redirect);
		} else {
			$redirect_url = Base::get_url('private', array('controller' => 'dashboard'));
		}

		$this->redirect($redirect_url);
	}
}