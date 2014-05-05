<?php defined('SYSPATH') or die('No direct script access.');

class Controller_CL4Admin extends Controller_CL4_CL4Admin {
	public function before() {
		parent::before();
		if ($this->auto_render) $this->template->page_name = 'cl4admin';
	}

	/**
	 * Adds the CSS for cl4admin
	 */
	protected function add_css() {
		//if ($this->auto_render) {
		//	$this->add_style('dbadmin', 'css/dbadmin.css');
		//}
	} // function add_css

	/**
	 * The default action
	 * Just displays the editable list using display_editable_list()
	 */
	public function action_index() {
		$override_options = array(
			//'get_form_view_file' => 'cl4/orm_form_ul',
			'get_form_view_file' => i18n::lang() . '/form/orm_form_mobile',
			'mode' => 'view',
			'sort_by_column' => $this->sort_column,
			'sort_by_order' => $this->sort_order,
			'page_offset' => $this->page_offset,
			'in_search' => ( ! empty($this->search) || ! empty($this->sort_column)),
			'page_max_rows' => 200,
			'editable_list_options' => array(
				'table_options_multiorm' => array(
					'table_attributes' => array(
						'class' => 'cl4_content ui-responsive table-stroke',
						'data-role' => "table",
						'data-mode' =>"reflow",
					),
				),
				'per_row_links' => array(
					'view' => FALSE,     // view button
					'edit' => $this->check_perm('edit'),     // edit button
					'delete' => $this->check_perm('delete'),   // delete button
					'add' => $this->check_perm('add'),      // add (duplicate) button
					'checkbox' => FALSE, //($this->check_perm('edit') || $this->check_perm('export')), // checkbox
				),
				'top_bar_buttons' => array(
					'add' => $this->check_perm('add'),             // add (add new) button
					'add_multiple' => FALSE, //$this->check_perm('add'),    // add multiple button
					'edit' => FALSE, //$this->check_perm('edit'),            // edit (edit selected) button
					'export_selected' => FALSE, //$this->check_perm('export'), // export selected button
					'export_all' => $this->check_perm('export'),      // export all button
					'search' => $this->check_perm('search'),          // search button
				),
				'top_bar_buttons_custom' => array(

				)
			),
		);
		$this->display_editable_list($override_options);
	}

	/**
	 * Creates a drop down of all model the available models as returned by get_model_list()
	 * Returns the view cl4/cl4admin/header
	 *
	 * @return  string
	 */
	public function display_model_select() {
		$model_list = $this->get_model_list();
		asort($model_list);
		$model_select = Form::select('model', $model_list, $this->model_name, array('id' => 'cl4_model_select'));

		return View::factory('cl4/cl4admin/header', array(
			'model_select' => $model_select,
			'form_action' => URL::site($this->request->uri()) . URL::query(),
		));
	}

	/**
	 * Sets the page title on the template.
	 * Standard format is: [action] - [model display name] - Administration - [site long name]
	 *
	 * @param  string  $action  The page title.
	 * @return void
	 */
	protected function set_page_title($action = NULL) {
		$this->template->page_name = strtolower($action) . '-' . strtolower($this->model_name);
		$this->template->page_title = ( ! empty($action) ? $action . ' - ' : '') . $this->model_display_name . ' - Administration';
	}
}