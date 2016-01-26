<?php defined('SYSPATH') or die ('No direct script access.');
/**
 * General functions that are used frequently throughout the entire website
 *
 * @package    Base
 * @author     Claero Systems
 * @copyright  (c) 2014 Claero Systems
 */
class CL4_Base {
	/**
	 * Prepare a string to be inserted in to a .csv file.
	 *
	 * @param $text
	 *
	 */
	public static function clean_for_csv($text) {

	}
	/**
	 * Attempt to convert the given phone number in to the standard cl4 format, which is x-xxx-xxx-xxxx-xx
	 * @param $phone_number
	 *
	 * @return string
	 */
	public static function convert_phone_to_cl4($phone_number) {
		$converted = '';

		// remove any leading '+'
		if (substr($phone_number, 0, 1) == '+') $phone_number = substr($phone_number, 1, strlen($phone_number) - 1);

		// attempt to find the extension
		$phone_ext_parts = explode('x', $phone_number);
		if (isset($phone_ext_parts[1])) {
			$phone_ext = preg_replace("/\D/", "", $phone_ext_parts[1]);
			$phone_part_1 = preg_replace("/\D/", "-", $phone_ext_parts[0]);
		} else {
			$phone_ext = '';
			$phone_part_1 = preg_replace("/\D/", "-", $phone_number);
		}

		// replace all the duplicate dashes with a single and remove any trailing dashes
		$phone_part_1 = trim(str_replace('--', '-', $phone_part_1), '-');
		$phone_parts = explode('-', $phone_part_1);
		$phone_part_count = count($phone_parts);

		// we only have 345-3234
		if ($phone_part_count == 2) {
			$converted = '--' . $phone_part_1 . '-' . $phone_ext;
			// we have 323-434-5456
		} else if ($phone_part_count == 3) {
			$converted = '-' . $phone_part_1 . '-' . $phone_ext;
			// we have 1-455-545-6567
		} else if ($phone_part_count == 4) {
			$converted = $phone_part_1 . '-' . $phone_ext;
			// we have 1-432-434-5455-455
		} else if ($phone_part_count == 5) {
			$converted = $phone_part_1; // already has the extension in it
		} else {
			$converted = $phone_number;
		}

		return $converted;
	}

	/**
	 * copy a file to AWS, all status messages are in Message
	 *
	 * @param $source_file_path
	 * @param $target_file_path
	 */
	public static function copy_media_to_aws($source_file_path, $target_file_path) {
		$status = FALSE;
		// copy the file to AWS
		require_once(APPPATH . '/vendor/s3/S3.php');
		$s3 = new S3(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY);
		// see if the file is already on AWS
		if ($s3->getObjectInfo(AWS_MEDIA_BUCKET, $target_file_path, FALSE)) {
			// the file already exists on AWS
			//Message::add("The file already exists on S3: " .  AWS_MEDIA_BUCKET . '/' . $target_file_path, Message::$debug);
		} else {
			if ($s3->putObjectFile($source_file_path, AWS_MEDIA_BUCKET, $target_file_path)) {
				Message::add("{$target_file_path} stored in cloud.", Message::$debug);
				$status = TRUE;
			} else {
				Message::add("An error occurred and the file was not uploaded to S3:  " . AWS_MEDIA_BUCKET . '/' . $target_file_path, Message::$error);
			}
		}
		return $status;
	}

	/**
	 * formats a description string based on max length, etc for standard look and feel
	 *
	 * @param    float       $amount                 the amount
	 * @param    boolean     $htmlFlag               whether to include the html tags
	 *
	 * @return   string  $return                 the HTML formatted string
	 */
	public static function format_description($description, $max_length, $html_flag = TRUE) {
		$return_string = '';

		if (strlen($description) > $max_length) {
			// truncate
			$return_string = '';
			$return_string .= ($html_flag) ? '<span class="description" title="' . htmlentities($description) . '">' : '';
			$return_string .= htmlentities(mb_substr($description, 0, $max_length) . '...');
			$return_string .= ($html_flag) ? '</span>' : '';
		} else {
			$return_string = htmlentities($description);
		} // if

		return $return_string;
	}

