<?php defined('SYSPATH') or die('No direct script access.');

// cl4-specific model meta data options and defaults
return array(
	'default_options' => array(
		'replace_spaces' => TRUE,
		'get_form_view_file' => 'cl4/orm_form_mobile', // the default view to use when displaying the edit or search form
		'get_view_view_file' => 'cl4/orm_view_mobile', // the default view to use when displaying a view of a record
	),
);
