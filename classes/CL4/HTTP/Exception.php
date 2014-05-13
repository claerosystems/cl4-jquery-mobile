<?php defined('SYSPATH') or die('No direct script access.');

class CL4_HTTP_Exception extends Kohana_HTTP_Exception {

	/**
	 * Generate a Response for all Exceptions without a more specific override
	 *
	 * The user should see a nice error page, however, if we are in development
	 * mode we should show the normal Kohana error page.
	 *
	 * @return Response
	 */
	public function get_response()
	{
		// Lets log the Exception, Just in case it's important!
		Kohana_Exception::log($this);

		if (1 || Kohana::$environment >= Kohana::DEVELOPMENT)
		{
			// Show the normal Kohana error page.
			return parent::get_response();
		}
		else
		{
			// Generate a nicer looking "Oops" page.
			$view = Base::get_view('error'); // View::factory('errors/default');

			$response = Response::factory()
				->status($this->getCode())
				->body($view->render());

			return $response;
		}
	}
}