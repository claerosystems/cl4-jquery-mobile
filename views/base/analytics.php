<?php if (ANALYTICS_ID != '') { ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo ANALYTICS_ID; ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', '<?php echo ANALYTICS_ID; ?>');
    </script>
<?php } ?>