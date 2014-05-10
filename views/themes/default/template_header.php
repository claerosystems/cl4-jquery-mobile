<div id="template_header" data-role="header" data-position="fixed">
	<div class="ui-grid-b ui-responsive">
		<div class="ui-block-a">
			<div class="ui-body">
				<a title="<?php echo URL_ROOT; ?>" data-ajax="false" href="/"><img src="http://claero.com/wp-content/uploads/2012/10/claero_logo_and_text-300x511.png"></a>
			</div>
		</div>
		<div class="ui-block-b">
			<div class="ui-body">
			</div>
		</div>
		<div class="ui-block-c">
			<div class="ui-body">
			</div>
		</div>
	</div>
	<div data-role="navbar" data-iconpos="left">
		<ul>
			<li><a href="#mainmenu"><i class="fa fa-th"></i> Menu</a></li>
			<li><?php echo HTML::anchor(Base::get_url('public', array('page' => 'home')), '<i class="fa fa-home"></i> ' . __('Home'), array('class' => ($body_class == 'Data') ? 'ui-btn-active ui-state-persist' : '')) ?></li>
			<li><?php echo HTML::anchor(Base::get_url('public', array('page' => 'contact')), '<i class="fa fa-phone"></i> ' . __('Contact'), array('class' => ($body_class == 'Data') ? 'ui-btn-active ui-state-persist' : '')) ?></li>
			<?php if ($logged_in) { ?>
				<li><?php echo HTML::anchor(Base::get_url('login', array('action' => 'logout')), '<i class="fa fa-unlock"></i> ' . __('Logout'), array('class' => ($body_class == 'Login') ? 'ui-btn-active ui-state-persist' : '')) ?></li>
			<?php } else { ?>
				<li><?php echo HTML::anchor(Base::get_url('login', array('action' => 'login')), '<i class="fa fa-lock"></i> ' . __('Login'), array('class' => ($body_class == 'Login') ? 'ui-btn-active ui-state-persist' : '')) ?></li>
			<?php } ?>
		</ul>
	</div>
</div>