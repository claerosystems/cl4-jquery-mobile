<?php defined('SYSPATH') or die('No direct script access.');

// cl4-specific model meta data options and defaults
return array(
    // this should contain arrays of field type specific meta data; if the field type or key is not set in here, the default_meta_data will be used
    'default_meta_data_field_type' => array(
        'Text' => array(
            'field_attributes' => array(
                'maxlength' => 255,
                'size' => 30,
                'data-mini' => 'true',
            ),
        ),
		'Phone' => array(
			'field_attributes' => array(
				'data-mini' => 'true',
			),
		),
		'Money' => array(
			'field_attributes' => array(
				'data-mini' => 'true',
			),
		),
        'Select' => array(
			'field_attributes' => array(
				'data-mini' => 'true',
			),
            'field_options' => array(
                // data to get the data to display in this field
                'source' => array(
                    // possibilities: model, sql, table_name, array
                    'source' => 'model',
                    /**
                     * This should be a:
                     *  - model: name of model or it will attempt to retrieve it based on the column name from the model
                     *  - sql: a SELECT statement retrieving the label and value
                     *  - table_name: the table name for the db table
                     *  - array: an array of data where the key is the value and the value is the label
                     *  - method: allows the use of a method or function to retrieve the source values; works very similar to the validation rules when using a method
                     */
                    'data' => NULL,
                    'value' => 'id',
                    'label' => 'name',
                    'parent_label' => 'parent',
                    'order_by' => 'name',
                ),
            ),
        ),
        'Radios' => array(
			'field_attributes' => array(
				'data-mini' => 'true',
			),
            'field_options' => array(
                'default_value' => 0,
                // see how to use these in the select defaults
                'source' => array(
                    'source' => 'model',
                    'data' => NULL,
                    'value' => 'id',
                    'label' => 'name',
                    'order_by' => NULL,
                ),
            ),
        ),
        'Yes_No' => array(
			'field_attributes' => array(
				'data-mini' => 'true',
			),
            'field_options' => array(
                'default_value' => 0,
            ),
        ),
        'Gender' => array(
			'field_attributes' => array(
				'data-mini' => 'true',
			),
            'field_options' => array(
                'default_value' => 0,
            ),
        ),
        'TextArea' => array(
			'field_attributes' => array(
				'data-mini' => 'true',
			),
            'field_attributes' => array(
                'cols' => 100,   // the number of columns in a text area
                'rows' => 5,     // the number of rows in a text area
            ),
        ),
        'Date' => array(
			'field_attributes' => array(
				'data-mini' => 'true',
			),
            'field_attributes' => array( //
                'maxlength' => 10,       // a date in the format YYYY-MM-DD
                'size' => 10,
                'data-mini' => 'true',
            ),
        ),
        'DateTime' => array(
			'field_attributes' => array(
				'data-mini' => 'true',
			),
            'field_attributes' => array( // only applies to the date field
                'maxlength' => 10,       // a date in the format YYYY-MM-DD
                'size' => 10,
                'data-mini' => 'true',
            ),
        ),
        'File' => array(
			'field_attributes' => array(
				'data-mini' => 'true',
			),
            'field_attributes' => array(
                'size' => 30,
                'data-mini' => 'true',
            ),
            'field_options' => array(
                'file_options' => array(
                    // see config/cl4file.php for a full list of options
                ),
            ),
        ),
        'URL' => array(
			'field_attributes' => array(
				'data-mini' => 'true',
			),
            'field_attributes' => array(
                'maxlength' => 255,
                'size' => 30,
                'data-mini' => 'true',
            ),
        ),
        'Email' => array(
			'field_attributes' => array(
				'data-mini' => 'true',
			),
            'field_attributes' => array(
                'maxlength' => 255,
                'size' => 30,
                'data-mini' => 'true',
            ),
        ),
        'Password' => array(
			'field_attributes' => array(
				'data-mini' => 'true',
			),
            'field_attributes' => array(
                'maxlength' => 255,
                'size' => 30,
                'data-mini' => 'true',
            ),
        ),
    ),
	'default_options' => array(
		'replace_spaces' => TRUE,
		'get_form_view_file' => 'cl4/orm_form_mobile', // the default view to use when displaying the edit or search form
		'get_view_view_file' => 'cl4/orm_view_mobile', // the default view to use when displaying a view of a record


        // the default options for the attributes of the form tag
        'form_attributes' => array(
            'enctype' => 'multipart/form-data', // todo: maybe only include this if a file type column is present?
            'method' => 'post',
            'name' => '', // empty string will default to table name
            'id' => '', // empty string will default to table name
            'class' => 'cl4_form',
            'data-ajax' => 'false',
            'data-mini' => 'true',
        ),
	),
);
