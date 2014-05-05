<h1>Timed Out</h1>
<div class="login">
	<p>Your login has timed out. To continue using your current login, enter your password and hit enter.</p>

	<?php echo Form::open(Base::get_url('login'), array('id' => 'login_form')); ?>
	<?php echo Form::hidden('redirect', $redirect); ?>
	<?php echo Form::hidden('timed_out', 1); ?>

	<div data-role="fieldcontain" class="ui-hide-label">
		<label for="username">Username</label>
		<input type="email" data-clear-btn="false" name="username" id="username" value="<?php echo HTML::chars($username); ?>" placeholder="Username"  autocapitalize="off" readonly>
	</div>

	<div data-role="fieldcontain" class="ui-hide-label">
		<label for="password">Password</label>
		<input type="password" name="password" id="password" value="" placeholder="Password" autocomplete="off" autocapitalize="off" autofocus>
	</div>

	<?php echo HTML::anchor(Base::get_url('login', array('action' => 'logout')), 'Cancel & Logout', array('data-inline' => 'true', 'data-role' => 'button')) ?>
	<?php echo Form::submit(NULL, 'Re-Login', array('class' => 'login_button', 'data-icon' => 'arrow-r', 'data-iconpos' => 'right', 'data-inline' => 'true', 'data-theme' => 'b')); ?>
	<?php echo Form::close(); ?>
</div>