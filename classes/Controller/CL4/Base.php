<?php defined('SYSPATH') or die('No direct script access.');

/**
 * A default base Controller class.
 * Some of the functionality is required by cl4 and other modules.
 */
class Controller_CL4_Base extends Controller_Template {
    /**
     * The template to use. The string is replaced with the View in before().
     * @var  View
     */
    public $template = 'themes/default/template'; // this is the default template file

    public $session;

    public $allowed_languages = array('en-ca'); // set allowed languages
    public $locale; // the locale string, eg. 'en-ca' or 'fr-ca'
    public $language; // the two-letter language code, eg. 'en' or 'fr'

    /**
     * Used in the page id so it can't have spaces or weird chars
     * @var
     */
    public $page_name = 'home';

    public $page_title = "Welcome";

    /**
     * Controls access for the whole controller.
     * If the entire controller REQUIRES that the user be logged in, set this to TRUE.
     * If some or all of the controller DOES NOT need to be logged in, set to this FALSE; to control which actions require authentication or a specific permission, us the $secure_actions array.
     */
    public $auth_required = FALSE;

    /**
     * Controls access for separate actions
     *
     * Examples:
     * not set (FALSE) => when $auth_required is TRUE, then it will be considered a secure action, but will only require that the user is logged in
     *            when $auth_required is FALSE, then everyone will have access to the action
     * 'list' => FALSE the list action does not require the user to be logged in (the following are all the same as FALSE: "", 0, "0", NULL, array() (empty array))
     * 'profile' => TRUE allows any logged in user to access that action
     * 'adminpanel' => 'admin' will only allow users with the permission admin to access action_adminpanel
     * 'moderatorpanel' => array('login', 'moderator') will only allow users with the permissions login AND moderator to access action_moderatorpanel
     */
    public $secure_actions = FALSE;

    /**
     * An array of actions as found in the request that shouldn't use auto_render.
     * @var  array
     */
    protected $no_auto_render_actions = array();

    /**
     * If the messages should be added to the template.
     * @var  boolean
     */
    protected $display_messages = TRUE;

    /**
     * Array of scripts, keyed by name with value of array containing keys path, media and array of required styles before adding this style.
     * Add to using add_style().
     * @var  array
     */
    protected $styles = array();

    /**
     * Array of scripts, keyed by name with value of array containing keys path and array of required scripts before adding this script.
     * Add to using add_script().
     * @var  array
     */
    protected $scripts = array();

    /**
     * String of on load JavaScript.
     * @var  array
     */
    protected $on_load_js = '';