	/**
	 * Return a formatted filesize.
	 *
	 * @param $bytes
	 * @param int $dec
	 * @return string
	 */
	public static function format_filesize($bytes, $dec = 2) {
		$size   = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
		$factor = floor((strlen($bytes) - 1) / 3);

		return sprintf("%.{$dec}f", $bytes / pow(1024, $factor)) . ' ' . @$size[$factor];
	}

	/**
	 * Return a formatted dollar value from a decimal or integer.
	 *
	 * @param $amount
	 * @param int $decimals
	 * @return string
	 */
	public static function format_money($amount, $decimals = 2) {
		return (is_numeric($amount)) ? '$' . number_format($amount, $decimals) : $amount;
	}

	/**
	 * Prepare and send a PDF document to the browser using WKHTMLTOPDF
	 *
	 * @param $prefix            used to differentiate different pdf types (modules/report/etc.)
	 * @param $page_content      the HTML to be included in the PDF
	 * @param $footer_content    the HTML to be included at the bottom of each page of the PDF
	 * @param $output_filename   the name of the file to be output to the user (somthing.pdf)
	 */
	public static function generate_pdf($prefix, $page_content, $footer_content, $header_content, $output_filename) {
		// save the preview HTML in to a temp file that can be accessed by wkhtmltopdf
		$temp_file_path = tempnam(ABS_ROOT . '/html/wkhtmltopdf_tmp', $prefix . 'r_');
		$target_file_path = $temp_file_path . '.html';
		rename($temp_file_path, $target_file_path);
		file_put_contents($target_file_path, $page_content);

		// save the pdf header in to a temp file that can be accessed by wkhtmltopdf
		$temp_header_path = tempnam(ABS_ROOT . '/html/wkhtmltopdf_tmp', $prefix . 'h_');
		$target_header_path = $temp_header_path . '.html';
		rename($temp_header_path, $target_header_path);
		file_put_contents($target_header_path, $header_content);

		// save the pdf footer in to a temp file that can be accessed by wkhtmltopdf
		$temp_footer_path = tempnam(ABS_ROOT . '/html/wkhtmltopdf_tmp', $prefix . 'f_');
		$target_footer_path = $temp_footer_path . '.html';
		rename($temp_footer_path, $target_footer_path);
		file_put_contents($target_footer_path, $footer_content);

		// extract just the filename from the target paths
		$file_path_parts = explode('/', $target_file_path);
		$target_file_name = array_pop($file_path_parts);

		$header_path_parts = explode('/', $target_header_path);
		$target_header_name = array_pop($header_path_parts);

		$footer_path_parts = explode('/', $target_footer_path);
		$target_footer_name = array_pop($footer_path_parts);

		//echo file_get_contents(URL_ROOT . '/wkhtmltopdf_tmp/' . $target_file_name);exit;
		//echo file_get_contents(URL_ROOT . '/wkhtmltopdf_tmp/' . $target_footer_name);exit;
		//echo Debug::vars(URL_ROOT . '/wkhtmltopdf_tmp/' . $target_file_name, URL_ROOT . '/wkhtmltopdf_tmp/' . $target_footer_name, $output_filename);
		//exit;

		// create the PDF using WkHtmlToPDF
		require_once(ABS_ROOT . '/application/vendor/phphtmltopdf/WkHtmlToPdf.php');
		$pdf = new WkHtmlToPdf();
		$pdf->setOptions(array(
			//'tmp' => ABS_ROOT . '/html/wkhtmltopdf_tmp',
			'footer-html' => URL_ROOT . '/wkhtmltopdf_tmp/' . $target_footer_name,
			'header-html' => URL_ROOT . '/wkhtmltopdf_tmp/' . $target_header_name,
			//'header-line' => NULL,
			//'footer-line' => '1',
			'footer-spacing' => '2',
			'header-spacing' => '2',
			'margin-top' => '20',
			'margin-bottom' => '20',
			'margin-left' => '10',
			'margin-right' => '10',
		));
		$pdf->addPage(URL_ROOT . '/wkhtmltopdf_tmp/' . $target_file_name);

		//echo Debug::vars($pdf);

		$status = $pdf->send($output_filename);

		echo Debug::vars($status);
	}

