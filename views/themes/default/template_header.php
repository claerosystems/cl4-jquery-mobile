<div data-role="header" data-position="fixed">
	<div class="ui-grid-b ui-responsive">
		<div class="ui-block-a">
			<div class="ui-body">
				<a title="<?php echo URL_ROOT; ?>" data-ajax="false" href="/"><img src="/images/claero_logo_and_text-200x34.png"></a>
			</div>
		</div>
		<div class="ui-block-b">
			<div class="ui-body">
				<h1><?php echo $page_title; ?></h1>
			</div>
		</div>
		<div class="ui-block-c">
			<div class="ui-body">
				<?php echo date('F d, Y'); ?>
				<?php if ( ! empty($user) && $user->loaded()) { ?>
					<br>Welcome <a data-ajax="false" href="<?php echo Base::get_url('private', array('controller' => 'account')); ?>"><?php echo $user->name(); ?></a>
				<?php } ?>
			</div>
		</div>
	</div>
	<div data-role="navbar" data-iconpos="left">
		<ul>
			<li><i class="fa fa-home"></i><?php echo HTML::anchor(Base::get_url('public', array('page' => 'home')), __('Home'), array('data-icon' => 'home', 'class' => ($body_class == 'Data') ? 'ui-btn-active ui-state-persist' : '')) ?></li>
			<li><?php echo HTML::anchor(Base::get_url('public', array('page' => 'contact')), __('Contact'), array('class' => ($body_class == 'Data') ? 'ui-btn-active ui-state-persist' : '')) ?></li>
			<li><?php echo HTML::anchor(Base::get_url('public', array('page' => 'contact')), '&nbsp;') ?></li>
			<?php if ($logged_in) { ?>
				<li><?php echo HTML::anchor(Base::get_url('login', array('action' => 'logout')), __('Logout'), array('class' => ($body_class == 'Login') ? 'ui-btn-active ui-state-persist' : '')) ?></li>
			<?php } else { ?>
				<li><?php echo HTML::anchor(Base::get_url('login', array('action' => 'login')), __('Login'), array('class' => ($body_class == 'Login') ? 'ui-btn-active ui-state-persist' : '')) ?></li>
			<?php } ?>
		</ul>
	</div>
</div>