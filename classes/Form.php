<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Class Form extended for jquery mobile
 */
class Form extends CL4_Form {
	/**
	 * Pass an empty (string), FALSE (bool), 0000-00-00 (string), 0000-00-00 00:00:00 (string) or an invalid date to get a blank field.
	 *
	 * @param string $name
	 * @param string $value
	 * @param array $attributes
	 * @param array $options
	 */
	public static function date($name, $value = FALSE, array $attributes = NULL, array $options = array()) {
		$html = '';

		$default_options = array(
			'clean_date' => FALSE,
			'default_current_date' => FALSE,
		);
		$options += $default_options;

		if ($attributes === NULL) $attributes = array();
		$attributes += array(
			//'size' => 10,
			//'maxlength' => 10,
			'data-role' => 'date',
		);

		//$attributes = HTML::set_class_attribute($attributes, 'js_cl4_date_field-date');

		// check if the value of the date is actually empty
		if (Form::check_date_empty_value($value)) {
			if ($options['default_current_date']) {
				$value = date(Form::DATE_FORMAT);
			} else {
				$value = '';
			}
		} else if ($options['clean_date']) {
			$unix = strtotime($value);
			$value = date(Form::DATE_FORMAT, $unix);
		}

		$html .= Form::input($name, $value, $attributes);

		return $html;
	}

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

	/**
	 * Creates radio buttons for a form.
	 *
	 * @param string $name       The name of these radio buttons.
	 * @param array  $source     The source to build the inputs from.
	 * @param mixed  $selected   The selected input.
	 * @param array  $attributes Attributes to apply to the radio inputs.
	 * @param array  $options    Options to modify the creation of our inputs.
	 *        orientation => the way that radio buttons and checkboxes are laid out, allowed: horizontal, vertical, table, table_vertical (puts text above the <input> separated by a <br />) (default: horizontal)
	 *        radio_attributes => an array where the keys are the radio values and the values are arrays of attributes to be added to the radios
	 *
	 * @return string
	 */
	public static function radios($name, $source, $selected = NULL, array $attributes = NULL, array $options = array()) {
		$html = '';

		$default_options = array(
			'orientation' => 'horizontal',
			'view' => NULL,
			'replace_spaces' => TRUE,
			'table_tag' => true,
			'columns' => 2,
			'escape_label' => TRUE,
			'source_value' => Form::$default_source_value,
			'source_label' => Form::$default_source_label,
			'table_attributes' => array(
				'class' => 'radio_table',
			),
			'radio_attributes' => array(),
			'label_attributes' => array(),
		);
		if (isset($options['table_attributes'])) $options['table_attributes'] += $default_options['table_attributes'];
		$options += $default_options;

		// if the view is empty, set to radios_[orientation] if the orientation is included in our list of orientations (for security)
		if (empty($options['view'])) {
			switch ($options['orientation']) {
				case 'horizontal' :
				case 'table' :
				case 'table_vertical' :
				case 'vertical' :
					$view_name = $options['orientation'];
					break;
				default :
					$view_name = 'horizontal';
					break;
			} // switch
			$options['view'] = 'cl4/form/radios_' . $view_name;
		} // if

		if (empty($attributes['id'])) {
			// since we have no ID, but we need one for the labels, so just use a unique id
			$attributes['id'] = uniqid();
		}

		$fields = array();
		foreach ($source as $radio_key => $radio_value) {
			if ($options['escape_label']) {
				$radio_value = HTML::chars($radio_value);
			}
			if ($options['replace_spaces']) {
				$radio_value = str_replace(' ', '&nbsp;', $radio_value);
			}

			$checked = ($selected == $radio_key);

			// make an attribute for this radio based on the current id plus the value of the radio
			$this_attributes = Arr::overwrite($attributes, array('id' => $attributes['id'] . '-' . $radio_key));

			if (isset($options['radio_attributes'][$radio_key])) {
				$this_attributes = HTML::merge_attributes($this_attributes, $options['radio_attributes'][$radio_key]);
			}

			$label_attributes = array(
				'for' => $this_attributes['id'],
			);
			if (isset($options['label_attributes'][$radio_key])) {
				$label_attributes = HTML::merge_attributes($label_attributes, $options['label_attributes'][$radio_key]);
			}

			$fields[] = array(
				'radio' => Form::radio($name, $radio_key, $checked, $this_attributes),
				'label' => $radio_value,
				'label_tag' => '<label' . HTML::attributes($label_attributes) . '>',
			);
		} // foreach

		return View::factory($options['view'], array(
			'fields' => $fields,
			'options' => $options,
		));
	} // function radios
}