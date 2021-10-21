<?php
require('/var/www/php/header.php');
$page_title = 'Rules';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="manifest" href="/manifest.json">
    <?php include('/var/www/php/meta/icons.php')?>

    <!-- normal meta info -->
    <title><?php echo $page_title?></title>
    <meta name="description" content="Rules of this site">
    <meta name="theme-color" content="#212529">
    
    <!-- facebook meta info -->
    <meta property="og:title" content="<?php echo $page_title?>" />
    <meta property="og:description" content="Rules of this site">
    <meta property="og:image" content="/favicon.ico" />
    <meta property="og:url" content="/" />

    <!-- twitter meta info -->
    <meta name="twitter:title" content="<?php echo $page_title?>" />
    <meta name="twitter:description" content="Rules of this site" />
    <meta name="twitter:image" content="/favicon.ico" />

    <?php require('/var/www/php/page-deps.php') ?>
</head>

<body>
    <?php require('/var/www/php/navbar.php')?>

    <div class="container-fluid mt-4 mb-4">
        <p class="text-center">1. Be decent</p>
        <p class="text-center">2. If video unfunny i will delete</p>
    </div>
</body>

</html>