<p>Your password has been reset. Your new login information is as follows:</p>
<p>Username: <?php echo $username; ?><br>
	Password: <?php echo $password; ?></p>
<p>To login, <?php echo HTML::anchor($url, 'click here', array('target' => '_blank'), TRUE); ?> or copy and paste the following link into your browser:</p>
<p><?php echo HTML::chars(URL::site($url, TRUE)); ?></p>
<p>If you continue to have problems, or this request was not made by you, you can choose to ignore this email or
	contact the administrator by replying to this email.</p>