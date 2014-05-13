<p>A password reset has been request for your account on <?php echo HTML::chars(LONG_NAME); ?>.</p>
<p>Please <?php echo HTML::anchor($url, 'click here', array('target' => '_blank'), TRUE); ?> or copy and paste the link below into your browser to reset your password:</p>
<p><?php echo HTML::chars(URL::site($url, TRUE)); ?></p>
<p>Upon clicking the link, a new password will be sent to you.</p>
<p>If this request was not made by you, you can choose to ignore this email or
	contact the administrator by replying to this email.</p>