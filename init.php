<?php defined('SYSPATH') or die('No direct script access.');

$routes = Kohana::$config->load('cl4.routes');
$lang_options = '(' . implode(',', Kohana::$config->load('cl4.languages')) . ')';

if ($routes['login']) {
	// login page
	Route::set('login', 'login(/<action>)', array('action' => '[a-z_]{0,}',))
		->defaults(array(
			'controller' => 'Login',
			'action' => NULL,
	));
}

if ($routes['account']) {
	// account: profile, change password, forgot, register
	Route::set('account', 'account(/<action>)', array('action' => '[a-z_]{0,}',))
	->defaults(array(
		'controller' => 'Account',
		'action' => 'index',
	));
}

if ($routes['cl4admin']) {
	// claero admin
	// Most cases: /dbadmin/user/edit/2
	// Special case for download: /dbadmin/demo/download/2/public_filename
	// Special case for add_multiple: /dbadmin/demo/add_mulitple/5 (where 5 is the number of records to add)
	Route::set('cl4admin', 'dbadmin(/<model>(/<action>(/<id>(/<column_name>))))', array(
		'model' => '[a-zA-Z0-9_]{0,}',
		'action' => '[a-z_]+',
		'id' => '\d+',
		'column_name' => '[a-z_]+')
	)->defaults(array(
		'controller' => 'CL4Admin',
		'model' => NULL, // this is the default object that will be displayed when accessing cl4admin (dbadmin) without a model
		'action' => 'index',
		'id' => NULL,
		'column_name' => NULL,
	));
}

if ($routes['model_create']) {
	// model create
	Route::set('model_create', 'model_create(/<model>/<action>)', array(
		'model' => '[a-zA-Z0-9_]{0,}',
	))->defaults(array(
		'controller' => 'Model_Create',
		'action' => 'index',
		'model' => NULL,
	));
}

if ($routes['ajax']) {
	Route::set('ajax', '(<lang>/)ajax/<action>(/<id>)', array(
		'lang' => $lang_options,
		'action' => '(check_login|set_smart_parameter|get_smart_parameter)'
	))->defaults(array(
			'controller' => 'ajax',
			'lang' => DEFAULT_LANG,
			'action' => 'index',
			'id' => NULL,
		));
}

if ($routes['private']) {
	// routes for private pages
	Route::set('private', '(<lang>/)<controller>(/<action>(/<id>))', array(
		'lang' => $lang_options,
		'controller' => '(dashboard)',
	))->defaults(array(
			'controller' => 'dashboard',
			'lang' => DEFAULT_LANG,
			'action' => 'index',
		));
}

if ($routes['public']) {
	// routes for public pages
	Route::set('public', '(<lang>/)(<page>(/<action>(/<id>)))', array(
		'lang' => $lang_options,
		'page' => '(home|contact|privacy|404)',
		'action' => '(view)'
	))->defaults(array(
			'controller' => 'Public',
			'lang' => DEFAULT_LANG,
			'action' => 'view',
			'page' => 'home',
		));
}