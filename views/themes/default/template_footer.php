<?php
$thisTime = microtime();
$thisTime = explode(" ", $thisTime);
$thisTime = $thisTime[1] + $thisTime[0];
$finishLoadTime = $thisTime;
$totalTime = ($finishLoadTime - PAGE_START);
?>

<div data-role="footer" data-theme="a" class="stats">
	Copyright 2003-<?php echo date('Y'); ?> Tucker Energy Services - All Rights Reserved
	<div data-role="navbar" data-theme="c">
		<ul class="no_bullet no_indent">
			<li>Generate Time: <?php echo number_format($totalTime, 2); ?>s</li>
			<li>Render Time: <span id="render_time"></span>s</li>
			<li>Locale: <?php echo I18n::$lang; ?></li>
			<li>Your IP: <?php echo (isset($_SERVER["REMOTE_ADDR"])) ? $_SERVER["REMOTE_ADDR"] : 'unknown'; ?></li>
			<?php /* <li>Our IP: <?php echo  (isset($_SERVER["SERVER_ADDR"])) ? $_SERVER["SERVER_ADDR"] : 'unknown'; ?></li>
			<?php if ($logged_in) { ?>
				<li><span class="timer_message" title="When this counter reaches zero your session will time out.  It is reset every time you access the web site.">Login Timeout: <span id="timer"></span></span></li>
			<?php } ?>
 <?php */ ?>
		</ul>
	</div>
</div>

<div data-role="footer" data-position="fixed">
	<div data-role="navbar" data-theme="c">
		<?php switch($_SERVER['SERVER_NAME']) {
			case 'secure.tuckerenergy.com':
				break;
			case 'securestage.tuckerenergy.com':
				echo '<div class="development_msg">Staging Site - for testing purposes only</div>';
				break;
			case 'securedev.tuckerenergy.com':
				echo '<div class="development_msg">This Site is Currently Under Development</div>';
				break;
			case 'securelocal.tuckerenergy.com':
				echo '<div class="development_msg">This Site is Currently Under Development</div>';
				break;
		} ?>
	</div>
</div>