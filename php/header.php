<?php
    require('../vendor/autoload.php');
    require('../php/functions.php');
    require('../php/db.php');
    session_start();

    $CDN_DOMAIN = getenv('CDN_DOMAIN');
    $MAIN_DOMAIN = getenv('MAIN_DOMAIN');
    $BRANDING = getenv('BRANDING');
    $SITE_VERSION = getenv('SITE_VERSION');
?>