    public function before() {
        if (in_array($this->request->action(), $this->no_auto_render_actions)) {
            $this->auto_render = FALSE;
        }

        parent::before();

        // initialize the locale if there are allowed languages
        $this->allowed_languages = Kohana::$config->load('cl4.languages');
        if ( ! empty($this->allowed_languages) && count($this->allowed_languages) > 0) {
            $language_selection = TRUE;
            //try {
                // use the locale parameter from the route, if not set, then the cookie, if not set, then use the first locale in the list
                $this->locale = Request::current()->param('locale', Cookie::get('language', $this->allowed_languages[0]));
                // make sure the locale is valid
                if ( ! in_array($this->locale, $this->allowed_languages)) $this->locale = $this->allowed_languages[0];
                // set up the locale
                i18n::lang($this->locale);
                // try to remember the locale in a cookie
                Cookie::set('language', $this->locale, Date::MONTH);
            //} catch (Exception $e) {
                // failed to set and/or store the locale
            //}
            $this->language = substr(i18n::lang(), 0, 2);

            //echo Debug::vars($this->request);exit;

            // create the language switch link and set the locale
            if ($this->locale == 'fr-ca') {
                // french, set the date
                setlocale(LC_TIME, 'fr_CA.utf8');
                setlocale(LC_MESSAGES, 'fr_CA.utf8');
                // create the switch language link
                $language_switch_link = '<a href="/en">EN</a> / FR';
                $date_input_options = "            format: 'dddd dd, mmmm yyyy'" . EOL;
            } else {
                // english, set the date
                setlocale(LC_TIME, 'en_CA.utf8');
                setlocale(LC_MESSAGES, 'en_CA.utf8');
                // create the switch language link
                $language_switch_link = 'EN / <a href="/fr">FR</a>';
                $date_input_options = "            lang: 'fr', " . EOL; // defined in master js file, must execute before this does
                $date_input_options .= "            format: 'dddd mmmm dd, yyyy'" . EOL;
            } // if

        } else {
            // there are no language selection
            setlocale(LC_TIME, 'en_CA.utf8');
            i18n::lang('en-ca');
            $language_selection = FALSE;
        }

        // if the site is unavailable, redirect the user to the unavailable page
        if (defined('UNAVAILABLE_FLAG') && UNAVAILABLE_FLAG) {
            $msg = (defined('UNAVAILABLE_MESSAGE')) ? UNAVAILABLE_MESSAGE : 'The site is currently unavailable.';
            echo __($msg);
            exit;

            throw HTTP_Exception_503::factory(503, __($msg));
        }

        $this->check_login();

        $this->session = Session::instance()->as_array();

        if ($this->auto_render) {
            // set up css
            $this->styles['jquery_mobile'] = "/cl4/css/vendor/jquery.mobile-1.4.5.css";
            $this->styles['jquery_mobile_datepicker'] = "/cl4/css/vendor/jquery.mobile.datepicker.css";
            //$this->styles['font_awesome'] = "/cl4/font-awesome/css/font-awesome.min.css";
            $this->styles['cl4'] = "/cl4/css/cl4.css";
            $this->styles['base'] = "/cl4/css/base.css";

            // set up template parameters for optional use when generating views in the main controllers
            // NB. these only get used when you pass $this->template_parameters to your template, they are not part of the main template parameters (below)
            $this->template_parameters['session'] = $this->session;

            // set up the main template parameters
            $this->template->session = $this->session; // for debug purposes anyway

            // todo: 20140725 CSN take this out...
            $this->template_parameters['button_default_attributes'] = array(
                'data-role' => 'button',
                'data-mini' => 'true',
                'data-inline' => 'true',
                'data-theme' => 'c',
            );
            $this->template_parameters['edit_button_attributes'] = ARR::merge(
                $this->template_parameters['button_default_attributes'],
                array(
                    'data-icon' => 'edit',
                    //'data-rel' => 'dialog',
                )
            );
            $this->template_parameters['add_button_attributes'] = ARR::merge(
                $this->template_parameters['button_default_attributes'],
                array(
                    'data-icon' => 'add',
                    //'data-rel' => 'dialog',
                )
            );
            $this->template_parameters['delete_button_attributes'] = ARR::merge(
                $this->template_parameters['button_default_attributes'],
                array(
                    'data-icon' => 'delete',
                    //'data-rel' => 'dialog',
                )
            );
            $this->template_parameters['back_button_attributes'] = ARR::merge(
                $this->template_parameters['button_default_attributes'],
                array(
                    'data-icon' => 'arrow-l',
                    'data-iconpos' => 'left',
                )
            );
            $this->template_parameters['close_button_attributes'] = ARR::merge(
                $this->template_parameters['button_default_attributes'],
                array(
                    //'data-icon' => 'delete',
                    'data-rel' => 'close',
                )
            );
            $this->template_parameters['select_attributes'] = array(
                'data-mini' => 'true',
            );
        }

        // set up the default template values for the base template
        $this->initialize_template();
    } // function before

    public function action_404() {
        throw HTTP_Exception::factory(404, __('The page you requested was not found.'));
    }

    /**
     * Automatically executed after the controller action. Can be used to apply
     * transformation to the request response, add extra output, and execute
     * other custom code.
     * Completes the setup of the template.
     *
     * @return  void
     */
    public function after() {
        if ($this->auto_render) {
            // add a body class for page
            if ( ! empty($this->page)) {
                $this->template->body_class .= ' p_' . $this->page;
            }

            //$this->template->on_load_js = $this->on_load_js;

            $this->template->styles = $this->styles;

            // look for any status message and display
            if ($this->display_messages) {
                $this->template->message = Message::display();
            }

            if (CL4::is_dev()) {
                // this is so a session isn't started needlessly when in debug mode
                $this->template->session = Session::instance()->as_array();
            }
        } // if

        parent::after();

        if ($this->auto_render === TRUE) {
            //$this->response->check_cache( null, $this->request );

            // don't cache the actual php-generated page content
            $this->response->headers(array('Cache-Control' => 'no-cache'));
        }
    } // function after

