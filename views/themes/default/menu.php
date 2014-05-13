	<!--<li><a href="#mainmenu"><i class="fa fa-th"></i> Menu</a></li>-->
	<li><?php echo HTML::anchor(Base::get_url('public', array('page' => 'home')), '<i class="fa fa-home"></i> ' . __('Home'), array('class' => (trim($body_class) == 'p_home') ? 'ui-btn-active ui-state-persist' : '')) ?></li>
	<li><?php echo HTML::anchor(Base::get_url('public', array('page' => 'contact')), '<i class="fa fa-phone"></i> ' . __('Contact'), array('class' => (trim($body_class) == 'p_contact') ? 'ui-btn-active ui-state-persist' : '')) ?></li>
<?php if ($logged_in) { ?>
	<li><?php echo HTML::anchor(Base::get_url('login', array('action' => 'logout')), '<i class="fa fa-unlock"></i> ' . __('Logout'), array('class' => (trim($body_class) == 'p_login') ? 'ui-btn-active ui-state-persist' : '')) ?></li>
<?php } else { ?>
	<li><?php echo HTML::anchor(Base::get_url('login', array('action' => 'login')), '<i class="fa fa-lock"></i> ' . __('Login'), array('class' => (trim($body_class) == 'p_login') ? 'ui-btn-active ui-state-persist' : '')) ?></li>
<?php } ?>