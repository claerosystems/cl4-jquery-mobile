<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
<?php if (ANALYTICS_ID != NULL) { ?>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo ANALYTICS_ID; ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', '<?php echo ANALYTICS_ID; ?>');
    </script>
<?php } ?>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
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
            }
        }
    }
?>
<?php foreach ($styles as $style_url) echo EOL . TAB . '<link rel="stylesheet" href="' . $style_url . '">'; ?>
<?php if ( ! empty($custom_css)) { ?>
<?php foreach ($custom_css as $css)  echo EOL . TAB . '<link rel="stylesheet" href="' . $css . '">'; ?>
<?php  } ?>

<?php if ( ! empty($extra_head_html)) echo $extra_head_html; ?>

<?php if (DEVELOPMENT_FLAG) { ?>
    <script src="//s3.amazonaws.com/private.trialto.com/media/cl4/js/vendor/jquery-2.1.1.min.js"></script>
    <script src="//s3.amazonaws.com/private.trialto.com/media/cl4/js/vendor/jquery.mobile-1.4.5.js"></script>
    <script src="//s3.amazonaws.com/private.trialto.com/media/cl4/js/vendor/datepicker.js"></script>
    <script src="//s3.amazonaws.com/private.trialto.com/media/cl4/js/vendor/jquery.mobile.datepicker.js"></script>
    <script src="//s3.amazonaws.com/private.trialto.com/media/cl4/js/base.js"></script>
<?php } else { ?>
    <script src="//s3.amazonaws.com/private.trialto.com/media/cl4/js/combined.min.js"></script>
<?php /* combined.js is:
// @codekit-append "/vendor/jquery-2.1.1.min.js";
// @codekit-append "/vendor/jquery.mobile-1.4.5.min.js";
// @codekit-append "/vendor/datepicker.js";
// @codekit-append "/vendor/jquery.mobile.datepicker.js";
// @codekit-append "/base.js";
    */ ?>
<?php } ?>

    <script>var cl4_in_debug = <?php echo (int) DEBUG_FLAG; ?>;</script>
</head>
<body class="<?php echo HTML::chars(trim($body_class)); ?> <?php if (defined('PROVINCE_CODE')) echo PROVINCE_CODE; ?>" data-provincecode="<?php if (defined('PROVINCE_CODE')) echo PROVINCE_CODE; ?>" data-route="<?php if ( ! empty($route_name)) echo $route_name; ?>" data-baseurl="<?php if ( ! empty($route_name)) echo Base::get_url($route_name); ?>">
<a name="wc_top"></a>
<!--[if lt IE 7]>
<p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->

<!- PAGE START ***************************************************************************************->
<div data-role="page" id="page_<?php echo $page_name; ?>" class="ui-responsive-panel">
    <?php //if ($logged_in) echo Base::get_view('admin'); // include admin panel ?>

    <?php if ( ! empty($panel_html)) echo $panel_html; ?>

    <div data-role="panel" id="mainmenu" data-position="left" data-position-fixed="true" data-dismissible="false">
        <?php echo View::factory('themes' . '/' . APP_THEME . '/menu_panel')->set($kohana_view_data)->render(); ?>
    </div><!-- /panel -->

    <?php echo (empty($header_html)) ? View::factory('themes/' . APP_THEME . '/template_header')->set($kohana_view_data) : $header_html; ?>

    <!- CONTENT START ***************************************************************************************->
    <div class="jqm-content" data-role="content">

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

<?php if (DEVELOPMENT_FLAG) { ?>
    <script src="//s3.amazonaws.com/private.trialto.com/media/cl4/js/cl4.js"></script>
    <script src="//s3.amazonaws.com/private.trialto.com/media/cl4/js/ajax.js"></script>
    <script src="//s3.amazonaws.com/private.trialto.com/media/cl4/js/model_create.js"></script>
<?php } else { ?>
    <script src="//s3.amazonaws.com/private.trialto.com/media/cl4/js/cl4.js"></script>
    <script src="//s3.amazonaws.com/private.trialto.com/media/cl4/js/ajax.js"></script>
<?php } ?>

<?php if ( ! empty($custom_js)) { ?>
    <?php foreach ($custom_js as $js)  echo EOL  . TAB . '<script src="' . $js . '"></script>'; ?>
<?php  } ?>

<script>
    $(document).on("mobileinit", function () {
        $.extend($.mobile, {
            ajaxEnabled: false
        });
    });
    $(function() {
        <?php echo ( ! empty($on_load_js)) ? $on_load_js . EOL : ''; ?>
    });
</script>

<a name="template_bottom"></a>
</body>
</html>
