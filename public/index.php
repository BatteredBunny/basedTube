<?php
require('/var/www/php/header.php');
require('/var/www/php/time_elapsed.php');
$page_title = $BRANDING;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="manifest" href="/manifest.json">
    <?php include('/var/www/php/meta/icons.php') ?>

    <!-- normal meta info -->
    <title><?php echo $page_title ?></title>
    <meta name="description" content="Best youtube clone on the internet!!!">
    <meta name="theme-color" content="#212529">

    <!-- facebook meta info -->
    <meta property="og:title" content="<?php echo $page_title ?>" />
    <meta property="og:description" content="Best youtube clone on the internet!">
    <meta property="og:image" content="/favicon.ico" />
    <meta property="og:url" content="/" />

    <!-- twitter meta info -->
    <meta name="twitter:title" content="<?php echo $page_title ?>" />
    <meta name="twitter:description" content="Best youtube clone on the internet!" />
    <meta name="twitter:image" content="/favicon.ico" />

    <?php require('/var/www/php/page-deps.php') ?>
</head>

<body>
    <?php require('/var/www/php/navbar.php') ?>

    <div class="container-fluid mt-4 mb-4" data-simplebar>
        <div class="videos">
            <?php

            $current_page = 1;
            $video_amount = getVideoAmount($client);
            $total_pages = getPageAmount($video_amount);
            if (isset($_GET['page']) && $_GET['page'] > 0) {
                $current_page = $_GET['page'];
            }

            if ($video_amount != 0) {
                if ($current_page > $total_pages) { // If invalid page
                    header('Location: /?page=' . $total_pages);
                    exit();
                } else if ($current_page < 1) {
                    header('Location: /?page=1');
                    exit();
                }
            }

            if (isset($_GET['page']) && $_GET['page'] > 0) {
                $result = pg_query_params($client, 'SELECT "name", "views", "date", "id", "author", "visibility", "thumbnail_id", "file_id", "file_name" FROM stuff.videos ORDER BY date DESC LIMIT 24 OFFSET $1', array(($_GET['page'] - 1) * 24)) or die('Query failed: ' . pg_last_error());
            } else {
                $result = pg_query('SELECT "name", "views", "date", "id", "author", "visibility", "thumbnail_id", "file_id", "file_name" FROM stuff.videos ORDER BY date DESC LIMIT 24') or die('Query failed: ' . pg_last_error());
            }

            while ($entry = pg_fetch_array($result)) {
                if ($entry['visibility'] != 0) {
                    if (!isset($_SESSION['user-id'])) {
                        continue;
                    }

                    if (!isLoggedIn($client)) {
                        continue;
                    }

                    if ($entry['author'] != $_SESSION['user-id']) {
                        continue;
                    }
                }

                require('/var/www/php/video.php');
            }

            pg_free_result($result);
            ?>
        </div>

        <?php
        get_pagination($current_page, '/?page=', $video_amount);
        ?>
    </div>

    <footer>
        <?php if (isset($_SESSION["wants_intro"]) && $_SESSION["wants_intro"] == true) {
            echo '<script src="https://cdn.jsdelivr.net/npm/intro.js@4.2.2/intro.min.js"></script>';
            echo '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intro.js@4.2.2/introjs.min.css">';
            echo '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intro.js@4.2.2/themes/introjs-modern.min.css">';
            echo '<script src="/js/welcome.js"></script>';

            $_SESSION["wants_intro"] = false;
            unset($_SESSION["wants_intro"]);
        } ?>
    </footer>
</body>

</html>

<?php
pg_close($client);
?>