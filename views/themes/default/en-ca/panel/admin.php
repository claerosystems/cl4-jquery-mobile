<div data-role="panel" id="admin" data-position="right" data-display="overlay">
	<ul data-role="listview" data-inset="true" data-filter="false">
		<li><?php echo HTML::anchor(Base::get_url('private', array('controller' => 'account')), __('My Account')) ?></li>
		<li><?php echo HTML::anchor(Base::get_url('login', array('action' => 'logout')), __('Logout'), array('data-ajax' => 'false')) ?></li>
	</ul>
	<ul data-role="listview" data-inset="true" data-filter="false" data-divider-theme="a">
		<?php if (1) { ?>
			<li data-role="list-divider">Database Admin</li>
			<li data-theme="c"><?php echo HTML::anchor(Base::get_url('cl4admin', array('model' => 'Country')), __('Country'), array()); ?></li>
			<li data-theme="c"><?php echo HTML::anchor(Base::get_url('cl4admin', array('model' => 'Province')), __('Province'), array()); ?></li>
		<?php } ?>
		<?php if (Auth::instance()->get_user()->has('group', 1)) { ?>
			<li data-role="list-divider">System Administrator</li>
			<li data-theme="c"><?php echo HTML::anchor(Base::get_url('cl4admin', array('controller' => 'CL4Admin', 'model' => 'User_Group')), __('User - Group'), array()); ?></li>
			<li data-theme="c"><?php echo HTML::anchor(Base::get_url('cl4admin', array('controller' => 'CL4Admin')), __('DB Admin'), array()); ?></li>
			<li data-theme="c"><?php echo HTML::anchor(Base::get_url('cl4admin', array('controller' => 'Model_Create')), __('Model Create'), array()); ?></li>
		<?php } ?>

	</ul>
</div>