    /**
     * Checks if the user is logged in and if they have permissions to the current action
     * If the user is not logged in, then they are redirected to the timed out page or login page
     * If the user is logged in, but not allowed, then they are sent to the no access page
     * If they are logged in and have access, then it will updat the timestamp in the session
     * If c_ajax == 1, then a JSON string will be returned instead, using AJAX_Status and it's constants
     *
     * @return  Controller_Base
     */
    public function check_login() {
        // ***** Authentication *****
        // check to see if they are allowed to access the action

        if ( ! Auth::instance()->controller_allowed($this, $this->request->action())) {
            $is_ajax = (bool) Arr::get($_REQUEST, 'c_ajax', FALSE);

            if (Auth::instance()->logged_in()) {
                // user is logged in but not allowed to access the page/action
                if ($is_ajax) {
                    echo AJAX_Status::ajax(array(
                        'status' => AJAX_Status::NOT_ALLOWED,
                        'debug_msg' => 'Referrer: ' . $this->request->uri(),
                    ));
                    exit;
                } else {
                    $this->redirect(Base::get_url('login', array('action' => 'noaccess')) . $this->get_login_redirect_query());
                }
            } else {
                if (Auth::instance()->timed_out()) {
                    if ($is_ajax) {
                        echo AJAX_Status::ajax(array(
                            'status' => AJAX_Status::TIMEDOUT,
                        ));
                        exit;
                    } else {
                        // store the get and post if timeout post is enabled
                        $this->process_timeout();

                        // display password page because the sesion has timeout
                        $this->redirect(Base::get_url('login', array('action' => 'timedout')) . $this->get_login_redirect_query());
                    }
                } else {
                    if ($is_ajax) {
                        // just not logged in and is ajax so return a json array with the status of not logged in
                        echo AJAX_Status::ajax(array(
                            'status' => AJAX_Status::NOT_LOGGED_IN,
                        ));
                        exit;
                    } else {
                        // just not logged in, so redirect them to the login with a redirect parameter back to the current page
                        $this->redirect(Base::get_url('login') . $this->get_login_redirect_query());
                    }
                }
                echo Debug::vars('here3');exit;
            } // if
            echo Debug::vars('here4');exit;
        } // if

        if (Auth::instance()->logged_in() && $this->auto_render) {
            // update the session auth timestamp
            Auth::instance()->update_timestamp();
        } // if

        return $this;
    } // function check_login

    /**
     * Returns the query containing the redirect for the login controller.
     * Used within the check_login() method to pass the redirect through the login action/controller.
     * Bases the redirect on current URL/URI and the full get/query string.
     *
     * @return  string
     */
    protected function get_login_redirect_query() {
        return URL::array_to_query(array('redirect' => $this->request->uri() . '?' . http_build_query($_GET)), '&');
    }

    /**
     * Setup the default template values.
     *
     * @return void
     */
    protected function initialize_template() {
        if ($this->auto_render) {
            // Initialize default values
            $this->template->logged_in = Auth::instance()->logged_in();
            if ($this->template->logged_in) {
                $this->template->user = Auth::instance()->get_user();
            }

            // set some empty variables
            $this->template->page_name = $this->page_name;
            $this->template->page_title = $this->page_title;
            $this->template->meta_tags = array();
            $this->template->body_class = '';
            $this->template->pre_message = '';
            $this->template->body_html = '';
            $this->template->on_load_js = '';
        } // if
    }

    /**
     * If the login timeout post functionality is enabled, this will store the passed
     * GET and POST in the session key for use in Controller_CL4_Login to re-post the data.
     * If there is no get or post, this will unset the session key
     *
     * @return  void
     */
    protected function process_timeout() {
        if (Kohana::$config->load('cl4login.enable_timeout_post')) {
            // store the post so we can post it again after the user enters their password
            $timeout_post_session_key = Kohana::$config->load('cl4login.timeout_post_session_key');
            $query = $this->request->query();
            $post = $this->request->post();
            if ( ! empty($query) || ! empty($post)) {
                Session::instance()->set($timeout_post_session_key, array(
                    'post_to' => $this->request->uri(),
                    'get' => $query,
                    'post' => $post,
                ));
            } else {
                Session::instance()->delete($timeout_post_session_key);
            }
        } // if
    } // function process_timeout
}