<?php defined('SYSPATH') or die('No direct script access.');

class Controller_CL4_Ajax extends Controller_Base {
	public $action = NULL;
	public $record_limit = NULL;
	public $cascade_sql = FALSE;
	public $output = NULL;
	public $output_type = 'html'; // 'html', 'json', or 'xml' ?\
	public $status = 1;
	public $html = 'no html specified';

	public function before() {
		$this->auto_render = FALSE;

		// process the request parameters
		$this->action = CL4::get_param('action');
		$this->record_limit = CL4::get_param('limit', 10);
		$this->output_type = CL4::get_param('type', 'json'); // 'html', 'json', or 'xml' ?

		$this->output = array(
			'html' => '',
			'status' => 1,
		);

		parent::before();
	}

	public function action_after() {
		if ( ! empty($this->status)) $this->output['status'] = $this->status;
		if ( ! empty($this->html)) $this->output['html'] = $this->html;

		$result = json_encode($this->output);

		// execute the query if not done in the switch statement
		// this case is specifically formatted for use with jquery cascade ajax calls
		if (! empty($this->cascade_sql)) {
			$db = Database::instance();
			$query = $db->query(Database::SELECT, $this->cascade_sql, FALSE);
			if ($query !== FALSE) {
				$found_data = array();
				foreach ($query AS $data) {
					$row = array();
					$row['When'] = $data['parent_id'];
					$row['Value'] = $data['child_id'];
					$row['Text'] = utf8_encode($data['child_description']);
					$found_data[] = $row;
				} // while
				$result = json_encode($found_data);
			} else {
				$result = "ajax_system_select.php: query failed ({$this->cascade_sql})";
			} // if
		} // if

		// send the output
		echo $result;
		exit;
	}

	public function action_autocomplete_company_name() {
		$search_text = CL4::get_param('q', NULL, 'string');

		$result = DB::select('id', 'name')
			->from('company')
			->where('name', 'LIKE', "{$search_text}%")
			->and_where('active_flag', '=', 1)
			->and_where('company_type_id', '=', CLIENT_COMPANY_TYPE_ID)
			->order_by('name', 'ASC')
			->limit(100)
			->execute()
			->as_array();
		$jason_result = array();
		foreach ($result as $data) {
			$jason_result[] = array(
				'id' => $data['id'],
				'name' => $data['name'],
			);
		}
		$this->html = $jason_result;
		$this->action_after();
	}

	public function action_check_login() {
		$this->output = array(
			'html' => 'checking login',
			'status' => 1,
			'logged_in' => 0,
		);

		if (Auth::instance()->logged_in()) $this->output['logged_in'] = 1;

		$this->action_after();
	}

	/**
	 * get / set a user or company setting via ajax
	 */
	public function action_get_smart_parameter() {
		$parameter_name = CL4::get_param('parameter_name', NULL);
		$default = CL4::get_param('default', NULL);
		$type = CL4::get_param('type', 'user');
		// todo: security check!!!
		if ( ! empty($parameter_name)) {
			try {
				$this->html = Base::get_smart_parameter($parameter_name, $default, $type);
				$this->status = 1;
			} catch (exception $e) {
				$this->html = 'failed to get smart parameter: ' . Kohana_Exception::text($e);
				$this->status = 0;
			}
		} else {
			$this->html = 'failed to get smart parameter, no parameter name specified';
			$this->status = 0;
		}
		$this->action_after();
	}
}