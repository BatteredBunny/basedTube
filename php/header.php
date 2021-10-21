<?php
    require('/var/www/vendor/autoload.php');
    require('/var/www/php/functions.php');
    require('/var/www/php/db.php');
    session_start();

    $CDN_DOMAIN = getenv('CDN_DOMAIN');
    $MAIN_DOMAIN = getenv('MAIN_DOMAIN');
    $BRANDING = getenv('BRANDING');
    $SITE_VERSION = getenv('SITE_VERSION');
?>