<div class="login_box forgot_password_wrapper">
	<h1>Forgot Password</h1>

	<p>Please send me a link to reset my password.</p>

<?php echo Form::open(Request::current()); ?>
	<p>To start, enter your email address: <?php echo Form::email('reset_username', '', array('autocapitalize' => 'off', 'autocorrect' => 'off', 'autofocus')); ?></p>
	<p>Also enter the characters you see in the CAPTCHA below:</p>
<?php
echo Form::submit(NULL, 'Reset Password');
echo Form::close();
?>
</div>