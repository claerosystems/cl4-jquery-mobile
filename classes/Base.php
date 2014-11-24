<?php defined('SYSPATH') or die ('No direct script access.');

class Base extends CL4_Base {
	/**
	 * Generate a table with login history records based on the filters.
	 *
	 * @param array $filter
	 *
	 * @return string
	 */
	public static function get_login_history($filter = array(), $host_name_lookup_flag = TRUE) {
		// get the login history
		$login_query = DB::select('l.*', 'u.first_name', 'u.last_name', 'u.username')
		                 ->from(array('auth_log', 'l'))
		                 ->join(array('user', 'u'))->on('u.id', '=', 'l.user_id');

		if ( ! empty($filter['user_id'])) $login_query->where('l.user_id', '=', $filter['user_id']);
		if ( ! empty($filter['from'])) $login_query->where('l.access_time', '>=', $filter['from']);
		if ( ! empty($filter['to'])) $login_query->where('l.access_time', '<=', $filter['to']);

		if ( ! empty($filter['order_by_column']) && ! empty($filter['order_by_direction'])) {
			$login_query->order_by($filter['order_by_column'], $filter['order_by_direction']);
		} else {
			$login_query->order_by('l.access_time', 'DESC');
		}

		if ( ! empty($filter['limit'])) {
			$login_query->limit($filter['limit']);
		} else {
			$login_query->limit(15);
		}

		//echo (string) $login_query; exit;

		$login_list = $login_query->execute()->as_array();

		// create the auth table for display
		$auth_table = new HTMLTable(array(
			'heading' => array(
				__('Access Time'),
				__('User'),
				__('Type'),
				__('IP Address Detected'),
				//__('Browser'),
			),
			'table_attributes' => array(
				'class' => 'responsive_table login_history',
				'data-role' => 'table',
				//'data-mode' => 'columntoggle',
				//'id' => 'login_history'
			),
			'populate_all_cols' => TRUE,
		));
		//$auth_table->set_th_attribute(0, 'data-priority', 1);
		//$auth_table->set_th_attribute(1, 'data-priority', 2);
		$auth_table->set_th_attribute(2, 'data-priority', 1);
		$auth_table->set_th_attribute(3, 'data-priority', 5);
		$auth_type_map = Kohana::$config->load('auth.auth_type_map');
		foreach($login_list as $login) {
			$ip = $login['ip_address'];
			if ($host_name_lookup_flag && empty($login['host_detected'])) {
				$ip .= ' | ' . gethostbyaddr($login['ip_address']);
			} else if ( ! empty($login['host_detected'])) {
				$ip .= ' | ' . $login['host_detected'];
			}
			$auth_table->add_row(array(
				$login['access_time'],
				$login['first_name'] . ' ' . $login['last_name'] . '<br>' . $login['username'],
				$auth_type_map[$login['auth_type_id']],
				$ip . '<br>' . $login['browser'],
				//$login->browser,
			));
		}

		return $auth_table->get_html();
	}
}