	/**
	 * Return the gravatar link for the given email and size.
	 *
	 * @param $email
	 * @param $size
	 *
	 * @return string
	 */
	public static function get_gravatar($email, $size) {
		if (HTTP_PROTOCOL == 'https') {
			$base_link = "https://secure.gravatar.com/avatar/";
		} else {
			$base_link = "http://www.gravatar.com/avatar/";
		}

		return $base_link . md5(strtolower(trim($email))) . "?s=" . $size . '&d=mm'; //'&d=blank'; //'&d=identicon'; // "?d=" . urlencode(URL_ROOT . '/images/loading.gif')
	}

	/**
	 * return the full <img> tab to the image from the model, id, and column specified, this is normally on AWS
	 * if the image is not on AWS, create the image on AWS and return the path
	 *
	 * @param mixed $db_connection
	 * @param mixed $table_name
	 * @param mixed $column_name
	 * @param mixed $id
	 * @param mixed $options
	 */
	public static function get_image($model_name, $id, $column_name, $options = array()) {
		$img_tag = FALSE;
		$img_attributes = (isset($options['img_attributes'])) ? $options['img_attributes'] : array();
		$max_height = (isset($options['max_height'])) ? $options['max_height'] : 0;
		$max_width = (isset($options['max_width'])) ? $options['max_width'] : 0;

		if (in_array($model_name, Portfolio::get_allowed_models())) {
			//echo Debug::vars($model_name, $column_name, $id);exit;
			$target_model = ORM::factory($model_name, $id);
			if ($target_model->loaded() && ! empty($target_model->$column_name)) {
				switch($model_name) {
					case 'Global_Company_Client_View':
						$upload_root = GLOBAL_UPLOAD_ROOT;
						break;
					default:
						$upload_root = COMPANY_UPLOAD_ROOT;
						break;
				}

				$target_path = strtolower($model_name) . '/' . $column_name . '/' . $target_model->$column_name;
				$original_image_path = $upload_root . '/' . $target_path;
				if ( ! file_exists($original_image_path)) {
					return "<!-- no bottle shot found: \n\n" . $original_image_path . "\n\n -->";
					//return '<span class="ui-state-error ui-corner-all" style="padding:0.5em;">missing image</span>';
				}

				// set up path based on options
				if ($max_height > 0 || $max_width > 0) {
					$resize_source_path = strtolower($model_name) . '/' . $column_name . '/' . 'c_m' . $max_width . 'xm' . $max_height . '_' . $target_model->$column_name;
					$aws_source = REQUEST_COMPANY_URL . '/' . $resize_source_path;
					$local_source = $upload_root . '/' . $resize_source_path;
				} else {
					$aws_source = REQUEST_COMPANY_URL . '/' . $target_path;
					$local_source = $original_image_path;
				}

				// see if this already exists on AWS and just return the link
				$resize_flag = FALSE;
				if ( ! file_exists( AWS_MEDIA_URL . $aws_source)) {
					if ($max_height > 0 || $max_width > 0) {
						//try {
						$source_image = Image::factory($original_image_path);

						// do we need to resize?
						if ($max_height > 0 && $source_image->height > $max_height) {
							// too high
							$source_image->resize(NULL, $max_height);
							$resize_flag = TRUE;
						}
						if ($max_width > 0 && $source_image->width > $max_width) {
							// too wide
							$source_image->resize($max_width, NULL);
							$resize_flag = TRUE;
						}
						//} catch (Exception $e) {

						//	Kohana_Exception::handler($e, FALSE, TRUE);
						//	return FALSE;
						//}
					}

					if ($resize_flag) {
						// create the new image in a temporary location
						$source_image->sharpen(10);
						$source_image->save($local_source);
						//$img_attributes['width'] = $source_image->width;
						//$img_attributes['width'] = $source_image->height;
					} else {
						// just grab the original image, it was already within the  specified limits
						$aws_source = REQUEST_COMPANY_URL . '/' . $target_path;
						$local_source = $original_image_path;
					}
					// move the file to AWS if it is not already there
					Wine::copy_media_to_aws($local_source, $aws_source);
				}

				// return the file path on AWS
				// todo: double-check this?  how about catch above? send local as backup?
				$img_src = AWS_MEDIA_URL . $aws_source;

				$img_tag = HTML::image($img_src, $img_attributes);
			} else {
				// no image set
				//die('could not load model or empty value');
			}
		} else {
			die('Invalid model received for action_get_image(), if this is valid, please add `' . $model_name . '` to the list of acceptable models.');
		}
		return $img_tag;
	}

