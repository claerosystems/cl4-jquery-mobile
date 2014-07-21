<?php defined('SYSPATH') OR die('No direct access allowed.');

class CL4_ORM_Suggest extends CL4_ORM_Text {
	public static function edit($column_name, $html_name, $value, array $attributes = NULL, array $options = array(), ORM $orm_model = NULL) {
		if ( ! empty($value_text)) {
			$value_text = $orm_model->get_source_data($column_name, $value);
		} else {
			$value_text = '';
		}

		return Form::suggest($html_name, $value, $value_text, $orm_model->object_name(), $column_name, $attributes);
	}

	public static function save($post, $column_name, array $options = array(), ORM $orm_model = NULL) {
		$value = Arr::get($post, $column_name);

		if ($value !== NULL || $options['is_nullable']) {
			$orm_model->$column_name = ($value == 'none' || $value == 'all' || $value == '' ? 0 : $value);
		}
	}

	public static function view($value, $column_name, ORM $orm_model = NULL, array $options = array(), $source = NULL) {
		$found_value = Arr::get($source, $value);
		if ($found_value !== NULL) {
			return $found_value;
		} else {
			return 0;
		}
	}

	public static function view_html($value, $column_name, ORM $orm_model = NULL, array $options = array(), $source = NULL) {
		$found_value = ORM_Select::view($value, $column_name, $orm_model, $options, $source);
		if ($found_value !== NULL && $found_value !== 0) {
			return ORM_Select::prepare_html(__($found_value), $options['nbsp']);
		} else if ($value > 0) {
			// the value is still > 0 but we don't know what the value is because it's not in the data
			return __(Kohana::message('cl4', 'cl4_unknown_html'));
		} else {
			// the value is not set (0 or NULL likely)
			return __(Kohana::message('cl4', 'cl4_not_set_html'));
		}
	}
}