<h1>Welcome to the <?php echo LONG_NAME; ?></h1>
<div class="login">
	<?php $default_username = Cookie::get('username', ''); ?>
	<?php echo Form::open(Base::get_url('login', array('action' => 'login')), array('id' => 'login_form')); ?>
	<?php echo Form::hidden('redirect', $redirect); ?>

	<div data-role="fieldcontain" class="ui-hide-label">
		<label for="username">Username</label>
		<input type="email" data-clear-btn="false" name="username" id="username" value="<?php echo $default_username; ?>" placeholder="Username" autocapitalize="off"<?php if (empty($default_username)) echo ' autofocus'; ?>>
	</div>

	<div data-role="fieldcontain" class="ui-hide-label">
		<label for="password">Password</label>
		<input type="password" name="password" id="password" value="" placeholder="Password" autocomplete="off" autocapitalize="off"<?php if ( ! empty($default_username)) echo ' autofocus'; ?>>
	</div>
	<?php //echo HTML::anchor(Route::get('login')->uri(array('action' => 'forgot')), 'Forgot Password?', array('data-inline' => 'true', 'data-role' => 'button')) ?>

	<?php echo HTML::anchor('#forgot_form', 'Forgot Password', array(
		'data-inline' => 'true',
		'data-role' => 'button',
		//'data-theme' => 'c',
		'data-rel' => 'popup',
		'data-position-to' => 'window',
		'data-transition' => 'fade'
	)) ?>
	<?php echo Form::submit('login', 'Login', array(
		'data-ajax' => 'false',
		'data-inline' => 'true',
		'class' => 'login_button',
		'data-icon' => 'arrow-r',
		'data-iconpos' => 'right',
		'data-theme' => 'c')); ?>

	<div data-role="popup" id="forgot_form" data-theme="a" class="ui-corner-all">
		<a href="#" data-rel="back" class="ui-btn ui-corner-all ui-shadow ui-btn ui-icon-delete ui-btn-icon-notext ui-btn-right">Forgot Password</a>
		<div style="padding:10px 20px;" class="responsive_form">
			<?php echo Form::open(Base::get_url('login', array('action' => 'forgot')), array('data-ajax' => 'false')); ?>
			<p>Please enter your email address and click on 'Reset Password' to regain access to your account.</p>
			<p>If your email address matches an active user account on this system, you will receive an email with a link that will create a new password for you.
			The new password will then be emailed to you.</p>
			<div class="ui-field-contain">
				<label for="email">Your Email (username):</label>
				<?php echo Form::email('reset_username', NULL, array('id' => 'email')); ?>
			</div>
			<?php echo Form::button('reset_password', 'Reset Password', array(
				'id' => 'reset_password',
				'class' => 'ui-btn ui-corner-all ui-shadow ui-btn-b ui-btn-inline',
			)); ?>
		</div>
	</div>
	<?php echo Form::close(); ?>

	<?php //echo HTML::anchor('#', 'Demo', array('id' => 'demo_button', 'data-inline' => 'true', 'data-role' => 'button')) ?>
	<?php //echo Form::submit(NULL, 'Login', array('class' => 'login_button', 'data-icon' => 'arrow-r', 'data-iconpos' => 'right', 'data-inline' => 'true', 'data-theme' => 'c')); ?>
	<?php echo Form::close(); ?>

	<p>If you need assistance to log in, please call 1-403-242-2667 or 1-888-555-xxxx.</p>
	<p>You may request an account using the link below.</p>
	<?php echo HTML::anchor(Base::get_url('login', array('action' => 'register')), 'Register for an Account', array(
		'data-inline' => 'true',
		'data-role' => 'button',
		'data-theme' => 'c',
		'data-ajax' => 'false',
	)); ?>
</div>