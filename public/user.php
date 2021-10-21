<?php
require('/var/www/php/header.php');
require('/var/www/php/time_elapsed.php');
$total_pages_title = "";
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="manifest" href="/manifest.json">
    <?php include('/var/www/php/meta/icons.php')?>

    <!-- normal meta info -->
    <title>User</title>
    <meta name="description" content="User page">
    <meta name="theme-color" content="#212529">
        
    <!-- facebook meta info -->
    <meta property="og:title" content="User" />
    <meta property="og:description" content="User page">
    <meta property="og:image" content="/favicon.ico" />

    <!-- twitter meta info -->
    <meta name="twitter:title" content="User" />
    <meta name="twitter:description" content="User page" />
    <meta name="twitter:image" content="/favicon.ico" />

    <?php require('/var/www/php/page-deps.php') ?>
</head>

<body>
    <?php require('/var/www/php/navbar.php') ?>
    <div class="container-fluid mt-5 mb-4">
        <?php
        if (isset($_GET['name'])) {
            if (strlen($_GET['name']) == 0) {
                header('Location: /');
                exit();
            }

            $id = getIdByUser($client, $_GET['name']);

            if ($id == "") {
                header('Location: /');
                exit();
            }
            
            $video_amount = getUserVideoAmount($client, $id);
            $total_pages = getPageAmount($video_amount);
            $video_amount_total = getVideoAmount($client);
            $current_page = 1;
            
            if (isset($_GET['page']) && $_GET['page'] > 0) {
                $current_page = $_GET['page'];
            }

            if ($current_page > $total_pages) { // If invalid page
                header('Location: /user?name=' . $_GET['name'] . '&page=' . $total_pages);
                exit();
            } else if ($current_page < 1) {
                header('Location: /user?name=' . $_GET['name'] . '&page=1');
                exit();
            }

            if (isLoggedIn($client) && $_SESSION['user-id'] == $id) {
                $result = pg_query_params(
                    $client,
                    'SELECT * FROM stuff.videos WHERE author=$1 ORDER BY date DESC LIMIT 24 OFFSET $2',
                    array($id, ($current_page - 1) * 24)
                )
                    or die('Query failed: ' . pg_last_error());
            } else {
                $result = pg_query_params(
                    $client,
                    'SELECT * FROM stuff.videos WHERE visibility=0 and author=$1 ORDER BY date DESC LIMIT 24 OFFSET $2',
                    array($id, ($current_page - 1) * 24)
                )
                    or die('Query failed: ' . pg_last_error());
            }

            if (!$result) {
                header('Location: /');
                exit();
            }

            $views = getViewsAmount($client, $id);
            $views_total = getViewsTotal();

            require('/var/www/php/user-profile.php');

            echo '<div class="videos">';

            while ($entry = pg_fetch_array($result)) {
                require('/var/www/php/video.php');
            }

            pg_free_result($result);

            echo "</div>";

            get_pagination($current_page, '/user?name=' . $_GET['name'] . '&page=', $video_amount);
        }
        ?>
    </div>
</body>

</html>

<?php
pg_close($client);
?>