	/**
	 * Get the localized version of the given message based on i18n::lang()
	 *
	 * @param $file
	 * @param $path
	 *
	 * @return string
	 */
	public static function get_message($file, $path, $data = NULL) {
		$message = Kohana::message(i18n::lang() . '/' . $file, $path, __('[message not found]'));
		if (is_array($data)) {
			$message = strtr($message, $data);
		}
		return $message;
	}

	/**
	 * Check for a parameter with the given key in the request data, POST overrides Request ovverides GET
	 * in this case empty values are returned as they were found, in other words '' and zero will work (unlike CL4::get_param)
	 *
	 * @param  string  the key of the parameter
	 * @param  mixed  the default value
	 * @param  string  used for type casting, can be 'int', 'string' or 'array'
	 * @return  mixed  the value of the parameter, or $default, or null
	 */
	public static function get_param($key, $default = NULL, $type = NULL) {
		$value = $default;
		$some_unique_val = time() + rand();

		if (isset($_POST[$key])) {
			$value = $_POST[$key];
		} else if ($key == 'controller') {
			$value = Request::current()->controller();
		} else if ($key == 'action') {
			$value = Request::current()->action();
		} else if (Request::current()->param($key, $some_unique_val) != $some_unique_val) {
			$value = Request::current()->param($key);
		} else if (isset($_GET[$key])) {
			$value = $_GET[$key];
		}

		return CL4::clean_param($value, $type);
	} // function get_param

	/**
	 * (set and get) looks for a user parameter and uses the saved setting as default, and sets the saved setting if found
	 * POST overrides route parameter which overrides GET
	 *
	 * @param    string      $parameter_name     the name of the parameter
	 * @param    mixed       $default            (optional) the default value to set/return, uses timeportal conf defaults instead
	 */
	public static function get_smart_parameter($parameter_name, $default = NULL, $type = NULL) {
		$session =& Session::instance()->as_array();
		$source = $session['auth_user'];

		$parameter_value = Base::get_param($parameter_name, NULL, $type);
		if ($parameter_value !== NULL) {
			// save and return the new setting
			$source->setting($parameter_name, $parameter_value);
			//echo "<p>found and set parameter ($parameter_name, $parameter_value)</p>";
			return $parameter_value;
		} else {
			// try to use the saved setting if one exists, otherwise use the default
			$saved_value = $source->setting($parameter_name);
			if ( ! empty($saved_value) || $saved_value == 0) {
				//echo "<p>found saved value ($parameter_name, $saved_value)</p>";
				return $saved_value;
			} else {
				// save and return the default setting (should only ever happen the first time this setting is requested for this user/company)
				// use the default from the conf file if one is not passed
				if (empty($default)) $default = Kohana::$config->load("base.user_setting_default.{$parameter_name}");
				$source->setting($parameter_name, $default);
				//echo "<p>set and return default value ($parameter_name, $default)</p>";
				return $default;
			}
		}
	}

	public static function get_suggest_query($object, $column_name, $search_text, $value = NULL) {
		$query = FALSE;

		$options = $object->get_meta_data($column_name)['field_options']['source'];
		if ($options['source'] == 'sql') {
			if (empty($options['override_query_function'])) {
				$query = DB::select(
					array(DB::expr($options['id_field'][0]), $options['id_field'][1]),
					array(DB::expr($options['name_field'][0]), $options['name_field'][1])
				);

				$query->from($options['from_table']);

				if ( ! empty($options['join_table'])) {
					$query->join($options['join_table']);
					if ( ! empty($options['on']) && is_array($options['on'])) {
						$query->on($options['on'][0], $options['on'][1], $options['on'][2]);
					}
				}

				if ( ! empty($options['and_where']) && is_array($options['and_where'])) {
					foreach($options['and_where'] as $and_where) {
						$query->and_where($and_where[0], $and_where[1], $and_where[2]);
					}
				}

				$query->and_where_open();
				foreach($options['search_columns'] as $search_column) {
					$query->or_where($search_column, 'LIKE', "%{$search_text}%");
				}
				$query->and_where_close();

				if ( ! empty($value)) {
					$query->and_where(DB::expr($options['id_field'][0]), '=', $value);
				}

				$query->order_by($options['order_by'], 'ASC');

				$query->limit($options['limit']);
			} else {
				$query = call_user_func($options['override_query_function'], $search_text, $value);
			}
		}
		//echo (string) $query;exit;

		return $query;
	}

