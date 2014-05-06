<?php defined('SYSPATH') or die('No direct script access.');

class Form extends CL4_Form {
	/**
	 * Creates a set of input fields to capture a structured phone number.
	 * The database field needs to be 32 characters long to accommodate the entire phone number
	 *
	 * The format is [country code]-[area code]-[exchange]-[line]-[extension]:
	 *
	 *	 echo Form::phone('start_date','1-613-744-7011-1'); 1 (613) 744-7011 x1
	 *	 echo Form::phone('start_date','-613-744-7011-'); (613) 744-7011
	 *	 echo Form::phone('start_date','--744-7011-'); 744-7011
	 *
	 * @param   string  input name
	 * @param   string  input value false will set the field to be empty, this the default;
	 * @param   array   html attributes
	 * @return  string
	 */
	public static function phone($name, $value = NULL, array $attributes = array(), array $options = array()) {
		$default_options = array(
			'country_code_size' => 3,
			'country_code_max_length' => 3,
			'area_code_size' => 3,
			'area_code_max_length' => 5,
			'exchange_size' => 4,
			'exchange_max_length' => 8,
			'line_size' => 4,
			'line_max_length' => 8,
			'extension_size' => 4,
			'extension_max_length' => 4,
			'show_country_code' => TRUE, // changes the country code field to a hidden field and removes the + before it
			'show_extension' => TRUE, // changes the extension to a hidden field and removes the "ext." before it
		);
		$options += $default_options;

		$set_title_attribute = ( ! array_key_exists('title', $attributes));

		// get the default values for the form fields
		$default_data = CL4::parse_phone_value($value);

		$html = '<div class="phone_grid">';

		// add the country code
		$_attributes = $attributes;
		if ($options['show_country_code']) {
			if ($set_title_attribute) {
				$_attributes['title'] = 'Country Code';
			}
			$html .= '<div class="phone_sep">+</div> <div class="phone_int_code">' . Form::input_with_suffix_size($name, $default_data['country_code'], $_attributes, 'cl4_phone_field', 'country_code', $options['country_code_size'], $options['country_code_max_length']) . '</div>';
		} else {
			$_attributes = HTML::set_class_attribute($_attributes, 'cl4_phone_field-country_code');
			if ( ! empty($_attributes['id'])) $_attributes['id'] .= '-country_code';
			$html .= Form::hidden($name . '[country_code]', $default_data['country_code'], $_attributes);
		}

		// add the area code
		$attributes = Form::increment_tabindex($attributes);
		$_attributes = $attributes;
		if ($set_title_attribute) {
			$_attributes['title'] = 'Area Code';
		}
		$html .= '<div class="phone_sep">(</div> <div class="phone_area_code">' . Form::input_with_suffix_size($name, $default_data['area_code'], $_attributes, 'cl4_phone_field', 'area_code', $options['area_code_size'], $options['area_code_max_length']) . '</div>';

		// add the exchange field
		$attributes = Form::increment_tabindex($attributes);
		$_attributes = $attributes;
		if ($set_title_attribute) {
			$_attributes['title'] = 'Phone Number Part 1 (Exchange)';
		}
		$html .= '<div class="phone_sep">)</div> <div class="phone_exchange">' . Form::input_with_suffix_size($name, $default_data['exchange'], $_attributes, 'cl4_phone_field', 'exchange', $options['exchange_size'], $options['exchange_max_length']) . '</div>';

		// add the line field
		$attributes = Form::increment_tabindex($attributes);
		$_attributes = $attributes;
		if ($set_title_attribute) {
			$_attributes['title'] = 'Phone Number Part 2 (Line)';
		}
		$html .= '<div class="phone_sep">-</div> <div class="phone_line">' . Form::input_with_suffix_size($name, $default_data['line'], $_attributes, 'cl4_phone_field', 'line', $options['line_size'], $options['line_max_length']) . '</div>';

		if ($options['show_extension']) {
			// add the extension field
			$attributes = Form::increment_tabindex($attributes);
			$_attributes = $attributes;
			if ($set_title_attribute) {
				$_attributes['title'] = 'Extension';
			}
			$html .= '<div class="phone_sep">x</div>  <div class="phone_area_code">' . Form::input_with_suffix_size($name, $default_data['extension'], $_attributes, 'cl4_phone_field', 'extension', $options['extension_size'], $options['extension_max_length']) . '</div>';
		} else {
			$_attributes = HTML::set_class_attribute($_attributes, 'cl4_phone_field-extension');
			if ( ! empty($_attributes['id'])) $_attributes['id'] .= '-extension';
			$html .= Form::hidden($name . '[extension]', $default_data['extension'], $_attributes);
		}

		return $html . '</div>';
	} // function phone
}