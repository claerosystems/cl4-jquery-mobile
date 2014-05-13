<div id="template_header" data-role="header" data-position="fixed">
	<div class="ui-grid-a ui-responsive">
		<div class="ui-block-a">
			<div class="ui-body">
				<a title="<?php echo URL_ROOT; ?>" data-ajax="false" href="/"><img src="http://claero.com/wp-content/uploads/2012/10/claero_logo_and_text-300x511.png"></a>
			</div>
		</div>
		<div class="ui-block-b">
			<div class="ui-body">
			</div>
		</div>
	</div>
	<div data-role="navbar" data-iconpos="left" class="mobile_on">
		<ul>
			<li><a href="#mainmenu"><i class="fa fa-th"></i> Menu</a></li>
			<?php if ($logged_in) { ?>
				<li><?php echo HTML::anchor(Base::get_url('login', array('action' => 'logout')), '<i class="fa fa-unlock"></i> ' . __('Logout'), array('class' => (trim($body_class) == 'p_login') ? 'ui-btn-active ui-state-persist' : '')) ?></li>
			<?php } else { ?>
				<li><?php echo HTML::anchor(Base::get_url('login', array('action' => 'login')), '<i class="fa fa-lock"></i> ' . __('Login'), array('class' => (trim($body_class) == 'p_login') ? 'ui-btn-active ui-state-persist' : '')) ?></li>
			<?php } ?>
		</ul>
	</div>
	<div data-role="navbar" data-iconpos="left" class="mobile_off">
		<ul>
<?php echo View::factory('themes' . '/' . APP_THEME . '/menu')->set($kohana_view_data)->render(); ?>
		</ul>
	</div>
</div>