	public static function get_url($route, $params = array()) {
		return URL_ROOT . '/' . Route::get($route)->uri($params);
	}

	/**
	 * return the localized view based on i18n::lang()
	 */
	public static function get_view($path, $template_data = array()) {
		// see if requested view exists, otherwise back out to default language
		$requested_view = 'themes' . '/' . APP_THEME . '/' . i18n::lang() . '/' . $path;
		if (Kohana::find_file('views', $requested_view, NULL, FALSE)) {
			return View::factory($requested_view, $template_data);
		} else {
			return View::factory('themes' . '/' . APP_THEME . '/' . DEFAULT_LANG . '/' . $path, $template_data);
		}
	}

	/**
	 * Tries to identify if the given phone number has been entered or not.
	 *
	 * If it looks like the phone number is empty or invalid we return TRUE, otherwise FALSE
	 *
	 * @param $phone_number
	 */
	public static function is_phone_empty($phone_number) {
		//if (empty($phone_number) || ($phone_number == '----') || ($phone_number == '1----') || (sizeof($phone_number) < 11)) {
		if (strlen($phone_number) < 11) {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Adds a message using Kohana::message(), prepends the path with i18n::lang() . '/' and includes data merge
	 * Saves doing this:
	 *     Message::add(Kohana::message($file, i18n::lang() . '/' . $path), $data), $level);
	 *
	 * @see  Kohana::message()
	 * @see  __()
	 *
	 * @param   string  $file   The message file name
	 * @param   string  $path   The key path to get
	 * @param   array   $data   Values to replace in the message during translation
	 * @param   int     $level  The message level
	 * @return  array   The current array of messages in the session
	 */
	public static function message($file, $path = NULL, $data = NULL, $level = NULL) {
		return Message::add(__(Kohana::message(i18n::lang() . '/' . $file, $path), $data), $level);
	}

	/**
	 * Attempt to remove the accents from the given string.  Also replaces ',', ' ', etc.
	 *
	 * @param $toClean
	 *
	 * @return string
	 */
	public static function remove_accents($toClean) {
		$normalizeChars = array(
			'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj','Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A',
			'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I',
			'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U',
			'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a',
			'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i',
			'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u',
			'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'ƒ'=>'f'
		);
		$normalizeHtmlChars = array(
			'&Aacute;'=>'A', '&Agrave;'=>'A', '&Acirc;'=>'A', '&Atilde;'=>'A', '&Aring;'=>'A', '&Auml;'=>'A', '&AElig;'=>'AE', '&Ccedil;'=>'C',
			'&Eacute;'=>'E', '&Egrave;'=>'E', '&Ecirc;'=>'E', '&Euml;'=>'E', '&Iacute;'=>'I', '&Igrave;'=>'I', '&Icirc;'=>'I', '&Iuml;'=>'I', '&ETH;'=>'Eth',
			'&Ntilde;'=>'N', '&Oacute;'=>'O', '&Ograve;'=>'O', '&Ocirc;'=>'O', '&Otilde;'=>'O', '&Ouml;'=>'O', '&Oslash;'=>'O',
			'&Uacute;'=>'U', '&Ugrave;'=>'U', '&Ucirc;'=>'U', '&Uuml;'=>'U', '&Yacute;'=>'Y',
			'&aacute;'=>'a', '&agrave;'=>'a', '&acirc;'=>'a', '&atilde;'=>'a', '&aring;'=>'a', '&auml;'=>'a', '&aelig;'=>'ae', '&ccedil;'=>'c',
			'&eacute;'=>'e', '&egrave;'=>'e', '&ecirc;'=>'e', '&euml;'=>'e', '&iacute;'=>'i', '&igrave;'=>'i', '&icirc;'=>'i', '&iuml;'=>'i', '&eth;'=>'eth',
			'&ntilde;'=>'n', '&oacute;'=>'o', '&ograve;'=>'o', '&ocirc;'=>'o', '&otilde;'=>'o', '&ouml;'=>'o', '&oslash;'=>'o',
			'&uacute;'=>'u', '&ugrave;'=>'u', '&ucirc;'=>'u', '&uuml;'=>'u', '&yacute;'=>'y',
			'&szlig;'=>'sz', '&thorn;'=>'thorn', '&yuml;'=>'y'
		);
		// 20151203 CSN comenting this out: $toClean = str_replace('&', '-and-', $toClean);
		//$toClean = trim(preg_replace('/[^\w\d_ -]/si', '', $toClean)); // remove all illegal chars
		// 20151203 CSN comenting this out: $toClean = str_replace(', ', '-', $toClean);
		// 20151203 CSN comenting this out: $toClean = str_replace(' ', '_', $toClean);
		// 20151203 CSN comenting this out: $toClean = str_replace('--', '-', $toClean);
		//$toClean = strstr($toClean, $normalizeChars);
		//$toClean = htmlentities($toClean);



		$a = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ';
		$b = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
		$toClean = utf8_decode($toClean);
		$toClean = strtr($toClean, utf8_decode($a), $b);
		// 20151203 CSN comenting this out: $toClean = strtolower($toClean);
		$toClean = utf8_encode($toClean);

		$toClean = trim(preg_replace('/[^\w\d_ -]/si', '', $toClean));

		return $toClean;
	}

	/**
	 * Returns the number of bytes from php.ini value shorthand notation, taken from http://php.net/manual/en/function.ini-get.php
	 * @param $size_str
	 *
	 * @return int
	 */
	public static function return_bytes ($size_str) {
		switch (substr ($size_str, -1))
		{
			case 'M': case 'm': return (int)$size_str * 1048576;
			case 'K': case 'k': return (int)$size_str * 1024;
			case 'G': case 'g': return (int)$size_str * 1073741824;
			default: return $size_str;
		}
	}

	public static function send_ajax($data) {
		echo json_encode($data);
		exit;
	}

	/**
	 * Send an email.
	 *
	 * todo: refactor to handle options better
	 *
	 * @param       $from
	 * @param       $to
	 * @param       $subject
	 * @param       $message
	 * @param null  $attachment
	 * @param array $options
	 * @param       $options['send_from_email']
	 * @param       $options['send_from_name']
	 *
	 * @throws Exception
	 * @throws Mandrill_Error
	 */
	public static function send_email($to_email, $to_name, $subject, $html_content, $options = array()) {
		require_once(ABS_ROOT . '/modules/cl4-jquery-mobile/vendor/mandrill/src/Mandrill.php');

		try {
			$mandrill = new Mandrill(MANDRILL_API_KEY);

			$from_email = ( ! empty($options['send_from_email'])) ? $options['send_from_email'] : DEFAULT_FROM_EMAIL;
			$from_name = ( ! empty($options['send_from_name'])) ? $options['send_from_name'] : DEFAULT_FROM_NAME;
			$text_content = ( ! empty($options['text_content'])) ? $options['text_content'] : '';
			$attachments = ( ! empty($options['attachments'])) ? $options['attachments'] : '';

			$to = array(
				array(
					'email' => $to_email,
					'name' => $to_name,
					'type' => 'to'
				)
			);

			if ( ! empty($options['additional_recipients'])) {
				foreach ($options['additional_recipients'] as $recipient) {
					$to[] = $recipient;
				}
			}

			$headers = ( ! empty($options['headers'])) ? $options['headers'] : array();

			$header_template_options = array();
			if ( ! empty($options['skip_header_logo'])) $header_template_options['skip_header_logo'] = 1;

			$html = (empty($options['skip_header'])) ? Base::get_view('email/header', $header_template_options) : '';
			$html .= $html_content;
			$html .= (empty($options['skip_footer'])) ? Base::get_view('email/footer') : '';

			$message = array(
				'html' => $html,
				'text' => $text_content,
				'subject' => $subject,
				'from_email' => $from_email,
				'from_name' => $from_name,
				'to' => $to,
				'headers' => $headers, //array('Reply-To' => $from_email),
				//'important' => false,
				'track_opens' => null,
				'track_clicks' => null,
				'auto_text' => (empty($text_content)) ? TRUE : FALSE,
				'attachments' => $attachments,
				/*
				'auto_html' => null,
				'inline_css' => null,
				'url_strip_qs' => null,
				'preserve_recipients' => null,
				'view_content_link' => null,
				'bcc_address' => 'message.bcc_address@example.com',
				'tracking_domain' => null,
				'signing_domain' => null,
				'return_path_domain' => null,
				'merge' => true,
				'global_merge_vars' => array(
					array(
						'name' => 'merge1',
						'content' => 'merge1 content'
					)
				),
				'merge_vars' => array(
					array(
						'rcpt' => 'recipient.email@example.com',
						'vars' => array(
							array(
								'name' => 'merge2',
								'content' => 'merge2 content'
							)
						)
					)
				),
				'tags' => array('password-resets'),
				'subaccount' => 'customer-123',
				'google_analytics_domains' => array('example.com'),
				'google_analytics_campaign' => 'message.from_email@example.com',
				'metadata' => array('website' => 'www.example.com'),
				'recipient_metadata' => array(
					array(
						'rcpt' => 'recipient.email@example.com',
						'values' => array('user_id' => 123456)
					)
				),
				'attachments' => array(
					array(
						'type' => 'text/plain',
						'name' => 'myfile.txt',
						'content' => 'ZXhhbXBsZSBmaWxl'
					)
				),
				'images' => array(
					array(
						'type' => 'image/png',
						'name' => 'IMAGECID',
						'content' => 'ZXhhbXBsZSBmaWxl'
					)
				)
				*/
			);

			//echo Debug::vars($message);exit;

			$result = $mandrill->messages->send($message);

			if ( ! empty($result[0]['status']) && $result[0]['status'] == 'sent') {
				Message::add('Email with subject ' . $subject . ' sent from ' . $from_email . ' to ' . $to_email, Message::$debug);
				return TRUE;
			} else if ( ! empty($result[0]['status']) && $result[0]['status'] == 'queued') {
				Message::add('Email with subject ' . $subject . ' from ' . $from_email . ' to ' . $to_email . ' has been queued for delivery', Message::$debug);
				return TRUE;
			} else {
				Base::message('base', 'email_error', array('%subject%' => $subject, '%from%' => $from_email, '%to' => $to_email), Message::$error);
				Message::add(Debug::vars($result), Message::$debug);
			}

			/*
			Array
			(
				[0] => Array
					(
						[email] => recipient.email@example.com
						[status] => sent
						[reject_reason] => hard-bounce
						[_id] => abc123abc123abc123abc123abc123
					)

			)
			*/
		} catch(Mandrill_Error $e) {
			// Mandrill errors are thrown as exceptions
			Message::add('A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage(), Message::$error);
		}
		return FALSE;
	}

	/**
	 * this function performs all of the setup that takes place when a user logs in (or when they click on reload session)
	 */
	public static function set_environment($user) {
		$session = & Session::instance()->as_array();

		// add the remembered tab settings
		$session['tabs'] = array();
		//foreach (array('tabs', 'client_view_tabs', 'global_tabs') as $tab_name) {
		//	$session['tabs'][$tab_name] = $user->setting($tab_name);
		//}

		// add some other frequently used stuff to the session
		$remember_username = Cookie::get('username', NULL);
		$session['remember_device_username_flag'] = ($user->username == $remember_username) ? TRUE : FALSE;
		$authautologin = Cookie::get('authautologin', FALSE);
		$session['remember_device_login_flag'] = ($authautologin) ? TRUE : FALSE;
	}

	/**
	 * set the saved parameter with the given value
	 *
	 * @param        $parameter_name
	 * @param        $value
	 * @param string $type
	 */
	public static function set_smart_parameter($parameter_name, $value, $type = 'user') {
		$session =& Session::instance()->as_array();
		if ($type == 'user') {
			$source = $session['auth_user'];
		} else if ($type == 'company') {
			$source = Wine::company(); // todo: implement this
		}
		$source->setting($parameter_name, $value);
	}
}