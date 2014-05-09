<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php
		if (DEVELOPMENT_FLAG) {
			echo 'Dev-';
		}
		echo HTML::chars($page_title); ?></title>

	<?php
	if ( ! empty($meta_tags)) {
		foreach ($meta_tags as $name => $content) {
			if ( ! empty($content)) {
				echo TAB . HTML::meta($name, $content) . EOL;
			} // if
		} // foreach
	} // if
	?>

	<!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
	<?php if (1 || DEVELOPMENT_FLAG) { ?>
		<link rel="stylesheet" href="/cl4/css/vendor/jquery.mobile-1.4.2.css">
		<link rel="stylesheet" href="/cl4/font-awesome/css/font-awesome.min.css">
		<link rel="stylesheet" href="/cl4/css/cl4.css">
		<link rel="stylesheet" href="/cl4/css/base.css">
	<?php } else { ?>
		<?php // todo: add cdn, compressed, minified, scsss'ed, etc. ?>
	<?php } ?>

	<?php //<link href='//fonts.googleapis.com/css?family=Droid+Sans+Mono' rel='stylesheet' type='text/css'> ?>

	<?php if (1 || DEVELOPMENT_FLAG) { ?>
		<!--<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>-->
		<!--<script src="//code.jquery.com/mobile/1.4.0/jquery.mobile-1.4.0.min.js"></script>-->
		<script src="/cl4/js/vendor/jquery-2.1.0.min.js"></script>
		<script src="/cl4/js/vendor/jquery.mobile-1.4.2.js"></script>
		<script src="/cl4/js/base.js"></script>
	<?php } else { ?>
		<?php // todo: add cdn, compressed, minified, scsss'ed, etc. ?>
	<?php } ?>

	<?php if (0) echo '<script src="/cl4/js/model_create.js"></script>'; ?>

	<script>var cl4_in_debug = <?php echo (int) DEBUG_FLAG; ?>;</script>

</head>
<body class="<?php echo HTML::chars(trim($body_class)); ?>">
<a name="wc_top"></a>
<!--[if lt IE 7]>
<p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->

<!- PAGE START ***************************************************************************************->
<div data-role="page" id="page_<?php echo $page_name; ?>">
	<?php if ($logged_in) echo CL4::get_view('admin'); // include admin panel ?>

	<?php if ( ! empty($panel_html)) echo $panel_html; ?>

	<?php echo (empty($header_html)) ? View::factory('themes/' . APP_THEME . '/template_header')->set($kohana_view_data) : $header_html; ?>

	<!- CONTENT START ***************************************************************************************->
	<div data-role="content">

		<?php //echo Debug::vars($_POST, $_FILES); ?>

		<?php $message_html = (string) $message;
		if ( ! empty($pre_message) || ! empty($message_html)) echo $pre_message, $message_html; ?>

		<?php echo $body_html; ?>

		<br><br><br><br>
		<?php
		if (DEBUG_FLAG) {
			$error_id = uniqid('error');
			echo EOL . EOL . '<!-- DEBUG START -->' . EOL;
			?>
			<?php echo View::factory('base/debug'); ?>
		<?php } // if (DEBUG_FLAG) ?>
	</div>
	<!- CONTENT END ***************************************************************************************->

	<?php echo (empty($footer_html)) ? View::factory('themes/' . APP_THEME . '/template_footer')->set($kohana_view_data) : $footer_html; ?>

</div>
<!- PAGE END ***************************************************************************************->

<?php if ( ! empty($extra_html)) echo $extra_html; ?>

<?php if (1 || DEVELOPMENT_FLAG) { ?>
	<script src="/cl4/js/cl4.js"></script>
	<script src="/cl4/js/ajax.js"></script>
	<!--<script src="/cl4/js/model_create.js"></script>-->
<?php } else { ?>

<?php } ?>

<?php // Javascript to run once the page is loaded
if ( ! empty($on_load_js)) { ?>
	<script>
		$(function() {
			<?php echo $on_load_js . EOL; ?>
		});
	</script>
<?php } ?>

<!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
<?php if (ANALYTICS_ID != NULL) { ?>
	<script>
		(function(b,o,i,l,e,r){b.GoogleAnalyticsObject=l;b[l]||(b[l]=
			function(){(b[l].q=b[l].q||[]).push(arguments)});b[l].l=+new Date;
			e=o.createElement(i);r=o.getElementsByTagName(i)[0];
			e.src='//www.google-analytics.com/analytics.js';
			r.parentNode.insertBefore(e,r)}(window,document,'script','ga'));
		ga('create','<?php echo ANALYTICS_ID; ?>');ga('send','pageview');
	</script>
<?php } ?>

<a name="template_bottom"></a>
</body>
</html>
